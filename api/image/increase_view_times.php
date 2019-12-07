<?php
#####################################################
#Date: 20:30 4/12/2019
#Author: Dang Bao
#In:  Image_id from client
#Out: Client call this API to increase number of view
#     times of an image in database by one
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

#Get input jwt
$data = json_decode(file_get_contents("php://input"));

#Create connection to database
$database = new Database();
$db = $database->getConnection();

#Check the input
$id = $_GET['id'];

if ($id) {
    try {
        #Decode jwt to get user information
        $change_image = new Image($db);
        $change_image->id = $id;

        $result = $change_image->increase_view_times();

        if ($result) {
            #If it can get some images, retutn it.
            #Return response code
            http_response_code(200);
        } else {
            #If it can not get anything, return notice.
            http_response_code(401);
            echo json_encode(["message" => "Access denied."]);
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
