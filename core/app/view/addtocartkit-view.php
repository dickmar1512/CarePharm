<?php
	$idpaquete = $_POST["idpaquete"];
    $i=0;
	if(!isset($_SESSION["cart"]))
	{
		$prodKit = Det_kit::getById($idpaquete);
        foreach ($prodKit as $prod) 
        { 
        	$product = array("product_id"=>$prod->idprod,"q"=>$prod->cantidad, "precio_unitario"=>$prod->precio,"descripcion"=>"","descuento"=>$prod->descuento,"idpaquete"=>$idpaquete);
           $cart[$i]	= $product;
           $i++;
        }

        $_SESSION["cart"] = $cart;
	
		$cart = $_SESSION["cart"];
		$num_succ = 0;
		$process=false;
		$errors = array();

		foreach($cart as $c)
		{
			$product2 = ProductData::getById($c["product_id"]);
			$q = $product2->stock;

			if($product2->is_stock == 0)
			{
				$num_succ++;
			}
			else
			{
				if($c["q"] <= $q)
				{
					$num_succ++;
				}
				else
				{
					$error = array("product_id"=>$c["product_id"],"message"=>"No hay suficiente cantidad de producto en inventario.");
					$errors[count($errors)] = $error;
				}
			}			
	    }

	//echo $num_succ;
	if($num_succ==count($cart))
	{
		$process = true;
	}

	if($process==false)
	{
		unset($_SESSION["cart"]);
	$_SESSION["errors"] = $errors;
		?>	
	<script>
		window.location="index.php?view=sell";
	</script>
	<?php
	}
}
else 
{

	$cart = $_SESSION["cart"];
	$index=0;

	$product2 = ProductData::getById($cart[$index]["product_id"]);
	$q = $product2->stock;

	$can = true;
	if($_POST["q"]<=$q or $product2->is_stock == 0)
	{
	}
	else
	{
		$error = array("product_id"=>$_POST["product_id"],"message"=>"No hay suficiente cantidad de producto en inventario.");
		$errors[count($errors)] = $error;
		$can=false;
	}

	if($can==false)
	{
	$_SESSION["errors"] = $errors;
		?>	
	<script>
		window.location="index.php?view=sell";
	</script>
	<?php
	}
	?>

<?php
if($can==true)
{
	foreach($cart as $c)
	{
		if($c["product_id"]==$_POST["product_id"])
		{
			echo "found";
			$found=true;
			break;
		}
		$index++;
	//	print_r($c);
	//	print "<br>";
	}

	if($found==true)
	{
		$q1 = $cart[$index]["q"];
		$q2 = $_POST["q"];
		$cart[$index]["q"]=$q1+$q2;
		$_SESSION["cart"] = $cart;
	}

	if($found==false)
	{
	    $nc = count($cart);

	    $prodKit = Det_kit::getById($idpaquete);
        foreach ($prodKit as $prod) 
        { 
        	$product = array("product_id"=>$prod->idprod,"q"=>$prod->cantidad, "precio_unitario"=>$prod->precio,"descripcion"=>"","descuento"=>$prod->descuento,"idpaquete"=>$idpaquete);
           $cart[$nc]	= $product;
           $nc++;
        }
		$_SESSION["cart"] = $cart;
	}

}
}

print "<script>window.location='index.php?view=sell';</script>";

?>