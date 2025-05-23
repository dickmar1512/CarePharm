<?php
$dbPath = '../efact1.3.4/bd/BDFacturador.db';

try {
    $db = new SQLite3($dbPath);
    $results = $db->query("SELECT * FROM DOCUMENTO");
} catch (Exception $e) {
    die("Error al conectar o consultar la base de datos: " . $e->getMessage());
}

$listaSituacion = [
    "ListaSituacion" => [
        ["id" => "01", "nombre" => "Por Generar XML"],
        ["id" => "02", "nombre" => "XML Generado"],
        ["id" => "03", "nombre" => "Enviado y Aceptado SUNAT"],
        ["id" => "04", "nombre" => "Enviado y Aceptado SUNAT con Obs."],
        ["id" => "05", "nombre" => "Rechazado por SUNAT"],
        ["id" => "06", "nombre" => "Con Errores"],
        ["id" => "07", "nombre" => "Por Validar XML"],
        ["id" => "08", "nombre" => "Enviado a SUNAT Por Procesar"],
        ["id" => "09", "nombre" => "Enviado a SUNAT Procesando"],
        ["id" => "10", "nombre" => "Rechazado por SUNAT"],
        ["id" => "11", "nombre" => "Enviado y Aceptado SUNAT"],
        ["id" => "12", "nombre" => "Enviado y Aceptado SUNAT con Obs."]
    ]
];
?>

<!-- HTML + Bootstrap -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentos</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <?php
  ?>
    <h2 class="mb-4">Listado de Documentos</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm">
            <thead class="table-dark">
                <tr>
                    <th>NUM_RUC</th>
                    <th>TIP_DOCU</th>
                    <th>NUM_DOCU</th>
                    <th>FEC_CARG</th>
                    <th>FEC_GENE</th>
                    <th>FEC_ENVI</th>
                    <th>DES_OBSE</th>
                    <th>NOM_ARCH</th>
                    <th>IND_SITU</th>
                    <th>TIP_ARCH</th>
                    <th>FIRM_DIGITAL</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $results->fetchArray(SQLITE3_ASSOC)) : 
                      $xmlPath = '../efact1.3.4/sunat_archivos/sfs/FIRMA/'.htmlspecialchars($row['NOM_ARCH']).'.xml';
                      $hash = '';
                      // Verifica si el archivo existe
                      if (file_exists($xmlPath)) {              

                          // Carga el archivo XML
                          $xml = simplexml_load_file($xmlPath);

                          if (!$xml) {
                              die("No se pudo cargar el XML.");
                          }

                          // Registrar los namespaces (dependiendo del XML de SUNAT)
                          $namespaces = $xml->getNamespaces(true);

                          // Registrar namespace cbc (usado frecuentemente)
                          $xml->registerXPathNamespace('cbc', $namespaces['cbc'] ?? 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
                          $xml->registerXPathNamespace('cac', $namespaces['cac'] ?? 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
                          $hash = $xml->xpath('//ds:DigestValue')[0];
                      }
                      $idSituacion = $row['IND_SITU'];

                      $situacion = array_filter($listaSituacion['ListaSituacion'], function($item) use ($idSituacion) {
                                        return $item['id'] == $idSituacion;
                                    });
                      $situacionNombre = current($situacion)['nombre'];              
                  ?>
                    <tr>
                        <td><?= htmlspecialchars($row['NUM_RUC']) ?></td>
                        <td><?= htmlspecialchars($row['TIP_DOCU']) ?></td>
                        <td><?= htmlspecialchars($row['NUM_DOCU']) ?></td>
                        <td><?= htmlspecialchars($row['FEC_CARG']) ?></td>
                        <td><?= htmlspecialchars($row['FEC_GENE'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['FEC_ENVI'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['DES_OBSE']) ?></td>
                        <td><?= htmlspecialchars($row['NOM_ARCH']) ?></td>
                        <td><?= htmlspecialchars($situacionNombre) ?></td>
                        <td><?= htmlspecialchars($row['TIP_ARCH']) ?></td>
                        <td><?= htmlspecialchars((string)$hash ?? '-')?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>