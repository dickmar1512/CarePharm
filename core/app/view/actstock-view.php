<?
$products = ProductData::getAll();

foreach ($products as $prod) :
	$idprod = $prod->id;
	$prod->update_stock2($idprod);
endforeach;

print "<script>window.location='./?view=products';</script>";
?>