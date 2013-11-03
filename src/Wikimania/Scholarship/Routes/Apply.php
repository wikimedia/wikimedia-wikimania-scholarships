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

use Wikimania\Scholarship\Countries;
use Wikimania\Scholarship\Forms\Apply as ApplyForm;

/**
 * Routes related to applying for a scholarship.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Apply {

	/**
	 * Add routes to the given Slim application.
	 *
	 * @param \Slim\Slim $app Application
	 * @param string $prefix Route prefix
	 */
	public static function addRoutes ( \Slim\Slim $app, $prefix = '' ) {
		$app->map( "{$prefix}/apply", function () use ($app) {
			$form = new ApplyForm();

			$submitted = false;
			if ( $app->request->isPost() ) {
				if ( $form->validate() ) {
					// input is valid, save to database
					if ( $form->save() ) {
						// send confirmation email
						$message = $app->wgLang->message( 'form-email-response' );
						$message = preg_replace( '/\$1/',
							"{$form->get('fname')} {$form->get('lname')}", $message );

						//FIXME: using mail directly?
						mail( $form->get('email'),
							$app->wgLang->message( 'form-email-subject' ),
							wordwrap( $message, 72 ),
							"From: Wikimania Scholarships <wikimania-scholarships@wikimedia.org>\r\n" .
							"MIME-Version: 1.0\r\n" .
							"X-Mailer: Wikimania registration system\r\n" .
							"Content-type: text/plain; charset=utf-8\r\n" .
							"Content-Transfer-Encoding: 8bit" );
						$submitted = true;
					}
				}
			}

/*
			if ( isset( $_GET['special'] ) ) {
				$special = true;
			}

			$twigCtx['mock'] = $mock;
			$twigCtx['registration_open'] = time() > $open_time;
			$twigCtx['registration_closed'] = time() > $close_time && !isset( $special );
 */
			// FIXME: these need to come from config
			$app->view->setData( 'mock', true );
			$app->view->setData( 'registration_open', true );
			$app->view->setData( 'registration_closed', false );

			$app->view->setData( 'form', $form );
			$app->view->setData( 'submitted', $submitted );
			$countries = Countries::$COUNTRY_NAMES;
			asort( $countries );
			$app->view->setData( 'countries', $countries );

			$app->render( 'apply.html' );
		})->via( 'GET', 'POST' )->name( 'apply' );

		$app->get( "{$prefix}/contact", function () use ($app) {
			$app->render( 'contact.html' );
		})->name( 'contact' );

		$app->get( "{$prefix}/credits", function () use ($app) {
			$app->render( 'credits.html' );
		})->name( 'credits' );

		$app->get( "{$prefix}/privacy", function () use ($app) {
			$app->render( 'privacy.html' );
		})->name( 'privacy' );

		$app->get( "{$prefix}/translate", function () use ($app) {
			$app->render( 'translate.html' );
		})->name( 'translate' );
	}
}
