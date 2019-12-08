<?php
#####################################################
#Date: 20:00 4/12/2019
#Author: Dang Bao
#In:  Image_id from client
#Out: Return image info with input id
#####################################################
// required headers
include_once '../../config/header.php';
// required to encode json web token
include_once '../../config/core.php';
include_once '../../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../libs/php-jwt-master/src/ExpiredException.php';
include_once '../../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../libs/php-jwt-master/src/JWT.php';

header("Content-Type: image/jpg");

use \Firebase\JWT\JWT;

include_once '../../config/database.php';
include_once '../../objects/image.php';
include_once '../../objects/user.php';


#Create connection to database
$database = new Database();
$db = $database->getConnection();
$image_id = $_GET['id'];

if ($image_id) {
    try {
        #Create object image
        $all_image = new Image($db);
        $all_image->id = $image_id;
        #Call function from image object.
        $all_image_return = $all_image->get_image_info_by_image_id();

        #Check if the return list is nothing (no image with this image_id)
        $size_of_list_image = sizeof($all_image_return);
        if ($size_of_list_image > 0) {

            $image_url = "..\/..\/" . $all_image_return[0]['url'];
            echo json_encode($image_bitmap = ['image_bitmap' => base64_encode(file_get_contents($image_url))]);
        } else {
            #Return empty list
            http_response_code(400);
            echo json_encode(["message" => "Image is not comfirmed."]);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Access denied."]);
}
