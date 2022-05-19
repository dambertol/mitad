function buscar_categoria_sector() {
    var sector_id = $("#sector").val();
    $('#categoria').find('option').remove();
    $("#categoria").selectpicker("refresh");
    $.ajax({
        type: "POST",
        url: "incidencias/ajax/buscar_categoria_sector",
        dataType: "json",
        data: {sector_id: sector_id, csrf_mlc2: csrfData}
    }).done(function (data) {
        if (data['no_data'] === undefined) {
            data.categorias.forEach(function (categoria) {
                $("#categoria").append('<option value="' + categoria.id + '" selected="">' + categoria.descripcion + '</option>');
            });
        } else {
            $("#categoria").val('');
        }
        $("#categoria").selectpicker("refresh");
    });
}

function buscar_tecnico_categoria() {
    var categoria_id = $("#categoria").val();
    var tecnico_act = $("#tecnico").val();
    $('#tecnico').find('option').remove();
    $("#tecnico").selectpicker("refresh");
    $.ajax({
        type: "POST",
        url: "incidencias/ajax/buscar_tecnico_categoria",
        dataType: "json",
        data: {categoria_id: categoria_id, incidencia_id: incidencia_id, csrf_mlc2: csrfData}
    }).done(function (data) {
        $("#tecnico").append('<option value="" selected="selected">-- Sin Técnico Asignado --</option>');
        if (data['no_data'] === undefined) {
            data.tecnicos.forEach(function (tecnico, index) {
                $("#tecnico").append('<option value="' + tecnico.id + '">' + tecnico.usuario + '</option>');
            });
            if (tecnico_act !== '') {
                $("#tecnico").val(tecnico_act);
            }
        }
        $("#tecnico").selectpicker("refresh");
    });
}