<?php
require 'core/controller/Database.php';
$db = new Database();
$con = $db->connect();
$tables = ['box', 'ingresos_pagos', 'detalle_paq', 'detalle_orden', 'configuration'];
foreach($tables as $t){
    try {
        $res = $con->query("SHOW COLUMNS FROM $t");
        echo "TABLE $t:\n";
        if($res){
            while($row = $res->fetch_assoc()){
                echo $row['Field'].' ';
            }
        } else {
            echo "Error or table not found";
        }
        echo "\n";
    } catch(Exception $e) {
        echo "TABLE $t: Not found\n";
    }
}
?>
