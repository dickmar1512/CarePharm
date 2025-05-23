
	$("#buscar").click(function () {
		var numDoc = $("#num_comprobante_modificado").val();
		var tipo = $("#notacredito_motivo_id").val();
		var serie = $("#serie_comprobante").val();
		var comp = $("#numero_comprobante").val();
		var motivo = $("#motivo").val();
		//console.log(tipo);
		if(tipo=="01" || tipo=="02" || tipo=="06"){
			// window.location.href = "index.php?view=onesell2&id="+3;
			$.ajax({
				    type : "POST",
				    data: {
				    		"numDoc": numDoc,
				    		"motivo": motivo,
				    		"serie": serie,
				    		"comp": comp,
				    		"tipo":tipo
				    	},
				    url: './?view=addnotacredito',

			    	success : function(data){
			    			if(data)	
			    				window.location.href = "./?view=notacreditot&num="+serie+'-'+comp;
			    	},
			});
		}

		else if(tipo=='03' || tipo=='05' || tipo=='07'){

			$.ajax({
				    type : "POST",
				    data: {
				    		"numDoc": numDoc,
				    		"tipo": tipo
				    	},
				    url: 'obtener_datos_factura_ajax.php',

			    	success : function(data){
			    			$("#datos").html('');
		            		$("#datos").html(data);   		
			    	},
			});
		}

		else if(tipo=='04'){
			var dscto=$("#dscto").val();

			$.ajax({
				    type : "POST",
				    data: {
				    		"numDoc": numDoc,
				    		"motivo": motivo,
				    		"serie": serie,
				    		"comp": comp,
				    		"tipo": tipo,
				    		"dscto": dscto
				    	},
				    url: './?view=addnotacredito',

			    	success : function(data){
			    			window.location.href = "./?view=notacredito&num="+serie+'-'+comp;		
			    	},
			});
		}

	});

	$( "#notacredito_motivo_id" ).change(function() {
	  	if($( "#notacredito_motivo_id" ).val()=='04'){
	  		$( "#dscto_global" ).attr('style','display:block');
	  	}

	  	else{
	  		$( "#dscto_global" ).attr('style','display:none');
	  	}
	});