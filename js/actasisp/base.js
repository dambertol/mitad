function buscar_datos_padron(padron) {
	if (padron.match(/^[0-9]+$/) !== null) {
		$.ajax({
			type: "POST",
			url: "actasisp/actas/buscar_padron",
			dataType: "json",
			data: {padron: padron, csrf_mlc2: csrfData}
		}).done(function(data) {
			var padron = null;
			if (data['error'] === undefined) {
				padron = data.padron;
				$("#calle").val(padron['fren_Calle']);
				$("#altura").val(padron['fren_Altura']);
				$("#piso").val(padron['fren_Piso']);
				$("#dpto").val(padron['fren_Depto']);
				$("#manzana").val(padron['fren_Manzana']);
				$("#casa").val(padron['fren_Lote']);
				$("#localidad").selectpicker('val', padron['localidad_id']);
				$("#inspector").focus();
			} else {
				$("#calle").val('');
				$("#altura").val('');
				$("#piso").val('');
				$("#dpto").val('');
				$("#manzana").val('');
				$("#casa").val('');
				$("#localidad").selectpicker('val', null);
				$("#calle").focus();
			}
		});
	} else
	{
		$("#calle").val('');
		$("#altura").val('');
		$("#piso").val('');
		$("#dpto").val('');
		$("#manzana").val('');
		$("#casa").val('');
		$("#localidad").selectpicker('val', null);
		$("#calle").focus();
	}
}

function buscar_persona(id) {
	$.ajax({
		type: "POST",
		url: "personas/get_persona",
		dataType: "json",
		data: {id: id, csrf_mlc2: csrfData}
	}).done(function(data) {
		var persona = null;
		if (data['error'] === undefined) {
			persona = data.persona;
			$("#dni").val(persona['dni']);
			$("#cuil").val(persona['cuil']);
			$("#nombre").val(persona['nombre']);
			$("#apellido").val(persona['apellido']);
			$("#sexo").selectpicker('val', persona['sexo']);
			$("#telefono").val(persona['telefono']);
			$("#celular").val(persona['celular']);
			$("#email").val(persona['email']);
			$("#fecha_nacimiento").val(persona['fecha_nacimiento']);
			$("#nacionalidad").selectpicker('val', persona['nacionalidad_id']);
			$("#carga_domicilio").selectpicker('val', persona['carga_domicilio']);
			$("#calle").val(persona['calle']);
			$("#barrio").val(persona['barrio']);
			$("#piso").val(persona['piso']);
			$("#altura").val(persona['altura']);
			$("#dpto").val(persona['dpto']);
			$("#manzana").val(persona['manzana']);
			$("#casa").val(persona['casa']);
			$("#localidad").selectpicker('val', persona['localidad_id']);
		} else {
			limpiar_persona();
		}
	});
}

function limpiar_persona() {
	$("#dni").val('');
	$("#cuil").val('');
	$("#nombre").val('');
	$("#apellido").val('');
	$("#sexo").selectpicker('val', null);
	$("#telefono").val('');
	$("#celular").val('');
	$("#email").val('');
	$("#fecha_nacimiento").val('');
	$("#nacionalidad").selectpicker('val', null);
	$("#carga_domicilio").selectpicker('val', 'NO');
	$("#calle").val('');
	$("#barrio").val('');
	$("#piso").val('');
	$("#altura").val('');
	$("#dpto").val('');
	$("#manzana").val('');
	$("#casa").val('');
	$("#localidad").selectpicker('val', null);
}

function domicilio_row(tipo) {
	if (tipo === 'Agregar') {
		if ($('#persona').selectpicker('val') === '' || $('#persona').selectpicker('val') === 'agregar') {
			if ($('#carga_domicilio').selectpicker('val') === 'SI') {
				$('#row-domicilio :input').attr("disabled", false);
				$("#localidad").selectpicker('refresh');
				$("#row-domicilio").show();
			} else {
				$('#row-domicilio :input').attr("disabled", true);
				$("#localidad").selectpicker('refresh');
				$("#row-domicilio").hide();
			}
		} else {
			$('#row-domicilio :input').attr("disabled", true);
			$("#localidad").selectpicker('refresh');
			$("#row-domicilio").show();
		}
	} else if (tipo === 'Editar') {
		if ($('#carga_domicilio').selectpicker('val') === 'SI') {
			$('#row-domicilio :input').attr("disabled", false);
			$("#localidad").selectpicker('refresh');
			$("#row-domicilio").show();
		} else {
			$('#row-domicilio :input').attr("disabled", true);
			$("#localidad").selectpicker('refresh');
			$("#row-domicilio").hide();
		}
	} else {
		$("#row-domicilio").show();
	}
}
