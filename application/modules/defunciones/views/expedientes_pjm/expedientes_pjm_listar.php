<!--
        /*
         * Vista listado de Expedientes PJM.
         * Autor: Leandro
         * Creado: 22/11/2019
         * Modificado: 10/03/2021 (Leandro)
         */
-->
<script>
    var expedientes_pjm_table;
    function complete_expedientes_pjm_table() {
        $('#expedientes_pjm_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#expedientes_pjm_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#expedientes_pjm_table thead th').eq(i).text();
            var indice = $('#expedientes_pjm_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 1) { // Fecha Pago
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(expedientes_pjm_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else if (indice === 5 || indice === 6 || indice === 7 || indice === 8) { // Monto UT || Monto $ || Licencias || Parcelas
                    $(this).html('<input class="form-control input-xs numberFilter' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + expedientes_pjm_table.column(i).search() + '"/>');
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + expedientes_pjm_table.column(i).search() + '"/>');
                }
            }
        });
        $('#expedientes_pjm_table tfoot th').eq(11).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'expedientes_pjm_table\');"><i class="fa fa-eraser"></i></button>');
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
        expedientes_pjm_table.columns().every(function() {
            var column = this;
            if (this[0][0] === 1) { // Fecha Pago
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
            } else if (this[0][0] === 5 || this[0][0] === 6 || this[0][0] === 7 || this[0][0] === 8) { // Monto UT || Monto $ || Licencias || Parcelas
                $('input,select', expedientes_pjm_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
                    if (column.search() !== this.value) {
                        var str_numero = this.value.toString();
                        column.search(str_numero).draw();
                    }
                });
            } else {
                $('input,select', expedientes_pjm_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#expedientes_pjm_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#expedientes_pjm_table thead').append(r);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Expedientes PJM'; ?></h2>
                <?php echo anchor('defunciones/expedientes_pjm/agregar', 'Crear Expediente PJM', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>