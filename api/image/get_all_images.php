<?php
#####################################################
#Date: 21:00 3/12/2019
#Author: Dang Bao
#In:  jwt from client
#Out: Return all images in database with their owner info
#####################################################
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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


#Create connection to database
$database = new Database();
$db = $database->getConnection();


try {

    #Create object image
    $all_image = new Image($db);
    #Call function from image object.
    $all_image_return = $all_image->get_all_images();

    if ($all_image_return) {
        $size_of_list_image = sizeof($all_image_return);

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
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        "message" => "Access denied.",
        "error" => $e->getMessage()
    ]);
}
