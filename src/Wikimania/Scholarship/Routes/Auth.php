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

use \Wikimania\Scholarship\Dao\User as UserDao;
use \Wikimania\Scholarship\Password;

/**
 * Routes related to authentication.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Auth {

	const NEXTPAGE_SESSION_KEY = 'NEXT_PAGE';
	const USER_SESSION_KEY = 'AUTH_USER_ID';

	/**
	 * Add routes to the given Slim application.
	 *
	 * @param \Slim\Slim $app Application
	 * @param string $prefix Route prefix
	 */
	public static function addRoutes ( \Slim\Slim $app, $prefix = '' ) {

		$app->map( "{$prefix}/login", function () use ($app) {
			if ( isset( $_SESSION[Auth::NEXTPAGE_SESSION_KEY] ) ) {
				$next = $_SESSION[Auth::NEXTPAGE_SESSION_KEY];
			} else {
				$next = $app->urlFor( 'review_home' );
			}

			if ( $app->request->isPost() ) {
				$uname = $app->request->post( 'username' );
				$app->view->setData( 'username', $uname );
				$pass = $app->request->post( 'password' );

				if ( $uname !== null && $pass !== null ) {
					$dao = new UserDao();
					$user = $dao->getUser( $uname );
					$check = Password::comparePasswordToHash( $pass, $user['password'] );

					if ( $check && !$user['blocked'] ) {
						// clear session
						foreach ( $_SESSION as $key => $value ) {
							unset( $_SESSION[$key] );
						}

						// generate new session id
						session_regenerate_id(true);

						// store authed user id
						$_SESSION[Auth::USER_SESSION_KEY] = $user['id'];

						$app->redirect( $next );

					} else {
						// FIXME: needs to use localization
						$app->flashNow( 'error', 'Login failed' );
					}
				}
			}

			$app->render( 'auth/login.html' );
		})->via( 'GET', 'POST' )->name( 'auth_login' );

		$app->get( "{$prefix}/logout", function () use ( $app ) {
			foreach ( $_SESSION as $key => $value ) {
				unset( $_SESSION[$key] );
			}

			// delete the session cookie on the client
			if ( ini_get( 'session.use_cookies' ) ) {
				$params = session_get_cookie_params();
				setcookie( session_name(), '', time() - 42000,
					$params['path'], $params['domain'],
					$params['secure'], $params['httponly']
				);
			}

			// destroy local session storage
			session_destroy();

			$app->redirect( $app->urlFor( 'home' ) );
		})->name( 'auth_logout' );

		$app->map( "{$prefix}/pwchange", function () use ( $app ) {
			if ( !isset( $_SESSION[Auth::USER_SESSION_KEY] ) ) {
				$_SESSION[Auth::NEXTPAGE_SESSION_KEY] = $app->request->getResourceUri();
				$app->redirect( $app->urlFor( 'auth_login' ) );
			}

			$dao = new UserDao();
			$userId = $_SESSION[Auth::USER_SESSION_KEY];
			$isadmin = $dao->isSysAdmin( $userId );

			if ( $app->request->isPost() ) {
				$oldPass = $app->request->post( 'oldpw' );
				$newPass = $app->request->post( 'newpw1' );
				$repeatPass = $app->request->post( 'newpw2' );
				$force = false;
				$id = $userId;

				if ( $newPass !== $repeatPass ) {
					$app->flashNow( 'error', 'Passwords do not match.' );

				} elseif ( empty( $newPass ) ) {
					$app->flashNow( 'error', 'Password can not be empty.' );

				} else {
					if ( $isadmin ) {
						$id = $app->request->post( 'id' );
						$force = $app->request->post( 'force' );
					}

					if ( $dao->updatePassword( $oldPass, $newPass, $id, $force ) ) {
						$app->flashNow( 'info', 'Password change successful.' );

					} else {
						$app->flashNow( 'error', 'Password change failed. Old password may be invalid.' );
					}
				}
			}

			$force = false;
			$id = $userId;
			if ( $isadmin ) {
				$id = $app->request->params( 'id' );
				if ( $id === null ) {
					$id = $userId;

				} elseif ( $id !== $userId ) {
					$force = true;
				}
			}
			$user = $dao->getUserInfo( $id );

			if ( !$user ) {
				$app->flash( 'error', 'Invalid user id.' );
				$app->redirect( $app->urlFor( 'auth_pwchange' ) );
			}

			$app->view->setData( 'user', $user );
			$app->view->setData( 'isadmin', $isadmin );
			$app->view->setData( 'force', $force );

			$app->render( 'auth/pwchange.html' );
		})->via( 'GET', 'POST' )->name( 'auth_pwchange' );

	}
}
