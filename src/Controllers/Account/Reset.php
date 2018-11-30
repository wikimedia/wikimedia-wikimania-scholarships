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

namespace Wikimania\Scholarship\Controllers\Account;

use Wikimedia\Slimapp\Controller;

/**
 * Reset password using recovery token
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2015 Bryan Davis, Wikimedia Foundation and contributors.
 */
class Reset extends Controller {

	/**
	 * @param int $id User ID
	 * @param string $token Password reset token
	 */
	protected function handleGet( $id, $token ) {
		if ( $this->dao->validatePasswordResetToken( $id, $token ) ) {
			$this->view->set( 'id', $id );
			$this->view->set( 'token', $token );
			$this->render( 'account/reset.html' );

		} else {
			$this->flash( 'error',
				$this->i18nContext->message( 'reset-password-bad-token' ) );
			$this->redirect( $this->urlFor( 'account_recover' ) );
		}
	}

	/**
	 * @param int $id User ID
	 * @param string $token Password reset token
	 */
	protected function handlePost( $id, $token ) {
		$this->form->requireString( 'newpw1' );
		$this->form->requireString( 'newpw2' );

		$dest = $this->urlFor( 'account_reset', [
			'uid' => $id,
			'token' => $token,
		] );

		if ( $this->form->validate() ) {
			$newPass = $this->form->get( 'newpw1' );

			if ( $newPass !== $this->form->get( 'newpw2' ) ) {
				$this->flash( 'error',
					$this->i18nContext->message( 'reset-password-no-match' ) );

			} elseif ( empty( $newPass ) ) {
				$this->flash( 'error',
					$this->i18nContext->message( 'reset-password-empty' ) );

			} else {
				if ( $this->dao->resetPassword( $id, $token, $newPass ) ) {
					$this->flash( 'info',
						$this->i18nContext->message( 'reset-password-success' ) );
					$dest = $this->urlFor( 'login' );
				} else {
					$this->flash( 'error',
						$this->i18nContext->message( 'reset-password-fail' ) );
				}
			}
		} else {
			$this->flash( 'error',
				$this->i18nContext->message( 'reset-password-invalid' ) );
		}

		$this->redirect( $dest );
	}

}
