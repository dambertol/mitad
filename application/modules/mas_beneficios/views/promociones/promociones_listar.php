<!--
        /*
         * Vista listado de Promociones.
         * Autor: Leandro
         * Creado: 20/07/2020
         * Modificado: 20/07/2020 (Leandro)
         */
-->
<script>
    var promociones_table;
    function complete_promociones_table() {
        agregar_filtros('promociones_table', promociones_table, 4);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Promociones'; ?></h2>
                <?php echo anchor("mas_beneficios/promociones/$agregar", 'Crear Promoción', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="alert alert-info alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                    </button>
                    <i class="fa fa-info"></i>INFORMACIÓN<br>
                    En esta sección encontrará las promociones que tiene cargadas, puede agregar más si lo desea o editar los datos de alguna de ellas.<br />
                    Cada nueva promoción, o modificación deberá ser aprobada por la Municipalidad antes de ser publicada.<br />
                    <b>Nota: la imagen que cargue a la promoción será la que se mostrará en la página de "Promociones".</b>
                </div>
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>