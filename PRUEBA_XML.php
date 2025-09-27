<?php
$xmlPath = '../efact1.3.4/sunat_archivos/sfs/FIRMA/10432470330-03-B001-00000003.xml';

// Verifica si el archivo existe
if (!file_exists($xmlPath)) {
    die("Archivo XML no encontrado: $xmlPath");
}

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

// Ejemplos de extracción de datos
$ruc = $xml->xpath('//cac:PartyIdentification//cbc:ID')[0] ?? '';
$razonSocial = $xml->xpath('//cac:AccountingSupplierParty//cac:PartyLegalEntity//cbc:RegistrationName')[0] ?? '';
$numeroFactura = $xml->xpath('//cbc:ID')[0] ?? '';
$fechaEmision = $xml->xpath('//cbc:IssueDate')[0] ?? '';
$montoTotal = $xml->xpath('//cbc:PayableAmount')[0] ?? '';
$hash = $xml->xpath('//ds:DigestValue')[0] ?? '';

// Mostrar resultados
echo "<h3>Datos del Comprobante</h3>";
echo "<ul>";
echo "<li><strong>RUC:</strong> " . htmlspecialchars((string)$ruc) . "</li>";
echo "<li><strong>Razón Social:</strong> " . htmlspecialchars((string)$razonSocial) . "</li>";
echo "<li><strong>Número:</strong> " . htmlspecialchars((string)$numeroFactura) . "</li>";
echo "<li><strong>Fecha de Emisión:</strong> " . htmlspecialchars((string)$fechaEmision) . "</li>";
echo "<li><strong>Monto Total:</strong> " . htmlspecialchars((string)$montoTotal) . "</li>";
echo "<li><strong>Hash:</strong> " . htmlspecialchars((string)$hash) . "</li>";
echo "</ul>";
?>
