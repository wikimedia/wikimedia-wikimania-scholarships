<?php
	
class User {
	protected $db;

	function __construct() {
		global $db_driver, $db_user, $db_pass, $db_host, $db_name;
		$dsn = $db_driver . '://' . $db_user . ':' . $db_pass . '@'
			. $db_host . '/' . $db_name;
		$this->db = &DB::Connect($dsn);
		if (PEAR::isError($this->db)) 
   			die($this->db->getMessage());
		$this->db->setFetchMode(DB_FETCHMODE_ASSOC);
	}

	function __destruct() {
		if (DB::isError($this->db))
			die('Could not connect to database!');
		$this->db->disconnect();
	}

	function GetUser($username) {
		return $this->db->query('select id, username, password, email, reviewer from users where username = ? and isvalid = 1', array($username))->fetchRow();
	}

	function GetUsername($id) {
		return $this->db->query('select username from users where id = ?', array($id))->fetchRow();
	}
		
	function GetListofUsers($state) {
		switch ($state) {
			case "all":
				return $this->db->getAll("select * from users");
				break;
			case "reviewer": 
				return $this->db->getAll("select * from users where `reviewer` = 1");
				break;
		}
	}

	function GetUserInfo($user_id) {
		return $this->db->query("select * from `users` where `id` = ?", array($user_id))->fetchrow();
	}

	function IsSysAdmin($user_id) {
		$res = $this->db->query("select `isadmin` from `users` where `id` = ?", array($user_id))->fetchrow();
		return $res['isadmin'];
	}

	function NewUserCreate($answers) {
		$fieldnames = array("username","password","email","reviewer","isvalid","isadmin");
		$this->db->query(
			sprintf("insert into users (%s) values ('%s')", join($fieldnames, ', '), join($answers, "', '")));
		$res = $this->db->insert_id;
		return $res;
	}

	function UpdateUserInfo($answers, $id) {
		$fieldnames = array("username","email","reviewer","isvalid","isadmin");
		$query = "update users set ";
		for ($i=0 ; $i <= 4 ; $i++) {
			$field = $fieldnames[$i];
			$query .= ($i==0) ? '' : ',';
			$query .= '`' . $field . '`="' . $answers[$field] . '" ';
		}
		$query .= "where `id` = ?";
		$this->db->query($query, array($id));
	}
	
	function UpdatePassword($oldpw, $newpw, $id, $force = NULL) {
		if ($force==1) {
			$this->db->query("update users set password = ? where id = ?", array(sha1($newpw),$id));
			return 1;
		} else {
			$userdata = $this->db->query('select password from users where id = ?', array($id))->fetchRow();
			if ($userdata['password'] == sha1($oldpw)) {
				$this->db->query("update users set password = ? where id = ?", array(sha1($newpw),$id));
				return 1;
			} else {
				return 0;
			}
		}
	}

}
?>
