<!--
        /*
         * Vista listado de Autorizaciones
         * Autor: Leandro
         * Creado: 17/11/2017
         * Modificado: 22/01/2021 (Leandro)
         */
-->
<script>
    var autorizaciones_table;
    function complete_autorizaciones_table() {
        $('#autorizaciones_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#autorizaciones_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#autorizaciones_table thead th').eq(i).text();
            var indice = $('#autorizaciones_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 2) { // Tipo
                    $(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_tipos)); ?>);
                    $(this).find('select').val(autorizaciones_table.column(i).search());
                } else if (indice === 4 || indice === 8) { // Fecha Autorizacion || Fecha Carga
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(autorizaciones_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else if (indice === 7) { // Estado
                    $(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_estados)); ?>);
                    $(this).find('select').val(autorizaciones_table.column(i).search());
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + autorizaciones_table.column(i).search() + '"/>');
                }
            }
        });
        $('#autorizaciones_table tfoot th').eq(10).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'autorizaciones_table\');"><i class="fa fa-eraser"></i></button>');
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
        autorizaciones_table.columns().every(function() {
            var column = this;
            if (this[0][0] === 2 || this[0][0] === 7) { // Tipo y Estado
                $('input,select', autorizaciones_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
                    if (column.search() !== this.value) {
                        column.search(this.value, 'exact').draw();
                    }
                });
            } else if (this[0][0] === 4 || this[0][0] === 8) { // Fecha Autorizacion y Fecha Carga
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
            } else {
                $('input,select', autorizaciones_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#autorizaciones_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#autorizaciones_table thead').append(r);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Autorizaciones'; ?></h2>
                <?php echo anchor('vales_combustible/autorizaciones/agregar', 'Crear Autorización', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>