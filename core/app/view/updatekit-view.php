<?php

if(count($_POST)>0)
{	
	$kit = KitData::getById($_POST["kit_id"]);

	$kit->barcode = $_POST["barcode"];
	$kit->nombre = $_POST["name"];
	$kit->precio = $_POST["price_out"];

  	$kit->descripcion = $_POST["description"];

  	$is_active = 0;

  	if(isset($_POST["is_active"])){ $is_active=1;}

  	$kit->estado = $is_active;

	$kit->user_id = $_SESSION["user_id"];
	$kit->update();

	if(isset($_FILES["image"])){
		$image = new Upload($_FILES["image"]);
		if($image->uploaded)
		{
			$image->Process("storage/products/");
			if($image->processed)
			{
				$kit->image = $image->file_dst_name;
				$kit->update_image();
			}
		}
	}
	setcookie("kitupd","true");
	print "<script>window.location='./?view=editkit&id=$_POST[kit_id]';</script>";
}

?>