<?php
#####################################################
#Date: 21:00 3/12/2019
#Author: Dang Bao
#In:  jwt from client
#Out: Return all image in database with their owner info.
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
include_once '../../objects/user.php';

$data = json_decode(file_get_contents("php://input"));

$database = new Database();
$db = $database->getConnection();

$jwt = isset($data->jwt) ? $data->jwt : "";

if ($jwt) {
    try {
        $decoded = JWT::decode($jwt, $key, ['HS256']);
        $query = 'INSERT INTO loves VALUES (:user_id, :image_id);';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $decoded->data->id); // user_id
        $stmt->bindParam(':image_id', $data->image_id);
        if ($stmt->execute()) {
            echo json_encode(["message" => "successful update"]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "You're already love this image !"]);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "message" => "You're not log in yet !",
            "error" => $e->getMessage(),
        ]);
    }
} else {
    http_response_code(401);
    echo json_encode(["message" => "You're not log in yet !"]);
}
