<?php
$est = $_GET["est"];
$idpaq = $_GET["id"];
$fecha_fin= $_GET["fecha"];
$op  = new KitData();
$op->estado= $est;
$op->fecha_fin=$fecha_fin;
$op->idpaquete = $idpaq;
$op->updateestado();

Core::redir("././?view=paquetes");
?>