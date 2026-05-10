<?php
require 'core/controller/Database.php';
$db = new Database();
$con = $db->connect();

$tables_to_alter = [
    'box' => 'estado',
    'configuration' => 'estado',
    'ingresos_pagos' => 'estado',
    'detalle_paq' => 'estado',
    'detalle_orden' => 'estado',
    'cut' => 'estado'
];

foreach($tables_to_alter as $table => $column) {
    try {
        // Check if column exists
        $check = $con->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        if ($check && $check->num_rows == 0) {
            $alter = "ALTER TABLE `$table` ADD `$column` INT NOT NULL DEFAULT 1";
            if ($con->query($alter)) {
                echo "Successfully added $column to $table.\n<br>";
            } else {
                echo "Error adding $column to $table: " . $con->error . "\n<br>";
            }
        } else if ($check && $check->num_rows > 0) {
            echo "Column $column already exists in $table.\n<br>";
        } else {
            echo "Table $table might not exist.\n<br>";
        }
    } catch (Exception $e) {
        echo "Exception on $table: " . $e->getMessage() . "\n<br>";
    }
}
echo "Done.";
?>
