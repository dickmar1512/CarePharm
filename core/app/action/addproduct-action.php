<?php
header('Content-Type: application/json');
// Recibir y decodificar los datos JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    $product = new ProductData();
    $product->barcode          = $data["barcode"];
    $product->name             = addslashes($data["name"]);
    $product->principio_activo = $data["prin_act"];
    $product->price_in         = $data["price_in"];
    $product->price_may        = $data["price_may"];
    $product->price_out        = $data["price_out"];
    $product->anaquel          = $data["anaquel"];
    $product->stock            = $data["q"];
    $product->unit             = $data["selUnidadMedida"];
    $product->description      = $data["description"];
    $product->presentation     = $data["presentacion"];
    $product->fecha_venc       = $data["fecha_venc"];
    
    $category_id="NULL";
    if($data["category_id"]!=""){ $category_id = $data["category_id"];}
    $inventary_min="10";
    if($data["inventary_min"]!=""){ $inventary_min = $data["inventary_min"];}
  
    $product->category_id   = $category_id;
    $product->inventary_min = $inventary_min;
    $product->user_id       = $_SESSION["user_id"];
  
    if(isset($data['is_stock']))
    {
      $product->is_stock = 1;
    }
    else
    {
      $product->is_stock = 0;
    }
  
    if(isset($_FILES["image"]))
    {
      $image = new Upload($_FILES["image"]);
      if($image->uploaded){
        $image->Process("storage/products/");
        if($image->processed)
        {
          $product->image = $image->file_dst_name;
          $prod = $product->add_with_image();
        }
        }else
        {
         $prod= $product->add();
        }
    }
    else
    {
     $prod= $product->add();
    }
    
    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["success" => false, "error" => "No se recibieron datos"]);
exit;
?>