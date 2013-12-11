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
 * Display a list of Phase 2 applications.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Phase2List extends Controller {

	protected function handleGet() {
		$regionList = $this->dao->getRegionList();
		array_unshift( $regionList, 'All' );

		$this->form->expectInt( 'partial', array( 'default' => 0 ) );
		$this->form->expectInArray( 'region', $regionList, array( 'default' => 'All' ) );
		$this->form->expectBool( 'export' );
		$this->form->validate( $_GET );


		$partial = $this->form->get( 'partial' );
		$region = $this->form->get( 'region' );

		$rows = $this->dao->getP2List( $partial, $region );

		if ( $this->request->get( 'export' ) ) {
			if ( $partial === 0 ) {
				$partialName = 'full';
			} elseif ( $partial == 1 ) {
				$partialName = 'partial';
			} else {
				$partialName = 'all';
			}

			$ts = gmdate( 'Ymd\THi' );
			$this->response->headers->set( 'Content-type',
				'text/download; charset=utf-8' );
			$this->response->headers->set( 'Content-Disposition',
				'attachment; filename="' . "p2_{$partialName}_{$region}_{$ts}" . '.csv"' );

			echo 'id,name,email,residence,sex,age,"partial?","# p2 scorers",onwiki,offwiki,future,"English Ability","p2 score"', "\n";

			$fp = fopen( 'php://output', 'w' );
			foreach ( $rows as $row ) {
				$csv = array(
					(int)$row['id'],
					ltrim( "{$row['fname']} {$row['lname']}", '=@' ),
					ltrim( $row['email'], '=@' ),
					ltrim( $row['country_name'], '=@' ),
					ltrim( $row['sex'], '=@' ),
					(int)$row['age'],
					(int)$row['partial'],
					(int)$row['nscorers'],
					round( $row['onwiki'], 3 ),
					round( $row['offwiki'], 3 ),
					round( $row['future'], 3 ),
					round( $row['englishAbility'], 3 ),
					round( $row['p1score'], 4 ),
				);
				fputcsv( $fp, $csv );
			}
			fclose( $fp );

		} else {
			$this->view->set( 'regionList', $regionList );
			$this->view->set( 'partial', $partial );
			$this->view->set( 'region', $region );
			$this->view->set( 'records', $rows );
			$this->render( 'review/p2/list.html' );
		}
	}

}
