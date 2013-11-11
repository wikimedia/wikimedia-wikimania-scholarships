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
		if ( isset( $_SESSION[AuthManager::NEXTPAGE_SESSION_KEY] ) ) {
			$next = $_SESSION[AuthManager::NEXTPAGE_SESSION_KEY];

		} else {
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
				// FIXME: should use localization
				$this->flash( 'info', 'Login successful.' );
				$this->redirect( $next );

			} else {
				// FIXME: should use localization
				$this->flash( 'error', 'Login failed.' );
			}

		} else {
				// FIXME: should use localization
			$this->flash( 'error', 'Username and password required.' );
		}

		$this->redirect( $this->urlFor( 'login' ) );
	}

}
