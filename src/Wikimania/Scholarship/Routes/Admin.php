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

use Wikimania\Scholarship\Dao\User as UserDao;
use Wikimania\Scholarship\Form;
use Wikimania\Scholarship\Password;

/**
 * Routes related to admin tasks.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Admin {

	/**
	 * Add routes to the given Slim application.
	 *
	 * @param \Slim\Slim $app Application
	 * @param string $prefix Route prefix
	 */
	public static function addRoutes ( \Slim\Slim $app, $prefix = '' ) {

		// FIXME: nasty copy-n-paste hack
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

		$requireAdmin = function () use ( $app ) {
			if ( !$app->view->get( 'isadmin' ) ) {
				$app->flash( 'error', 'Admin rights required' );
				$app->redirect( $app->urlFor( 'auth_login' ) );
			}
		};

		$app->get( "{$prefix}/users", $requireAuth, $requireAdmin, function () use ( $app ) {
			$form = new Form();
			$form->expectInArray( 'state', array( 'all', 'reviewer' ),
				array( 'default' => 'all') );
			$form->validate( $_GET );

			$state = $form->get( 'state' );
			$dao = new UserDao();
			$rows = $dao->GetListofUsers( $state );

			$app->view->set( 'state', $state );
			$app->view->set( 'records', $rows );
			$app->render( 'admin/users.html' );
		})->name( 'admin_users' );

		$app->map( "{$prefix}/user/:id", $requireAuth, $requireAdmin, function ( $id ) use ( $app ) {
			$dao = new UserDao();

			if ( $app->request->isPost() ) {
				$form = new Form();
				$form->expectString( 'username', array( 'required' => true ) );
				$form->expectString( 'password',
					array( 'required' => ( $id == 'new' ) )
				);
				$form->expectString( 'email', array( 'required' => true ) );
				$form->expectBool( 'reviewer' );
				$form->expectBool( 'isvalid' );
				$form->expectBool( 'isadmin' );
				$form->expectBool( 'blocked' );

				if ( $form->validate() ) {
					$user = array(
						'username' => $form->get( 'username' ),
						'email' => $form->get( 'email' ),
						'reviewer' => $form->get( 'reviewer' ),
						'isvalid' => $form->get( 'isvalid' ),
						'isadmin' => $form->get( 'isadmin' ),
						'blocked' => $form->get( 'blocked' ),
					);

					if ( $id == 'new' ) {
						$user['password'] = Password::encodePassword(
							$form->get( 'password' )
						);
						$newId = $dao->newUserCreate( $user );
						if ( $newId !== false ) {
							$app->flash( 'info', "User {$newId} created." );
							$id = $newId;

							mail( $user['email'],
								$app->wgLang->message( 'new-account-subject' ),
								wordwrap(
									sprintf( $app->wgLang->message( 'new-account-email' ),
										$user['username'], $form->get( 'password' )
									),
									72
								),
								"From: Wikimania Scholarships <wikimania-scholarships@wikimedia.org>\r\n" .
								"MIME-Version: 1.0\r\n" .
								"X-Mailer: Wikimania registration system\r\n" .
								"Content-type: text/plain; charset=utf-8\r\n" .
								"Content-Transfer-Encoding: 8bit"
							);

						} else {
							$app->flash( 'error', 'User creation failed. Check logs.' );
						}
						$app->redirect( $app->urlFor( 'admin_user', array( 'id' => $id ) ) );
					} else {
						$dao->updateUserInfo( $user, $id );
						$app->flashNow( 'info', 'Changes saved.' );
					}

				} else {
					$app->flashNow( 'error', 'Invalid submission.' );
					$app->view->set( 'errors', $form->getErrors() );
					$user = array(
						'username' => $form->get( 'username' ),
						'email' => $form->get( 'email' ),
						'reviewer' => $form->get( 'reviewer' ),
						'isvalid' => $form->get( 'isvalid' ),
						'isadmin' => $form->get( 'isadmin' ),
						'blocked' => $form->get( 'blocked' ),
					);
				}

			} else {
				if ( $id === 'new' ) {
					$user = array(
						'username' => '',
						'password' => Password::randomPassword( 12 ),
						'email' => '',
						'reviewer' => 0,
						'isvalid' => 1,
						'isadmin' => 0,
						'blocked' => 0,
					);

				} else {
					$user = $dao->getUserInfo( $id );
				}
			}

			$app->view->set( 'id', $id );
			$app->view->set( 'u', $user );
			$app->render( 'admin/user.html' );
		})->via( 'GET', 'POST' )->name( 'admin_user' );
	}
}
