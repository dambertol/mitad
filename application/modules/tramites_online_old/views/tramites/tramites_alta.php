<!--
        /*
         * Vista Alta de Trámite.
         * Autor: Leandro
         * Creado: 17/03/2020
         * Modificado: 10/03/2021 (Leandro)
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Consultas'; ?></h2>
                <?php if (!empty($audi_modal)): ?>
                    <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
                        <i class="fa fa-info-circle"></i>
                    </button>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php $data_submit = array('id' => 'btn-iniciar', 'class' => 'btn btn-primary btn-sm disabled', 'title' => $txt_btn, 'disabled' => true); ?>
                <?php echo form_open(uri_string(), 'enctype="multipart/form-data" class="form-horizontal" id="form-tramite"'); ?>
                <div id="smartwizard">
                    <ul>
                        <li><a href="#paso-0">1. Consulta<br /><small>Datos generales</small></a></li>
                        <li><a href="#paso-1">2. Persona<br /><small>Datos personales</small></a></li>
                        <li><a href="#paso-2">3. Inmueble<br /><small>Datos del inmueble</small></a></li>
                    </ul>
                    <div>
                        <div id="paso-0" class="">
                            <br />
                            <div id="form-paso-0" role="form" data-toggle="validator">
                                <div class="alert alert-info alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <i class="fa fa-info"></i>INFORMACIÓN<br>
                                    Recuerdá revisar los requisitos de cada trámite haciendo click <a href="https://lujandecuyo.gob.ar/guia-tramites/" target="_blank">AQUÍ</a>
                                </div>
                                <div style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                                    <div style="padding:5px 15px;">
                                        <h2 class="text-center">Consulta</h2>
                                    </div>
                                    <?php foreach ($fields_tramite as $field_tramite): ?>
                                        <div class="form-group">
                                            <?php echo $field_tramite['label']; ?> 
                                            <?php echo $field_tramite['form']; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php foreach ($fields_adjunto as $field_adjunto): ?>
                                        <div class="form-group">
                                            <?php echo $field_adjunto['label']; ?> 
                                            <?php echo $field_adjunto['form']; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div id="paso-1" class="">
                            <br />
                            <div id="form-paso-1" role="form" data-toggle="validator">
                                <div class="alert alert-info alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                                    </button>
                                    <i class="fa fa-info"></i>INFORMACIÓN<br>
                                    En caso de que sus datos no sean correctos, contactate con la Municipalidad al 0800 222 7800 antes de iniciar la consulta
                                </div>
                                <div style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                                    <div style="padding:5px 15px;">
                                        <h2 class="text-center">Persona</h2>
                                    </div>
                                    <?php foreach ($fields_persona as $field_persona): ?>
                                        <div class="form-group">
                                            <?php echo $field_persona['label']; ?> 
                                            <?php echo $field_persona['form']; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div id="paso-2" class="">
                            <br />
                            <div id="form-paso-2" role="form" data-toggle="validator">
                                <div style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                                    <div style="padding:5px 15px;">
                                        <h2 class="text-center">Inmueble</h2>
                                    </div>
                                    <?php foreach ($fields_inmueble as $field_inmueble): ?>
                                        <div class="form-group">
                                            <?php echo $field_inmueble['label']; ?> 
                                            <?php echo $field_inmueble['form']; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="text-center">
                    <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                    <a href="tramites_online/tramites/<?php echo $back_url; ?>" class="btn btn-default btn-sm">Cancelar</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>
<script>
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
    $('#categoria').on('changed.bs.select', function(e) {
        buscar_tipo_tramite();
    });
    $(document).ready(function() {
        $('#smartwizard').smartWizard({
            theme: 'arrows',
            transitionEffect: 'fade',
            keyNavigation: false,
            useURLhash: false,
            showStepURLhash: false,
            lang: {
                next: 'Siguiente',
                previous: 'Anterior'
            },
            anchorSettings: {
                markDoneStep: true,
                markAllPreviousStepsAsDone: true,
                removeDoneStepOnNavigateBack: true,
                enableAnchorOnDoneStep: true
            }
        });
        $("#smartwizard").on("leaveStep", function(e, anchorObject, stepNumber, stepDirection) {
            var elmForm = $("#form-paso-" + stepNumber);
            if (stepDirection === 'forward' && elmForm) {
                if (stepNumber === 0) {
                    if ($("#tipo").selectpicker('val') === '') {
                        Swal.fire({
                            type: 'error',
                            title: 'Error.',
                            text: 'Seleccione el tipo de consulta.',
                            buttonsStyling: false,
                            confirmButtonClass: 'btn btn-primary',
                            confirmButtonText: 'Aceptar'
                        });
                        return false;
                    }
                }
                elmForm.validator('validate');
                var elmErr = elmForm.find('.has-error');
                var filesCount = $('#certificado_catastral').fileinput('getFilesCount');
                if ((elmErr && elmErr.length > 0) || filesCount === 0) {
                    Swal.fire({
                        type: 'error',
                        title: 'Error.',
                        text: 'Revise los campos antes de pasar al próximo paso.',
                        buttonsStyling: false,
                        confirmButtonClass: 'btn btn-primary',
                        confirmButtonText: 'Aceptar'
                    });
                    return false;
                }
            }
            return true;
        });
        $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
            if (stepNumber === 2) {
                $('#btn-iniciar').removeClass('disabled');
                $('#btn-iniciar').prop('disabled', false);
            } else {
                $('#btn-iniciar').addClass('disabled');
                $('#btn-iniciar').prop('disabled', true);
            }
        });
        $('#btn-iniciar').on('click', function() {
            if (!$(this).hasClass('disabled')) {
                var elmForm = $("#form-tramite");
                if (elmForm) {
                    elmForm.validator('validate');
                    var elmErr = elmForm.find('.has-error');
                    if (elmErr && elmErr.length > 0) {
                        Swal.fire({
                            type: 'error',
                            title: 'Error.',
                            text: 'Revise los campos antes de iniciar la consulta.',
                            buttonsStyling: false,
                            confirmButtonClass: 'btn btn-primary',
                            confirmButtonText: 'Aceptar'
                        });
                        return false;
                    } else {
                        elmForm.submit();
                        return false;
                    }
                }
            }
        });
        $("#otros").fileinput({
            theme: "fa",
            language: "es",
            dropZoneEnabled: false,
            maxFileSize: 8192,
            autoReplace: true,
            required: false,
            maxFileCount: 10,
            showRemove: true,
            removeClass: "btn btn-danger",
            removeIcon: "<i class=\"glyphicon glyphicon-trash\"></i> ",
            showClose: false,
            showUpload: false,
            allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx']
        });
        $('#cuil').inputmask({
            mask: '99-99999999-9',
            removeMaskOnSubmit: true
        });
    });
</script>