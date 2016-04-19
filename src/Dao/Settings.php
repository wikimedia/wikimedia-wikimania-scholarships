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

namespace Wikimania\Scholarship\Dao;

use Wikimedia\Slimapp\Dao\AbstractDao;

/**
 * Data access object for Admin Settings in scholarship applications.
 *
 * @author Kushal Khandelwal <kushal124@wikimedia.org>
 * @author Niharika Kohli <niharikakohli29@gmail.com>
 * @copyright © 2014 Wikimedia Foundation and contributors.
 */
class Settings extends AbstractDao {
	/**
	 * @return array Settings from DB
	 */
	public function getSettings() {
		$settings = [];
		$records = $this->fetchAll(
			'SELECT setting_name, value FROM settings'
		);
		foreach ( $records as $idx => $row ) {
			$settings[$row['setting_name']] = $row['value'];
		}
		return $settings;
	}

	/**
	 * @param array $settings Settings
	 * @return bool True on success, false otherwise
	 */
	public function updateSettings( array $settings ) {
		// TODO: change schema to track user changing settings
		$stmt = $this->dbh->prepare( self::concat(
			'REPLACE INTO settings (setting_name, value)',
			'VALUES (:name, :value)'
		) );
		try {
			$this->dbh->beginTransaction();
			foreach ( $settings as $name => $value ) {
				$stmt->execute( [ 'name' => $name, 'value' => $value ] );
			}
			$this->dbh->commit();
			return true;

		} catch ( PDOException $e ) {
			$this->dbh->rollback();
			$this->logger->error( 'Failed to update settings', [
				'method' => __METHOD__,
				'exception' => $e,
			] );
			return false;
		}
	}
}
