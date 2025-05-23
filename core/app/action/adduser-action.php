<?php
header('Content-Type: application/json');
// Recibir y decodificar los datos JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    $is_admin = isset($data["is_admin"]) ? 1 : 0;

    $user = new UserData();
    $user->name = $data["name"];
    $user->lastname = $data["lastname"];
    $user->username = $data["username"];
    $user->email = $data["email"];
    $user->is_admin = $is_admin;
	$user->created_at=date("Y-m-d H:i:s");
    $user->password = sha1(md5($data["password"]));

    $user->add();
    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["success" => false, "error" => "No se recibieron datos"]);
exit;
?>