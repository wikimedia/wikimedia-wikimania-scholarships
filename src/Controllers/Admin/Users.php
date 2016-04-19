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

namespace Wikimania\Scholarship\Controllers\Admin;

use Wikimedia\Slimapp\Controller;

/**
 * List users.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Users extends Controller {

	protected function handleGet() {
		$this->form->expectString( 'name' );
		$this->form->expectString( 'email' );
		$this->form->expectInt( 'items',
			[ 'min_range' => 1, 'max_range' => 250, 'default' => 50 ]
		);
		$this->form->expectInt( 'p', [ 'min_range' => 0, 'default' => 0 ] );
		$this->form->expectString( 's', [ 'default' => 'id' ] );
		$this->form->expectInArray( 'o', [ 'asc', 'desc' ],
			[ 'default' => 'asc' ]
		);
		$this->form->validate( $_GET );

		$this->view->set( 'name', $this->form->get( 'name' ) );
		$this->view->set( 'email', $this->form->get( 'email' ) );
		$this->view->set( 'items', $this->form->get( 'items' ) );
		$this->view->set( 'p', $this->form->get( 'p' ) );
		$this->view->set( 's', $this->form->get( 's' ) );
		$this->view->set( 'o', $this->form->get( 'o' ) );

		$params = [
			'name' => $this->form->get( 'name' ),
			'email' => $this->form->get( 'email' ),
			'sort' => $this->form->get( 's' ),
			'order' => $this->form->get( 'o' ),
			'items' => $this->form->get( 'items' ),
			'page' => $this->form->get( 'p' ),
		];

		$ret = $this->dao->search( $params );
		$this->view->set( 'records', $ret->rows );
		$this->view->set( 'found', $ret->found );

		// pagination information
		list( $pageCount, $first, $last ) = $this->pagination(
			$ret->found, $this->form->get( 'p' ), $this->form->get( 'items' ) );
		$this->view->set( 'pages', $pageCount );
		$this->view->set( 'left', $first );
		$this->view->set( 'right', $last );

		$this->render( 'admin/users.html' );
	}

}
