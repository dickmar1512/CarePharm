<?php
header('Content-Type: application/json');
// Recibir y decodificar los datos JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    $tipo = $data["optTipoPersona"];

    $client = new PersonData();

    if ($tipo == 3)
    {
        $client->numero_documento = $data["dni"];
        $client->name = $data["name"];
        $client->lastname = $data["lastname"];
    }
    else
    {
        $client->numero_documento = $data["ruc"];
        $client->name = $data["razon_social"];
        $client->lastname = "";
    }
    
    $client->company = "";
    $client->address1 = $data["address1"];
    $client->email1 = $data["email1"];
    $client->phone1 = $data["phone1"];
    $client->id = $data["client_id"];

    $client->update_client();
    
    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["success" => false, "error" => "No se recibieron datos"]);
exit;

?>