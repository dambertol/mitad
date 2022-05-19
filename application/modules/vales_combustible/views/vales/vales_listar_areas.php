<!--
        /*
         * Vista listado de Vales de Areas
         * Autor: Leandro
         * Creado: 11/07/2018
         * Modificado: 22/01/2021 (Leandro)
         */
-->
<script>
    var vales_areas_table;
    var checked = false;
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
    function complete_vales_areas_table() {
        $('#vales_areas_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#vales_areas_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#vales_areas_table thead th').eq(i).text();
            var indice = $('#vales_areas_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 1 || indice === 3) { // Fecha || Vencimiento
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(vales_areas_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else if (indice === 4) { // Tipo
                    $(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_tipos)); ?>);
                    $(this).find('select').val(vales_areas_table.column(i).search());
                } else if (indice === 8) { // Estado
                    $(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_estados)); ?>);
                    $(this).find('select').val(vales_areas_table.column(i).search());
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + vales_areas_table.column(i).search() + '"/>');
                }
            }
        });
        $('#vales_areas_table tfoot th').eq(9).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'vales_areas_table\');" title="Limpiar filtros"><i class="fa fa-eraser"></i></button>');
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
        $('.numberFilter').each(function(index, element) {
            $(element).inputmask('decimal', {
                radixPoint: ',',
                unmaskAsNumber: true,
                digits: 2,
                autoUnmask: true,
                placeholder: '',
                removeMaskOnSubmit: true,
                positionCaretOnClick: 'select'
            });
        });
        vales_areas_table.columns().every(function() {
            var column = this;
            if (this[0][0] === 1 || this[0][0] === 3) { // Fecha y Vencimiento
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
            } else if (this[0][0] === 4 || this[0][0] === 8) { // Tipo y Estado
                $('input,select', vales_areas_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
                    if (column.search() !== this.value) {
                        column.search(this.value, 'exact').draw();
                    }
                });
            } else {
                $('input,select', vales_areas_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#vales_areas_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#vales_areas_table thead').append(r);
        $('#vales_areas_table thead tr:nth-child(2) th:last-child').append('<a class="btn btn-xs btn-blue" id="cambiar_checkboxs" href="javascript:cambiar_checkboxs();" title="Marcar/Desmarcar todos"><i class="fa fa-fw fa-check-square-o"></i></a>');
    }
    function drawCallback_vales_areas_table() {
        $('#vales_areas_form input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            radioClass: 'iradio_flat-blue'
        });
    }
    function duplicar_vale(vale_id) {
        const url = CI.base_url + 'vales_combustible/vales/repetir';
        Swal.fire({
            title: 'Confirmar',
            type: 'info',
            html: 'Se repetirá el vale seleccionado<br><br>\n\
                                                <div class="form-horizontal">\n\
                                                        <div class="row">\n\
                                                                <div class="form-group">\n\
                                                                        <label for="fecha_dup" class="col-sm-4 control-label">Fecha *</label> \n\
                                                                        <div class="col-sm-8">\n\
                                                                                <input type="text" name="fecha_dup" value="<?php echo (new DateTime())->modify('tuesday this week')->format('d/m/Y'); ?>" id="fecha_dup" class="form-control dateFormat" required="" autocomplete="off">\n\
                                                                        </div>\n\
                                                                </div>\n\
                                                        </div>\n\
                                                </div>',
            onOpen: function() {
                $('#fecha_dup').datetimepicker({
                    locale: 'es',
                    format: 'L',
                    useCurrent: false,
                    showClear: true,
                    showTodayButton: true,
                    showClose: true,
                    daysOfWeekDisabled: [0, 1, 3, 4, 5, 6]
                });
                $('#fecha_dup').attr("autocomplete", "off");
                $("#fecha_dup").on("dp.change", function(e) {
                    if (e.date.weekday() !== 1) {	//No es Martes
                        $('#fecha_dup').val(e.date.startOf('week').add(1, 'days').format("DD/MM/YYYY"));
                    }
                });
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
                        data: {vale_id: vale_id, fecha: fecha, csrf_mlc2: csrfData},
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
                    vales_areas_table.ajax.reload(null, false);
                });
            }
        }).catch((message) => {
            if (message) {
                var error = message;
            } else {
                var error = 'Error al duplicar el Vale';
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
    function cambiar_checkboxs() {
        if (checked) {
            $('#vales_areas_form input[type="checkbox"]').iCheck('uncheck');
        } else {
            $('#vales_areas_form input[type="checkbox"]').iCheck('check');
        }
        checked = !checked;
    }
    function submit_form(accion) {
        var vales_sel = $("[name='vale[]']:checked").length;
        if (vales_sel > 0)
        {
            $('#vales_areas_form_tipo').val(accion);
            $('#vales_areas_form').attr('action', 'vales_combustible/vales/acciones_masivas_' + accion);
            if (accion === 'Anular') {
                Swal.fire({
                    title: 'Confirmar',
                    text: accion + " " + vales_sel + " vales seleccionados",
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
                        $('#vales_areas_form').submit();
                    }
                });
            } else if (accion === 'Repetir') {
                Swal.fire({
                    title: 'Confirmar',
                    type: 'info',
                    html: 'Repetir vales seleccionados<br><br>\n\
                                                <div class="form-horizontal">\n\
                                                        <div class="row">\n\
                                                                <div class="form-group">\n\
                                                                        <label for="fecha_dup" class="col-sm-4 control-label">Fecha *</label> \n\
                                                                        <div class="col-sm-8">\n\
                                                                                <input type="text" name="fecha_dup" value="<?php echo (new DateTime())->modify('tuesday this week')->format('d/m/Y'); ?>" id="fecha_dup" class="form-control dateFormat" required="" autocomplete="off">\n\
                                                                        </div>\n\
                                                                </div>\n\
                                                        </div>\n\
                                                </div>',
                    onOpen: function() {
                        $('#fecha_dup').datetimepicker({
                            locale: 'es',
                            format: 'L',
                            useCurrent: false,
                            showClear: true,
                            showTodayButton: true,
                            showClose: true,
                            daysOfWeekDisabled: [0, 1, 3, 4, 5, 6]
                        });
                        $('#fecha_dup').attr("autocomplete", "off");
                        $("#fecha_dup").on("dp.change", function(e) {
                            if (e.date.weekday() !== 1) {	//No es Martes
                                $('#fecha_dup').val(e.date.startOf('week').add(1, 'days').format("DD/MM/YYYY"));
                            }
                        });
                    },
                    showCloseButton: true,
                    showCancelButton: true,
                    focusCancel: true,
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-default',
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    customClass: 'sweetalert-lg'
                }).then((result) => {
                    if (result.value) {
                        var fecha = $('#fecha_dup').val();
                        if (fecha) {
                            $('#vales_areas_form_fecha').val(fecha);
                            $('#vales_areas_form').submit();
                        } else {
                            Swal.fire({
                                type: 'error',
                                title: 'Error.',
                                text: 'Debe ingresar la nueva fecha para los vales duplicados',
                                buttonsStyling: false,
                                confirmButtonClass: 'btn btn-primary',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    }
                });
            }
        } else {
            Swal.fire({
                type: 'error',
                title: 'Error.',
                text: 'Debe seleccionar uno o más vales para realizar las acciones.',
                buttonsStyling: false,
                confirmButtonClass: 'btn btn-primary',
                confirmButtonText: 'Aceptar'
            });
        }
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Vales'; ?></h2>
                <?php echo anchor('vales_combustible/vales/solicitar', 'Solicitar Vale', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="alert alert-info alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                    </button>
                    <i class="fa fa-info"></i>INFORMACIÓN<br>
                    El vencimiento de los Vales con estado "Pendiente" es sólo orientativo. El vencimiento real será definido cuando se apruebe el Vale.
                </div>
                <?php echo form_open(uri_string(), 'class="form-horizontal" id="vales_areas_form"'); ?>
                <?php if (TRUE): ?>
                    <div class="btn-group pull-right" role="group" style="margin-left: 5px;">
                        <a href="#" class="btn btn-blue dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                            <i class="fa fa-database"></i> Acciones Masivas <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu animated fadeInDown" role="menu">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:submit_form('Anular');"><i class="fa fa-fw fa-ban"></i> Anular</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:submit_form('Repetir');"><i class="fa fa-fw fa-repeat"></i> Repetir</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
                <input type="hidden" id="vales_areas_form_tipo" name="tipo" value="">
                <input type="hidden" id="vales_areas_form_fecha" name="fecha" value="">
                <input type="hidden" id="vales_areas_form_back_url" name="back_url" value="listar_areas">
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>