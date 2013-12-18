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

use Wikimania\Scholarship\Controller;
use Wikimania\Scholarship\Countries;
use Wikimania\Scholarship\Forms\Apply as ApplyForm;
use Wikimania\Scholarship\Wikis;

/**
 * Process a scholarship application.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class ScholarshipApplication extends Controller {

	/**
	 * @var int $applicationOpen
	 */
	protected $periodOpen;

	/**
	 * @var int $applicationClose
	 */
	protected $periodClose;


	/**
	 * @param int $open Unix timestanp that applications will first be accepted
	 * @param int $close Unix timestamp that applications will last be accepted
	 * @param \Slim\Slim $slim Slim application
	 */
	public function __construct( $open, $close, \Slim\Slim $slim = null ) {
		parent::__construct( $slim );
		$this->periodOpen = $open;
		$this->periodClose = $close;
	}

	protected function handle() {
			$submitted = false;

			if ( $this->slim->request->isPost() ) {
				// FIXME: get/post should be split and use redirects but I'm too lazy
				// to do that right now. It would be nice if Controller handled most
				// of the heavy lifting for that. Slim's `flash` is nice but rigged to
				// be very view specific rather than a generic internally data passing
				// system.
				if ( $this->form->validate() ) {
					// input is valid, save to database
					if ( $this->form->save() ) {
						// send confirmation email
						$message = $this->slim->wgLang->message( 'form-email-response' );
						$message = preg_replace( '/\$1/',
							"{$this->form->get('fname')} {$this->form->get('lname')}", $message );

						$this->mailer->mail(
							$this->form->get('email'),
							$this->slim->wgLang->message( 'form-email-subject' ),
							$message
						);
						$submitted = true;

					} else {
						$this->flashNow( 'error',
							$this->slim->wgLang->message( 'form-save-error' )
						);
					}
				}
			}

			$now = time();

			$this->slim->view->setData( 'registration_open',
				$now > $this->periodOpen || $this->slim->mock );
			// FIXME: legacy app allowed '?special' to override
			$this->slim->view->setData( 'registration_closed',
				$now > $this->periodClose && !$this->slim->mock );

			$this->slim->view->setData( 'form', $this->form );
			$this->slim->view->setData( 'submitted', $submitted );
			$this->slim->view->setData( 'countries', Countries::$COUNTRY_NAMES );
			$this->slim->view->setData( 'wikilist', Wikis::$WIKI_NAMES );

			$this->slim->render( 'apply.html' );
	}

} //end Apply
