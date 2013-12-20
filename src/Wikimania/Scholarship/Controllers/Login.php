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

namespace Wikimania\Scholarship\Controllers;

use Wikimania\Scholarship\AuthManager;
use Wikimania\Scholarship\Controller;

/**
 * Routes related to authentication.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Login extends Controller {

	protected function handleGet() {
		$this->render( 'login.html' );
	}

	protected function handlePost() {
		$next = false;
		if ( isset( $_SESSION[AuthManager::NEXTPAGE_SESSION_KEY] ) ) {
			$next = $_SESSION[AuthManager::NEXTPAGE_SESSION_KEY];
			$next = filter_var( $next, \FILTER_VALIDATE_URL, \FILTER_FLAG_PATH_REQUIRED );
		}

		if ( $next === false ) {
			$next = $this->urlFor( 'review_home' );
		}

		$this->form->expectString( 'username', array( 'required' => true ) );
		$this->form->expectString( 'password', array( 'required' => true ) );

		if ( $this->form->validate() ) {
			$authed = $this->authManager->authenticate(
				$this->form->get( 'username' ),
				$this->form->get( 'password' )
			);

			if ( $authed ) {
				$this->flash( 'info', $this->wgLang->message( 'login-success' ) );
				$this->redirect( $next );

			} else {
				$this->flash( 'error', $this->wgLang->message( 'login-failed' ) );
				$this->log->info( 'Failed login attempt for {username}', array(
					'username' => $this->form->get( 'username' ),
				) );
			}

		} else {
			$this->flash( 'error', $this->wgLang->message( 'login-error' ) );
		}

		$this->redirect( $this->urlFor( 'login' ) );
	}

}
