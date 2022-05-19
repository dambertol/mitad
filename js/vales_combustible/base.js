function get_datos_remitos() {
	var remito = $('#remito option:selected').val();
	if (remito !== 'NULL') {
		$.ajax({
			url: "vales_combustible/remitos/get_datos_remito",
			type: "POST",
			dataType: "json",
			data: {remito: remito, csrf_mlc2: csrfData}
		}).done(function(data) {
			if (data !== "error") {
				$('#persona').val(data.persona);
				$('#patente').val(data.patente_maquinaria);
			}
		});
	} else {
		$('#persona').val('');
		$('#patente').val('');
	}
}

function get_facturas_tipo() {
	var tipo = $('#tipo_combustible').val();
	if (tipo !== undefined && tipo !== null) {
		$.ajax({
			url: "vales_combustible/facturas/get_facturas_tipo",
			type: "POST",
			dataType: "json",
			data: {tipo: tipo, csrf_mlc2: csrfData}
		}).done(function(data) {
			var select = $("#factura");
			$('option', select).remove();
			select.append(new Option('-- Sin Factura --', 'NULL'));
			if (data !== "error") {
				$.each(data, function() {
					select.append(new Option(this.factura, this.id));
				});
			}
			select.selectpicker('refresh');
		});
	} else {
		var select = $("#factura");
		$('option', select).remove();
		select.append(new Option('-- Sin Factura --', 'NULL'));
		select.selectpicker('refresh');
	}
}

function get_ordenes_tipo() {
	var tipo = $('#tipo_combustible').val();
	if (tipo !== undefined && tipo !== null) {
		$.ajax({
			url: "vales_combustible/ordenes_compra/get_ordenes_tipo",
			type: "POST",
			dataType: "json",
			data: {tipo: tipo, csrf_mlc2: csrfData}
		}).done(function(data) {
			var select = $("#orden_compra");
			$('option', select).remove();
			select.append(new Option('-- Sin Orden de Compra --', 'NULL'));
			if (data !== "error") {
				$.each(data, function() {
					select.append(new Option(this.orden, this.id));
				});
			}
			select.selectpicker('refresh');
		});
	} else {
		var select = $("#orden_compra");
		$('option', select).remove();
		select.append(new Option('-- Sin Orden de Compra --', 'NULL'));
		select.selectpicker('refresh');
	}
}

function imprimir_vales() {
	var desde = $('#desde').val();
	if (!desde) {
		Swal.fire({
			type: 'error',
			title: 'Error.',
			text: 'Debe ingresar desde',
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			confirmButtonText: 'Aceptar'
		});
	}
	var hasta = $('#hasta').val();
	if (!hasta) {
		Swal.fire({
			type: 'error',
			title: 'Error.',
			text: 'Debe ingresar hasta',
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			confirmButtonText: 'Aceptar'
		});
	}

	if (desde && hasta)
	{
		$.ajax({
			url: "vales_combustible/vales/marcar_impresos",
			type: "POST",
			dataType: "json",
			data: {desde: desde, hasta: hasta, area: $('#area').val(), csrf_mlc2: csrfData}
		}).done(function(data) {
			$('#div-imprimir').printThis({
				base: CI.base_url,
			});
		});
	}
}

function imprimir_vales_pdf() {
	var desde = $('#desde').val();
	if (!desde) {
		Swal.fire({
			type: 'error',
			title: 'Error.',
			text: 'Debe ingresar desde',
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			confirmButtonText: 'Aceptar'
		});
	}
	var hasta = $('#hasta').val();
	if (!hasta) {
		Swal.fire({
			type: 'error',
			title: 'Error.',
			text: 'Debe ingresar hasta',
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			confirmButtonText: 'Aceptar'
		});
	}

	if (desde && hasta)
	{
		$.ajax({
			url: "vales_combustible/vales/marcar_impresos",
			type: "POST",
			dataType: "json",
			data: {desde: desde, hasta: hasta, area: $('#area').val(), csrf_mlc2: csrfData}
		}).done(function(data) {
			window.location.href = CI.base_url + 'vales_combustible/vales/imprimir_pdf/Si/' + desde + '/' + hasta + "/" + $('#area').val();
		});
	}
}

function recargar_vales() {
	var desde = $('#desde').val();
	if (!desde) {
		Swal.fire({
			type: 'error',
			title: 'Error.',
			text: 'Debe ingresar desde',
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			confirmButtonText: 'Aceptar'
		});
	}
	var hasta = $('#hasta').val();
	if (!hasta) {
		Swal.fire({
			type: 'error',
			title: 'Error.',
			text: 'Debe ingresar hasta',
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			confirmButtonText: 'Aceptar'
		});
	}

	if (desde && hasta)
	{
		window.location.replace(CI.base_url + "vales_combustible/vales/imprimir/" + $('#reimprimir').val() + "/" + desde + "/" + $('#hasta').val() + "/" + $('#area').val());
	}
}

