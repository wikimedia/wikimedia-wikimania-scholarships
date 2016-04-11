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
 * Display a list of Phase 1 applications.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Phase1List extends Controller {

	const TYPE_SUCCESS = 'success';
	const TYPE_FAIL = 'fail';

	/**
	 * @var string $type
	 */
	protected $type = self::TYPE_SUCCESS;

	public function setType( $type ) {
		$this->type = $type;
	}

	protected function getRows() {
		if ( $this->type === self::TYPE_SUCCESS ) {
			return $this->dao->getPhase1Success();
		} else {
			return $this->dao->getPhase1EarlyRejects();
		}
	}

	protected function handleGet() {
		$rows = $this->getRows();

		if ( $this->request->get( 'action' ) == 'export' ) {
			$ts = gmdate( 'Ymd\THi' );
			$this->response->headers->set( 'Content-type',
				'text/download; charset=utf-8' );
			$this->response->headers->set( 'Content-Disposition',
				'attachment; filename="p1' . $this->type . '_' . $ts . '.csv"' );

			echo "id,name,email,p1score\n";
			$fp = fopen( 'php://output', 'w' );
			foreach ( $rows as $row ) {
				$csv = [
					(int)$row['id'],
					ltrim( "{$row['fname']} {$row['lname']}", '=@' ),
					ltrim( $row['email'], '=@' ),
					round( $row['p1score'], 4 ),
				];
				fputcsv( $fp, $csv );
			}
			fclose( $fp );

		} else {
			$this->view->set( 'title',
				'Phase 1 - ' . ucfirst( $this->type ) . ' List' );
			$this->view->set( 'records', $rows );
			$this->render( 'review/p1/list.html' );
		}
	}

}
