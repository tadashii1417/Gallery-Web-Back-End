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
}