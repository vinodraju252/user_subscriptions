<?php
/**
 * Send Email Class
 */
class Db {
	private $_host;
	private $_username;
	private $_password;
	private $_dbname;
	public $connect = null;

	/**
	 * Need to construct required details
	 */
	function __construct($host, $user, $pass, $dbname) {
		$this->_host = $host;
		$this->_username = $user;
		$this->_password = $pass;
		$this->_dbname = $dbname;
		$con = new mysqli($this->_host, $this->_username, $this->_password, $this->_dbname);
		if (!$con) {
			die('Could not connect to database!');
		} else {
			$this->connect = $con;
		}
	}

	function getDetails($postVal, $table) {
		$row = [];
		$query = "SELECT id,first_name,last_name,email,is_verified,is_active FROM " . $table . " WHERE email='" . $postVal['email'] . "' AND is_active='Y'";
		$result = $this->connect->query($query);
		$results = $result->fetch_assoc();
		if (!empty($results)) {
			$row = $results;
		}
		return $row;
	}

	function insertUser($postVal) {
		$datetime = date('Y-m-d H:i:s');
		$query = $this->connect->prepare("INSERT INTO users (first_name, last_name, email, created_on) VALUES (?, ?, ?, ?)");
		$query->bind_param("ssss", $postVal['first_name'], $postVal['last_name'], $postVal['to_email'], $datetime);
		return $query->execute();
	}

	function updateUser($postVal) {
		$status = $postVal['type'] == 1 ? 'Y' : 'N';
		$column = $postVal['type'] == 1 ? 'is_verified' : 'is_active';
		$datetime = date('Y-m-d H:i:s');
		$query = $this->connect->prepare("UPDATE users SET $column=?, modified_on=? WHERE email=?  AND is_active='Y'");
		$query->bind_param('sss', $status, $datetime, $postVal['email']);
		return $query->execute();
	}

	function insertNotification($postVal) {
		$datetime = date('Y-m-d H:i:s');
		$query = $this->connect->prepare("INSERT INTO notifications (user_id, data_url, created_on) VALUES (?, ?, ?)");
		$query->bind_param("sss", $postVal['user_id'], $postVal['data_url'], $datetime);
		return $query->execute();
	}

	/**
	 * close db connection
	 */
	private function close() {
		if ($this->connect != null) {
			$this->connect->close();
			$this->connect = null;
		}
		return true;
	}

	/**
	 * Class destruct
	 */
	public function __destruct() {
		if ($this->connect != null) {
			$this->close();
		}
	}
}