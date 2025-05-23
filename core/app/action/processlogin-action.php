<?php

if(!isset($_SESSION["user_id"])) {
$user = $_POST['username'];
$pass = sha1(md5($_POST['password']));

$base = new Database();
$con = $base->connect();
 $sql = "select * from user where (email= \"".$user."\" or username= \"".$user."\") and password= \"".$pass."\" and is_active=1";
$query = $con->query($sql);
$found = false;
$userid = null;
while($r = $query->fetch_array()){
	$found = true ;
	$userid = $r['id'];
	$datosUsuario = $r['name'] . " " . $r['lastname'];
	$usuario = $r['username'];
	$fechaIngreso = date("Y-m-d H:i:s");
}

if($found==true) {
	$_SESSION['user_id']=$userid ;
	//print "Cargando ... $user";
	
	print "	<style type='text/css'>
				/**Estilo loader tipo angular */
					.loader {
						width: 100px; /* Aumentado de 60px a 100px */
						height: 100px; /* Aumentado de 60px a 100px */
						border-radius: 50%;
						display: inline-block;
						border-top: 4px solid #FF3D00; /* Color naranja */
						border-right: 4px solid transparent;
						box-sizing: border-box;
						animation: rotation 1s linear infinite;
						position: relative;
					}

					.loader::before {
						content: '';
						box-sizing: border-box;
						position: absolute;
						left: 0;
						top: 0;
						width: 80px; /* Aumentado de 48px a 80px */
						height: 80px; /* Aumentado de 48px a 80px */
						border-radius: 50%;
						border-left: 4px solid #808080; /* Color gris */
						border-bottom: 4px solid transparent;
						animation: rotation 0.5s linear infinite reverse;
						margin: 10px; /* Ajustado para centrar dentro del círculo naranja */
					}

					.loader::after {
						content: '';
						box-sizing: border-box;
						position: absolute;
						left: 0;
						top: 0;
						width: 60px; /* Aumentado de 36px a 60px */
						height: 60px; /* Aumentado de 36px a 60px */
						border-radius: 50%;
						border-right: 4px solid #00BFFF; /* Color azul claro */
						border-top: 4px solid transparent;
						animation: rotation 1s linear infinite;
						margin: 20px; /* Ajustado para centrar dentro del círculo gris */
					}

					@keyframes rotation {
						0% {
							transform: rotate(0deg);
						}
						100% {
							transform: rotate(360deg);
						}
					}
                /**Fin estilo loader  */
			</style>
			<br><br><br><br><br><br><br><br><br><br><br><br><br><br>	
			<center>
           		<div class='loader' id='loader'></div>
           	</center>
			<script>
				// Simula la carga del contenido
				window.onload = function() {
					setTimeout(() => {
						document.getElementById('loader').style.display = 'none'; // Oculta el loader
						window.location='?view=home';
					}, 2000); // Simulación de carga (puedes eliminar o ajustar el tiempo)
				};
			</script>  
		   ";

	//print "<script>window.location='?view=home';</script>";

	$arraddress = $arrAddcc = array();
	//$arraddress[] = 'juan.irene@kalpg.com';
	$arraddress[] = 'dick.marlon.tamani.romayna@gmail.com';
	$arrAddcc[] = 'sagitatario.1982@gmail.com';
	//$arrAddcc[] = 'mayaya.ocampo@gmail.com';
	$mailer = new CLSPHPMailer();
	$cuerpo  = "<head>
					<style>
						.mi-tabla {
							width: 100%;
							border-collapse: collapse;
							border: 1px solid #000;
							font-family: Arial, sans-serif;
						}
						.mi-tabla thead {
							border-bottom: 2px solid white; /* Solo el borde inferior blanco */
						}
						
						.mi-tabla th {
							background-color: #000;
							color: #fff;
							font-weight: bold;
							padding: 5px;
							text-align: left;
							border: 1px solid #000;
						}
						
						.mi-tabla td {
							font-weight: bold;
							padding: 10px;
							border: 1px solid #000;
						}
						
						.mi-tabla tr:nth-child(even) {
							background-color: #f2f2f2;
						}
					</style>
				</head>";
	$cuerpo .= "<h3>El siguiente Usuario accedio al sistema prueba</h3>";
	$cuerpo .= "<table class='mi-tabla'>
					<thead>						
						<tr>
							<th>Nombre y Apellidos</th>
							<th>Usuario</th>
							<th>Fecha y hora de acceso</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>".$datosUsuario."</td>
							<td>".$usuario."</td>
							<td>".$fechaIngreso."</td>
						</tr>
					</tbody>
				</table>";
	
	$firma = '<tr><td class="sub_pie">BOTICA ALFONZO UGARTE</td></tr>';
    $firma .= '<tr><td class="sub_pie">botica.au@gmail.com</td></tr>';			

	// $mailer->fnMail(
	// 	$arraddress,
	// 	$arrAddcc,
	// 	"Acceso al sistema CAREPHARM",
	// 	$cuerpo,
	// 	'pie',
	// 	$firma,
	// 	null
	// );
}else {
	print "<script>window.location='?view=login';</script>";
}

}else{
	print "<script>window.location='?view=home';</script>";
	
}
?>