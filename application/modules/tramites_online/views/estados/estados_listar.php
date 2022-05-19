<!--
        /*
         * Vista listado de Estados.
         * Autor: Leandro
         * Creado: 27/04/2021
         * Modificado: 12/06/2021 (Leandro)
         */
-->
<script>
    var estados_table;

    function complete_estados_table() {
        agregar_filtros('estados_table', estados_table, 9);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Estados'; ?></h2>
                <?php echo anchor('tramites_online/estados/agregar', 'Crear Estado', 'class="btn btn-primary btn-sm"') ?>
                <div class="pull-right">
                    <?php echo anchor('tramites_online/formularios/', 'Ir a Formularios', 'class="btn btn-info btn-sm" target="_blank"') ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>