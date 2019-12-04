<?php

class Category
{
    private $conn;
    private $table_name = "categories";

    public $id;
    public $name;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    #####################################################
    #Date: 20:40 4/12/2019
    #Author: Dang Bao
    #In:  
    #Out: Return all category in database
    #####################################################
    function get_all_category()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return FALSE;
    }
}
