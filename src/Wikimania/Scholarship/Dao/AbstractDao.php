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

use \PDO;
use \PDOException;

/**
 * Base class for data access objects.
 *
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
abstract class AbstractDao {

	/**
	 * @var PDO $db
	 */
	protected $dbh;


	public function __construct() {
		// FIXME: yuck
		global $CONFIG;
		try {
			$this->dbh = new PDO( $CONFIG['db']['dsn'],
				$CONFIG['db']['user'], $CONFIG['db']['password'],
				array(
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_PERSISTENT => true, //FIXME: good idea?
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				)
			);
		} catch ( PDOException $e ) {
			// FIXME: yuck
			error_log( __METHOD__ . " [{$sql}]: {$e}" );
			die( "Failed to connect to database" );
		}
	}


	/**
	 * Prepare and execute an SQL statement and return the first row of results.
	 *
	 * @param string $sql SQL
	 * @param array $params Prepared statement parameters
	 * @return array First response row
	 */
	protected function fetch( $sql, $params = null ) {
		$stmt = $this->dbh->prepare( $sql );
		$stmt->execute( $params );
		return $stmt->fetch();
	}


	/**
	 * Prepare and execute an SQL statement and return all results.
	 *
	 * @param string $sql SQL
	 * @param array $params Prepared statement parameters
	 * @return array Result rows
	 */
	protected function fetchAll( $sql, $params = null ) {
		$stmt = $this->dbh->prepare( $sql );
		$stmt->execute( $params );
		return $stmt->fetchAll();
	}

	/**
	 * Prepare and execute an SQL statement and return all results plus the
	 * number of rows found on the server side.
	 *
	 * @param string $sql SQL
	 * @param array $params Prepared statement parameters
	 * @return object StdClass with rows and found memebers
	 */
	protected function fetchAllWithFound( $sql, $params = null ) {
		$ret = new \StdClass;
		$ret->rows = $this->fetchAll( $sql );

		$ret->found = $this->fetch( 'SELECT FOUND_ROWS() AS found' );
		$ret->found = $ret->found['found'];

		return $ret;
	}

	/**
	 * Prepare and execute an SQL statement in a transaction.
	 *
	 * @param string $sql SQL
	 * @param array $params Prepared statement parameters
	 * @return bool False if an exception was generated, true otherwise
	 */
	protected function update( $sql, $params = null ) {
		$stmt = $this->dbh->prepare( $sql );
		try {
			$this->dbh->begintransaction();
			$stmt->execute( $params );
			$this->dbh->commit();
			return true;
		} catch ( PDOException $e) {
			$this->dbh->rollback();
			//fixme: logging
			error_log( __METHOD__ . " [{$sql}]: {$e}" );
			return false;
		}
	}


	/**
	 * Prepare and execute an SQL statement in a transaction.
	 *
	 * @param string $sql SQL
	 * @param array $params Prepared statement parameters
	 * @return int|bool Last insert id or false if an exception was generated
	 */
	protected function insert( $sql, $params = null ) {
		$stmt = $this->dbh->prepare( $sql );
		try {
			$this->dbh->beginTransaction();
			$stmt->execute( $params );
			$this->dbh->commit();
			return $this->dbh->lastInsertId();
		} catch ( PDOException $e) {
			$this->dbh->rollback();
			// FIXME: logging
			error_log( __METHOD__ . " [{$sql}]: {$e}" );
			return false;
		}
	}

} //end AbstractDao
