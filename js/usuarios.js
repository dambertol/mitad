function buscar_empleado_usuario(legajo) {
	if (legajo.length > 6 && (legajo.match(/^[0-9]+$/) !== null)) {
		$.ajax({
			type: "POST",
			url: "usuarios/buscar",
			dataType: "json",
			data: {legajo: legajo, csrf_mlc2: csrfData}
		}).done(function(data) {
			var empleado = null;
			if (data['error'] === undefined) {
				empleado = data.empleado;
				$("#nombre").val(empleado['pers_Nombre']);
				$("#apellido").val(empleado['pers_Apellido']);
				$("#email").focus();
			} else {
				$("#nombre").val('');
				$("#apellido").val('');
				$("#dni").focus();
			}
		});
	} else
	{
		$("#nombre").val('');
		$("#apellido").val('');
		$("#dni").focus();
	}
}

function buscar_persona_usuario(id) {
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
			$("#sexo").selectpicker('val', persona['sexo']);
			$("#cuil").val(persona['cuil']);
			$("#nombre").val(persona['nombre']);
			$("#apellido").val(persona['apellido']);
			$("#telefono").val(persona['telefono']);
			$("#celular").val(persona['celular']);
			$("#email").val(persona['email']);
			$("#fecha_nacimiento").val(persona['fecha_nacimiento']);
			$("#nacionalidad").selectpicker('val', persona['nacionalidad_id']);
		} else {
			limpiar_persona_usuario();
		}
	});
}

function limpiar_persona_usuario() {
	$("#dni").val('');
	$("#sexo").selectpicker('val', null);
	$("#cuil").val('');
	$("#nombre").val('');
	$("#apellido").val('');
	$("#telefono").val('');
	$("#celular").val('');
	$("#email").val('');
	$("#fecha_nacimiento").val('');
	$("#nacionalidad").selectpicker('val', null);
}