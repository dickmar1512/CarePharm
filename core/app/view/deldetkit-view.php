<?php
$iddet = $_GET["iddet"];
$idpaq = $_GET["id"];

$op  = Det_kit::delId($iddet);

Core::redir("././?view=editkit&id=$idpaq");
?>