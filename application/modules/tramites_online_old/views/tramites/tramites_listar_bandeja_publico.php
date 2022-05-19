<!--
        /*
         * Vista listado de Trámites.
         * Autor: Leandro
         * Creado: 13/05/2020
         * Modificado: 10/03/2021 (Leandro)
         */
-->
<script>
    var tramites_table;
    function complete_tramites_table() {
        $('#tramites_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#tramites_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#tramites_table thead th').eq(i).text();
            var indice = $('#tramites_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 1 || indice === 6) { // Inicio | Ult Movimiento
                    $(this).html('<div style="position:relative;"><input class="form-control input-xs dateFilter' + clase + '" id="dateFilter' + i + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + moment(tramites_table.column(i).search()).format("DD/MM/YYYY") + '"/></div>');
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + tramites_table.column(i).search() + '"/>');
                }
            }
        });
        $('#tramites_table tfoot th').eq(7).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'tramites_table\');"><i class="fa fa-eraser"></i></button>');
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
        tramites_table.columns().every(function() {
            var column = this;
            if (this[0][0] === 1 || this[0][0] === 6) { // Inicio | Ult Movimiento
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
                $('input,select', tramites_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                    if (e.type === 'change' || e.which === 13) {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                        e.preventDefault();
                    }
                });
            }
        });
        var r = $('#tramites_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#tramites_table thead').append(r);
    }
    $(document).ready(function() {
        setInterval(function() {
            tramites_table.draw('page');
        }, 300000);	//5 min
    });
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Consultas'; ?></h2>
                <?php if (!empty($crear)) : ?>
                    <?php echo anchor("tramites_online/tramites/$crear", 'Iniciar Consulta', 'class="btn btn-primary btn-sm"') ?>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>