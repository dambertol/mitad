<!--
        /*
         * Vista listado de Vehículos.
         * Autor: Leandro
         * Creado: 17/11/2017
         * Modificado: 22/01/2021 (Leandro)
         */
-->
<script>
    var vehiculos_table;
    var checked = false;
    function complete_vehiculos_table() {
        $('#vehiculos_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#vehiculos_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#vehiculos_table thead th').eq(i).text();
            var indice = $('#vehiculos_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 5) { // Tipo Combustible
                    $(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_tipos)); ?>);
                    $(this).find('select').val(vehiculos_table.column(i).search());
                } else if (indice === 7) { // Venc Seguro
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(vehiculos_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else if (indice === 8) { // Estado
                    $(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_estados)); ?>);
                    $(this).find('select').val(vehiculos_table.column(i).search());
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + vehiculos_table.column(i).search() + '"/>');
                }
            }
        });
        $('#vehiculos_table tfoot th').eq(9).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'vehiculos_table\');" title="Limpiar filtros"><i class="fa fa-eraser"></i></button>');
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
        vehiculos_table.columns().every(function() {
            var column = this;
            if (this[0][0] === 5) { // Tipo
                $('input,select', vehiculos_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
                    if (column.search() !== this.value) {
                        column.search(this.value, 'exact').draw();
                    }
                });
            } else if (this[0][0] === 7) { // Venc Seguro
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
            } else if (this[0][0] === 8) { // Estado
                $('input,select', vehiculos_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
                    if (column.search() !== this.value) {
                        column.search(this.value, 'exact').draw();
                    }
                });
            } else {
                $('input,select', vehiculos_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#vehiculos_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#vehiculos_table thead').append(r);
<?php if ($acciones_masivas): ?>
            $('#vehiculos_table thead tr:nth-child(2) th:last-child').append('<a class="btn btn-xs btn-blue" id="cambiar_checkboxs" href="javascript:cambiar_checkboxs();" title="Marcar/Desmarcar todos"><i class="fa fa-fw fa-check-square-o"></i></a>');
<?php endif; ?>
    }
    function drawCallback_vehiculos_table() {
        $('#vehiculos_form input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            radioClass: 'iradio_flat-blue'
        });
    }
    function aprobar_vehiculo(vehiculo_id) {
        Swal.fire({
            title: 'Confirmar',
            text: "Se aprobará el vehículo seleccionado",
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
                window.location.href = CI.base_url + 'vales_combustible/vehiculos/aprobar/' + vehiculo_id;
            }
        });
    }
<?php if ($acciones_masivas): ?>
        function cambiar_checkboxs() {
            if (checked) {
                $('#vehiculos_form input[type="checkbox"]').iCheck('uncheck');
            } else {
                $('#vehiculos_form input[type="checkbox"]').iCheck('check');
            }
            checked = !checked;
        }
        function submit_form(accion) {
            var vehiculos_sel = $("[name='vehiculo[]']:checked").length;
            if (vehiculos_sel > 0)
            {
                $('#vehiculos_form_tipo').val(accion);
                $('#vehiculos_form').attr('action', 'vales_combustible/vehiculos/acciones_masivas_' + accion)
                Swal.fire({
                    title: 'Confirmar',
                    text: accion + " " + vehiculos_sel + " vehículos seleccionados",
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
                        $('#vehiculos_form').submit();
                    }
                });
            } else {
                Swal.fire({
                    type: 'error',
                    title: 'Error.',
                    text: 'Debe seleccionar uno o más vehículos para realizar las acciones.',
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'Aceptar'
                });
            }
        }
<?php endif; ?>
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Vehículos'; ?></h2>
                <?php if ($boton_agregar): ?>
                    <?php echo anchor('vales_combustible/vehiculos/agregar', 'Crear Vehículo', 'class="btn btn-primary btn-sm"') ?>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo form_open(uri_string(), 'class="form-horizontal" id="vehiculos_form"'); ?>
                <?php if ($acciones_masivas): ?>
                    <div class="btn-group pull-right" role="group" style="margin-left: 5px;">
                        <a href="#" class="btn btn-blue dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                            <i class="fa fa-database"></i> Acciones Masivas <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu animated fadeInDown" role="menu">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:submit_form('Aprobar');"><i class="fa fa-fw fa-check-circle"></i> Aprobar</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
                <input type="hidden" id="vehiculos_form_tipo" name="tipo" value="">
                <input type="hidden" id="vehiculos_form_back_url" name="back_url" value="listar">
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>