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

$data = json_decode(file_get_contents("php://input"));
// authenticate
$jwt = $data->jwt;

try {
    $decoded = JWT::decode($jwt, $key, ['HS256']);
    if ($decoded->data->role != "admin") {
        echo json_encode(array("message" => "Action not permitted."));
        return;
    }
} catch(Exception $e) {
    http_response_code(403);
    echo json_encode(array("message" => "Access denied."));
}


try {
    $query = 'SELECT * FROM images WHERE status = 0';
    $stmt = $db->prepare($query);
    if ($stmt->execute()) {
        $all_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['images' => $all_images]);
    } else {
        http_response_code(410);
        echo json_encode([
            "message" => "Resources no longer exist.",
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Unknown error occured.",
        "error" => $e->getMessage()
    ]);
}
