<!--
        /*
         * Vista Consultar Deuda.
         * Autor: Leandro
         * Creado: 15/11/2019
         * Modificado: 11/06/2020 (Leandro)
         */
-->
<script>
    $(document).ready(function () {
        $("#button_exportar").click(function () {
            $("#form_reporte").data('submitted', false);
            $("#form_reporte").submit();
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Escribanos'; ?></h2>
                <?php if (!empty($audi_modal)): ?>
                    <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
                        <i class="fa fa-info-circle"></i>
                    </button>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="alert alert-info alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                    </button>
                    <i class="fa fa-info"></i>INFORMACIÓN<br>
                    Puede pegar una lista de padrones separados por comas
                </div>
                <?php $data_submit = array('id' => 'button_exportar', 'class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
                <?php echo form_open(uri_string(), 'id="form_reporte" class="form-horizontal"'); ?>
                <div class="row">
                    <?php foreach ($fields as $field): ?>
                        <div class="form-group">
                            <?php echo $field['label']; ?> 
                            <?php echo $field['form']; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="ln_solid"></div>
                <div class="text-center">
                    <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                    <a href="major/escritorio" class="btn btn-default btn-sm">Cancelar</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>