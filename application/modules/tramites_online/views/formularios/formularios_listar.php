<!--
        /*
         * Vista listado de Formularios.
         * Autor: Leandro
         * Creado: 22/04/2021
         * Modificado: 10/05/2021 (Leandro)
         */
-->
<script>
    var formularios_table;

    function complete_formularios_table() {
        agregar_filtros('formularios_table', formularios_table, 5);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Formularios'; ?></h2>
                <?php echo anchor('tramites_online/formularios/agregar', 'Crear Formulario', 'class="btn btn-primary btn-sm"') ?>
                <div class="pull-right">
                    <?php echo anchor('tramites_online/estados/', 'Ir a Estados', 'class="btn btn-info btn-sm" target="_blank"') ?>
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