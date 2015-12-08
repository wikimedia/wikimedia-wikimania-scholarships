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

use Wikimania\Scholarship\Auth\UserData;

use PDOException;
use Wikimedia\Slimapp\Auth\Password;
use Wikimedia\Slimapp\Auth\UserManager;
use Wikimedia\Slimapp\Dao\AbstractDao;

/**
 * Data access object for scholarship applications.
 */
class User extends AbstractDao implements UserManager {

	/**
	 * Get a user by name.
	 *
	 * @param string $username Username
	 * @return UserData
	 */
	public function getUserData( $username ) {
		$data = $this->fetch(
			'SELECT * FROM users WHERE username = ? AND isvalid = 1',
			array( $username )
		);
		if ( $data === false ) {
			$this->logger->info( "No data found for user '{$username}'" );
			$data = array();
		}
		return new UserData( $data );
	}

	public function getUsername( $id ) {
		return $this->fetch(
			'SELECT username FROM users WHERE id = ?',
			array( $id )
		);
	}

	public function search( array $params ) {
		$defaults = array(
			'name' => null,
			'email' => null,
			'sort' => 'id',
			'order' => 'asc',
			'items' => 20,
			'page' => 0,
		);
		$params = array_merge( $defaults, $params );
		$where = array();
		$crit = array();
		$validSorts = array(
			'id', 'username', 'email', 'reviwer', 'isvalid',
			'isadmin', 'blocked',
		);
		$sortby = in_array( $params['sort'], $validSorts ) ?
			$params['sort'] : $defaults['sort'];
		$order = $params['order'] === 'desc' ? 'DESC' : 'ASC';
		if ( $params['items'] == 'all' ) {
			$limit = '';
			$offset = '';
		} else {
			$crit['int_limit'] = (int)$params['items'];
			$crit['int_offset'] = (int)$params['page'] * (int)$params['items'];
			$limit = 'LIMIT :int_limit';
			$offset = 'OFFSET :int_offset';
		}
		if ( $params['name'] !== null ) {
			$where[] = 'username like :name';
			$crit['name'] = $params['name'];
		}
		if ( $params['email'] !== null ) {
			$where[] = 'email like :email';
			$crit['email'] = $params['email'];
		}
		$where[] = 'blocked = 0';
		$sql = self::concat(
			'SELECT SQL_CALC_FOUND_ROWS * FROM users',
			self::buildWhere( $where ),
			"ORDER BY {$sortby} {$order}, id {$order}",
			$limit, $offset
		);
		return $this->fetchAllWithFound( $sql, $crit );
	}

	public function getUserInfo( $user_id ) {
		return $this->fetch(
			"SELECT * FROM users WHERE id = ?",
			array( $user_id )
		);
	}

	public function newUserCreate( $answers ) {
		$fields = array(
			'username', 'password', 'email', 'reviewer', 'isvalid', 'isadmin'
		);
		$placeholders = array();
		$vals = array();
		foreach ( $fields as $field ) {
			$placeholders[] = ":{$field}";
			$vals[$field] = $answers[$field];
		}

		$sql = 'INSERT INTO users (' .
			implode( ', ', $fields ) . ') VALUES (' .
			implode( ',', $placeholders ) . ')';

		return $this->insert( $sql, $vals );
	}

	/**
	 * @param array $answers Updated user data
	 * @param int $id User id
	 * @return bool True if update suceeded, false otherwise
	 */
	public function updateUserInfo( $answers, $id ) {
		$fields = array(
			'username', 'email', 'reviewer', 'isvalid', 'isadmin', 'blocked'
		);
		$placeholders = array();
		foreach ( $fields as $field ) {
			$placeholders[] = "{$field} = :{$field}";
		}

		$sql = self::concat(
			'UPDATE users SET',
			implode( ', ', $placeholders ),
			'WHERE id = :id'
		);
		$stmt = $this->dbh->prepare( $sql );
		$answers['id'] = $id;

		try {
			$this->dbh->beginTransaction();
			$stmt->execute( $answers );
			$this->dbh->commit();
			return true;

		} catch ( PDOException $e) {
			$this->dbh->rollback();
			$this->logger->error( 'Failed to update user', array(
				'method' => __METHOD__,
				'exception' => $e,
				'sql' => $sql,
				'params' => $answers,
			) );
			return false;
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
				$this->logger->notice( 'Invalid old password; will not update', array(
					'method' => __METHOD__,
					'user' => $id,
				) );
				return false;
			}
		}

