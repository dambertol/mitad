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
                <h2>Información de la Consulta <?php echo $tramite->id; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="form-horizontal">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#tab_actual" aria-controls="tab_actual" role="tab" data-toggle="tab"><i class="fa fa-clock-o"></i> Pases</a></li>
                            <li role="presentation"><a href="#tab_tramite" aria-controls="tab_tramite" role="tab" data-toggle="tab"><i class="fa fa-exchange"></i> Consulta</a></li>
                            <li role="presentation"><a href="#tab_persona" aria-controls="tab_persona" role="tab" data-toggle="tab"><i class="fa fa-users"></i> Persona</a></li>
                            <li role="presentation"><a href="#tab_inmueble" aria-controls="tab_inmueble" role="tab" data-toggle="tab"><i class="fa fa-building"></i> Inmueble</a></li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab_actual">
                                <br />
                                <div style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                                    <div style="padding:5px 15px;">
                                        <h2 class="text-center">Historial de Pases</h2>
                                        <table class="table table-hover table-bordered table-condensed table-striped dt-responsive dataTable no-footer dtr-inline" role="grid">
                                            <thead>
                                                <tr>
                                                    <th style="width:14%;">Fecha</th>
                                                    <th style="width:24%;">Origen</th>
                                                    <th style="width:24%;">Destino</th>
                                                    <th style="width:34%;">Observaciones</th>
                                                    <th style="width:4%;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($pases)): ?>
                                                    <?php $cant_pases = 0; ?>
                                                    <?php $style = 'color:black;'; ?>
                                                    <?php foreach ($pases as $Pase): ?>
                                                        <?php $cant_pases++; ?>
                                                        <?php if ($cant_pases === sizeof($pases)): ?>
                                                            <?php $style = 'color:red; font-weight:bold;'; ?>
                                                        <?php endif; ?>
                                                        <tr style="<?php echo $style; ?>">
                                                            <td><?= empty($Pase->fecha) ? '' : date_format(new DateTime($Pase->fecha), 'd/m/Y H:i:s'); ?></td>
                                                            <td><?= $Pase->estado_origen; ?></td>
                                                            <td><?= $Pase->estado_destino; ?></td>
                                                            <td><?= $Pase->observaciones; ?></td>
                                                            <td>
                                                                <a style="<?php echo $style; ?>" class="btn btn-xs btn-default" data-remote="false" data-toggle="modal" data-target="#remote_modal" href="tramites_online/pases/modal_ver/<?= $Pase->id; ?>"><i class="fa fa-search"></i></a>
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
                                <?php if (!empty($fields_pase)): ?>
                                    <?php if (!empty($ultimo_pase->mensaje_destino)): ?>
                                        <div class="alert alert-info alert-dismissible fade in" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                                            </button>
                                            <i class="fa fa-info"></i>INFORMACIÓN<br>
                                            <?php echo $ultimo_pase->mensaje_destino; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                                        <div style="padding:5px 15px;">
                                            <h2 class="text-center">Enviar Pase</h2>
                                        </div>
                                        <?php $data_submit = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
                                        <?php echo form_open(uri_string(), 'enctype="multipart/form-data" class="form-horizontal" id="form-pase"'); ?>
                                        <?php foreach ($fields_pase as $field_pase): ?>
                                            <div class="form-group">
                                                <?php echo $field_pase['label']; ?> 
                                                <?php echo $field_pase['form']; ?>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php foreach ($fields_adjunto_pase as $field_adjunto_pase): ?>
                                            <div class="form-group">
                                                <?php echo $field_adjunto_pase['label']; ?> 
                                                <?php echo $field_adjunto_pase['form']; ?>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="text-center">
                                            <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                                            <a href="tramites_online/tramites/<?php echo $back_url; ?>" class="btn btn-default btn-sm">Cancelar</a>
                                            <?php echo ($txt_btn === 'Enviar' || $txt_btn === 'Eliminar') ? form_hidden('id', $tramite->id) : ''; ?>
                                        </div>
                                        <?php echo form_close(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_tramite">	
                                <br />
                                <div style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                                    <div style="padding:5px 15px;">
                                        <h2 class="text-center">Transferencia</h2>
                                    </div>
                                    <?php if (!empty($generar_numero) && $generar_numero): ?>
                                        <div class="form-group text-center">
                                            <button type="button" class="btn btn-success" id="generar-numero" title="Generar N° de Transferencia">
                                                Generar Número de Transferencia
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    <?php foreach ($fields_tramite as $field_tramite): ?>
                                        <div class="form-group">
                                            <?php echo $field_tramite['label']; ?> 
                                            <?php echo $field_tramite['form']; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (!empty($adjuntos_tramite)): ?>
                                        <?php foreach ($adjuntos_tramite as $Adjunto_tramite): ?>
                                            <div class="form-group">
                                                <label for="<?php echo $Adjunto_tramite->id; ?>" class="col-sm-2 control-label"><?php echo $Adjunto_tramite->tipo; ?></label> 
                                                <div class="col-sm-10">
                                                    <div class="control-label left">
                                                        <?php echo anchor_popup($Adjunto_tramite->ruta . $Adjunto_tramite->nombre, 'Ver Archivo'); ?>
                                                    </div>
                                                </div>								
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_persona">	
                                <br />
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
                            <div role="tabpanel" class="tab-pane" id="tab_inmueble">	
                                <br />
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
                            <div role="tabpanel" class="tab-pane" id="tab_pases">	
                                <br />
                                <table class="table table-hover table-bordered table-condensed table-striped dt-responsive dataTable no-footer dtr-inline" role="grid">
                                    <thead>
                                        <tr>
                                            <th style="width:18%;">Fecha</th>
                                            <th style="width:36%;">Origen</th>
                                            <th style="width:36%;">Destino</th>
                                            <th style="width:10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($pases)): ?>
                                            <?php foreach ($pases as $Pase): ?>
                                                <tr>
                                                    <td><?= empty($Pase->fecha) ? '' : date_format(new DateTime($Pase->fecha), 'd/m/Y H:i:s'); ?></td>
                                                    <td><?= $Pase->estado_origen; ?></td>
                                                    <td><?= $Pase->estado_destino; ?></td>
                                                    <td>
                                                        <a class="btn btn-xs btn-default" data-remote="false" data-toggle="modal" data-target="#remote_modal" href="tramites_online/pases/modal_ver/<?= $Pase->id; ?>"><i class="fa fa-search"></i></a>
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
                        <?php if (empty($txt_btn)): ?>
                            <div class="ln_solid"></div>
                            <div class="text-center">
                                <a href="tramites_online/tramites/<?php echo $back_url; ?>" class="btn btn-default btn-sm">Cancelar</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>
<script>
    var tramite_id = <?php echo $tramite->id; ?>;
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
    $(document).ready(function() {
        $('#cuil').inputmask({
            mask: '99-99999999-9',
            removeMaskOnSubmit: true
        });
<?php if (!empty($fields_pase)) : ?>
            $("#adjuntos").fileinput({
                theme: "fa",
                language: "es",
                dropZoneEnabled: false,
                maxFileSize: 8192,
                autoReplace: true,
                maxFileCount: 10,
                showRemove: true,
                removeClass: "btn btn-danger",
                removeIcon: "<i class=\"glyphicon glyphicon-trash\"></i> ",
                showClose: false,
                showUpload: false,
                allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx']
            });
<?php endif; ?>
    });
    $(document).ready(function() {
        $("#form-pase").submit(function(event) {
            var destino_id = $("#destino").val();
            if (destino_id === '3' && $("#observaciones").val() === "") {	//Cancelado (HC)
                Swal.fire({
                    type: 'error',
                    title: 'Error.',
                    text: 'Debe ingresar una justificación en el campo Observaciones para cancelar la consulta.',
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'Aceptar'
                });
                event.preventDefault();
            } else {
                $("#form-pase").data('submitted', false);
                return true;
            }
        });
    });
</script>