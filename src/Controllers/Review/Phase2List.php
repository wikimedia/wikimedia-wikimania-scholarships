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
 * Display a list of Phase 2 applications.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class Phase2List extends Controller {

	protected function handleGet() {
		$regionList = $this->dao->getRegionList();
		array_unshift( $regionList, 'All' );

		$globalnsList = [
			'Global South',
			'Global North',
		];

		$languageGroupList = [
			'Small',
			'Medium',
			'Large',
			'Multilingual',
		];

		array_unshift( $globalnsList, 'All' );
		array_unshift( $languageGroupList, 'All' );

		$this->form->requireInArray( 'region', $regionList, [
			'default' => 'All',
		] );
		$this->form->requireInArray( 'globalns', $globalnsList, [
			'default' => 'All',
		] );
		$this->form->requireInArray( 'languageGroup', $languageGroupList, [
			'default' => 'All',
		] );
		$this->form->expectBool( 'export' );
		$this->form->validate( $_GET );

		$region = $this->form->get( 'region' );
		$globalns = $this->form->get( 'globalns' );
		$languageGroup = $this->form->get( 'languageGroup' );
		$rows = $this->dao->getP2List( $region, $globalns, $languageGroup );

		if ( $this->request->get( 'export' ) ) {
			$ts = gmdate( 'Ymd\THi' );
			$this->response->headers->set( 'Content-type',
				'text/download; charset=utf-8' );
			$this->response->headers->set( 'Content-Disposition',
				'attachment; filename="' . "p2_{$region}_{$ts}" . '.csv"' );

			echo 'id,name,email,residence,region,"global north/south",';
			echo 'size,gender,age,"# p2 scorers",relexp,expshare,"p2 score"';
			echo "\n";

			$fp = fopen( 'php://output', 'w' );
			foreach ( $rows as $row ) {
				$csv = [
					(int)$row['id'],
					ltrim( "{$row['fname']} {$row['lname']}", '=@' ),
					ltrim( $row['email'], '=@' ),
					ltrim( $row['country_name'], '=@' ),
					ltrim( $row['region'], '=@' ),
					ltrim( $row['globalns'], '=@' ),
					ltrim( $row['size'], '=@' ),
					ltrim( $row['gender'], '=@' ),
					(int)$row['age'],
					(int)$row['nscorers'],
					round( $row['relexp'], 3 ),
					round( $row['expshare'], 3 ),
					round( $row['p2score'], 4 ),
				];
				fputcsv( $fp, $csv );
			}
			fclose( $fp );

		} else {
			$this->view->set( 'regionList', $regionList );
			$this->view->set( 'globalnsList', $globalnsList );
			$this->view->set( 'languageGroupList', $languageGroupList );
			$this->view->set( 'region', $region );
			$this->view->set( 'globalns', $globalns );
			$this->view->set( 'languageGroup', $languageGroup );
			$this->view->set( 'records', $rows );
			$this->render( 'review/p2/list.html' );
		}
	}

}
