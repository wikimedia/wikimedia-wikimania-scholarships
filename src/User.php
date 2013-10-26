<?php

class User extends AbstractDao {

	public function __construct() {
		parent::__construct();
	}

	public function GetUser( $username ) {
		return $this->fetch(
			'SELECT * FROM users WHERE username = ? AND isvalid = 1',
			array( $username )
		);
	}

	public function GetUsername( $id ) {
		return $this->fetch(
			'SELECT username FROM users WHERE id = ?',
			array( $id )
		);
	}

	public function GetListofUsers( $state ) {
		switch ( $state ) {
			case 'all':
				return $this->fetchAll( 'SELECT * FROM users' );
				break;

			case 'reviewer':
				return $this->fetchAll( 'SELECT * FROM users WHERE reviewer = 1' );
				break;
		}
	}

	public function GetUserInfo( $user_id ) {
		return $this->fetch(
			"SELECT * FROM users WHERE id = ?",
			array( $user_id )
		);
	}

	public function IsSysAdmin( $user_id ) {
		$res = $this->fetch(
			"SELECT isadmin FROM users WHERE id = ?",
			array( $user_id )
		);
		return $res['isadmin'];
	}

	public function NewUserCreate( $answers ) {
		// FIXME: yuck, order matters in $answers!
		$fields = array( 'username', 'password', 'email', 'reviewer', 'isvalid', 'isadmin' );
		$sql = 'INSERT INTO users (' .
			implode( ', ', $fields ) . ') VALUES (' .
			implode( ',', array_fill( 0, count( $fields ), '?' ) ) . ')';

		// FIXME: prior implementation died on error, fix callers
		return $this->insert( $sql, $answers );
	}

	public function UpdateUserInfo( $answers, $id ) {
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

	public function UpdatePassword( $oldpw, $newpw, $id, $force = null ) {
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

	public function UserIsBlocked( $id ) {
		$res = $this->query( "SELECT blocked FROM users WHERE id = ?", array( $id ) );
		return $res['blocked'];
	}

}
