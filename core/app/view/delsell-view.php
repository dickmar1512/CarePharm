<?php

$sell = SellData::getById($_GET["id"]);
$operations = OperationData::getAllProductsBySellId($_GET["id"]);

foreach ($operations as $op) {
	if ($op->operation_type_id == 2) {
		$product = $op->getProduct();
		if ($product->is_stock == 1) {
			$product->stock = $op->q;
			$product->sumar_stock();
		}
	}
	$op->cancel();
}

$sell->cancel();
Core::redir("././?view=sells");

?>