<?php
if(isset($_SESSION["reabastecer"]))
{
	$tipo_comprobante = $_POST["optTipoComprobante"];
	$serie            = $_POST["serie"];
	$comprobante      = $_POST["comprobante"];
	$fecemi           = $_POST["fecemi"];
	$total            = $_POST["total"];
	$cash             = $_POST["money"];

	$cart = $_SESSION["reabastecer"];

	if(count($cart)>0)
	{
		$process = true;

		if($process == true)
		{
			$sell = new SellData();
			$sell->user_id          = $_SESSION["user_id"];
			$sell->tipo_comprobante = $tipo_comprobante;
			$sell->serie            = $serie;
			$sell->comprobante      = $comprobante;
			$sell->fecha_emi        = $fecemi;
			$sell->total            = $total;
			$sell->cash             = $cash;

			if(isset($_POST["client_id"]) && $_POST["client_id"]!="")
			{
			 	$sell->person_id=$_POST["client_id"];
 				$s = $sell->add_re_with_client();
			}
			else
			{
 				$s = $sell->add_re2();
			}

			foreach($cart as  $c)
			{
				$op = new OperationData();
				$op2 = new ProductData();
				$op->product_id = $c["product_id"] ;

				$product = ProductData::getById($c["product_id"]);				
				$op->cu= $c["price_in"];
				$op->prec_alt = $c["price_in"];	
				$op->operation_type_id = 1; // 1 - entrada
			 	$op->sell_id = $s[1];
			 	$op->descuento= 0;
			 	$op->q = $c["q"];
				
				if($tipo_comprobante == 60)
				{
					$op->descripcion = "INGRESO DIVERSO: Por Inventario de produtos";
				}
				
				$fecha_actual = date('Y-m-d H:i:s');
				$op->created_at = $fecha_actual;

				$op2->id = $c["product_id"];
				$op2->reg_san=$c["rs"];				
				$op2->laboratorio=$c["labo"];
				$op2->price_in=$c["price_in"];

				if(isset($_POST["is_oficial"]))
				{
					$op->is_oficial = 1;
				}

				$add = $op->add();
				//$add2 = $op2->update_cu();
				$op2->update_cu();

				if($product->is_stock == 1)
				{
					if ($c["fec_venc"] != "") {
						$sql_venc = "UPDATE product SET fecha_venc = '" . $c["fec_venc"] . "' WHERE id = " . $op->product_id;
						Executor::doit($sql_venc);
					}

				    $lote = new LoteData();
				    $lote->id_prod  = $c["product_id"];
				    $lote->num_lot  = $c["nl"];
				    $lote->fech_ing = "now()";
				    $lote->id_sell  = $s[1];
				    $lote->user_id  = $_SESSION["user_id"];
				    $lote->add();
				}
			}

			unset($_SESSION["reabastecer"]);
			setcookie("selled","selled");

			print "<script>window.location='./?view=onere&id=$s[1]';</script>";
		}
	}
}

?>
