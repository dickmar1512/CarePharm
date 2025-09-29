<?php 
	//Incluyo la clase
include "lib/simplexlsx.class.php";
$xlsx = new SimpleXLSX( 'excel_productos.xlsx' );//Instancio la clase y le paso como parametro el archivo a leer
$fp = fopen( 'datos.csv', 'w');//Abrire un archivo "datos.csv", sino existe se creara
  foreach( $xlsx->rows() as $fields ) 
  {//Itero la hoja de calculo
        fputcsv( $fp, $fields);//Doy formato CSV a una línea y le escribo los datos
}
fclose($fp);//Cierro el archivo "datos.csv"


 $db_host="localhost";
 $db_name="dbcarepharm";
 $db_user="milenio";
 $db_pass="armagedon";
   
     $xlsx = new SimpleXLSX('excel_productos.xlsx' );
    try {
      $conn = new PDO( "mysql:host=$db_host;dbname=$db_name", "$db_user", "$db_pass");
      $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
     catch(PDOException $e)
    {
        echo $sql . "<br>" . $e->getMessage();
    }

    foreach ($xlsx->rows() as $fields)
     {         
         $image = $fields[1];         
         $barcode = $fields[2];
         $name = $fields[3];
         $description = $fields[4];
         $stock = $fields[5];         
         $is_stock = $fields[6];
         $inventary_min = $fields[7];
         $price_in = $fields[8];
         $price_out = $fields[9];
         $unit = $fields[10];         
         $presentation = $fields[11];         
         $user_id = $fields[12];         
         $category_id = $fields[13];
         $created_at = $fields[14];
         $is_active = $fields[15];
         $stmt->execute();
     }

    $stmt = $conn->prepare( "INSERT INTO product (image, barcode, name, description, stock, is_stock, inventary_min, price_in, price_out, unit, presentation, user_id, category_id, created_at, is_active) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
     $stmt->bindParam( 1, $image);
     $stmt->bindParam( 2, $barcode);
     $stmt->bindParam( 3, $name);
     $stmt->bindParam( 4, $description);
     $stmt->bindParam( 5, $stock);
     $stmt->bindParam( 6, $is_stock);
     $stmt->bindParam( 7, $inventary_min);
     $stmt->bindParam( 8, $price_in);
     $stmt->bindParam( 9, $price_out);
     $stmt->bindParam( 10, $unit);
     $stmt->bindParam( 11, $presentation);
     $stmt->bindParam( 12, $user_id);
     $stmt->bindParam( 13, $category_id);
     $stmt->bindParam( 14, $created_at);
     $stmt->bindParam( 15, $is_active);     
 ?>