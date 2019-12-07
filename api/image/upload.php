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
include_once '../../objects/image.php';

$database = new Database();
$db = $database->getConnection();

$image = new Image($db);
$jwt = isset($_POST['jwt']) ? $_POST['jwt'] : "";

if ($jwt) {
    try {
        $decoded = JWT::decode($jwt, $key, ['HS256']);

        if (is_uploaded_file($_FILES['image']['tmp_name'])) {
            $tmp_file = $_FILES['image']['tmp_name'];
            $img_info = getimagesize($tmp_file);
            $img_name = $_FILES['image']['name'];
            $img_size = $_FILES['image']['size'];

            $tmp = explode('.', $img_name);
            $ext = end($tmp);
            $url = uniqid() . '.' . $ext;

            $upload_dir = '../../upload/images/' . $url;

            if (move_uploaded_file($tmp_file, $upload_dir)) {

                $image->userId = $decoded->data->id;
                $image->size = $img_size;
                $image->width = $img_info[0];
                $image->height = $img_info[1];
                $image->url = "/upload/images/" . $url;
                $image->categoryId = $_POST['category_id'];
                $image->description = $_POST['description'];

                if ($image->create()) {
                    http_response_code(200);
                    echo json_encode(
                        [
                            "message" => "File upload successful.",
                            "url" => $image->url,
                        ]
                    );
                } else {
                    echo json_encode(["message" => "Fail to insert into database"]);
                }
            } else {
                echo json_encode(["message" => "Upload fail"]);
            }
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
