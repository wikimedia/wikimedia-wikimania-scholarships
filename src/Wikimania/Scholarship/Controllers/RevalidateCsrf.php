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
use Wikimania\Scholarship\CsrfMiddleware;

/**
 * Routes related to authentication.
 *
 * @author Niharika Kohli <niharikakohli29@gmail.com>
 * @copyright © 2013 Niharika Kohli and Wikimedia Foundation.
 */
class RevalidateCsrf extends Controller {

	public function __construct( \Slim\Slim $slim = null ) {
		parent::__construct( $slim );
	}

	protected function handleGet() {
		// Touch the key in the session to make sure it is kept alive
		$token = $_SESSION[CsrfMiddleware::PARAM];
		$_SESSION[CsrfMiddleware::PARAM] = $token;
		$this->response()->header(
			'Content-Type',
			'application/json;charset=utf-8'
		);
		$this->response()->header(
			'Cache-Control',
			'no-cache, no-store, must-revalidate'
		);
		$this->response()->header( 'Pragma', 'no-cache' );
		$this->response()->header( 'Expires', '0' );
		$this->render( 'csrf.json' );
	}

}
