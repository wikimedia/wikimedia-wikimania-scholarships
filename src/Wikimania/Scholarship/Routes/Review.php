<?php
/**
 * @section LICENSE
 * This file is part of Wikimania Scholarship Application.
 *
 * Wikimania Scholarship Application is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * Wikimania Scholarship Application is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
 * Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with Wikimania Scholarship Application.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * @file
 */

namespace Wikimania\Scholarship\Routes;

use Wikimania\Scholarship\Dao\Apply as ApplyDao;
use Wikimania\Scholarship\Form;

/**
 * Routes related to reviewing applications.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Review {

	/**
	 * Add routes to the given Slim application.
	 *
	 * @param \Slim\Slim $app Application
	 * @param string $prefix Route prefix
	 */
	public static function addRoutes ( \Slim\Slim $app, $prefix = '' ) {

		$requireAuth = function () use ( $app ) {
			if ( !isset( $_SESSION[Auth::USER_SESSION_KEY] ) ) {
				$_SESSION[Auth::NEXTPAGE_SESSION_KEY] = $app->request->getResourceUri();
				$app->redirect( $app->urlFor( 'auth_login' ) );
			}
		};

		$app->get( "{$prefix}/", $requireAuth, function () use ( $app ) {
			$app->redirect( $app->urlFor( 'review_phase1' ) );
		})->name( 'review_home' );

		$phaseGrid = function ( $phase ) use ( $app ) {
			return function () use ( $phase, $app ) {
				$form = new Form();
				$form->expectInArray( 'apps',
					array( 'unreviewed', 'all', 'myapps' ),
					array( 'default' => 'all' )
				);
				$form->expectInt( 'items',
					array( 'min' => 1, 'max' => 250, 'default' => 50 )
				);
				$form->expectInt( 'p', array( 'min' => 0, 'default' => 0 ) );
				$form->expectInt( 'min', array( 'default' => -2 ) );
				$form->expectInt( 'max', array( 'default' => 999 ) );
				$form->validate( $_GET );

				$dao = new ApplyDao();
				$params = array(
					'min' => $form->get( 'min' ),
					'max' => $form->get( 'max' ),
					'phase' => $phase,
					'items' => $form->get( 'items' ),
					'page' => $form->get( 'p' ),
					'apps' => $form->get( 'apps' ),
				);
				$ret = $dao->gridData( $params );

				$app->view->setData( 'phase', $phase );
				$app->view->setData( 'records', $ret->rows );
				$app->view->setData( 'found', $ret->found );

				// pagination information
				$params['pages'] = ceil( $ret->found / $form->get( 'items' ) );
				$params['left'] = max( 0, $params['page'] - 4 );
				$params['right'] = min( max( 0, $params['pages'] - 1 ), $params['page'] + 4 );
				$app->view->setData( 'params', $params );

				$app->render( 'review/grid.html' );
			};
		};

		$app->get( "{$prefix}/phase1", $requireAuth,
			$phaseGrid( 1 ) )->name( 'review_phase1' );

		$app->get( "{$prefix}/phase2", $requireAuth,
			$phaseGrid( 2 ) )->name( 'review_phase2' );

	}
}
