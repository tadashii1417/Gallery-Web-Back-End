<?php

class Image
{
    private $conn;
    private $table_name = "images";

    public $id;
    public $size;
    public $width;
    public $height;
    public $description;
    public $url;
    public $categoryId;
    // tags
    public $userId;
    public $viewCount = 0;
    public $download = 0;
    public $love = 0;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
            SET
                size = :size,
                width = :width,
                height = :height,
                description = :description,
                url = :url,
                category_id = :categoryId,
                user_id = :userId
                ";
        $stmt = $this->conn->prepare($query);

        // bind param into sql stmt
        $stmt->bindParam(':size', $this->size);
        $stmt->bindParam(':width', $this->width);
        $stmt->bindParam(':height', $this->height);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':url', $this->url);
        $stmt->bindParam(':categoryId', $this->categoryId);
        $stmt->bindParam(':userId', $this->userId);

        if ($stmt->execute()) {
            return TRUE;
        }
        return FALSE;
    }

    #####################################################
    #Date: 21:00 3/12/2019
    #Author: Dang Bao
    #In:
    #Out: Return a list of all image in database with all of it info.
    #####################################################
    function get_all_images()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return FALSE;
    }

    #####################################################
    #Date: 16:00 4/12/2019
    #Author: Dang Bao
    #In:
    #Out: Increase number of download times in database by one
    #####################################################
    function increase_download_times()
    {
        $query = "UPDATE " . $this->table_name . " SET download = download + 1 WHERE id =:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        if ($stmt->execute()) {
            return TRUE;
        }
        return FALSE;
    }

    #####################################################
    #Date: 18:00 4/12/2019
    #Author: Dang Bao
    #In:  
    #Out: Client call this API to increase number of love
    #     times of an image in database by one
    #####################################################
    function increase_love_times()
    {
        $query = "UPDATE " . $this->table_name . " SET love = love + 1 WHERE id =:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        if ($stmt->execute()) {
            return TRUE;
        }
        return FALSE;
    }


    #####################################################
    #Date: 20:30 4/12/2019
    #Author: Dang Bao
    #In:  
    #Out: Client call this API to increase number of view
    #     times of an image in database by one
    #####################################################
    function increase_view_times()
    {
        $query = "UPDATE " . $this->table_name . " SET view_count = view_count + 1 WHERE id =:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        if ($stmt->execute()) {
            return TRUE;
        }
        return FALSE;
    }

    #####################################################
    #Date: 19:00 4/12/2019
    #Author: Dang Bao
    #In:  
    #Out: Return all images in database with their owner info
    #       with category like input
    #####################################################
    function get_all_image_by_category_id()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE category_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->categoryId);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return FALSE;
    }

    #####################################################
    #Date: 20:00 4/12/2019
    #Author: Dang Bao
    #In:  
    #Out: Return image info with input id
    #####################################################
    function get_image_info_by_image_id()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return FALSE;
    }
}
