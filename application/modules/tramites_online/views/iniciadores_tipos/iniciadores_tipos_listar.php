<!--
        /*
         * Vista listado de Tipos de Iniciadores.
         * Autor: Leandro
         * Creado: 22/04/2021
         * Modificado: 22/04/2021 (Leandro)
         */
-->
<script>
    var iniciadores_tipos_table;
    function complete_iniciadores_tipos_table() {
        agregar_filtros('iniciadores_tipos_table', iniciadores_tipos_table, 1);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Tipos de Iniciadores'; ?></h2>
                <?php echo anchor('tramites_online/iniciadores_tipos/agregar', 'Crear Tipo de Iniciador', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>