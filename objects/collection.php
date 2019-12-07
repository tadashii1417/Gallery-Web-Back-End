<?php
//include_once '../config/core.php';

class Collection
{

	private $conn;
	private $table_name = "collections";

	public $id;
	public $name;
	public $description;
	public $user_id;

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
                user_id = :user_id
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
		print_r( $stmt->errorInfo());
		return FALSE;
	}


	public function update()
	{
		// if no posted password, do not update the password
		$query = "UPDATE " . $this->table_name . "
            SET
                name = :name,
                descriptoin = :description,
                user_id = :user_id
            WHERE id = :id";

		// prepare the query
		$stmt = $this->conn->prepare($query);

		// sanitize
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->description  = htmlspecialchars(strip_tags($this->description));
		$this->user_id     = htmlspecialchars(strip_tags($this->user_id));

		// bind the values from the form
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':description', $this->description);
		$stmt->bindParam(':user_id', $this->user_id);

		// execute the query
		if ($stmt->execute()) {
			return TRUE;
		}

		return FALSE;
	}
}
