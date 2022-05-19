<!--
	/*
	 * Vista listado de Cédulas.
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 02/07/2019 (GENERATOR_MLC)
	 */
-->
<script>
    var cedulas_table;

    function complete_cedulas_table() {
        //agregar_filtros('cedulas_table', cedulas_table, 10);


        $('#cedulas_table tfoot th').each(function (i) {
            var clase = '';
            var tdclass = $('#cedulas_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#cedulas_table thead th').eq(i).text();
            var indice = $('#cedulas_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 7 || indice === 8) { // Inicio || Finalización
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(cedulas_table.column(i).search()).format("DD/MM/YYYY HH:mm") + '"/></div>');
                } else if (indice === 6) { // Estado
                    $(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_estados)); ?>);
                    $(this).find('select').val(cedulas_table.column(i).search());
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + cedulas_table.column(i).search() + '"/>');
                }
            }
        });

        $('.dateFilter').each(function (index, element) {
            $(element).datetimepicker({
                locale: 'es',
                format: 'L',
                useCurrent: false,
                showClear: true,
                showTodayButton: true,
                showClose: true
            });
        });


        $('#cedulas_table tfoot th').eq(9).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'cedulas_table\');" title="Limpiar filtros"><i class="fa fa-eraser"></i></button>');
        cedulas_table.columns().every(function () {
            var column = this;
            if (this[0][0] === 7 || this[0][0] === 8) { // Creacion || Update
                $("#dateFilter" + this[0][0]).on("dp.change", function (e) {
                    if (e.date) {
                        var sql_date = moment(e.date._d).format('YYYY-MM-DD');
                    } else {
                        var sql_date = '';
                    }
                    if (column.search() !== sql_date) {
                        column.search(sql_date).draw();
                    }
                });
            } else if (this[0][0] === 6) { // Estado
                $('input,select', cedulas_table.table().footer().children[0].children[this[0][0]]).on('change', function () {
                    if (column.search() !== this.value) {
                        column.search(this.value, 'exact').draw();
                    }
                });
            }
            else {
                $('input,select', cedulas_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function (e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }

        });
        var r = $('#cedulas_table tfoot tr');
        r.find('th').each(function () {
            $(this).css('padding', 5);
        });
        $('#cedulas_table thead').append(r);
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
<?php if (!empty($vencidas)) : ?>
    <div class="alert alert-warning alert-dismissible fade in alert-fixed" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        Cantidad de cedulas vencidas <strong><?php echo $vencidas; ?></strong>
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Cédulas'; ?></h2>
                <?php echo anchor('notificaciones/cedulas/solicitar', 'Solicitar Cédula', 'class="btn btn-primary btn-sm"') ?>
                <?php echo anchor('notificaciones/adjuntos/descargar/modelo_cedula.docx', 'Descargar Modelo Cedula', 'class="btn btn-info btn-sm" target="_blank"') ?>

                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
        <?php if ($show_btn): ?>
            <?php echo anchor('notificaciones/cedulas/listar/0', 'TODAS Cédula', 'class="btn btn-primary btn-sm"') ?>
            <?php echo anchor('notificaciones/cedulas/listar/1', 'Cedulas Pendientes de Aprobación', 'class="btn btn-warning btn-sm"') ?>
            <?php echo anchor('notificaciones/cedulas/listar/2', 'Cedulas En Proceso', 'class="btn btn-info btn-sm"') ?>
            <?php echo anchor('notificaciones/cedulas/listar/3', 'Cedulas Finalizadas', 'class="btn btn-success btn-sm"') ?>
        <?php endif; ?>
    </div>
</div>