<?php
define("ROOT", dirname(__FILE__));

$debug = false;
if ($debug) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

include "core/autoload.php";

date_default_timezone_set("America/Lima");

ob_start();
session_start();
Core::$root = "";

// si quieres que se muestre las consultas SQL debes decomentar la siguiente linea
 //Core::$debug_sql = true;

$lb = new Lb();
$lb->start();

?>

<script type="text/javascript">
	var cuenta = 0;
	function enviado() {
		if (cuenta == 0) {
			cuenta++;
			return true;
		}
		else {
			alert("El formulario ya está siendo enviado, por favor aguarde un instante.");
			return false;
		}
	}

	function validar_dni() {
		valor = document.getElementById("numDocUsuario").value;
		cantidad_digitos = valor.trim().length;

		if (valor != '') {
			if (cantidad_digitos == 8) {
				if (!/^([0-9])*$/.test(valor)) {
					alert('Dni inválido');
					document.getElementById("numDocUsuario").value = "";
				}
				else {
					generar_nombre(valor, 3);
				}
			}
			else {
				alert('Dni inválido');
			}
		}
	}
	function validar_no_dni() {
		valor = document.getElementById("numDocUsuario").value;
		cantidad_digitos = valor.trim().length;

		if (valor != '') {
			if (cantidad_digitos >= 3) {
				if (!/^([0-9])*$/.test(valor)) {
					alert('Dni inválido');
					document.getElementById("numDocUsuario").value = "";
				}
				else {
					generar_nombre(valor, 4);
				}
			}
			else {
				alert('Dni inválido');
			}
		}
	}

	function validar_ruc() {
		valor = document.getElementById("ruc").value;
		cantidad_digitos = valor.trim().length;

		if (valor != '') {
			if (cantidad_digitos == 11) {
				if (!/^([0-9])*$/.test(valor)) {
					alert('RUC inválido');
					document.getElementById("ruc").value = "";
				}
				else {
					generar_nombre(valor, 1);
				}
			}
			else {
				alert('RUC inválido');
			}
		}
	}

	function generar_nombre(numDocUsuario, tipo) {
		$.ajax({
			type: "POST",
			data: {
				"numDocUsuario": numDocUsuario,
				"tipo": tipo
			},
			url: 'generar_nombre_ajax.php',

			success: function (data) {
				var objDato = JSON.parse(data);
				console.log("objDato==>",objDato);
				if (tipo == 1) {					
					$("#comprobante_factura #rznSocialUsuario").val(objDato.name);
					$("#comprobante_factura #codUbigeoCliente").val(objDato.ubigeo);
					$("#comprobante_factura #desDireccionCliente").val(objDato.address1);
				}
				else if (tipo == 3) {
					$("#comprobante_boleta #rznSocialUsuario").val(objDato.name);
					$("#comprobante_boleta #codUbigeoCliente").val(objDato.ubigeo);
					$("#comprobante_boleta #desDireccionCliente").val(objDato.address1);
				}

			},
		});
	}
</script>