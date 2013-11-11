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

namespace Wikimania\Scholarship;

use Wikimania\Scholarship\Form;

/**
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Controller {

	/**
	 * @var \Slim\Slim $slim
	 */
	protected $slim;

	/**
	 * @var \Wikimania\Scholarship\Dao\AbstractDao $dao
	 */
	protected $dao;

	/**
	 * @var \Wikimania\Scholarship\Form $form
	 */
	protected $form;


	public function __construct(\Slim\Slim $slim = null) {
		$this->slim = $slim ?: \Slim\Slim::getInstance();
		$this->form = new Form();
	}


	public function setDao( $dao ) {
		$this->dao = $dao;
	}


	public function setForm( $form ) {
		$this->form = $form;
	}


	protected function handle() {
		$this->slim->pass();
	}


	public function __invoke() {
		$argv = func_get_args();
		$method = $this->slim->request->getMethod();
		$mname = 'handle' . ucfirst( strtolower( $method ) );
		if ( method_exists( $this, $mname ) ) {
			call_user_func_array( array( $this, $mname ), $argv );
		} else {
			call_user_func_array( array( $this, 'handle' ), $argv );
		}
	}


	public function __call( $name, $args ) {
		if ( method_exists( $this->slim, $name ) ) {
			return call_user_func_array( array( $this->slim, $name ), $args );
		}
		// emulate default PHP behavior
		trigger_error(
			'Call to undefined method ' . __CLASS__ . '::' . $name . '()',
			E_USER_ERROR
		);
	}


	public function __get( $name ) {
		return $this->slim->{$name};
	}


	/**
	 * Compute pagination data.
	 *
	 * @param int $total Total records
	 * @param int $current Current page number (0-indexed)
	 * @param int $pageSize Number of items per page
	 * @param int $around Numer of pages to show on each side of current
	 * @return array Page count, first page index, last page index
	 */
	protected function pagination( $total, $current, $pageSize, $around = 4 ) {
		$pageCount = ceil( $total / $pageSize );
		$first = max( 0, $current - $around );
		$last = min( max( 0, $pageCount - 1 ), $current + 4 );
		return array( $pageCount, $first, $last );
	}

} //end Controller
