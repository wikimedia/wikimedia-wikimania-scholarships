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
use Wikimania\Scholarship\Dao\User as UserDao;
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

		/**
		 * Simple authentication required route middleware.
		 */
		$requireAuth = function () use ( $app ) {
			if ( !isset( $_SESSION[Auth::USER_SESSION_KEY] ) ) {
				$_SESSION[Auth::NEXTPAGE_SESSION_KEY] = $app->request->getResourceUri();
				$app->redirect( $app->urlFor( 'auth_login' ) );
			}
			//FIXME: stick a user object in the session?
			$dao = new UserDao();
			$id = $_SESSION[Auth::USER_SESSION_KEY];
			$app->view->set( 'userId', $id );
			$app->view->set( 'username', $dao->getUsername( $id ) );
			$app->view->set( 'isadmin', $dao->isSysAdmin( $id ) );
		};


		$app->get( "{$prefix}/", $requireAuth, function () use ( $app ) {
			$app->redirect( $app->urlFor( 'review_phase1' ) );
		})->name( 'review_home' );


		$app->map( "{$prefix}/view", $requireAuth, function () use ( $app ) {
			$form = new Form();
			$form->expectInt( 'phase',
				array( 'min' => 0, 'max' => 2, 'default' => 2 )
			);

			$dao = new ApplyDao();

			if ( $app->request->isPost() ) {
				$form->expectInt( 'id', array( 'min' => 0, 'required' => true ) );
				$form->expectString( 'notes' );

				if ( $form->validate() ) {
					$id = $form->get( 'id' );
					$criteria = array(
						'valid', 'onwiki', 'future', 'offwiki', 'program',
						'englishAbility' );

					foreach ( $criteria as $c ) {
						$score = $app->request->post( $c );
						if ( $score !== null ) {
							$dao->insertOrUpdateRanking( $id, $c, $score );
						}
					}

					if ( $form->get( 'notes' ) !== null ) {
						$dao->updateNotes( $id, $form->get( 'notes' ) );
					}
					$app->flash( 'info', 'Changes saved.' );

				} else {
					// FIXME: log error(s)
					$app->flash( 'error', 'Invalid submission' );
				}
				$phase = $form->get( 'phase' );
				$app->redirect( $app->urlFor( 'review_view' ) . "?id={$id}&phase={$phase}" );
			}

			$form->validate( $_GET );
			$phase = $form->get( 'phase' );
			$userId = $_SESSION[Auth::USER_SESSION_KEY];


			$id = $app->request->get( 'id' );
			if ( $id === null || $id < 0 ) {
				$unreviewed = $dao->myUnreviewed( $userId, $phase );
				$id = min( $unreviewed );
			}

			if ( $id == '' or $id < 0 ) {
				$app->flashNow( 'error', "Plase click Phase {$phase} to return to the list" );
			}

			$app->view->set( 'phase', $phase );
			$app->view->set( 'id', $id );
			$app->view->set( 'myscorings', $dao->myRankings( $id, $phase ) );
			$app->view->set( 'allscorings', $dao->allRankings( $id, $phase ) );
			$app->view->set( 'reviewers', $dao->getReviewers( $id, $phase ) );
			$app->view->set( 'nextid', $dao->nextApp( $id, $phase ) );
			$app->view->set( 'previd', $dao->prevApp( $id, $phase ) );
			$app->view->set( 'schol', $dao->getScholarship( $id ) );

			$app->render( 'review/view.html' );
		})->via( 'GET', 'POST' )->name( 'review_view' );

		// Route factory for phase1 & phase2 review queues
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

				$app->view->set( 'phase', $phase );
				$app->view->set( 'records', $ret->rows );
				$app->view->set( 'found', $ret->found );

				// pagination information
				$params['pages'] = ceil( $ret->found / $form->get( 'items' ) );
				$params['left'] = max( 0, $params['page'] - 4 );
				$params['right'] = min( max( 0, $params['pages'] - 1 ), $params['page'] + 4 );
				$app->view->set( 'params', $params );

				$app->render( 'review/grid.html' );
			};
		};


		$app->get( "{$prefix}/phase1", $requireAuth,
			$phaseGrid( 1 ) )->name( 'review_phase1' );


		$app->get( "{$prefix}/phase2", $requireAuth,
			$phaseGrid( 2 ) )->name( 'review_phase2' );


		$app->get( "{$prefix}/p1/successList", $requireAuth, function () use ( $app ) {

			$dao = new ApplyDao();
			$rows = $dao->getPhase1Success();

			if ( $app->request->get( 'action' ) == 'export' ) {
				$ts = gmdate( 'Ymd\THi' );
				$app->response->headers->set( 'Content-type',
					'text/download; charset=utf-8' );
				$app->response->headers->set( 'Content-Disposition',
					'attachment; filename="p1success_' . $ts . '.csv"' );

				echo "id,name,email,p1score\n";
				foreach ( $rows as $row ) {
					echo "{$row['id']},{$row['fname']} {$row['lname']},{$row['email']},";
					echo round( $row['p1score'], 4 );
					echo "\n";
				}

			} else {
				$app->view->set( 'title', 'Phase 1 - Success List' );
				$app->view->set( 'records', $rows );
				$app->render( 'review/p1/list.html' );
			}
		})->name( 'review_p1_success' );

		$app->get( "{$prefix}/p1/failList", $requireAuth, function () use ( $app ) {

			$dao = new ApplyDao();
			$rows = $dao->getPhase1EarlyRejects();

			if ( $app->request->get( 'action' ) == 'export' ) {
				$ts = gmdate( 'Ymd\THi' );
				$app->response->headers->set( 'Content-type',
					'text/download; charset=utf-8' );
				$app->response->headers->set( 'Content-Disposition',
					'attachment; filename="p1fail_' . $ts . '.csv"' );

				echo "id,name,email,p1score\n";
				foreach ( $rows as $row ) {
					echo "{$row['id']},{$row['fname']} {$row['lname']},{$row['email']},";
					echo round( $row['p1score'], 4 );
					echo "\n";
				}

			} else {
				$app->view->set( 'title', 'Phase 1 - Fail List' );
				$app->view->set( 'records', $rows );
				$app->render( 'review/p1/list.html' );
			}
		})->name( 'review_p1_fail' );


		$app->get( "{$prefix}/p2/list", $requireAuth, function () use ( $app ) {
			$dao = new ApplyDao();
			$regionList = $dao->getRegionList();
			array_unshift( $regionList, 'All' );

			$form = new Form();
			$form->expectInt( 'partial', array( 'default' => 0 ) );
			$form->expectInArray( 'region', $regionList, array( 'default' => 'All' ) );
			$form->expectBool( 'export' );
			$form->validate( $_GET );


			$partial = $form->get( 'partial' );
			$region = $form->get( 'region' );

			$rows = $dao->getP2List( $partial, $region );

			if ( $app->request->get( 'export' ) ) {
				if ( $partial === 0 ) {
					$partialName = 'full';
				} elseif ( $partial == 1 ) {
					$partialName = 'partial';
				} else {
					$partialName = 'all';
				}

				$ts = gmdate( 'Ymd\THi' );
				$app->response->headers->set( 'Content-type',
					'text/download; charset=utf-8' );
				$app->response->headers->set( 'Content-Disposition',
					'attachment; filename="' . "p2_{$partialName}_{$region}_{$ts}" . '.csv"' );

				echo "id,name,email,residence,sex,age,partial?,# p2 scorers,onwiki,offwiki,future,English Ability,p2 score\n";
				foreach ( $rows as $row ) {
					echo "{$row['id']},{$row['fname']} {$row['lname']},{$row['email']},";
					echo "{$row['country_name']},{$row['sex']},{$row['age']},";
					echo "{$row['partial']},{$row['nscorers']},";
					echo round( $row['onwiki'], 3 ) . ',';
					echo round( $row['offwiki'], 3 ) . ',';
					echo round( $row['future'], 3 ) . ',';
					echo round( $row['englishAbility'], 3 ) . ',';
					echo round( $row['p2score'], 4 ) . "\n";
				}

			} else {
				$app->view->set( 'regionList', $regionList );
				$app->view->set( 'partial', $partial );
				$app->view->set( 'region', $region );
				$app->view->set( 'records', $rows );
				$app->render( 'review/p2/list.html' );
			}
		})->name( 'review_p2_list' );

	} // end addRoutes
} // end class Review
