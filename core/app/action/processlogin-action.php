<?php
// Cargar las clases necesarias
header('Content-Type: application/json');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si ya está logueado
if(isset($_SESSION["user_id"])) {
    echo json_encode(['success' => true]);
    exit;
}

// Validar que vengan los datos
if(empty($_POST['username']) || empty($_POST['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario y contraseña son requeridos'
    ]);
    exit;
}

$user = $_POST['username'];
$pass = sha1(md5($_POST['password']));

$base = new Database();
$con = $base->connect();
$sql = "SELECT * FROM user WHERE (email= \"".$user."\" OR username= \"".$user."\") AND password= \"".$pass."\" AND is_active=1";
$query = $con->query($sql);
$found = false;
$userid = null;

while($r = $query->fetch_array()){
    $found = true;
    $userid = $r['id'];
    $datosUsuario = $r['name'] . " " . $r['lastname'];
    $usuario = $r['username'];
    $fechaIngreso = date("Y-m-d H:i:s");
}

if($found) {
    $_SESSION['user_id'] = $userid;
    
    // Enviar correo (puedes mover esto a un proceso en segundo plano si es muy lento)
    $arraddress = $arrAddcc = array();
    $arraddress[] = 'juan.irene@kalpg.com';
    $arrAddcc[] = 'sagitatario.1982@gmail.com';
    $arrAddcc[] = 'mayaya.ocampo@gmail.com';
    $asunto = "Acceso al sistema CAREPHARM";
    
    $mailer = new CLSPHPMailer();
    $cuerpo = "<head><style>.mi-tabla { width: 100%; border-collapse: collapse; border: 1px solid #000; font-family: Arial, sans-serif; }
        .mi-tabla thead { border-bottom: 2px solid white; }
        .mi-tabla th { background-color: #000; color: #fff; font-weight: bold; padding: 5px; text-align: left; border: 1px solid #000; }
        .mi-tabla td { font-weight: bold; padding: 10px; border: 1px solid #000; }
        .mi-tabla tr:nth-child(even) { background-color: #f2f2f2; }</style></head>";
    $cuerpo .= "<h3>El siguiente Usuario accedio al sistema</h3>";
    $cuerpo .= "<table class='mi-tabla'><thead><tr>
        <th>Nombre y Apellidos</th><th>Usuario</th><th>Fecha y hora de acceso</th></tr></thead>
        <tbody><tr><td>".$datosUsuario."</td><td>".$usuario."</td><td>".$fechaIngreso."</td></tr></tbody></table>";
    
    $firma = '<tr><td class="sub_pie">BOTICA ALFONZO UGARTE</td></tr>';
    $firma .= '<tr><td class="sub_pie">botica.au@gmail.com</td></tr>';            

    // Enviar el correo (puedes comentar esto si no siempre quieres enviar el correo)
   // $mailer->fnMail($arraddress, $arrAddcc, $asunto, $cuerpo, 'pie', $firma, null);
    
    echo json_encode(['success' => true]);
	exit(0);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario o contraseña incorrectos'
    ]);
	exit(0);
}
?>