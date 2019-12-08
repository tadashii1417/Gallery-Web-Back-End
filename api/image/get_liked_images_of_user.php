<?php
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

// $data = json_decode(file_get_contents("php://input"));
if (!isset($_GET['user_id'])) {
    http_response_code(400);
    return (json_encode(['message' => 'missing user_id']));
}

try {
    $query = 'SELECT d2.* FROM loves AS d1, images AS d2
    WHERE (d1.user_id = :user_id) AND (d1.image_id = d2.id) AND (d2.status = 1)';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_GET['user_id']);
    if ($stmt->execute()) {
        $ret = array();
        while ($row = $stmt->fetchObject()) {
            $temp_user = new User($db);
            $temp_user->id = $row->user_id;
            $row->owner = $temp_user->get_owner_info();
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
