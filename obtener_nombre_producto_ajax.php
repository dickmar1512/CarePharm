<?php
  include('funciones.php');
  
	if(isset($_POST['cod']))
  {    
      $dato = get_producto_cod($enlace, $_POST['cod']);

      $input=$_POST['cod_input_nuevo'];
      
      include('datos_producto_nuevo.php');
	}
 ?>