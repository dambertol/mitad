<!--
        /*
         * Vista listado de Tipos Vehículo.
         * Autor: Leandro
         * Creado: 10/07/2018
         * Modificado: 22/01/2021 (Leandro)
         */
-->
<script>
    var tipos_vehiculo_table;
    function complete_tipos_vehiculo_table() {
        $('#tipos_vehiculo_table tfoot th').each(function(i) {
            var clase = '';
            var tdclass = $('#tipos_vehiculo_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#tipos_vehiculo_table thead th').eq(i).text();
            if (title !== '') {
                $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + tipos_vehiculo_table.column(i).search() + '"/>');
            }
        });
        $('#tipos_vehiculo_table tfoot th').eq(1).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'tipos_vehiculo_table\');"><i class="fa fa-eraser"></i></button>');
        tipos_vehiculo_table.columns().every(function() {
            var column = this;
            $('input,select', tipos_vehiculo_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function(e) {
                if (e.type === 'change' || e.which === 13) {
                    if (column.search() !== this.value) {
                        column.search(this.value).draw();
                    }
                    e.preventDefault();
                }
            });
        });
        var r = $('#tipos_vehiculo_table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', '5px 2px');
        });
        $('#tipos_vehiculo_table thead').append(r);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Tipos Vehículo'; ?></h2>
                <?php echo anchor('vales_combustible/tipos_vehiculo/agregar', 'Crear Tipo Vehículo', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>