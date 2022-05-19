<!--
        /*
         * Vista listado de Órdenes de Compra
         * Autor: Leandro
         * Creado: 13/11/2017
         * Modificado: 22/01/2021 (Leandro)
         */
-->
<script>
    var ordenes_compra_table;
    function complete_ordenes_compra_table() {
        $('#ordenes_compra_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#ordenes_compra_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#ordenes_compra_table thead th').eq(i).text();
            var indice = $('#ordenes_compra_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 0) { // Fecha Inicio
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(ordenes_compra_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else if (indice === 3) { // Costo
                    $(this).html('<input class="form-control input-xs numberFilter' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + ordenes_compra_table.column(i).search() + '"/>');
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + ordenes_compra_table.column(i).search() + '"/>');
                }
            }
        });
        $('#ordenes_compra_table tfoot th').eq(4).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'ordenes_compra_table\');"><i class="fa fa-eraser"></i></button>');
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
        ordenes_compra_table.columns().every(function() {
            var column = this;
            if (this[0][0] === 0) {
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
            } else if (this[0][0] === 3) {
                $('input,select', ordenes_compra_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
                    if (column.search() !== this.value) {
                        var str_numero = this.value.toString();
                        column.search(str_numero).draw();
                    }
                });
            } else {
                $('input,select', ordenes_compra_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#ordenes_compra_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#ordenes_compra_table thead').append(r);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Órdenes de Compra'; ?></h2>
                <?php echo anchor('vales_combustible/ordenes_compra/agregar', 'Crear Orden de Compra', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>