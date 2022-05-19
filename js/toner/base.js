function limpiar_pedido() {
	var cant_rows = parseInt($('#cant_rows').val());
	for (var i = cant_rows; i > 1; i--) {
		quitar_detalle(null, i);
	}
	buscar_impresora(area_id, 1);
}

function buscar_impresora(area_id, cant_rows) {
	$.ajax({
		type: "POST",
		url: "toner/impresoras_areas/get_impresoras",
		dataType: "json",
		data: {area_id: area_id, csrf_mlc2: csrfData}
	}).done(function(data) {
		var impresoras = null;
		if (data['error'] === undefined) {
			impresoras = data.impresoras;
			$("#impresora_" + cant_rows).empty();
			$("#impresora_" + cant_rows).append($('<option>').text('-- Seleccionar --').attr('value', -1));
			$.each(impresoras, function(key, impresora) {
				$("#impresora_" + cant_rows).append($('<option>').text(impresora.nombre).attr('value', impresora.id.toString()));
			});
			$("#impresora_" + cant_rows).selectpicker('refresh');
			$("#consumible_" + cant_rows).empty();
			$("#consumible_" + cant_rows).selectpicker('refresh');
			$("#oc_" + cant_rows).val('');
		} else {
			$("#impresora_" + cant_rows).empty();
			$("#impresora_" + cant_rows).selectpicker('refresh');
			$("#consumible_" + cant_rows).empty();
			$("#consumible_" + cant_rows).selectpicker('refresh');
			$("#oc_" + cant_rows).val('');
		}
	});
}

function buscar_consumible(impresora_id, cant_rows) {
	$.ajax({
		type: "POST",
		url: "toner/consumibles_impresoras/get_consumibles",
		dataType: "json",
		data: {impresora_id: impresora_id, csrf_mlc2: csrfData}
	}).done(function(data) {
		var consumibles = null;
		if (data['error'] === undefined) {
			consumibles = data.consumibles;
			$("#consumible_" + cant_rows).empty();
			$("#consumible_" + cant_rows).append($('<option>').text('-- Seleccionar --').attr('value', -1));
			$.each(consumibles, function(key, consumible) {
				$("#consumible_" + cant_rows).append($('<option>').text(consumible.nombre).attr('value', consumible.id.toString()));
			});
			$("#consumible_" + cant_rows).selectpicker('refresh');
		} else {
			$("#consumible_" + cant_rows).empty();
			$("#consumible_" + cant_rows).selectpicker('refresh');
		}
	});
}

function insertar_detalle(filtra_area) {
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
	if (filtra_area) {
		buscar_impresora(area_id, cant_rows);
	}
	$('#impresora_' + cant_rows).on('changed.bs.select', function(e) {
		buscar_consumible(this.value, cant_rows);
	});
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
			text: 'Debe ingresar al menos un detalle',
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