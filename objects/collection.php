<?php
//include_once '../config/core.php';

class Collection
{

	private $conn;
	private $table_name = "collections";

	public $id;
	public $name;
	public $description;
	public $userID;

	public function __construct($db)
	{
		$this->conn = $db;
	}


	function create()
	{
		$query = "INSERT INTO " . $this->table_name . "
            SET
                name = :name,
                description = :description,
                user_id = :user_id,
                ";

		$stmt = $this->conn->prepare($query);

		// sanitize
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->description  = htmlspecialchars(strip_tags($this->description));
		$this->user_id     = htmlspecialchars(strip_tags($this->user_id));

		// bind param into sql stmt
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':description', $this->description);
		$stmt->bindParam(':user_id', $this->user_id);

		if ($stmt->execute()) {
			return TRUE;
		}

		return FALSE;
	}


	public function update()
	{
		// if password needs to be updated
		$password_set = !empty($this->password) ? ", password = :password"
			: "";

		// if no posted password, do not update the password
		$query = "UPDATE " . $this->table_name . "
            SET
                firstname = :firstname,
                lastname = :lastname,
                email = :email
                {$password_set}
            WHERE id = :id";

		// prepare the query
		$stmt = $this->conn->prepare($query);

		// sanitize
		$this->firstname = htmlspecialchars(strip_tags($this->firstname));
		$this->lastname  = htmlspecialchars(strip_tags($this->lastname));
		$this->email     = htmlspecialchars(strip_tags($this->email));

		// bind the values from the form
		$stmt->bindParam(':firstname', $this->firstname);
		$stmt->bindParam(':lastname', $this->lastname);
		$stmt->bindParam(':email', $this->email);

		if (!empty($this->password)) {
			$this->password = htmlspecialchars(strip_tags($this->password));
			$password_hash  = password_hash($this->password, PASSWORD_BCRYPT);
			$stmt->bindParam(':password', $password_hash);
		}

		// unique ID of record to be edited
		$stmt->bindParam(':id', $this->id);

		// execute the query
		if ($stmt->execute()) {
			return TRUE;
		}

		return FALSE;
	}

	#####################################################
	#Date: 11:00 7/12/2019
	#Author: Dang Bao
	#In:  
	#Out: To check if the collection id exits
	#####################################################
	function check_exit()
	{
		// if no posted password, do not update the password
		$query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";

		// prepare the query
		$stmt = $this->conn->prepare($query);

		// bind the values from the form
		$stmt->bindParam(':id', $this->id);

		// execute the query
		if ($stmt->execute()) {
			return TRUE;
		}

		return FALSE;
	}
}
