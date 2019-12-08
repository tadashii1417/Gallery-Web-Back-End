<?php
#####################################################
#Date: 11:00 7/12/2019
#Author: Dang Bao
#In:  Collection Id and Image id
#Out: If the collection id and image id exit. Insert info
#     to collections_images table
#####################################################
// required headers
include_once '../../config/header.php';
// required to encode json web token
include_once '../../config/core.php';
include_once '../../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../libs/php-jwt-master/src/ExpiredException.php';
include_once '../../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

include_once '../../config/database.php';
include_once '../../objects/collection_image.php';
include_once '../../objects/collection.php';
include_once '../../objects/image.php';

$database = new Database();
$db       = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));


$image = new Image($db);
$collection = new Collection($db);
$collection_image = new CollectionImage($db);

#Get data
$collection_id = isset($data->collection_id) ? $data->collection_id : "";
$image_id = isset($data->image_id) ? $data->image_id : "";
#Check if it is empty
if (($collection_id <> "") and ($image_id <> "")) {
    try {
        $image->id = $image_id;
        $collection->id = $collection_id;
        if (($image->check_exit()) and ($collection->check_exit())) {
            //regenerate jwt

            $collection_image->collection_id = $collection_id;
            $collection_image->image_id = $image_id;

            if ($collection_image->insert_image_to_collection()) {
                http_response_code(200);
                echo json_encode(["message" => "Insert successful"]);

            } else {
                http_response_code(401);
                echo json_encode(["message" => "Unable to insert data. Duplicate."]);
            }
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Unable to insert data. Something wrong."]);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "message" => "Access denied.",
            "error"   => $e->getMessage(),
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Access denied."]);
}
