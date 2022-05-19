<!--
        /*
         * Vista listado de Difuntos.
         * Autor: Leandro
         * Creado: 22/11/2019
         * Modificado: 10/03/2021 (Leandro)
         */
-->
<script>
    var difuntos_table;
    function complete_difuntos_table() {
        $('#difuntos_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#difuntos_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#difuntos_table thead th').eq(i).text();
            var indice = $('#difuntos_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 4 || indice === 10) { // Defuncion || Venc Conc
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(difuntos_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + difuntos_table.column(i).search() + '"/>');
                }
            }
        });
        $('#difuntos_table tfoot th').eq(11).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'difuntos_table\');"><i class="fa fa-eraser"></i></button>');
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
        difuntos_table.columns().every(function() {
            var column = this;
            if (this[0][0] === 4 || this[0][0] === 10) { // Defuncion || Venc Conc
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
                $('input,select', difuntos_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#difuntos_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#difuntos_table thead').append(r);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Difuntos'; ?></h2>
                <?php echo anchor('defunciones/difuntos/agregar', 'Crear Difunto', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>