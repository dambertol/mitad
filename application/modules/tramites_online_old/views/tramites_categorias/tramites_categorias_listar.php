<!--
        /*
         * Vista listado de Categorías de Trámites.
         * Autor: Leandro
         * Creado: 18/03/2020
         * Modificado: 10/03/2021 (Leandro)
         */
-->
<script>
    var tramites_categorias_table;
    function complete_tramites_categorias_table() {
        agregar_filtros('tramites_categorias_table', tramites_categorias_table, 3);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Categorías de Consultas'; ?></h2>
                <?php echo anchor('tramites_online/tramites_categorias/agregar', 'Crear Categoría de Consulta', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>