<!--
        /*
         * Vista listado de Vencimientos.
         * Autor: Leandro
         * Creado: 28/11/2019
         * Modificado: 10/03/2021 (Leandro)
         */
-->
<script>
    var concesiones_table;
    var permisos_table;
    function complete_concesiones_table() {
        $('#concesiones_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#concesiones_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#concesiones_table thead th').eq(i).text();
            var indice = $('#concesiones_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 3 || indice === 4) { // Inicio || Fin
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(concesiones_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + concesiones_table.column(i).search() + '"/>');
                }
            }
        });
        $('#concesiones_table tfoot th').eq(5).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'concesiones_table\');"><i class="fa fa-eraser"></i></button>');
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
        concesiones_table.columns().every(function() {
            var column = this;
            if (this[0][0] === 3 || this[0][0] === 4) { // Inicio || Fin
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
                $('input,select', concesiones_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#concesiones_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#concesiones_table thead').append(r);
    }
    function complete_permisos_table() {
        $('#permisos_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#permisos_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#permisos_table thead th').eq(i).text();
            var indice = $('#permisos_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 4) { // Vencimiento
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(permisos_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + permisos_table.column(i).search() + '"/>');
                }
            }
        });
        $('#permisos_table tfoot th').eq(5).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'permisos_table\');"><i class="fa fa-eraser"></i></button>');
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
        permisos_table.columns().every(function() {
            var column = this;
            if (this[0][0] === 4) { // Vencimiento
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
                $('input,select', permisos_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#permisos_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#permisos_table thead').append(r);
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
    <div class="col-xs-6">
        <div class="x_panel">
            <div class="x_title">
                <h2><?php echo (!empty($title_viewC)) ? $title_viewC : 'Concesiones'; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_tableC; ?>
                <?php echo $html_tableC; ?>
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="x_panel">
            <div class="x_title">
                <h2><?php echo (!empty($title_viewP)) ? $title_viewP : 'Permisos Constructores'; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_tableP; ?>
                <?php echo $html_tableP; ?>
            </div>
        </div>
    </div>
</div>