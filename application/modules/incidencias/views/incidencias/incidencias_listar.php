<!--
    /*
     * Vista listado de Incidencias.
     * Autor: Leandro
     * Creado: 12/04/2019
     * Modificado: 14/04/2021 (Leandro)
     */
-->
<script>
    var incidencias_table;
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
    function complete_incidencias_table() {
        $('#incidencias_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#incidencias_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#incidencias_table thead th').eq(i).text();
            var indice = $('#incidencias_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 1 || indice === 8) { // Inicio || Finalización
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(incidencias_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else if (indice === 7) { // Estado
                    $(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_estados)); ?>);
                    $(this).find('select').val(incidencias_table.column(i).search());
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + incidencias_table.column(i).search() + '"/>');
                }
            }
        });
        $('#incidencias_table tfoot th').eq(9).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'incidencias_table\');" title="Limpiar filtros"><i class="fa fa-eraser"></i></button>');
        $('.dateFilter').each(function(index, element) {
            $(element).datetimepicker({
                locale: 'es',
                format: 'L',
                useCurrent: false,
                showClear: true,
                showTodayButton: true,
                showClose: true
            });
        });
        incidencias_table.columns().every(function() {
            var column = this;
            if (this[0][0] === 1 || this[0][0] === 8) { // Inicio || Finalización
                $("#dateFilter" + this[0][0]).on("dp.change", function(e) {
                    if (e.date) {
                        var sql_date = moment(e.date._d).format('YYYY-MM-DD');
                    } else {
                        var sql_date = '';
                    }
                    if (column.search() !== sql_date) {
                        column.search(sql_date).draw();
                    }
                });
            } else if (this[0][0] === 7) { // Estado
                $('input,select', incidencias_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
                    if (column.search() !== this.value) {
                        column.search(this.value, 'exact').draw();
                    }
                });
            } else {
                $('input,select', incidencias_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#incidencias_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#incidencias_table thead').append(r);
    }
    function finalizar_incidencia(incidencia_id) {
        Swal.fire({
            title: 'Confirmar',
            text: "Se finalizará la incidencia",
            type: 'info',
            showCloseButton: true,
            showCancelButton: true,
            focusCancel: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-primary',
            cancelButtonClass: 'btn btn-default',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                window.location.href = CI.base_url + 'incidencias/incidencias/finalizar/' + incidencia_id;
            }
        });
    }
    function duplicar_incidencia(incidencia_id) {
        const url = CI.base_url + 'incidencias/incidencias/repetir';
        Swal.fire({
            title: 'Confirmar',
            type: 'info',
            html: 'Se duplicará la incidencia seleccionada<br><br>\n\
                                                <div class="form-horizontal">\n\
                                                        <div class="row">\n\
                                                                <div class="form-group">\n\
                                                                        <label for="fecha_dup" class="col-sm-4 control-label">Fecha Inicio *</label> \n\
                                                                        <div class="col-sm-8">\n\
                                                                                <input type="text" name="fecha_dup" value="<?php echo (new DateTime())->format('d/m/Y H:i'); ?>" id="fecha_dup" class="form-control dateTimeFormat" required="" autocomplete="off">\n\
                                                                        </div>\n\
                                                                </div>\n\
                                                        </div>\n\
                                                </div>',
            onOpen: function() {
                $('#fecha_dup').datetimepicker({
                    locale: 'es',
                    useCurrent: false,
                    showClear: true,
                    showTodayButton: true,
                    showClose: true
                });
                $('#fecha_dup').attr("autocomplete", "off");
            },
            showCloseButton: true,
            showCancelButton: true,
            focusCancel: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-primary',
            cancelButtonClass: 'btn btn-default',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            customClass: 'sweetalert-lg',
            showLoaderOnConfirm: true,
            preConfirm: function() {
                return new Promise(function(resolve, reject) {
                    var fecha = $('#fecha_dup').val();
                    $.ajax({
                        type: "POST",
                        url: url,
                        dataType: "json",
                        data: {incidencia_id: incidencia_id, fecha: fecha, csrf_mlc2: csrfData},
                        success: function(response) {
                            resolve(response.message);
                        },
                        error: function(response) {
                            reject(response.responseJSON.message);
                        }
                    });
                }).catch(error => {
                    Swal.showValidationMessage(`Error: ${error}`);
                });
            },
            allowOutsideClick: () => !swal.isLoading()
        }).then((message) => {
            if (message.value) {
                Swal.fire({
                    type: 'success',
                    title: 'Ok',
                    html: message.value,
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'Aceptar'
                }).then(function() {
                    incidencias_table.ajax.reload(null, false);
                });
            }
        }).catch((message) => {
            if (message) {
                var error = message;
            } else {
                var error = 'Error al duplicar la incidencia';
            }
            Swal.fire({
                type: 'error',
                title: 'Error.',
                html: error,
                buttonsStyling: false,
                confirmButtonClass: 'btn btn-primary',
                confirmButtonText: 'Aceptar'
            });
        });
    }
</script>
<?php if (!empty($error)) : ?>
    <div class="alert alert-danger alert-dismissible fade in alert-fixed" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>ERROR!</strong><?php echo $error; ?>
    </div>
<?php endif; ?>
<?php if (!empty($message)) : ?>
    <div class="alert alert-success alert-dismissible fade in alert-fixed" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>OK!</strong><?php echo $message; ?>
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Incidencias'; ?></h2>
                <?php echo anchor("incidencias/incidencias/$add_url", 'Crear Incidencia', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                    </button>
                    <i class="fa fa-info"></i>IMPORTANTE<br>
                    Estimado Usuario, se les informa que el personal de Informática SÓLO recibirá requerimientos realizados por INCIDENCIAS.<br />
                    <b>Procedimiento:</b><br />
                    <ul>
                        <li>La incidencia que realice debe ser lo más detallada y explicativa posible para evitar contacto con el usuario.</li>
                        <li>El usuario ó tecnico desinfectará el puesto de trabajo (teclado, mouse, escritorio).</li> 
                        <li>El usuario debe retirarse de su puesto para que el técnico pueda trabajar en el.</li> 
                    </ul>
                    <b>Para Delegaciones y oficinas externas:</b><br /> 
                    <ul>
                        <li>El equipo que se traiga para su revision debe tener un rótulo con: Nombre de la oficina, problema que tiene el equipo, nombre y telefono de contacto (previa carga de la incidencia).</li>
                        <li>El mismo debe ser dejado en el pasillo de la oficina 24 para su desinfeccion antes de ingresar a la oficina.</li>
                    </ul>
                    Muchas gracias!<br />
                </div>
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>