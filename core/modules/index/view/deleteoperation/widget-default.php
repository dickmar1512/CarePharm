<?php

print_r($_GET);

$operation = OperationData::getById($_GET["opid"]);
$operation->del();

print "<script>window.location='./?view=$_GET[ref]&product_id=$_GET[pid]';</script>";

?>