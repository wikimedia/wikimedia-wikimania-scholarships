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

use Wikimedia\Slimapp\Controller;

/**
 * Export full report details.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2015 Bryan Davis and Wikimedia Foundation.
 */
class Export extends Controller {

	protected function handleGet() {
		switch ( $this->request->get( 'action' ) ) {
			case 'export':
				$this->actionExport();
				break;
			default:
				$this->actionDefault();
		}
	}

	protected function actionDefault() {
		$this->form->expectInt( 'items',
			array( 'min_range' => 1, 'max_range' => 250, 'default' => 50 )
		);
		$this->form->expectInt( 'p', array( 'min_range' => 0, 'default' => 0 ) );
		$this->form->validate( $_GET );
		$this->view->set( 'items', $this->form->get( 'items' ) );
		$this->view->set( 'p', $this->form->get( 'p' ) );

		$params = array(
			'items' => $this->form->get( 'items' ),
			'page' => $this->form->get( 'p' ),
		);

		$ret = $this->dao->export( $params );
		$this->view->set( 'records', $ret->rows );
		$this->view->set( 'found', $ret->found );

		// pagination information
		list( $pageCount, $first, $last ) = $this->pagination(
			$ret->found, $this->form->get( 'p' ), $this->form->get( 'items' ) );
		$this->view->set( 'pages', $pageCount );
		$this->view->set( 'left', $first );
		$this->view->set( 'right', $last );

		$this->render( 'review/export.html' );
	}

	protected function actionExport() {
		$ret = $this->dao->export( array( 'items' => 'all' ) );

		$ts = gmdate( 'Ymd\THi' );
		$this->response->headers->set( 'Content-type',
			'text/download; charset=utf-8' );
		$this->response->headers->set( 'Content-Disposition',
			'attachment; filename="export_' . $ts . '.csv"' );

		if ( $ret->found ) {
			$fp = fopen( 'php://output', 'w' );
			fputcsv( $fp, array_keys( $ret->rows[0] ) );
			foreach ( $ret->rows as $row ) {
				fputcsv( $fp, $row );
			}
			fclose( $fp );
		}
	}
}
