<?php
	include('funciones.php');
  
	if(isset($_POST['txtBusqueda']))
  	{
    	$txtBusqueda = $_POST['txtBusqueda'];

    	$productos = get_busqueda($enlace, $txtBusqueda);

    	include('lista_productos.php');
	}
?>