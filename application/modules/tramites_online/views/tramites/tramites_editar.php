<!--
        /*
         * Vista Revisar Trámite.
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
                <h2>Información del Trámite: <?php echo $tramite->proceso; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php $data_submit = array('id' => 'btn-iniciar', 'class' => 'btn btn-primary btn-sm disabled', 'title' => $txt_btn, 'disabled' => true); ?>
                <?php echo form_open(uri_string(), 'enctype="multipart/form-data" class="form-horizontal" id="form-tramite"'); ?>
                <div id="smartwizard">
                    <ul>
                        <?php foreach ($fields_group as $paso_key => $paso): ?>
                            <li><a href="#paso-<?= $paso_key; ?>"><?= $paso_key; ?>. <?= $paso['nombre']; ?><br/>
                                    <small><?= $paso['subtitulo']; ?></small>
                                </a></li>
                        <?php endforeach; ?>
                        <li><a href="#paso-enviar">Enviar<br/>
                                <small>Enviar datos</small>
                            </a></li>
                    </ul>
                    <div>
                        <?php $step = 0; ?>
                        <?php foreach ($fields_group as $paso_key => $paso): ?>
                            <div id="paso-<?= $paso_key; ?>" class="">
                                <br/>
                                <?php if (!empty($paso['mensaje'])) : ?>
                                    <div class="alert alert-info alert-dismissible fade in" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                    aria-hidden="true">×</span>
                                        </button>
                                        <i class="fa fa-info"></i>IMPORTANTE<br>
                                        <?= $paso['mensaje']; ?>
                                    </div>
                                <?php endif; ?>
                                <div id="form-paso-<?= $step; ?>" role="form" data-toggle="validator">
                                    <?php $cant = 1; ?>
                                    <?php echo form_input(array('name' => "cant_$paso_key", 'type' => 'hidden', 'id' => "cant_$paso_key"), sizeof($paso['allFields'])); ?>
                                    <?php foreach ($paso['allFields'] as $fields): ?>
                                        <div id="<?= "$paso_key-$cant"; ?>" class="<?= $paso_key; ?>"
                                             style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                                            <div style="padding:5px 15px;">
                                                <h2 class="text-center"><?= $paso['nombre']; ?> <span
                                                            id="titulo_<?= $paso_key; ?>_1">1</span></h2>
                                            </div>
                                            <?php foreach ($fields as $Field): ?>
                                                <div class="form-group">
                                                    <?php if (isset($Field['type']) && $Field['type'] == 'h3'): ?>
                                                        <h3 class="text-center">
                                                            <?php echo $Field['value']; ?>
                                                        </h3>
                                                    <?php elseif (isset($Field['type']) && $Field['type'] == 'h4'): ?>
                                                        <div class="col-sm-2"></div>
                                                        <div class="col-sm-10">
                                                            <h4 class="">
                                                                <?php echo $Field['value']; ?>
                                                            </h4>
                                                        </div>
                                                    <?php elseif (isset($Field['type']) && $Field['type'] == 'textarea'): ?>
                                                        <?php echo $Field['label']; ?>
                                                        <div class="col-sm-10">
                                                            <textarea id="<?php echo $Field['id']; ?>" name="<?php echo $Field['name']; ?>"
                                                                      class="<?php echo $Field['class']; ?>" rows="5"></textarea>
                                                        </div>
                                                    <?php else: ?>
                                                        <?php echo $Field['label']; ?>
                                                        <?php echo $Field['form']; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if ($txt_btn === 'Iniciar Trámite' && $paso_key === 'nomenclatura'): ?>
                                                    <div class="form-group">
                                                        <div class="col-sm-offset-2 col-sm-10 text-center">
                                                            <?php echo form_button('cargar_datos', 'Cargar Datos M@jor', array('id' => 'cargar_datos', 'class' => 'btn btn-sm btn-primary')); ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php if ($paso['regla'] === 'Multiple'): ?>
                                                <div class="actions" style="min-height:20px; padding:0px 10px;">
                                                    <button type="button"
                                                            class="agregar-<?= $paso_key; ?> btn btn-success btn-xs pull-right"
                                                            title="Agregar <?= $paso['nombre']; ?>"><i class="fa fa-plus"></i></button>
                                                    <button type="button" class="quitar-<?= $paso_key; ?> btn btn-danger btn-xs pull-right"
                                                            title="Quitar <?= $paso['nombre']; ?>"><i class="fa fa-minus"></i></button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php if ($paso['regla'] === 'Multiple'): ?>
                                        <script>
                                            var regex = /^(.+?)(\d+)(_extra_button)?$/i;
                                            var cloneIndex<?= $paso_key; ?> = $(".<?= $paso_key; ?>").length + 1;
                                            var cant_<?= $paso_key; ?> = <?php echo sizeof($fields); ?>;
                                            $(document).ready(function () {
                                                $(".agregar-<?= $paso_key; ?>").on("click", agregar<?= $paso_key; ?>);
                                                $(".quitar-<?= $paso_key; ?>").on("click", quitar<?= $paso_key; ?>);
                                                toggleQuitar<?= $paso_key; ?>();
                                            });

                                            function agregar<?= $paso_key; ?>() {
                                                $(this).parents(".<?= $paso_key; ?>").clone()
                                                    .appendTo("#form-paso-<?= $step; ?>")
                                                    .attr("id", "<?= $paso_key; ?>-" + cloneIndex<?= $paso_key; ?>)
                                                    .find("*")
                                                    .each(function () {
                                                        var id = this.id || "";
                                                        var match = id.match(regex) || [];
                                                        if (match.length === 4 && (match[3] === undefined || match[3] === '_extra_button')) {
                                                            this.id = match[1] + (cloneIndex<?= $paso_key; ?>);
                                                            if (match[3] === '_extra_button') {
                                                                this.id += match[3];
                                                            }
                                                            this.name = match[1] + (cloneIndex<?= $paso_key; ?>);
                                                            this.value = "";
                                                            if (match[1] === 'titulo_<?= $paso_key; ?>_') {
                                                                this.textContent = cloneIndex<?= $paso_key; ?>;
                                                            }
                                                        }
                                                    })
                                                    .on('click', '.agregar-<?= $paso_key; ?>', agregar<?= $paso_key; ?>)
                                                    .on('click', '.quitar-<?= $paso_key; ?>', quitar<?= $paso_key; ?>);

                                                toggleQuitar<?= $paso_key; ?>();
                                                $('#cant_<?= $paso_key; ?>').val(cloneIndex<?= $paso_key; ?>);
                                                cloneIndex<?= $paso_key; ?>++;
                                            }

                                            function quitar<?= $paso_key; ?>() {
                                                var id = $(this).parents(".<?= $paso_key; ?>").attr("id") || "";
                                                var match = id.match(regex) || [];
                                                if (match.length === 4) {
                                                    $(this).parents(".<?= $paso_key; ?>").remove();
                                                    cloneIndex<?= $paso_key; ?>--;
                                                    renumerar<?= $paso_key; ?>(match[2]);
                                                }
                                                $('#cant_<?= $paso_key; ?>').val(cloneIndex<?= $paso_key; ?> - 1);
                                                toggleQuitar<?= $paso_key; ?>();
                                            }

                                            function renumerar<?= $paso_key; ?>(idDesde) {
                                                var i = idDesde;
                                                $(".<?= $paso_key; ?>").each(function () {
                                                    var id = this.id || "";
                                                    var match = id.match(regex) || [];
                                                    if (match.length === 3) {
                                                        if (match[2] > idDesde) {
                                                            $("#" + id).attr("id", "<?= $paso_key; ?>-" + i)
                                                                .find("*")
                                                                .each(function () {
                                                                    var id = this.id || "";
                                                                    var match = id.match(regex) || [];
                                                                    if (match.length === 3) {
                                                                        this.id = match[1] + (i);
                                                                        this.name = match[1] + (i);
                                                                        if (match[1] === 'titulo_<?= $paso_key; ?>_') {
                                                                            this.textContent = i;
                                                                        }
                                                                    }
                                                                });
                                                            i++;
                                                        }
                                                    }
                                                });
                                            }

                                            function toggleQuitar<?= $paso_key; ?>() {
                                                if ($(".<?= $paso_key; ?>").length === 1) {
                                                    $('.quitar-<?= $paso_key; ?>').hide();
                                                } else {
                                                    $('.quitar-<?= $paso_key; ?>').show();
                                                }
                                            }
                                        </script>
                                    <?php endif; ?>
                                        <?php $cant++; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php $step++; ?>
                        <?php endforeach; ?>
                        <div id="paso-enviar" class="">
                            <br/>
                            <div id="form-paso-enviar" role="form" data-toggle="validator">
                                <div style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                                    <div style="padding:5px 15px;">
                                        <h2 class="text-center">Enviar</h2>
                                    </div>

                                    <?php if (!empty($ultimo_pase->mensaje_destino)): ?>
                                        <div class="alert alert-info alert-dismissible fade in" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                        aria-hidden="true">×</span>
                                            </button>
                                            <i class="fa fa-info"></i>INFORMACIÓN<br>
                                            <?php echo $ultimo_pase->mensaje_destino; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div style="padding:5px 15px;">
                                        <h2 class="text-center">Enviar Pase</h2>
                                    </div>
                                    <?php $data_submit = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
                                    <?php foreach ($fields_pase as $field_pase): ?>
                                        <div class="form-group">
                                            <?php echo $field_pase['label']; ?>
                                            <?php echo $field_pase['form']; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ln_solid"></div>
            <div class="text-center">
                <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                <a href="tramites_online/tramites/<?php echo $back_url; ?>" class="btn btn-default btn-sm">Cancelar</a>
                <?php echo ($txt_btn === 'Enviar' || $txt_btn === 'Editar') ? form_hidden('id', $tramite->id) : ''; ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Historial de Pases del Trámite <?php echo $tramite->id; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">

                <div class="form-horizontal">
                    <table class="table table-hover table-bordered table-condensed table-striped dt-responsive dataTable no-footer dtr-inline"
                           role="grid">
                        <thead>
                        <tr>
                            <th style="width:12%;">Fecha</th>
                            <th style="width:24%;">Origen</th>
                            <th style="width:24%;">Destino</th>
                            <th style="width:34%;">Observaciones</th>
                            <th style="width:6%;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($pases)): ?>
                            <?php $cant_pases = 0; ?>
                            <?php $style = 'color:black;'; ?>
                            <?php foreach ($pases as $Pase): ?>
                                <?php $cant_pases++; ?>
                                <?php if ($cant_pases === sizeof($pases)): ?>
                                    <?php $style = 'color:#DEC10E; font-weight:bold;'; ?>
                                <?php endif; ?>
                                <?php if ($Pase->estado_destino === 'Finalizado ()'): ?>
                                    <?php $style = 'color:green; font-weight:bold;'; ?>
                                    <?php endif; ?>
                                <?php if ($Pase->estado_destino === 'Cancelado ()'): ?>
                                    <?php $style = 'color:red; font-weight:bold;'; ?>
                                    <?php endif; ?>
                                <tr style="<?php echo $style; ?>">
                                    <td><?= empty($Pase->fecha_inicio) ? '' : date_format(new DateTime($Pase->fecha_inicio), 'd/m/Y H:i:s'); ?></td>
                                    <td><?= substr($Pase->estado_origen,strpos($Pase->estado_origen, "-")+1); ?></td>
                                    <td><?= substr($Pase->estado_destino,strpos($Pase->estado_destino, "-")+1); ?></td>
                                    <td><?= $Pase->observaciones; ?></td>
                                    <td>
                                        <a style="<?php echo $style; ?>" class="btn btn-xs btn-default" data-remote="false"
                                           data-toggle="modal" data-target="#remote_modal"
                                           href="tramites_online/pases/modal_ver/<?= $Pase->id; ?>"><i class="fa fa-search"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">-- Sin pases --</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?php echo(!empty($audi_modal) ? $audi_modal : ''); ?>
<script>
    var tramite_id = <?php echo $tramite->id; ?>;
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
    $(document).ready(function () {
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
        $("#smartwizard").on("leaveStep", function (e, anchorObject, stepNumber, stepDirection) {
            var elmForm = $("#form-paso-" + stepNumber);
            if (stepDirection === 'forward' && elmForm) {
                elmForm.validator('validate');
                var elmErr = elmForm.find('.has-error');
                if (elmErr && elmErr.length > 0) {
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
        // TODO: Modificar para forms de 1 solo paso
        $("#smartwizard").on("showStep", function (e, anchorObject, stepNumber, stepDirection) {
            if (stepNumber === <?= sizeof($fields_group) - 1; ?>) {
                $('#btn-iniciar').removeClass('disabled');
                $('#btn-iniciar').prop('disabled', false);
            } else {
                $('#btn-iniciar').addClass('disabled');
                $('#btn-iniciar').prop('disabled', true);
            }
        });
        $('#btn-iniciar').on('click', function () {
            if (!$(this).hasClass('disabled')) {
                var elmForm = $("#form-tramite");
                if (elmForm) {
                    elmForm.validator('validate');
                    var elmErr = elmForm.find('.has-error');
                    if (elmErr && elmErr.length > 0) {
                        Swal.fire({
                            type: 'error',
                            title: 'Error.',
                            text: 'Revise los campos antes de iniciar el trámite.',
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
    });
</script>
