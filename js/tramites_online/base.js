function buscar_aptitud_urbanistica(combo) {
    var combo_id = combo.id;
    var zona = $("#" + combo_id).val();
    var div_contenedor = $("#" + combo_id).closest('.form-group').parent();
    if (zona.length > 0) {
        $.ajax({
            type: "POST",
            url: "tramites_online/ajax/buscar_aptitud_urbanistica",
            dataType: "json",
            data: {zona: zona, csrf_mlc2: csrfData}
        }).done(function (data) {
            var info = null;
            if (data['error'] === undefined) {
                info = data.info;
                div_contenedor.find("input[extra_param='ordenanza']").val(info.ordenanza_limites);
                div_contenedor.find("input[extra_param='ordenanza_usos']").val(info.ordenanza_usos);
                div_contenedor.find("input[extra_param='sup_min_terreno']").val(info.tamanio_lote);
                div_contenedor.find("input[extra_param='lado_minimo']").val(info.frente_minimo);
                div_contenedor.find("input[extra_param='retiro_frontal']").val(info.retiro_frontal);
                div_contenedor.find("input[extra_param='retiro_lateral']").val(info.retiro_bilateral);
                div_contenedor.find("input[extra_param='retiro_posterior']").val(info.retiro_posterior);
                div_contenedor.find("input[extra_param='fos_maximo']").val(info.fos);
                div_contenedor.find("input[extra_param='altura_maxima']").val(info.h_max);
            } else {
                Swal.fire({
                    type: 'error',
                    title: 'Error.',
                    text: data['error'],
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'Aceptar'
                });
                div_contenedor.find("input[extra_param='ordenanza']").val('');
                div_contenedor.find("input[extra_param='ordenanza_usos']").val('');
                div_contenedor.find("input[extra_param='sup_min_terreno']").val('');
                div_contenedor.find("input[extra_param='lado_minimo']").val('');
                div_contenedor.find("input[extra_param='retiro_frontal']").val('');
                div_contenedor.find("input[extra_param='retiro_lateral']").val('');
                div_contenedor.find("input[extra_param='retiro_posterior']").val('');
                div_contenedor.find("input[extra_param='fos_maximo']").val('');
                div_contenedor.find("input[extra_param='altura_maxima']").val('');
            }
        });
    } else {
        Swal.fire({
            type: 'error',
            title: 'Error.',
            text: 'Revise la zona.',
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-primary',
            confirmButtonText: 'Aceptar'
        });
    }
}
function buscar_inmueble(boton) {
    var regex = /^(.+?)(_extra_button)$/i;
    var match = boton.id.match(regex) || [];
    var nomenclatura = $("#" + match[1]).val();
    if (nomenclatura.length == 20 && (nomenclatura.match(/^[0-9]+$/) !== null)) {
        $.ajax({
            type: "POST",
            url: "tramites_online/ajax/buscar_inmueble",
            dataType: "json",
            data: {nomenclatura: nomenclatura, csrf_mlc2: csrfData}
        }).done(function (data) {
            var inmueble = null;
            if (data['error'] === undefined) {
                inmueble = data.inmueble;
                $("#" + match[1].replace('nomenclatura', 'padron')).val(Number(inmueble.trib_Cuenta));
                $("#" + match[1].replace('nomenclatura', 'tit_dni')).val(Number(inmueble.pers_Numero));
                $("#" + match[1].replace('nomenclatura', 'tit_nombre')).val(inmueble.pers_Nombre);
                $("#" + match[1].replace('nomenclatura', 'tit_apellido')).val(inmueble.pers_Apellido);
                $("#" + match[1].replace('nomenclatura', 'calle')).val(inmueble.fren_Calle + " " + inmueble.fren_Altura);
                $("#" + match[1].replace('nomenclatura', 'distrito')).val(inmueble.fren_Localidad);
                $("#" + match[1].replace('nomenclatura', 'sup_terreno')).val(inmueble.superficies[0].supe_Superficie);
                $("#" + match[1].replace('nomenclatura', 'zona_urb')).val(inmueble.zoni_Descripcion);
                $("#" + match[1].replace('nomenclatura', 'ordenanza')).val('');
                $("#" + match[1].replace('nomenclatura', 'deuda')).val(Number(inmueble.deuda).toFixed(2));
                $("#" + match[1].replace('nomenclatura', 'consulta')).val(inmueble.consulta);
                $("#" + match[1]).focus();
            } else {
                Swal.fire({
                    type: 'error',
                    title: 'Error.',
                    text: data['error'],
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'Aceptar'
                });
                $("#" + match[1].replace('nomenclatura', 'padron')).val('');
                $("#" + match[1].replace('nomenclatura', 'tit_dni')).val('');
                $("#" + match[1].replace('nomenclatura', 'tit_apellido')).val('');
                $("#" + match[1].replace('nomenclatura', 'tit_nombre')).val('');
                $("#" + match[1].replace('nomenclatura', 'calle')).val('');
                $("#" + match[1].replace('nomenclatura', 'distrito')).val('');
                $("#" + match[1].replace('nomenclatura', 'sup_terreno')).val('');
                $("#" + match[1].replace('nomenclatura', 'zona_urb')).val('');
                $("#" + match[1].replace('nomenclatura', 'ordenanza')).val('');
                $("#" + match[1].replace('nomenclatura', 'deuda')).val('');
                $("#" + match[1]).focus();
            }
        });
    } else {
        Swal.fire({
            type: 'error',
            title: 'Error.',
            text: 'Revise la nomenclatura debe contener 20 números.',
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-primary',
            confirmButtonText: 'Aceptar'
        });
        $("#padron").val('');
        $("#nomenclatura").focus();
    }
}

function buscar_procesos_oficina() {
    var oficina_id = $("#oficina").val();
    $("#proceso").find("option").remove();
    $("#proceso").selectpicker("refresh");
    $.ajax({
        type: "POST",
        url: "tramites_online/ajax/buscar_procesos_oficina",
        dataType: "json",
        data: {oficina_id: oficina_id, csrf_mlc2: csrfData}
    }).done(function (data) {
        if (data['no_data'] === undefined) {
            data.procesos.forEach(function (proceso, index) {
                if (proceso.tipo === 'Consulta') {
                    $("#proceso").append('<option data-content="' + proceso.nombre + ' <span class=\'label label-danger\'>' + proceso.tipo + '</span>" value="' + proceso.id + '" selected="">' + proceso.nombre + '</option>');
                } else if (proceso.tipo === 'Trámite') {
                    $("#proceso").append('<option data-content="' + proceso.nombre + ' <span class=\'label label-warning\'>' + proceso.tipo + '</span>" value="' + proceso.id + '" selected="">' + proceso.nombre + '</option>');
                }
            });
        }
        $("#proceso").val('');
        $("#proceso").selectpicker("refresh");
    });
}

function buscar_persona(id) {
    $.ajax({
        type: "POST",
        url: "personas/get_persona",
        dataType: "json",
        data: {id: id, csrf_mlc2: csrfData}
    }).done(function (data) {
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
            limpiar_persona();
        }
    });
}

function limpiar_persona() {
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