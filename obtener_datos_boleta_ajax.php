<?php
  include('funciones.php');
  
	if(isset($_POST['numDoc']))
  {    
      $dato = datosbyNroDocBoleta($enlace, $_POST['numDoc']);

      	if($_POST['tipo']=='03'){
      		include('datos_boleta_descripcion.php');
  		}

  		else if($_POST['tipo']=='05'){
      		include('datos_boleta_descuento.php');
  		}

  		else if($_POST['tipo']=='07'){
      		include('datos_boleta_devolucion.php');
  		}
	}
 ?>