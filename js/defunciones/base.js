function cambiarTipoUbicacion() {
    switch ($('#tipo').val()) {
        case 'Nicho':
        case 'Nicho Urna':
            mostrarTr('sector');
            ocultarTr('cuadro');
            mostrarTr('fila');
            renombrarTr('nicho', 'Nicho');
            mostrarTr('nicho');
            ocultarTr('denominacion');
            break;
        case 'Tierra':
            mostrarTr('sector');
            mostrarTr('cuadro');
            mostrarTr('fila');
            renombrarTr('nicho', 'Parcela');
            mostrarTr('nicho');
            ocultarTr('denominacion');
            break;
        case 'Mausoleo':
        case 'Pileta':
            mostrarTr('sector');
            mostrarTr('cuadro');
            mostrarTr('fila');
            renombrarTr('nicho', 'Parcela');
            mostrarTr('nicho');
            mostrarTr('denominacion');
            break;
    }
}
function cambiarTipoConcesion() {
    switch ($('#tipo_concesion').val()) {
        case 'Alquiler':
            mostrarTr('fin');
            $('#fin').attr('required')
            break;
        case 'Perpetua':
            ocultarTr('fin');
            $('#fin').removeAttr('required')
            break;
    }
}
function ocultarTr(id) {
    $('#' + id).parent().parent().hide();
    $('#' + id).val('');
}
function mostrarTr(id) {
    $('#' + id).parent().parent().show();
}
function renombrarTr(id, label) {
    $('#' + id).parent().parent().find('label').html(label);
}