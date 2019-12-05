<?php
//include_once '../config/core.php';

class User
{

	private $conn;
	private $table_name = "users";

	public $id;
	public $firstname;
	public $lastname;
	public $email;
	public $username;
	public $password;
	public $avatarUrl = "https://cdn0.iconfinder.com/data/icons/avatars-6/500/Avatar_boy_man_people_account_client_male_person_user_work_sport_beard_team_glasses-512.png";
	public $description;

	public function __construct($db)
	{
		$this->conn = $db;
	}


	public function getUploadedImages()
	{
		$query = "SELECT * FROM images
            WHERE user_id = :userId
        ";

		$stmt = $this->conn->prepare($query);

		$stmt->bindparam(':userId', $this->id);

		if ($stmt->execute()) {
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		return FALSE;
	}
	function create()
	{
		$query = "INSERT INTO " . $this->table_name . "
            SET
                firstname = :firstname,
                lastname = :lastname,
                email = :email,
                username = :username,
                password = :password,
				avatarUrl = :avatarUrl,
				description = :description
                ";

		$stmt = $this->conn->prepare($query);

		// sanitize
		$this->firstname = htmlspecialchars(strip_tags($this->firstname));
		$this->lastname  = htmlspecialchars(strip_tags($this->lastname));
		$this->email     = htmlspecialchars(strip_tags($this->email));
		$this->username  = htmlspecialchars(strip_tags($this->username));
		$this->password  = htmlspecialchars(strip_tags($this->password));
		$this->description  = htmlspecialchars(strip_tags($this->description));

		// bind param into sql stmt
		$stmt->bindParam(':firstname', $this->firstname);
		$stmt->bindParam(':lastname', $this->lastname);
		$stmt->bindParam(':email', $this->email);
		$stmt->bindParam(':username', $this->username);
		$stmt->bindParam(':avatarUrl', $this->avatarUrl);
		$stmt->bindParam(':description', $this->description);

		// hash the password before saving to database
		$password_hash = password_hash($this->password, PASSWORD_BCRYPT);
		$stmt->bindParam(':password', $password_hash);

		if ($stmt->execute()) {
			return TRUE;
		}

		return FALSE;
	}

	function usernameExists()
	{
		$query = "SELECT * FROM " . $this->table_name . "
            WHERE username = ?
            LIMIT 0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->username);

		$stmt->execute();
		$num = $stmt->rowCount();
		if ($num > 0) {
			$row             = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->id        = $row['id'];
			$this->username  = $row['username'];
			$this->password  = $row['password'];
			$this->firstname = $row['firstname'];
			$this->lastname  = $row['lastname'];
			$this->email     = $row['email'];
			$this->avatarUrl = $row['avatarUrl'];
			$this->description = $row['description'];

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
				email = :email,
				description = :description
                {$password_set}
            WHERE id = :id";

		// prepare the query
		$stmt = $this->conn->prepare($query);

		// sanitize
		$this->firstname = htmlspecialchars(strip_tags($this->firstname));
		$this->lastname  = htmlspecialchars(strip_tags($this->lastname));
		$this->email     = htmlspecialchars(strip_tags($this->email));
		$this->description     = htmlspecialchars(strip_tags($this->description));

		// bind the values from the form
		$stmt->bindParam(':firstname', $this->firstname);
		$stmt->bindParam(':lastname', $this->lastname);
		$stmt->bindParam(':email', $this->email);
		$stmt->bindParam(':description', $this->description);

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
		print_r($stmt->queryString);
		// print_r($stmt->errorInfo());
		return FALSE;
	}

	#####################################################
	#Date: 23:00 3/12/2019
	#Author: Dang Bao
	#In:
	#Out: Return information of the owner of this id
	#####################################################
	function get_owner_info()
	{
		$query = "SELECT id, username, firstname, lastname, description, avatarUrl, email, role, status FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id', $this->id);

		if ($stmt->execute()) {
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		return FALSE;
	}
}
