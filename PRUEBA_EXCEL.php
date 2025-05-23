use PhpOffice\PhpSpreadsheet\IOFactory;

<?php
require 'vendor/autoload.php'; // Asegúrate de tener PhpSpreadsheet instalado


// Configuración de la base de datos
$host = 'localhost';
$db   = 'nombre_base_datos';
$user = 'usuario';
$pass = 'contraseña';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel']['tmp_name'])) {
    $archivoExcel = $_FILES['excel']['tmp_name'];

    // Leer el archivo Excel
    $spreadsheet = IOFactory::load($archivoExcel);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Suponiendo que la primera fila es encabezado
        $headers = $rows[0];
        unset($rows[0]);

        // Prepara la consulta de inserción
        $placeholders = implode(',', array_fill(0, count($headers), '?'));
        $columns = implode(',', $headers);
        $sql = "INSERT INTO tu_tabla ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);

        foreach ($rows as $row) {
            $stmt->execute($row);
        }

        echo "Datos importados correctamente.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="excel" accept=".xlsx,.xls" required>
    <button type="submit">Importar Excel</button>
</form>