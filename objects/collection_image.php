<?php
//include_once '../config/core.php';

class CollectionImage
{

    private $conn;
    private $table_name = "collections_images";

    public $collection_id;
    public $image_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    function insert_image_to_collection()
    {
        #$query = "INSERT INTO " . $this->table_name . " SET collection_id = :collection_id, image_id = :image_id";
        $query = "INSERT INTO " . $this->table_name . "(`collection_id`, `image_id`) VALUES (:collection_id, :image_id)";

        $stmt = $this->conn->prepare($query);
        // bind param into sql stmt
        $stmt->bindParam(':collection_id', $this->collection_id);
        $stmt->bindParam(':image_id', $this->image_id);

        if ($stmt->execute()) {
            return TRUE;
        }
        return FALSE;
    }
}
