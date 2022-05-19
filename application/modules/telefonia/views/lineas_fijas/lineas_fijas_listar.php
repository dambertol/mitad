<!--
        /*
         * Vista listado de Líneas Fijas.
         * Autor: Leandro
         * Creado: 05/09/2019
         * Modificado: 26/05/2020 (Leandro)
         */
-->
<script>
    var lineas_fijas_table;
    function complete_lineas_fijas_table() {
        agregar_filtros('lineas_fijas_table', lineas_fijas_table, 8);
    }
    $(document).ready(function () {
        $('#estado').on('change', function () {
            lineas_fijas_table.ajax.reload(null, false);
        });
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Líneas Fijas'; ?></h2>
                <?php echo anchor('telefonia/lineas_fijas/agregar', 'Crear Línea Fija', 'class="btn btn-primary btn-sm"') ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="form-horizontal">
                    <?php foreach ($fields as $field): ?>
                        <div class="form-group">
                            <?php echo $field['label']; ?> 
                            <?php echo $field['form']; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <br />
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>