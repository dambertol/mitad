<!--
        /*
         * Vista listado de Promociones.
         * Autor: Leandro
         * Creado: 20/07/2020
         * Modificado: 22/03/2021 (Leandro)
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Descuentos'; ?></h2>
                <?php echo anchor("lujan_pass/promociones/$agregar", 'Crear Descuento', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="alert alert-info alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                    </button>
                    <i class="fa fa-info"></i>INFORMACIÓN<br>
                    En esta sección encontrará los descuentos que tiene cargados, puede agregar más si lo desea o editar los datos de alguno de ellos.<br />
                    Cada nuevo descuento, o modificación deberá ser aprobado por la Municipalidad antes de ser publicado.<br />
                    <b>Nota: la imagen que cargue al descuento será la que se mostrará en la página de "Descuentos".</b>
                </div>
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>