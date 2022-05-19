<!--
        /*
         * Vista listado de Valores Combustible
         * Autor: Leandro
         * Creado: 06/11/2017
         * Modificado: 22/01/2021 (Leandro)
         */
-->
<script>
    var valores_combustible_table;
    function complete_valores_combustible_table() {
        $('#valores_combustible_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#valores_combustible_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#valores_combustible_table thead th').eq(i).text();
            var indice = $('#valores_combustible_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 0) { // Fecha Inicio
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(valores_combustible_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else if (indice === 1) { // Tipo
                    $(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_tipos)); ?>);
                    $(this).find('select').val(valores_combustible_table.column(i).search());
                } else if (indice === 2) { // Costo
                    $(this).html('<input class="form-control input-xs numberFilter' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + valores_combustible_table.column(i).search() + '"/>');
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + valores_combustible_table.column(i).search() + '"/>');
                }
            }
        });
        $('#valores_combustible_table tfoot th').eq(3).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'valores_combustible_table\');"><i class="fa fa-eraser"></i></button>');
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
        valores_combustible_table.columns().every(function() {
            var column = this;
            if (this[0][0] === 0) { // Fecha Inicio
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
            } else if (this[0][0] === 1) { // Tipo
                $('input,select', valores_combustible_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
                    if (column.search() !== this.value) {
                        column.search(this.value, 'exact').draw();
                    }
                });
            } else if (this[0][0] === 2) { // Costo
                $('input,select', valores_combustible_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
                    if (column.search() !== this.value) {
                        var str_numero = this.value.toString();
                        column.search(str_numero).draw();
                    }
                });
            } else {
                $('input,select', valores_combustible_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#valores_combustible_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#valores_combustible_table thead').append(r);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Valores Combustible'; ?></h2>
                <?php echo anchor('vales_combustible/valores_combustible/agregar', 'Crear Valor Combustible', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>