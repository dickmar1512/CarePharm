<?php
if(count($_POST)>0)
{
	$kit = new KitData();

	$kit->barcode =$_POST["barcode"];
	$kit->nombre = $_POST["name"];
	$kit->descripcion=$_POST["description"];
	$kit->precio=$_POST["price_out"];
	$kit->user_id = $_SESSION["user_id"];
	if(isset($_FILES["image"]))
	  {
	    $image = new Upload($_FILES["image"]);
	    if($image->uploaded)
	    {
	      $image->Process("storage/products/");
	      if($image->processed)
	      {
	        $kit->imagen = $image->file_dst_name;
	        $kits = $kit->add_with_image();
	      }
	    }
	    else
	     {
	       $kits= $kit->add();
	     }
	  }
	  else
	  {
	   $kits= $kit->add();
	  }
	  print "<script>window.location='./?view=paquetes';</script>";
}
else
{}
?>