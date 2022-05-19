<!--
        /*
         * Vista listado de Comercios.
         * Autor: Leandro
         * Creado: 12/07/2018
         * Modificado: 20/07/2020 (Leandro)
         */
-->
<script>
    var comercios_table;
    function complete_comercios_table() {
        agregar_filtros('comercios_table', comercios_table, 8);
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Comercios'; ?></h2>
                <?php echo anchor("mas_beneficios/comercios/$agregar", 'Crear Comercio', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="alert alert-info alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                    </button>
                    <i class="fa fa-info"></i>INFORMACIÓN<br>
                    En esta sección encontrará los comercios que tiene cargados, puede agregar más si lo desea o editar los datos de alguno de ellos.<br />
                    Cada nuevo comercio, o modificación deberá ser aprobada por la Municipalidad antes de ser publicada.<br />
                    <b>Nota: la imagen que cargue al comercio será la que se mostrará en la página de "Comercios".</b>
                </div>
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>