		$stmt = $this->dbh->prepare( 'UPDATE users SET password = ? WHERE id = ?' );
		try {
			$this->dbh->beginTransaction();
			$stmt->execute( array( Password::encodePassword( $newpw ), $id ) );
			$this->dbh->commit();
			$this->logger->notice( 'Changed password for user', array(
				'method' => __METHOD__,
				'user' => $id,
			) );
			// Invalidate any password reset token that may have been issues->updatePasswordResetHash( $id, null );d
			$this->updatePasswordResetHash( $id, null );
			return true;

		} catch ( PDOException $e) {
			$this->dbh->rollback();
			$this->logger->error( 'Failed to update password for user', array(
				'method' => __METHOD__,
				'exception' => $e,
			) );
			return false;
		}
	}

	public function userIsBlocked( $id ) {
		$res = $this->query( "SELECT blocked FROM users WHERE id = ?", array( $id ) );
		return $res['blocked'];
	}

	/**
	 * Generate password reset token(s) for the given email address.
	 *
	 * @param string $email Email address
	 * @return array (token, user) pairs; token === false on error
	 */
	public function createPasswordResetToken( $email ) {
		$ret = array();
		$users = $this->search( array(
			'email' => $email,
			'items' => 'all',
		) );
		foreach ( $users->rows as $user ) {
			$token = bin2hex( Password::getBytes( 16, true ) );
			$hash = hash( 'sha256', $token );
			if ( !$this->updatePasswordResetHash( $user['id'], $hash ) ) {
				$token = false;
			}
			$ret[] = array( $token, $user );
		}
		return $ret;
	}

	protected function updatePasswordResetHash( $id, $hash ) {
		$ret = false;
		$stmt = $this->dbh->prepare(
			'UPDATE users SET reset_hash = ?, reset_date = now() WHERE id = ?'
		);
		try {
			$this->dbh->beginTransaction();
			$stmt->execute( array( $hash, $id ) );
			$this->dbh->commit();
			$this->logger->notice( 'Created reset token for user', array(
				'method' => __METHOD__,
				'user' => $id,
			) );
			$ret = true;

		} catch ( PDOException $e) {
			$this->dbh->rollback();
			$this->logger->error(
				'Failed to update reset_hash for user',
				array(
					'method' => __METHOD__,
					'exception' => $e,
			) );
		}
		return $ret;
	}

	/**
	 * Validate a user's password reset token.
	 *
	 * @param int $id User id
	 * @param string $token Reset token
	 * @return bool
	 */
	public function validatePasswordResetToken( $id, $token ) {
		$hash = hash( 'sha256', $token );
		$row = $this->fetch(
			'SELECT reset_hash, reset_date FROM users WHERE id = ?',
			array( $id )
		);
		return $row &&
			Password::hashEquals( $row['reset_hash'], $hash ) &&
			// Tokens are only good for 48 hours
			( time() - strtotime( $row['reset_date'] ) ) < 172800;
	}

	/**
	 * Reset a user's password after validating the reset token.
	 *
	 * @param int $id User id
	 * @param string $token Reset token
	 * @param string $pass New password
	 * @return bool
	 */
	public function resetPassword( $id, $token, $pass ) {
		$ret = false;
		if ( $this->validatePasswordResetToken( $id, $token ) ) {
			$ret = $this->updatePassword( null, $pass, $id, true );
			if ( $ret ) {
				// Consume token if change was successful
				$this->updatePasswordResetHash( $id, null );
			}
		}
		return $ret;
	}
}
