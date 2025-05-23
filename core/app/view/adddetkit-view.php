<?php
if(count($_POST)>0)
{	
		$detkit            = new Det_kit();
		$detkit->idpaquete = $_POST["idpaquete"];
		$detkit->idprod    = $_POST["product_id"];
		$detkit->precio    = $_POST["precio_unitario"];
		$detkit->descuento = $_POST["descuento"];
		$detkit->cantidad  = $_POST["q"];

	$detalle= $detkit->add();

	//setcookie("kitupd","true");
	print "<script>window.location='index.php?view=editkit&id=$detkit->idpaquete';</script>";
}

?>