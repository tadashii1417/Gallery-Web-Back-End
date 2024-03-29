<?php
#####################################################
#Date: 21:00 3/12/2019
#Author: Dang Bao
#In:  jwt from client
#Out: Return all image in database with their owner info.
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
include_once '../../objects/user.php';

$data = json_decode(file_get_contents("php://input"));

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$jwt = isset($data->jwt) ? $data->jwt : "";

if ($jwt) {
    try {
        $decoded = JWT::decode($jwt, $key, ['HS256']);

        $user->id = $decoded->data->id;

        $fetchedImages = $user->get_liked_images();

        if (isset($fetchedImages)) {
            echo json_encode(["images" => $fetchedImages]);
        } else {
            echo json_encode(["message" => "can't fetch images"]);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "message" => "Access denied.",
            "error" => $e->getMessage(),
        ]);
    }
} else {
    http_response_code(401);
    echo json_encode(["message" => "Access denied."]);
}
