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
