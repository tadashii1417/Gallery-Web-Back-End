<?php
#####################################################
#Date: 11:30 7/12/2019
#Author: Dang Bao
#In:  User ID and jwt to check admin 
#Out: Change ban status to 0.
#####################################################
// required headers
include_once '../../config/header.php';
include_once '../../config/database.php';
include_once '../../objects/user.php';
include_once '../../config/core.php';
include_once '../../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../libs/php-jwt-master/src/ExpiredException.php';
include_once '../../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

// get database connection
$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));


#Get data
$jwt = isset($data->jwt) ? $data->jwt : "";
$ban_id = isset($data->id) ? $data->id : "";
#Check if it is empty
if (($jwt <> "") and ($ban_id <> "")) {
    try {
        $decoded = JWT::decode($jwt, $key, ['HS256']);

        $user_check_role = $decoded->data->role;
        if ($user_check_role == "admin") {
            $user = new User($db);
            $user->id = $ban_id;
            if ($user->ban_user()) {
                http_response_code(200);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Error when processing."]);
            }
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Admin role needed."]);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "message" => "Something wrong. :(",
            "error"   => $e->getMessage(),
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Invalid input."]);
}
