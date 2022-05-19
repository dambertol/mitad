function actualizar_info_linea(tipo) {
	var linea_id = $("#linea").val();
	$.ajax({
		url: "telefonia/ajax/get_linea",
		type: "POST",
		dataType: "json",
		data: {linea_id: linea_id, csrf_mlc2: csrfData}
	}).done(function(data) {
		if (data['no_data'] === undefined) {
			switch (tipo) {
				case 1:
					$('#min_internacional').val(data.linea.min_internacional);
					$('#min_nacional').val(data.linea.min_nacional);
					$('#min_interno').val(data.linea.min_interno);
					$('#datos').val(data.linea.datos);
					break;
				case 2:
					$('#equipo').val(data.linea.equipo_id === null ? 'NULL' : data.linea.equipo_id);
					$('#equipo').selectpicker('refresh');
					$('#area').val(data.linea.nombre_area)
					$('#persona').val(data.linea.nombre_personal)
					$('#persona_externa').val(data.linea.persona);
					$('#min_internacional').val(data.linea.min_internacional);
					$('#min_nacional').val(data.linea.min_nacional);
					$('#min_interno').val(data.linea.min_interno);
					$('#datos').val(data.linea.datos);
					break;
				case 3:
					$('#equipo').val(data.linea.modelo + ' - ' + data.linea.imei);
					$('#area').val(data.linea.nombre_area)
					$('#persona').val(data.linea.nombre_personal)
					$('#persona_externa').val(data.linea.persona);
					break;
				case 4:
					$('#equipo_ant').val(data.linea.modelo + ' - ' + data.linea.imei);
					$('#area').val(data.linea.nombre_area)
					$('#persona').val(data.linea.nombre_personal)
					$('#persona_externa').val(data.linea.persona);
					$('#min_internacional').val(data.linea.min_internacional);
					$('#min_nacional').val(data.linea.min_nacional);
					$('#min_interno').val(data.linea.min_interno);
					$('#datos').val(data.linea.datos);
					break;
			}
		} else {
			switch (tipo) {
				case 1:
					$('#min_internacional').val('');
					$('#min_nacional').val('');
					$('#min_interno').val('');
					$('#datos').val('');
					break;
				case 2:
					$('#equipo').val('NULL');
					$('#equipo').selectpicker('refresh');
					$('#area').val('');
					$('#persona').val('');
					$('#persona_externa').val('');
					$('#min_internacional').val('');
					$('#min_nacional').val('');
					$('#min_interno').val('');
					$('#datos').val('');
					break;
				case 3:
					$('#equipo').val('');
					$('#area').val('');
					$('#persona').val('');
					$('#persona_externa').val('');
					break;
				case 4:
					$('#equipo_ant').val('');
					$('#area').val('');
					$('#persona').val('');
					$('#persona_externa').val('');
					$('#min_internacional').val('');
					$('#min_nacional').val('');
					$('#min_interno').val('');
					$('#datos').val('');
					break;
			}
		}
	});
}

function actualizar_info_equipo(tipo) {
	var equipo_id = $("#equipo").val();
	$.ajax({
		url: "telefonia/ajax/get_equipo",
		type: "POST",
		dataType: "json",
		data: {equipo_id: equipo_id, csrf_mlc2: csrfData}
	}).done(function(data) {
		if (data['no_data'] === undefined) {
			switch (tipo) {
				case 2:
					$("#linea").val(data.equipo.linea_id === null ? 'NULL' : data.equipo.linea_id);
					$("#linea").selectpicker('refresh');
					$('#area').val(data.equipo.nombre_area);
					$('#persona').val(data.equipo.nombre_personal);
					$('#persona_externa').val(data.equipo.persona);
					break;
			}
		} else {
			switch (tipo) {
				case 2:
					$("#linea").val('NULL');
					$("#linea").selectpicker('refresh');
					$('#area').val('');
					$('#persona').val('');
					$('#persona_externa').val('');
					break;
			}
		}
	});
}
