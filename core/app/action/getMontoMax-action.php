<?php
header('Content-Type: application/json');
// Recibir y decodificar los datos JSON
$products = SellData::getSellsUnBoxed();
if (count($products) > 0) {
    $total_total = 0;

    foreach ($products as $sell):
        $operations = OperationData::getAllProductsBySellId($sell->id);
		$total = 0;

        foreach ($operations as $operation) {
            $product = $operation->getProduct();
			$total += $operation->q * ($operation->prec_alt - $operation->descuento);
		}

		$total_total += $total;
    endforeach;    
}
echo json_encode(["success" => true, "montomax" => $total_total]);
exit;
?>