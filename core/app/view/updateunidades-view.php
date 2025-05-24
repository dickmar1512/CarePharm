<?php

if(count($_POST) > 0)
{
	$user = UnidadMedidaData::getById($_POST["id"]);
	$user->name = $_POST["name"];
	$user->sigla = $_POST["sigla"];

	$user->update();
	//exit();
	print "<script>window.location='./?view=unidades';</script>";
}


?>