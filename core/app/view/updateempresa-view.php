<?php

if(count($_POST)>0)
{
	$empresa = EmpresaData::getDatos();

	$empresa->Emp_IdEmpresa = $empresa->Emp_IdEmpresa;
	$empresa->Emp_Ruc = $_POST["ruc"];
	$empresa->Emp_RazonSocial = addslashes($_POST["razon_social"]);
	$empresa->Emp_Descripcion = addslashes($_POST["descripcion"]);
	$empresa->Emp_Direccion = addslashes($_POST["direccion"]);
	$empresa->Emp_Telefono = $_POST["telefono"];
	$empresa->Emp_Celular = $_POST["celular"];

	$empresa->update();

	print "<script>window.location='./?view=settings';</script>";
}


?>