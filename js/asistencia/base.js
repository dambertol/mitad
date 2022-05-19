function buscar_empleado_usuario(legajo) {
	if (legajo.length > 6 && (legajo.match(/^[0-9]+$/) !== null)) {
		$.ajax({
			type: "POST",
			url: "asistencia/personal_major/buscar",
			dataType: "json",
			data: {legajo: legajo, csrf_mlc2: csrfData}
		}).done(function(data) {
			var empleado = null;
			if (data['error'] === undefined) {
				empleado = data.empleado;
				$("#nombre").val(empleado['pers_Nombre']);
				$("#apellido").val(empleado['pers_Apellido']);
				if (data.usuario === 'Usuario General') {
					$("#email").val('');
					$('#email').prop('readonly', true);
					$("#password").val('********');
					$('#password').prop('readonly', true);
					$("#password_confirm").val('********');
					$('#password_confirm').prop('readonly', true);
					$('#aviso-usuario-modalFooter').html('<button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Aceptar</button>');
					$('#aviso-usuario-modalBody').html('<p style="color:#E74C3C; font-weight:bold;">El usuario existe en el sistema con permisos en otros Módulos.</p><p>Solo podrá agregar el grupo deseado para el Módulo Asistencia. En caso de querer modificar datos (email, contraseña, etc.) deberá contactarse con el administrador.</p><p>Muchas gracias!</p>');
					$('#aviso-usuario-modal').modal('show');
				} else if (data.usuario === 'Usuario Asistencia') {
					$('#aviso-usuario-modalFooter').html('<button type="button" class="btn btn-sm btn-primary" onclick="javascript:location.href = CI.base_url + \'asistencia/usuarios/listar\'">Ir a Usuarios</button>');
					$('#aviso-usuario-modalBody').html('<p style="color:#E74C3C; font-weight:bold;">El usuario ya existe en el Módulo Asistencia.</p><p>En caso de querer modificar datos (email, contraseña, etc.) deberá acceder desde la opción editar del usuario.</p><p>Muchas gracias!</p>');
					$('#aviso-usuario-modal').modal('show');
				} else {
					$('#email').prop('readonly', false);
					$("#password").val('');
					$('#password').prop('readonly', false);
					$("#password_confirm").val('');
					$('#password_confirm').prop('readonly', false);
					$("#email").focus();
				}
			} else {
				$("#nombre").val('');
				$("#apellido").val('');
				$("#email").val('');
				$('#email').prop('readonly', false);
				$("#password").val('');
				$('#password').prop('readonly', false);
				$("#password_confirm").val('');
				$('#password_confirm').prop('readonly', false);
				$("#username").focus();
			}
		});
	} else
	{
		$("#nombre").val('');
		$("#apellido").val('');
		$("#email").val('');
		$('#email').prop('readonly', false);
		$("#password").val('');
		$('#password').prop('readonly', false);
		$("#password_confirm").val('');
		$('#password_confirm').prop('readonly', false);
		$("#username").focus();
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

function actualizar_oficinas() {
	var secretaria = $('#secretaria option:selected').val();
	$('#bg-loader-ajax').show();
	$.ajax({
		url: "asistencia/personal_major/get_oficinas_secretaria",
		type: "POST",
		dataType: "json",
		data: {secretaria: secretaria, csrf_mlc2: csrfData},
		success: function(data) {
			if (data !== "error") {
				var select = $('#oficina');
				var options;
				if (select.prop) {
					options = select.prop('options');
				} else {
					options = select.attr('options');
				}
				$('#oficina').find('option').remove();
				$.each(data, function(val, text) {
					options[options.length] = new Option(text, val);
				});
				$('#oficina').selectpicker('refresh');
			}
		},
		complete: function() {
			$('#bg-loader-ajax').hide();
		}
	});
}