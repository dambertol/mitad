<!--
        /*
         * Vista ABM de Comercio.
         * Autor: Leandro
         * Creado: 12/07/2018
         * Modificado: 06/01/2021 (Leandro)
         */
-->
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
                <?php if (!empty($audi_modal)): ?>
                    <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
                        <i class="fa fa-info-circle"></i>
                    </button>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php $data_submit = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
                <?php echo form_open(uri_string(), 'enctype="multipart/form-data" class="form-horizontal"'); ?>
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
                    <?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar' || $txt_btn === 'Anular' || $txt_btn === 'Aprobar') ? form_hidden('id', $comercio->id) : ''; ?>
                    <a href="mas_beneficios/comercios/listar" class="btn btn-default btn-sm">Cancelar</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>
<?php if (!empty($txt_btn) && $txt_btn !== 'Eliminar' && $txt_btn !== 'Anular' && $txt_btn !== 'Aprobar'): ?>
    <script>
        $(document).ready(function () {
            $('#cuit').inputmask({
                mask: '99-99999999-9'
            });
            $('#cuit').blur(function () {
                var cuil = this.value;
                var resul = validaCuil(cuil);
                if (!resul) {
                    Swal.fire({
                        type: 'error',
                        title: 'Error.',
                        text: 'CUIL / CUIT inv??lido',
                        buttonsStyling: false,
                        confirmButtonClass: 'btn btn-primary'
                    });
                }
            });
            $("#imagen_url").fileinput({
                theme: "fa",
                language: "es",
                maxFileSize: 256,
                autoReplace: true,
                required: false,
                maxFileCount: 1,
                showRemove: true,
                removeClass: "btn btn-danger",
                removeIcon: "<i class=\"glyphicon glyphicon-trash\"></i> ",
                showClose: false,
                showUpload: false,
                allowedFileExtensions: ['jpg', 'jpeg', 'png']
            });
        });
    </script>
<?php endif; ?>