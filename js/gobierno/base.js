function buscar_parte(id) {
	$.ajax({
		type: "POST",
		url: "gobierno/partes/get_parte",
		dataType: "json",
		data: {id: id, csrf_mlc2: csrfData}
	}).done(function(data) {
		var parte = null;
		if (data['error'] === undefined) {
			parte = data.parte;
			$("#nombre_parte").val(parte['nombre']);
			$("#persona").selectpicker('val', parte['persona_id']);
			buscar_persona(parte['persona_id']);
		} else {
			limpiar_parte();
		}
	});
}

function limpiar_parte() {
	//PARTE
	$("#nombre_parte").val('');
	$("#persona").selectpicker('val', null);

	//PERSONA
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

	//ROWS
	$('#row-persona :input').attr("disabled", true);
	$('#row-domicilio :input').attr("disabled", true);
	$("#row-persona").hide();
	$("#row-domicilio").hide();
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
			if (persona['carga_domicilio'] === 'SI') {
				$('#row-domicilio :input').attr("disabled", true);
				$("#row-domicilio").show();
			} else {
				$('#row-domicilio :input').attr("disabled", true);
				$("#row-domicilio").hide();
			}
			$('#row-persona :input').attr("disabled", true);
			$("#row-persona").show();
		} else {
			limpiar_persona();
			$('#row-persona :input').attr("disabled", true);
			$("#row-persona").hide();
		}
	});
}

function limpiar_persona() {
	//PERSONA
	$("#dni").val('');
	$("#cuil").val('');
	$("#nombre").val('');
	$("#apellido").val('');
	$("#sexo").selectpicker('val', null);
	$("#sexo").selectpicker('refresh');
	$("#telefono").val('');
	$("#celular").val('');
	$("#email").val('');
	$("#fecha_nacimiento").val('');
	$("#nacionalidad").selectpicker('val', null);
	$("#nacionalidad").selectpicker('refresh');
	$("#carga_domicilio").selectpicker('val', 'NO');
	$("#calle").val('');
	$("#barrio").val('');
	$("#piso").val('');
	$("#altura").val('');
	$("#dpto").val('');
	$("#manzana").val('');
	$("#casa").val('');
	$("#localidad").selectpicker('val', null);

	//ROWS
	$('#row-domicilio :input').attr("disabled", true);
	$("#row-domicilio").hide();
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
			if ($('#carga_domicilio').selectpicker('val') === 'SI') {
				$('#row-domicilio :input').attr("disabled", true);
				$("#localidad").selectpicker('refresh');
				$("#row-domicilio").show();
			} else {
				$('#row-domicilio :input').attr("disabled", true);
				$("#localidad").selectpicker('refresh');
				$("#row-domicilio").hide();
			}
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
