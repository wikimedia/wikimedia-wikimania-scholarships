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

namespace Wikimania\Scholarship\Controllers\Review;

use Wikimania\Scholarship\Controller;

/**
 * Search applications.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Search extends Controller {

	protected function handleGet() {
		$this->form->expectString( 'l' );
		$this->form->expectString( 'f' );
		$this->form->expectString( 'r' );
		$this->form->expectString( 'rg' );
		$this->form->expectString( 'cs' );
		$this->form->expectString( 'ns' );
		$this->form->expectBool( 'p1' );
		$this->form->expectInt( 'items',
			array( 'min_range' => 1, 'max_range' => 250, 'default' => 50 )
		);
		$this->form->expectInt( 'p', array( 'min_range' => 0, 'default' => 0 ) );
		$this->form->validate( $_GET );

		$this->view->set( 'l', $this->form->get( 'l' ) );
		$this->view->set( 'f', $this->form->get( 'f' ) );
		$this->view->set( 'r', $this->form->get( 'r' ) );
		$this->view->set( 'rg', $this->form->get( 'rg' ) );
		$this->view->set( 'cs', $this->form->get( 'cs' ) );
		$this->view->set( 'ns', $this->form->get( 'ns' ) );
		$this->view->set( 'p1', $this->form->get( 'p1' ) );
		$this->view->set( 'items', $this->form->get( 'items' ) );
		$this->view->set( 'p', $this->form->get( 'p' ) );
		$this->view->set( 'found', null );

		if ( $this->form->get( 'l' ) || $this->form->get( 'f' ) ||
			$this->form->get( 'r' ) || $this->form->get( 'rg' ) ||
			$this->form->get( 'cs' ) || $this->form->get( 'ns' ) ||
			$this->form->get( 'p1' )
		) {

			$params = array(
				'first' => $this->form->get( 'f' ),
				'last' => $this->form->get( 'l' ),
				'residence' => $this->form->get( 'r' ),
				'region' => $this->form->get( 'rg' ),
				'size' => $this->form->get( 'cs' ),
				'globalns' =>$this->form->get( 'ns' ),
				'items' => $this->form->get( 'items' ),
				'page' => $this->form->get( 'p' ),
				'phase1' => $this->form->get( 'p1' ),
			);

			$ret = $this->dao->search( $params );
			$this->view->set( 'records', $ret->rows );
			$this->view->set( 'found', $ret->found );

			// pagination information
			list( $pageCount, $first, $last ) = $this->pagination(
				$ret->found, $this->form->get( 'p' ), $this->form->get( 'items' ) );
			$this->view->set( 'pages' , $pageCount );
			$this->view->set( 'left', $first );
			$this->view->set( 'right', $last );
		}

		$this->render( 'review/search.html' );
	}

}
