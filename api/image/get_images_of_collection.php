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

$data = json_decode(file_get_contents("php://input"));

try {
    $query = 'SELECT d2.* FROM collections_images AS d1, images AS d2
    WHERE (collection_id = :collection_id) AND (d1.image_id = d2.id)';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':collection_id', $data->collection_id);
    if ($stmt->execute()) {
        $ret = array();
        while ($row = $stmt->fetchObject()) {
            $ret[] = $row;
        }
        echo json_encode(["images" => $ret]);
    } else {
        http_response_code(400);
        echo json_encode([
            "message" => "Can't fetch images.",
            "error" => $stmt->errorInfo()
        ]);
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        "message" => "Can't fetch images.",
        "error" => $e->getMessage(),
    ]);
}