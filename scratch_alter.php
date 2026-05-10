<?php
require 'core/controller/Database.php';
$db = new Database();
$con = $db->connect();
$res = $con->query("SELECT * FROM tipo_documento");
while($row = $res->fetch_assoc()){
    print_r($row);
    echo "<br>";
}
?>
