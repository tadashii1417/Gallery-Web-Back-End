<?php
// required headers
include_once '../../config/header.php';


include_once '../../config/database.php';
include_once '../../objects/user.php';

$database = new Database();
$db = $database->getConnection();


try {
    $keyword = $_GET['keyword'];
    $keyword = htmlspecialchars(strip_tags($keyword));
    $query = 'SELECT * FROM images WHERE status = 1 AND description LIKE "%' . $keyword . '%"';
    $stmt = $db->prepare($query);
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
