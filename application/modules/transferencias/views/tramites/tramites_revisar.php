<!--
        /*
         * Vista Revisar Trámite.
         * Autor: Leandro
         * Creado: 25/05/2018
         * Modificado: 23/10/2019 (Leandro)
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
                <h2>Información del Trámite <?php echo $tramite->id; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="form-horizontal">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#tab_actual" aria-controls="tab_actual" role="tab" data-toggle="tab"><i class="fa fa-clock-o"></i> Pases</a></li>
                            <li role="presentation"><a href="#tab_transferencia" aria-controls="tab_transferencia" role="tab" data-toggle="tab"><i class="fa fa-exchange"></i> Transferencia</a></li>
                            <li role="presentation"><a href="#tab_escbribano" aria-controls="tab_escbribano" role="tab" data-toggle="tab"><i class="fa fa-users"></i> Escribano</a></li>
                            <li role="presentation"><a href="#tab_vendedor" aria-controls="tab_vendedor" role="tab" data-toggle="tab"><i class="fa fa-user-times"></i> Transmitente</a></li>
                            <li role="presentation"><a href="#tab_comprador" aria-controls="tab_comprador" role="tab" data-toggle="tab"><i class="fa fa-user-plus"></i> Adquirente</a></li>
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
                                                                <a style="<?php echo $style; ?>" class="btn btn-xs btn-default" data-remote="false" data-toggle="modal" data-target="#remote_modal" href="transferencias/pases/modal_ver/<?= $Pase->id; ?>"><i class="fa fa-search"></i></a>
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
                                            <a href="transferencias/tramites/<?php echo $back_url; ?>" class="btn btn-default btn-sm">Cancelar</a>
                                            <?php echo ($txt_btn === 'Enviar' || $txt_btn === 'Eliminar') ? form_hidden('id', $tramite->id) : ''; ?>
                                        </div>
                                        <?php echo form_close(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_transferencia">	
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
                            <div role="tabpanel" class="tab-pane" id="tab_escbribano">	
                                <br />
                                <div style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                                    <div style="padding:5px 15px;">
                                        <h2 class="text-center">Escribano</h2>
                                    </div>
                                    <?php foreach ($fields_escribano as $field_escribano): ?>
                                        <div class="form-group">
                                            <?php echo $field_escribano['label']; ?> 
                                            <?php echo $field_escribano['form']; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_vendedor">	
                                <br />
                                <?php $cant_v = 1; ?>
                                <?php foreach ($fields_vendedores as $fields_vendedor): ?>
                                    <div id="vendedor_<?php echo $cant_v; ?>" class="vendedor" style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                                        <div style="padding:5px 15px;">
                                            <h2 class="text-center">Transmitente <?php echo $cant_v; ?></h2>
                                        </div>
                                        <?php foreach ($fields_vendedor as $field_vendedor): ?>
                                            <div class="form-group">
                                                <?php echo $field_vendedor['label']; ?> 
                                                <?php echo $field_vendedor['form']; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php $cant_v++; ?>
                                <?php endforeach; ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_comprador">	
                                <br />
                                <?php $cant_c = 1; ?>
                                <?php foreach ($fields_compradores as $fields_comprador): ?>
                                    <div id="comprador_<?php echo $cant_c; ?>" class="comprador" style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                                        <div style="padding:5px 15px;">
                                            <h2 class="text-center">Adquirente <?php echo $cant_c; ?></h2>
                                        </div>
                                        <?php foreach ($fields_comprador as $field_comprador): ?>
                                            <div class="form-group">
                                                <?php echo $field_comprador['label']; ?> 
                                                <?php echo $field_comprador['form']; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php $cant_c++; ?>
                                <?php endforeach; ?>
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
                                                        <a class="btn btn-xs btn-default" data-remote="false" data-toggle="modal" data-target="#remote_modal" href="transferencias/pases/modal_ver/<?= $Pase->id; ?>"><i class="fa fa-search"></i></a>
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
                                <a href="transferencias/tramites/<?php echo $back_url; ?>" class="btn btn-default btn-sm">Cancelar</a>
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
    var cant_v = <?php echo sizeof($fields_vendedores); ?>;
    var cant_c = <?php echo sizeof($fields_compradores); ?>;
    var tramite_id = <?php echo $tramite->id; ?>;
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
    $(document).ready(function() {
        $('#cuil').inputmask({
            mask: '99-99999999-9',
            removeMaskOnSubmit: true
        });
        for (var i = 1; i <= cant_v; i++) {
            $('#cuil_v_' + i).inputmask({
                mask: '99-99999999-9',
                removeMaskOnSubmit: true
            });
        }
        for (var i = 1; i <= cant_c; i++) {
            $('#cuil_c_' + i).inputmask({
                mask: '99-99999999-9',
                removeMaskOnSubmit: true
            });
        }
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
                allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf']
            });
<?php endif; ?>
        $('#destino').on('changed.bs.select', function(e) {
            if (this.value === '11') {
                $('#escritura_nro_p').attr("disabled", false);
                $('#escritura_foja_p').attr("disabled", false);
                $('#escritura_fecha_p').attr("disabled", false);
                $('#escritura_nro_p').closest(".form-group").show();
                $('#escritura_foja_p').closest(".form-group").show();
                $('#escritura_fecha_p').closest(".form-group").show();
            } else if (this.value === '5') {
                $('#escritura_nro_p').attr("disabled", true);
                $('#escritura_foja_p').attr("disabled", true);
                $('#escritura_fecha_p').attr("disabled", true);
                $('#escritura_nro_p').closest(".form-group").hide();
                $('#escritura_foja_p').closest(".form-group").hide();
                $('#escritura_fecha_p').closest(".form-group").hide();
            }
        });
    });
<?php if (!empty($generar_numero) && $generar_numero): ?>
        $(document).ready(function() {
            $("#generar-numero").on("click", generarNumero);
            $("#form-pase").submit(function(event) {
                var destino_id = $("#destino").val();
                if (destino_id === '8' && ($("#transferencia_nro").val() === "" || $("#transferencia_eje").val() === "")) {	// Pago de Deuda y Aforos (HC)
                    Swal.fire({
                        type: 'error',
                        title: 'Error.',
                        text: 'Debe generar el Número de Transferencia.',
                        buttonsStyling: false,
                        confirmButtonClass: 'btn btn-primary',
                        confirmButtonText: 'Aceptar'
                    });
                    event.preventDefault();
                } else if (destino_id === '15' && $("#observaciones").val() === "") {	//Cancelado (HC)
                    Swal.fire({
                        type: 'error',
                        title: 'Error.',
                        text: 'Debe ingresar una justificación en el campo Observaciones para cancelar el trámite.',
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
        function generarNumero() {
            $.ajax({
                type: "POST",
                url: "transferencias/tramites/generar_numero_transferencia",
                dataType: "json",
                data: {tramite_id: tramite_id, csrf_mlc2: csrfData}
            }).done(function(data) {
                if (data['no_data'] === undefined) {
                    $("#transferencia_nro").val(data.numero);
                    $("#transferencia_eje").val(data.ejercicio);
                    $("#generar-numero").remove();
                } else {
                    Swal.fire({
                        type: 'error',
                        title: 'Error.',
                        text: 'Error al generar el Número de Transferencia.',
                        buttonsStyling: false,
                        confirmButtonClass: 'btn btn-primary',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
<?php else: ?>
        $(document).ready(function() {
            $("#form-pase").submit(function(event) {
                var destino_id = $("#destino").val();
                if (destino_id === '15' && $("#observaciones").val() === "") {	//Cancelado (HC)
                    Swal.fire({
                        type: 'error',
                        title: 'Error.',
                        text: 'Debe ingresar una justificación en el campo Observaciones para cancelar el trámite.',
                        buttonsStyling: false,
                        confirmButtonClass: 'btn btn-primary',
                        confirmButtonText: 'Aceptar'
                    });
                    event.preventDefault();
                } else if (destino_id === '11' && ($("#escritura_nro_p").val() === "" || $("#escritura_foja_p").val() === "" || $("#escritura_fecha_p").val() === "")) {	//Verifica Trámite (HC)
                    Swal.fire({
                        type: 'error',
                        title: 'Error.',
                        text: 'Debe ingresar los datos de la Escritura.',
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
<?php endif; ?>
</script>