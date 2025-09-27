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

<!-- HTML + Bootstrap + DataTables -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentos</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
</head>
<body class="p-4">
<div class="container-fluid">
    <h2 class="mb-4">Listado de Documentos</h2>
    
    <div class="table-responsive">
        <table id="documentosTable" class="table table-bordered table-striped table-hover table-sm">
            <thead class="table-dark">
                <tr>
                    <th>RUC</th>
                    <th>Tipo Doc.</th>
                    <th>Núm. Doc.</th>
                    <th>Fecha Carga</th>
                    <th>Fecha Gen.</th>
                    <th>Fecha Envío</th>
                    <th>Observaciones</th>
                    <th>Archivo</th>
                    <th>Situación</th>
                    <th>Tipo Arch.</th>
                    <th>Firma Digital</th>
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
                          $xml->registerXPathNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
                          $hashNodes = $xml->xpath('//ds:DigestValue');
                          if (!empty($hashNodes)) {
                              $hash = (string)$hashNodes[0];
                          }
                      }
                      $idSituacion = $row['IND_SITU'];

                      $situacion = array_filter($listaSituacion['ListaSituacion'], function($item) use ($idSituacion) {
                                        return $item['id'] == $idSituacion;
                                    });
                      $situacionNombre = !empty($situacion) ? current($situacion)['nombre'] : 'Estado Desconocido';              
                  ?>
                    <tr>
                        <td><?= htmlspecialchars($row['NUM_RUC']) ?></td>
                        <td><?= htmlspecialchars($row['TIP_DOCU']) ?></td>
                        <td><?= htmlspecialchars($row['NUM_DOCU']) ?></td>
                        <td><?= htmlspecialchars($row['FEC_CARG']) ?></td>
                        <td><?= htmlspecialchars($row['FEC_GENE'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['FEC_ENVI'] ?? '-') ?></td>
                        <td class="text-truncate" title="<?= htmlspecialchars($row['DES_OBSE']) ?>"><?= htmlspecialchars(substr($row['DES_OBSE'], 0, 50) . (strlen($row['DES_OBSE']) > 50 ? '...' : '')) ?></td>
                        <td><?= htmlspecialchars($row['NOM_ARCH']) ?></td>
                        <td>
                            <span class="badge bg-<?= getBadgeClass($idSituacion) ?>">
                                <?= htmlspecialchars($situacionNombre) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['TIP_ARCH']) ?></td>
                        <td class="text-truncate" title="<?= htmlspecialchars($hash) ?>"><?= htmlspecialchars($hash ? substr($hash, 0, 40) . '...' : '-') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- jQuery (requerido para DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTables Extensions -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#documentosTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "responsive": true,
        "pageLength": 10,
        "lengthMenu": [[10, 15, 20, 25, 50, 100, -1], [10, 15, 20, 25, 50, 100, "Todos"]],
        "dom": '<"row"<"col-md-3"l><"col-md-6 text-center"B><"col-md-3"f>>rtip',
        "buttons": [
            {
                extend: 'copy',
                text: 'Copiar',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'csv',
                text: 'CSV',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'excel',
                text: 'Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: 'PDF',
                className: 'btn btn-danger btn-sm',
                orientation: 'landscape',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                text: 'Imprimir',
                className: 'btn btn-info btn-sm'
            }
        ],
        "order": [[3, 'desc']], // Ordenar por fecha de carga descendente
        "columnDefs": [
            {
                "targets": [6, 10], // Columnas de observaciones y firma digital
                "orderable": false
            },
            {
                "targets": [0, 1, 2, 7, 9], // Columnas numéricas/texto corto
                "className": "text-center"
            }
        ]
    });
});
</script>

<?php
// Función auxiliar para asignar colores a los badges según el estado
function getBadgeClass($situacionId) {
    switch($situacionId) {
        case '01': return 'warning'; // Por Generar XML
        case '02': return 'info';    // XML Generado
        case '03': 
        case '11': return 'success'; // Enviado y Aceptado SUNAT
        case '04': 
        case '12': return 'primary'; // Enviado y Aceptado SUNAT con Obs.
        case '05': 
        case '10': return 'danger';  // Rechazado por SUNAT
        case '06': return 'danger';  // Con Errores
        case '07': return 'warning'; // Por Validar XML
        case '08': 
        case '09': return 'secondary'; // Enviado a SUNAT Por Procesar/Procesando
        default: return 'dark';      // Estado desconocido
    }
}
?>

</body>
</html>