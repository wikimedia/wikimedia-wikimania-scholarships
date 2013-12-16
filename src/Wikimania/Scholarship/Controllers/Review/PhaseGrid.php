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
 * Display a grid of reviews in a given phase.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class PhaseGrid extends Controller {

	/**
	 * @var int $phase
	 */
	protected $phase;

	public function setPhase( $p ) {
		$this->phase = $p;
	}

	protected function handleGet() {
		$this->form->expectInArray( 'apps',
			array( 'unreviewed', 'all', 'myapps' ),
			array( 'required' => true, 'default' => 'all' )
		);
		$this->form->expectInt( 'items',
			array( 'min_range' => 1, 'max_range' => 250, 'default' => 50 )
		);
		$this->form->expectInt( 'p', array( 'min_range' => 0, 'default' => 0 ) );
		$this->form->expectInt( 'min', array( 'default' => -2 ) );
		$this->form->expectInt( 'max', array( 'default' => 999 ) );
		$this->form->validate( $_GET );

		$params = array(
			'min' => $this->form->get( 'min' ),
			'max' => $this->form->get( 'max' ),
			'phase' => $this->phase,
			'items' => $this->form->get( 'items' ),
			'page' => $this->form->get( 'p' ),
			'apps' => $this->form->get( 'apps' ),
		);
		$ret = $this->dao->gridData( $params );

		$this->view->set( 'apps', $this->form->get( 'apps' ) );
		$this->view->set( 'phase', $this->phase );
		$this->view->set( 'records', $ret->rows );
		$this->view->set( 'found', $ret->found );

		// pagination information
		list( $pageCount, $first, $last ) = $this->pagination(
			$ret->found, $params['page'], $params['items'] );
		$params['pages'] = $pageCount;
		$params['left'] = $first;
		$params['right'] = $last;

		$this->view->set( 'params', $params );

		$this->render( 'review/grid.html' );
	}

}
