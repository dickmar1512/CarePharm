<?php
  include('funciones.php');
  
	if(isset($_POST['numDoc']))
  {    
       $dato = datosbyNroDocFactura($enlace, $_POST['numDoc']);

      	if($_POST['tipo']=='03'){
      		include('datos_factura_descripcion.php');
  		}

  		else if($_POST['tipo']=='05'){
      		include('datos_factura_descuento.php');
  		}

  		else if($_POST['tipo']=='07'){
      		include('datos_factura_devolucion.php');
  		}
	}
 ?>