<?php
#####################################################
#Date: 20:40 4/12/2019
#Author: Dang Bao
#In:  
#Out: Return all category in database
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
include_once '../../objects/category.php';

#Create connection to database
$database = new Database();
$db = $database->getConnection();


try {
    #Create object image
    $all_category = new Category($db);

    #Call function from image object.
    $all_category_return = $all_category->get_all_category();

    #Check if the return list is nothing (no image with this image_id)
    $size_of_list_category = sizeof($all_category_return);

    if ($size_of_list_category > 0) {
        http_response_code(200);
        echo json_encode(["categories" => $all_category_return]);
    } else {
        http_response_code(200);
        echo json_encode(["categories" => ""]);
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        "message" => "Access denied. fdg",
        "error" => $e->getMessage()
    ]);
}
