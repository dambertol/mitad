function buscar_legajo(legajo) {
	if (legajo.length > 6 && (legajo.match(/^[0-9]+$/) !== null)) {
		$.ajax({
			type: "POST",
			url: "recursos_humanos/legajos/buscar",
			dataType: "json",
			data: {legajo: legajo, csrf_mlc2: csrfData}
		}).done(function(data) {
			var empleado = null;
			if (data['error'] === undefined) {
				empleado = data.empleado;
				$("#nombre").val(empleado['pers_Nombre']);
				$("#apellido").val(empleado['pers_Apellido']);
				$("#nombre").focus();
			} else {
				$("#nombre").val('');
				$("#apellido").val('');
				Swal.fire({
					type: 'error',
					title: 'Error.',
					text: data['error'],
					buttonsStyling: false,
					confirmButtonClass: 'btn btn-primary',
					confirmButtonText: 'Aceptar'
				});
				$("#nombre").focus();
			}
		});
	} else
	{
		Swal.fire({
			type: 'error',
			title: 'Error.',
			text: 'Debe ingresar un número válido.',
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			confirmButtonText: 'Aceptar'
		});
	}
}