<?php
if(isset($_GET["product_id"]))
{
	$orden_id = $_GET["orden_id"];
	$cart = $_SESSION["cart2"];

	if(count($cart) == 1)
	{
		unset($_SESSION["cart2"]);
	}
	else
	{
		$ncart = null;

		$nx=0;

		foreach($cart as $c)
		{
			if($c["product_id"]!=$_GET["product_id"])
			{
				$ncart[$nx]= $c;
			}

			$nx++;
		}

		$_SESSION["cart2"] = $ncart;
	}
}
else
{
	unset($_SESSION["cart2"]);
}

print "<script>window.location='index.php?view=repuestosordentrabajo&id=$orden_id';</script>";
?>