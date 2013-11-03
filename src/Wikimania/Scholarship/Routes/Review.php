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
				$app->redirect( $app->urlFor( 'login' ) );
			}
		};

		$app->get( "{$prefix}/", $requireAuth, function () use ( $app ) {
			$app->redirect( $app->urlFor( 'review_phase1' ) );
		})->name( 'review_home' );

		$app->get( "{$prefix}/phase1", $requireAuth, function () use ( $app ) {
			$app->render( 'review/base.html' );
		})->name( 'review_phase1' );

		$app->get( "{$prefix}/phase2", $requireAuth, function () use ( $app ) {
			$app->render( 'review/base.html' );
		})->name( 'review_phase2' );

	}
}
