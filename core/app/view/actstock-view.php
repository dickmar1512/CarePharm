<?php
/**
 * Sincronización Maestra de Inventario
 * Este proceso recalcula el stock de todos los productos basándose exclusivamente
 * en el historial de operaciones (entradas y salidas) para resolver inconsistencias.
 */

$con = Database::getCon();

// Consulta para obtener el balance real de cada producto
$sql_sync = "UPDATE product p
             SET p.stock = (
                SELECT COALESCE(SUM(CASE 
                    WHEN op.operation_type_id = 1 THEN op.q 
                    WHEN op.operation_type_id = 2 THEN -op.q 
                    ELSE 0 
                END), 0)
                FROM operation op
                WHERE op.product_id = p.id AND op.estado = 1
             )
             WHERE p.is_active = 1";

$res = $con->query($sql_sync);

if($res){
    // Opcional: Podríamos agregar un log aquí
}

// Redirigir de vuelta a la lista de productos con un mensaje de éxito
print "<script>window.location='./?view=products&sync=success';</script>";
?>