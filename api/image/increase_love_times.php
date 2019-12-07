<?php
#####################################################
#Date: 18:00 4/12/2019
#Author: Dang Bao
#In:  jwt of user and image_id from client
#Out: Client call this API to increase number of like
#     times of an image in database by one
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

#Get input jwt
$data = json_decode(file_get_contents("php://input"));

#Create connection to database
$database = new Database();
$db = $database->getConnection();

#Create user object to get check user data
$user = new User($db);

#Check the input
$jwt = isset($data->jwt) ? $data->jwt : "";
$image_id = isset($data->id) ? $data->id : "";

#Check if jwt and image_id from client is not empty
if (($jwt <> "") and ($image_id <> "")) {
    try {
        #Decode jwt to get user information
        $decoded = JWT::decode($jwt, $key, ['HS256']);

        #Get Username
        $user->username = $decoded->data->username;

        #Check if the username exits
        $check_if_user_exit = $user->username_exists();

        #If the username exits, let do more
        if ($check_if_user_exit) {
            try {
                $change_image = new Image($db);
                $change_image->id = $image_id;
                $result = $change_image->increase_love_times();

                if ($result) {
                    #Increase successfully.
                    #Return response code
                    http_response_code(200);
                } else {
                    #If cannot increase, there are some error.
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
