<?php
  include('funciones.php');
  
	if(isset($_POST['tipo']))
  {    
    $numDocUsuario = $_POST['numDocUsuario'];
    $tipo = $_POST['tipo']; 

    $dato = datosbyDocumento($enlace, $numDocUsuario, $tipo);
    echo json_encode($dato);
	}
 ?>