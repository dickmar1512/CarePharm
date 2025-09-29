<?php
	if(isset($_SESSION["cart"]))
	{
		$cart = $_SESSION["cart"];

		if(count($cart) > 0)
		{
			$TIPO             = $_POST["TIPO"];
			$SERIE            = $_POST["SERIE"];
			$COMPROBANTE      = $_POST["COMPROBANTE"];
			$estado           = '1';
			$fecEmision       = $_POST["fecEmision"];
		    $horEmision       = $_POST["horEmision"];
			// antes de proceder con lo que sigue vamos a verificar que:
			// haya existencia de productos
			// si se va a facturar la cantidad a facturr debe ser menor o igual al producto facturado en inventario
			$num_succ = 0;
			$process = false;
			$errors = array();

			foreach($cart as $c)
			{
				$q = OperationData::getQYesF($c["product_id"]);

				if($c["q"]<=$q)
				{
					if(isset($_POST["is_oficial"]))
					{
						$qyf =OperationData::getQYesF($c["product_id"]); /// son los productos que puedo facturar
						if($c["q"]<=$qyf)
						{
							$num_succ++;
						}
						else
						{
							$error = array("product_id"=>$c["product_id"],"message"=>"No hay suficiente cantidad de producto para facturar en inventario.");
							$errors[count($errors)] = $error;
						}
					}
					else
					{
						// si llegue hasta aqui y no voy a facturar, entonces continuo ...
						$num_succ++;
					}
				}
				else
				{
					$error = array("product_id"=>$c["product_id"],"message"=>"No hay suficiente cantidad de producto en inventario.");
					$errors[count($errors)] = $error;
				}
			}

			if($num_succ==count($cart))
			{
				$process = true;
			}

			if($process == false)
			{
				$_SESSION["errors"] = $errors;
				?>
					<script>
						window.location="./?view=sell";
					</script>
				<?php
			}

			if($process == true)
			{
				$sell = new SellData();
				$sell->user_id = $_SESSION["user_id"];
				$sell->tipo_comprobante = 70;
				$sell->serie = $SERIE;
				$sell->comprobante = $COMPROBANTE;
				$sell->total = $_POST["total"];
				$sell->discount = $_POST["discount3"];
				$sell->cash = $_POST["money3"];
				$sell->tipo_pago = 1;
				$sell->person_id = '7';
				$sell->estado = $estado;
				$sell->created_at = $fecEmision.' '.$horEmision;


				if(isset($_POST["client_id"]) && $_POST["client_id"]!="")
				{
				 	$sell->person_id=$_POST["client_id"];
						$s = $sell->add_with_client();
				}
				else
				{
						$s = $sell->add2();
				}
				echo json_encode($cart);
				foreach($cart as  $c)
				{
					$op = new OperationData();
					$op->product_id = $c["product_id"] ;
					$op->operation_type_id=OperationTypeData::getByName("salida")->id;
					$product = ProductData::getById($c["product_id"]);
					$op->sell_id=$s[1];					
					$op->descripcion = $c["descripcion"];
					$op->cu = $product->price_in;
					$op->prec_alt = $c["precio_unitario"];
					$op->descuento = $c["descuento"];
					$op->idpaquete = $c["idpaquete"];
					$op->q= $c["q"];

					if(isset($_POST["is_oficial"]))
					{
						$op->is_oficial = 1;
					}

					$add = $op->add();

					$product2 = new ProductData();
					$product2->stock = $c["q"];
					$product2->id = $c["product_id"];

					$product2->restar_stock();
					
				}

				unset($_SESSION["cart"]);
				setcookie("selled","selled");


				print "<script>window.location='?view=notaventa&id=$s[1]';</script>";
			}
		}
	}
?>