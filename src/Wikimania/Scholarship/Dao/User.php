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
 * @copyright © 2013 Calvin W. F. Siu, Wikimania 2013 Hong Kong organizing team
 * @copyright © 2012-2013 Katie Filbert, Wikimania 2012 Washington DC organizing team
 * @copyright © 2011 Harel Cain, Wikimania 2011 Haifa organizing team
 * @copyright © 2010 Wikimania 2010 Gdansk organizing team
 * @copyright © 2009 Wikimania 2009 Buenos Aires organizing team
 */

namespace Wikimania\Scholarship\Dao;

use \PDOException;

/**
 * Data access object for scholarship applications.
 */
class User extends AbstractDao {

	public function getUser( $username ) {
		return $this->fetch(
			'SELECT * FROM users WHERE username = ? AND isvalid = 1',
			array( $username )
		);
	}

	public function getUsername( $id ) {
		return $this->fetch(
			'SELECT username FROM users WHERE id = ?',
			array( $id )
		);
	}

	public function getListofUsers( $state ) {
		switch ( $state ) {
			case 'all':
				return $this->fetchAll( 'SELECT * FROM users' );
				break;

			case 'reviewer':
				return $this->fetchAll( 'SELECT * FROM users WHERE reviewer = 1' );
				break;
		}
	}

	public function getUserInfo( $user_id ) {
		return $this->fetch(
			"SELECT * FROM users WHERE id = ?",
			array( $user_id )
		);
	}

	public function isSysAdmin( $user_id ) {
		$res = $this->fetch(
			"SELECT isadmin FROM users WHERE id = ?",
			array( $user_id )
		);
		return $res['isadmin'];
	}

	public function newUserCreate( $answers ) {
		// FIXME: yuck, order matters in $answers!
		$fields = array( 'username', 'password', 'email', 'reviewer', 'isvalid', 'isadmin' );
		$sql = 'INSERT INTO users (' .
			implode( ', ', $fields ) . ') VALUES (' .
			implode( ',', array_fill( 0, count( $fields ), '?' ) ) . ')';

		// FIXME: prior implementation died on error, fix callers
		return $this->insert( $sql, $answers );
	}

	public function updateUserInfo( $answers, $id ) {
		$fields = array( 'username', 'email', 'reviewer', 'isvalid', 'isadmin' );
		$placeholders = array();
		foreach ( $fields as $field ) {
			$placeholders[] = "{$field} = :{$field}";
		}
		$stmt = $this->dbh->prepare(
			'UPDATE users SET ' .
			implode( ', ', $placeholders ) .
			' WHERE id = :id'
		);

		$answers['id'] = $id;
		try {
			$this->dbh->beginTransaction();
			$stmt->execute( $answers );
			$this->dbh->commit();
		} catch ( PDOException $e) {
			$this->dbh->rollback();
			// FIXME
			die( "Fatal Error: {$e->getMessage()}" );
		}
	}

	public function updatePassword( $oldpw, $newpw, $id, $force = null ) {
		if ( !$force ) {
			$res = $this->fetch(
				'SELECT password FROM users WHERE id = ?',
				array( $id )
			);
			if ( !Password::comparePasswordToHash( $oldpw, $res['password'] ) ) {
				// passsword doesn't match expected
				return false;
			}
		}

		$stmt = $this->dbh->prepare( 'UPDATE users SET password = ? WHERE id = ?' );
		try {
			$this->dbh->beginTransaction();
			$stmt->execute( array( Password::encodePassword( $newpw ), $id ) );
			$this->dbh->commit();
			return true;
		} catch ( PDOException $e) {
			$this->dbh->rollback();
			//FIXME: logging
			return false;
		}
	}

	public function userIsBlocked( $id ) {
		$res = $this->query( "SELECT blocked FROM users WHERE id = ?", array( $id ) );
		return $res['blocked'];
	}

}
