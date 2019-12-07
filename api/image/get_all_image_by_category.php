<?php
#####################################################
#Date: 19:00 4/12/2019
#Author: Dang Bao
#In:  id of category from client
#Out: Return all images in database with their owner info
#       with category like input
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
include_once '../../objects/image.php';
include_once '../../objects/user.php';

$category_id = $_GET['id'];

#Create connection to database
$database = new Database();
$db = $database->getConnection();

if ($category_id) {
    try {
        #Create object image
        $all_image = new Image($db);
        $all_image->categoryId = $category_id;
        #Call function from image object.
        $all_image_return = $all_image->get_all_image_by_category_id();

        #Check if the return list is nothing (no image with this category_id)
        $size_of_list_image = sizeof($all_image_return);
        if ($size_of_list_image > 0) {
            #This loop to get all owner info of all result picture
            for ($temp_count = 0; $temp_count < $size_of_list_image; $temp_count++) {
                $temp_user = new User($db);
                $temp_user->id = $all_image_return[$temp_count]['user_id'];
                #Create an object for owner
                $temp_owner = ["owner" => $temp_user->get_owner_info()];
                #Add owner to picture info
                $all_image_return[$temp_count] = array_merge($all_image_return[$temp_count], $temp_owner);
                unset($temp_user);
            }
            #If it can get some images, retutn it.
            http_response_code(200);
            echo json_encode(["images" => $all_image_return]);
        } else {
            http_response_code(200);
            echo json_encode(["images" => ""]);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "message" => "Error when process.",
            "error" => $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Access denied."]);
}
