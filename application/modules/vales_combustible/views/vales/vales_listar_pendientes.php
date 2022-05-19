<!--
        /*
         * Vista listado de Vales Pendientes
         * Autor: Leandro
         * Creado: 11/07/2018
         * Modificado: 22/01/2021 (Leandro)
         */
-->
<script>
    var vales_pendientes_table;
    var checked = false;
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
    function complete_vales_pendientes_table() {
        $('#vales_pendientes_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#vales_pendientes_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#vales_pendientes_table thead th').eq(i).text();
            var indice = $('#vales_pendientes_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 1 || indice === 3) { // Fecha || Vencimiento
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(vales_pendientes_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else if (indice === 4) { // Tipo
                    $(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_tipos)); ?>);
                    $(this).find('select').val(vales_pendientes_table.column(i).search());
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + vales_pendientes_table.column(i).search() + '"/>');
                }
            }
        });
        $('#vales_pendientes_table tfoot th').eq(8).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'vales_pendientes_table\');" title="Limpiar filtros"><i class="fa fa-eraser"></i></button>');
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
        vales_pendientes_table.columns().every(function() {
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
            } else if (this[0][0] === 4) { // Tipo
                $('input,select', vales_pendientes_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
                    if (column.search() !== this.value) {
                        column.search(this.value, 'exact').draw();
                    }
                });
            } else {
                $('input,select', vales_pendientes_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#vales_pendientes_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#vales_pendientes_table thead').append(r);
        $('#vales_pendientes_table thead tr:nth-child(2) th:last-child').append('<a class="btn btn-xs btn-blue" id="cambiar_checkboxs" href="javascript:cambiar_checkboxs();" title="Marcar/Desmarcar todos"><i class="fa fa-fw fa-check-square-o"></i></a>');
    }
    function drawCallback_vales_pendientes_table() {
        $('#vales_pendientes_form input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            radioClass: 'iradio_flat-blue'
        });
    }
    function aprobar_vale(vale_id) {
        const url = CI.base_url + 'vales_combustible/vales/aprobar';
        Swal.fire({
            title: 'Confirmar',
            type: 'info',
            html: 'Se aprobará el vale seleccionado<br><br>\n\
                                                <div class="form-horizontal">\n\
                                                        <div class="row">\n\
                                                                <div class="form-group">\n\
                                                                        <label for="venc_aprob" class="col-sm-4 control-label">Vencimiento *</label> \n\
                                                                        <div class="col-sm-8">\n\
                                                                                <input type="text" name="venc_aprob" value="<?php echo (new DateTime())->add(new DateInterval('P7D'))->modify('tuesday this week')->format('d/m/Y'); ?>" id="venc_aprob" class="form-control dateFormat" required="" autocomplete="off">\n\
                                                                        </div>\n\
                                                                </div>\n\
                                                        </div>\n\
                                                </div>',
            onOpen: function() {
                $('#venc_aprob').datetimepicker({
                    locale: 'es',
                    format: 'L',
                    useCurrent: false,
                    showClear: true,
                    showTodayButton: true,
                    showClose: true,
                    daysOfWeekDisabled: [0, 1, 3, 4, 5, 6]
                });
                $('#venc_aprob').attr("autocomplete", "off");
                $("#venc_aprob").on("dp.change", function(e) {
                    if (e.date.weekday() !== 1) {	//No es Martes
                        $('#venc_aprob').val(e.date.startOf('week').add(1, 'days').format("DD/MM/YYYY"));
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
                    var vencimiento = $('#venc_aprob').val();
                    $.ajax({
                        type: "POST",
                        url: url,
                        dataType: "json",
                        data: {vale_id: vale_id, vencimiento: vencimiento, csrf_mlc2: csrfData},
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
                    vales_pendientes_table.ajax.reload(null, false);
                });
            }
        }).catch((message) => {
            if (message) {
                var error = message;
            } else {
                var error = 'Error al aprobar el Vale';
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
            $('#vales_pendientes_form input[type="checkbox"]').iCheck('uncheck');
        } else {
            $('#vales_pendientes_form input[type="checkbox"]').iCheck('check');
        }
        checked = !checked;
    }
    function submit_form(accion) {
        var vales_sel = $("[name='vale[]']:checked").length;
        if (vales_sel > 0)
        {
            $('#vales_pendientes_form_tipo').val(accion);
            $('#vales_pendientes_form').attr('action', 'vales_combustible/vales/acciones_masivas_' + accion);
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
                        $('#vales_pendientes_form').submit();
                    }
                });
            } else if (accion === 'Aprobar') {
                Swal.fire({
                    title: 'Confirmar',
                    type: 'info',
                    html: 'Aprobar ' + vales_sel + ' vales seleccionados<br><br>\n\
                                                <div class="form-horizontal">\n\
                                                        <div class="row">\n\
                                                                <div class="form-group">\n\
                                                                        <label for="venc_aprob" class="col-sm-4 control-label">Vencimiento *</label> \n\
                                                                        <div class="col-sm-8">\n\
                                                                                <input type="text" name="venc_aprob" value="<?php echo (new DateTime())->add(new DateInterval('P7D'))->format('d/m/Y'); ?>" id="venc_aprob" class="form-control dateFormat" required="" autocomplete="off">\n\
                                                                        </div>\n\
                                                                </div>\n\
                                                        </div>\n\
                                                </div>',
                    onOpen: function() {
                        $('#venc_aprob').datetimepicker({
                            locale: 'es',
                            format: 'L',
                            useCurrent: false,
                            showClear: true,
                            showTodayButton: true,
                            showClose: true
                        });
                        $('#venc_aprob').attr("autocomplete", "off");
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
                }).then((result) => {
                    if (result.value) {
                        var vencimiento = $('#venc_aprob').val();
                        if (vencimiento) {
                            $('#vales_pendientes_form_vencimiento').val(vencimiento);
                            $('#vales_pendientes_form').submit();
                        } else {
                            Swal.fire({
                                type: 'error',
                                title: 'Error.',
                                text: 'Debe ingresar la fecha de vencimiento para los vales aprobados',
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
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="alert alert-info alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                    </button>
                    <i class="fa fa-info"></i>INFORMACIÓN<br>
                    El vencimiento de los Vales Pendientes es sólo orientativo. El vencimiento real será ingresado cuando se apruebe el mismo.<br>
                    Los vales se manejarán semanalmente por lo tanto las fechas de inicio y vencimiento permitidas serán los días Martes. 
                </div>
                <?php echo form_open(uri_string(), 'class="form-horizontal" id="vales_pendientes_form"'); ?>
                <?php if (TRUE): ?>
                    <div class="btn-group pull-right" role="group" style="margin-left: 5px;">
                        <a href="#" class="btn btn-blue dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                            <i class="fa fa-database"></i> Acciones Masivas <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu animated fadeInDown" role="menu">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:submit_form('Aprobar');"><i class="fa fa-fw fa-check-circle"></i> Aprobar</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:submit_form('Anular');"><i class="fa fa-fw fa-ban"></i> Anular</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
                <input type="hidden" id="vales_pendientes_form_tipo" name="tipo" value="">
                <input type="hidden" id="vales_pendientes_form_vencimiento" name="vencimiento" value="">
                <input type="hidden" id="vales_pendientes_form_back_url" name="back_url" value="listar_pendientes">
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>