<?php
$iddet = $_GET["iddet"];
$idpaq = $_GET["id"];

$op  = Det_kit::delId($iddet);

Core::redir("./index.php?view=editkit&id=$idpaq");
?>