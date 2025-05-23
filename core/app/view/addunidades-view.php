<?php

$unidad = new UnidadMedidaData();
$unidad->name = $_POST["name"];
$unidad->sigla = $_POST["sigla"];
$unidad->add();

// setcookie("gradeadded",$grade->name);

print "<script>window.location='index.php?view=unidades';</script>";

?>