function buscar_persona(call_idx) {
	var dni = $("#persona").val();
	if (dni.length > 6 && (dni.match(/^[0-9]+$/) !== null)) {
		$.ajax({
			type: "POST",
			url: "vales_combustible/ajax/buscar_persona",
			dataType: "json",
			data: {dni: dni, call: call_idx, csrf_mlc2: csrfData}
		}).done(function(data) {
			var persona = null;
			if (data['call'] == call) {	// NO CAMBIAR
				if (data['no_data'] === undefined) {
					persona = data.persona;
					$("#persona_major").val(persona['Apellido'] + ', ' + persona['Nombre']);
				} else {
					$("#persona_major").val('-- NO ENCONTRADO --');
				}
			}
		});
	} else {
		$("#persona_major").val('-- NO ENCONTRADO --');
	}
}

function buscar_costo(call_idx) {
	var tipo = $("#tipo_combustible").val();
	var litros = $("#litros").val();
	var fecha = $("#fecha").val();
	if (litros && fecha) {
		$.ajax({
			type: "POST",
			url: "vales_combustible/valores_combustible/buscar_costo",
			dataType: "json",
			data: {tipo: tipo, litros: litros, fecha: fecha, call: call_idx, csrf_mlc2: csrfData}
		}).done(function(data) {
			if (data['call'] == call_costo) {	// NO CAMBIAR
				if (data['no_data'] === undefined) {
					$('#costo').prop('readonly', true);
					$("#costo").val(data.valor_combustible);
				} else {
					$('#costo').prop('readonly', false);
					$("#costo").val('');
				}
			}
		});
	} else {
		$('#costo').prop('readonly', false);
		$("#costo").val('');
	}
}

function buscar_combustible_vehiculo(select) {
	var vehiculo_id = $("#vehiculo").val();
	if (!select) {
		$('#tipo_combustible').find('option').remove();
		$("#tipo_combustible").selectpicker("refresh");
	}
	$.ajax({
		type: "POST",
		url: "vales_combustible/ajax/buscar_combustible_vehiculo",
		dataType: "json",
		data: {vehiculo_id: vehiculo_id, csrf_mlc2: csrfData}
	}).done(function(data) {
		if (data['no_data'] === undefined) {
			if (select) {
				$("#tipo_combustible").val(data.tipo_combustible_id);
			} else {
				data.combustible_vehiculo.forEach(function(combustible, index) {
					$("#tipo_combustible").append('<option value="' + combustible.tipo_combustible_id + '" selected="">' + combustible.tipo_combustible + '</option>');
				});
			}
		} else {
			$("#tipo_combustible").val('');
		}
		$("#tipo_combustible").selectpicker("refresh");
	});
}

function insertarDetalle() {
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
	$('select', new_tr).each(function() {
		$(this).selectpicker();
	});
	$('#detalle_' + cant_rows).after(new_tr);
	cant_rows++;
	$('#cant_rows').val(cant_rows);
	aplicar_formatos();
	$('.costo_total_calculo').off('keyup');
	$('.costo_total_calculo').on('keyup', function() {
		calcularTotalDetalle(this);
	});
}

function quitarDetalle(btn) {
	var cant_rows = parseInt($('#cant_rows').val());
	var id = $(btn).attr('id');
	var regExp = /([0-9]+)/g;
	var matches = regExp.exec(id);
	var nro_id = parseInt(matches[1]);
	if (cant_rows <= 1) {
		Swal.fire({
			type: 'error',
			title: 'Error.',
			text: 'Debe ingresar al menos un detalle a la Orden de Compra',
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

function calcularTotalDetalle(input) {
	var id = $(input).attr('id');
	var regExp = /([0-9]+)/g;
	var matches = regExp.exec(id);
	var nro_id = parseInt(matches[1]);
	var litros = parseFloat($('#litros_' + nro_id).val());
	var costo_unitario = parseFloat($('#costo_unitario_' + nro_id).val());
	var costo_total = ((isNaN(litros) ? 0 : litros) * (isNaN(costo_unitario) ? 0 : costo_unitario)).toFixed(2);
	$('#costo_total_' + nro_id).val(costo_total);

	var cant_rows = parseInt($('#cant_rows').val());
	var total = 0;
	for (var i = 1; i <= cant_rows; i++) {
		total += $('#costo_total_' + i).val();
	}
	$('#total').val(total);
}
