//PERSONA
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

//PARIENTE
function buscar_pariente(id) {
	$.ajax({
		type: "POST",
		url: "personas/get_persona",
		dataType: "json",
		data: {id: id, csrf_mlc2: csrfData}
	}).done(function(data) {
		var persona = null;
		if (data['error'] === undefined) {
			persona = data.persona;
			$("#pa_dni").val(persona['dni']);
			$("#pa_cuil").val(persona['cuil']);
			$("#pa_nombre").val(persona['nombre']);
			$("#pa_apellido").val(persona['apellido']);
			$("#pa_sexo").selectpicker('val', persona['sexo']);
			$("#pa_telefono").val(persona['telefono']);
			$("#pa_celular").val(persona['celular']);
			$("#pa_email").val(persona['email']);
			$("#pa_fecha_nacimiento").val(persona['fecha_nacimiento']);
			$("#pa_nacionalidad").selectpicker('val', persona['nacionalidad_id']);
			$("#pa_carga_domicilio").selectpicker('val', persona['carga_domicilio']);
			$("#pa_calle").val(persona['calle']);
			$("#pa_barrio").val(persona['barrio']);
			$("#pa_piso").val(persona['piso']);
			$("#pa_altura").val(persona['altura']);
			$("#pa_dpto").val(persona['dpto']);
			$("#pa_manzana").val(persona['manzana']);
			$("#pa_casa").val(persona['casa']);
			$("#pa_localidad").selectpicker('val', persona['localidad_id']);
		} else {
			limpiar_pariente();
		}
	});
}

function limpiar_pariente() {
	$("#pa_dni").val('');
	$("#pa_cuil").val('');
	$("#pa_nombre").val('');
	$("#pa_apellido").val('');
	$("#pa_sexo").selectpicker('val', null);
	$("#pa_telefono").val('');
	$("#pa_celular").val('');
	$("#pa_email").val('');
	$("#pa_fecha_nacimiento").val('');
	$("#pa_nacionalidad").selectpicker('val', null);
	$("#pa_carga_domicilio").selectpicker('val', 'NO');
	$("#pa_calle").val('');
	$("#pa_barrio").val('');
	$("#pa_piso").val('');
	$("#pa_altura").val('');
	$("#pa_dpto").val('');
	$("#pa_manzana").val('');
	$("#pa_casa").val('');
	$("#pa_localidad").selectpicker('val', null);
}

function domicilio_pariente_row(tipo) {
	if (tipo === 'Agregar') {
		if ($('#pariente').selectpicker('val') === '' || $('#pariente').selectpicker('val') === 'agregar') {
			if ($('#pa_carga_domicilio').selectpicker('val') === 'SI') {
				$('#row-domicilio-pariente :input').attr("disabled", false);
				$("#pa_localidad").selectpicker('refresh');
				$("#row-domicilio-pariente").show();
			} else {
				$('#row-domicilio-pariente :input').attr("disabled", true);
				$("#pa_localidad").selectpicker('refresh');
				$("#row-domicilio-pariente").hide();
			}
		} else {
			$('#row-domicilio-pariente :input').attr("disabled", true);
			$("#pa_localidad").selectpicker('refresh');
			$("#row-domicilio-pariente").show();
		}
	} else if (tipo === 'Editar') {
		if ($('#pa_carga_domicilio').selectpicker('val') === 'SI') {
			$('#row-domicilio-pariente :input').attr("disabled", false);
			$("#pa_localidad").selectpicker('refresh');
			$("#row-domicilio-pariente").show();
		} else {
			$('#row-domicilio-pariente :input').attr("disabled", true);
			$("#pa_localidad").selectpicker('refresh');
			$("#row-domicilio-pariente").hide();
		}
	} else {
		$("#row-domicilio-pariente").show();
	}
}

function insertar_detalle() {
	var cant_rows = parseInt($('#cant_rows').val());
	var new_tr = base_tr.clone().attr('id', 'detalle_' + (cant_rows + 1));
	$('input,select,button', new_tr).each(function() {
		if ($(this).attr('id')) {
			var id = $(this).attr('id');
			var name = $(this).attr('name');
			$(this).attr('id', id.replace(/([0-9]+)/g, (cant_rows + 1)));
			$(this).attr('name', name.replace(/([0-9]+)/g, (cant_rows + 1)));
		}
	});
	$('input', new_tr).each(function() {
		$(this).val('');
	});
	$('.bootstrap-select', new_tr).each(function() {
		$(this).replaceWith(function() {
			return $('select', this);
		});
	});
	$('select', new_tr).each(function() {
		$(this).find('.bs-title-option').remove();
		$(this).selectpicker();
		$(this).val([]).val('default').selectpicker("refresh");
	});
	$('#detalle_' + cant_rows).after(new_tr);
	cant_rows++;
	$('#cant_rows').val(cant_rows);
	aplicar_formatos();
}

function quitar_detalle(btn, id) {
	var cant_rows = parseInt($('#cant_rows').val());
	if (typeof id !== 'undefined' && id !== null) {
		var nro_id = parseInt(id);
	} else {
		var id = $(btn).attr('id');
		var regExp = /([0-9]+)/g;
		var matches = regExp.exec(id);
		var nro_id = parseInt(matches[1]);
	}
	if (cant_rows <= 1) {
		Swal.fire({
			type: 'error',
			title: 'Error.',
			text: 'Debe ingresar al menos un detalle a la Intervención',
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			confirmButtonText: 'Aceptar'
		})
	} else {
		$('#detalle_' + nro_id).remove();
		for (var i = (nro_id + 1); i <= cant_rows; i++) {
			var tr = $('#detalle_' + i);
			$('input,select,button', tr).each(function() {
				if ($(this).attr('id')) {
					var id = $(this).attr('id');
					var name = $(this).attr('name');
					$(this).attr('id', id.replace(/([0-9]+)/g, (i - 1)));
					$(this).attr('name', name.replace(/([0-9]+)/g, (i - 1)));
				}
			});
			tr.attr('id', 'detalle_' + (i - 1));
		}
		cant_rows--;
		$('#cant_rows').val(cant_rows);
	}
}