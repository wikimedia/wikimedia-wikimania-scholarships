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
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	echo "MW_INSTALL_PATH not found in environment.\n";
	echo "Did you remember to use mwscript to run this script?\n";
	exit(1);
}

require_once "$IP/maintenance/Maintenance.php";

/**
 * Print a list of wikis suitable for use in the Wikimania\Scholarship\Wiki
 * class.
 *
 * Usage: mwscript ../../../../..$(pwd)/listOfWikis.php --wiki enwiki
 */
class ListOfWikis extends Maintenance {
	public function execute() {
		global $wgLocalDatabases, $wgSiteMatrixPrivateSites, $wgSiteMatrixFishbowlSites, $wgSiteMatrixClosedSites;

		$list = array_diff(
				$wgLocalDatabases,
				$wgSiteMatrixPrivateSites,
				$wgSiteMatrixFishbowlSites,
				$wgSiteMatrixClosedSites
			);

		foreach ( $list as $dbname ) {
			$host = WikiMap::getWikiName( $dbname );
			if ( strpos( $host, '.' ) ) {
				echo "'{$host}',\n";
			}
		}
	}
}

$maintClass = "ListOfWikis";
require_once RUN_MAINTENANCE_IF_MAIN;
