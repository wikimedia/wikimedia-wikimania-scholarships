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
 * Request an account recovery by providing an email address
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2015 Bryan Davis, Wikimedia Foundation and contributors.
 */
class Recover extends Controller {

	protected function handleGet() {
		$this->render( 'account/recover.html' );
	}

	protected function handlePost() {
		$this->form->requireEmail( 'email' );

		$dest = $this->urlFor( 'login' );
		if ( $this->form->validate() ) {
			$email = $this->form->get( 'email' );
			$data  = $this->dao->createPasswordResetToken( $email );
			foreach ( $data as $result ) {
				list( $token, $user ) = $result;
				if ( $token !== false ) {
					$this->sendEmail( $user, $token );
				}
			}
			$this->flash( 'info',
				$this->i18nContext->message( 'recover-account-success' )
			);
		} else {
			$this->flash( 'error',
				$this->i18nContext->message( 'recover-account-bad-input' )
			);
			$dest =  $this->urlFor( 'account_recover' );
		}

		$this->redirect( $dest );
	}

	protected function sendEmail( $user, $token ) {
		$sent = $this->mailer->mail(
			$user['email'],
			$this->i18nContext->message( 'recover-account-subject' ),
			$this->i18nContext->message( 'recover-account-email', [
				$user['username'],
				$this->request->getUrl() .
				$this->urlFor( 'account_reset', [
					'uid' => $user['id'],
					'token' => $token,
				] ),
				$this->request->getUrl() . $this->urlFor( 'home' ),
			] )
		);

		if ( !$sent ) {
			$this->log->error(
				'Failed to send reset email for user',
				[
					'method' => __METHOD__,
					'user' => $user['id'],
			] );
		}
	}

}
