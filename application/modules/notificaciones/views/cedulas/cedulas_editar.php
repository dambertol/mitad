<!--
	/*
	 * Vista Solicitar Cédula.
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 12/07/2019 (Pablo)
	 */
-->
<style> legend.group-border {
        width: inherit;
        /* Or auto */
        padding: 0 10px;
        /* To give a bit of padding on the left and right */
        border-bottom: none;
        margin-bottom: 0px;
    }

    fieldset.group-border {
        border: 1px groove #ddd !important;
        padding: 0 1.4em 1.4em 1.4em !important;
        margin: 0 0 1.5em 0 !important;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
    }
</style>

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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Cédulas'; ?></h2>
                <?php echo anchor('uploads/notificaciones/modelo_cedula.doc', 'Descargar Modelo Cedula', 'class="btn btn-info btn-sm" target="_blank"') ?>
                <?php if (!empty($audi_modal)): ?>
                    <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal"
                            data-target="#audi-modal">
                        <i class="fa fa-info-circle"></i>
                    </button>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php $data_submit = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
                <?php echo form_open(uri_string(), 'class="form-horizontal"'); ?>

                <?php //dd($cedula);?>


                <fieldset class="group-border">
                    <legend class="group-border">Destinatario</legend>
                    <div class="row">

                        <div class="form-group">
                            <label for="domicilio_alternativo" class="col-sm-2 control-label">Domicilio Actualizado</label>
                            <div class="col-sm-10">
                                <input type="text" name="domicilio_alternativo" value="<?php echo $domicilio->alternativo ?>"
                                       maxlength="200" id="domicilio_alternativo" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tipo_identificacion" class="col-sm-2 control-label">Tipo Identificaci&oacute;n</label>
                            <div class="col-sm-10">
                                <select name="tipo_identificacion" id="tipo_identificacion" class="form-control">
                                    <option value="1" <?php echo ($destinatario->tipo_identificacion == '1') ? "selected" : "" ?>>DNI</option>
                                    <option value="2" <?php echo ($destinatario->tipo_identificacion == '2') ? "selected" : "" ?>>L.C.</option>
                                    <option value="3" <?php echo ($destinatario->tipo_identificacion == '3') ? "selected" : "" ?>>L.E.</option>
                                    <option value="4" <?php echo ($destinatario->tipo_identificacion == '4') ? "selected" : "" ?>>L.F.</option>
                                    <option value="5" <?php echo ($destinatario->tipo_identificacion == '5') ? "selected" : "" ?>>PASAPORTE</option>
                                    <option value="6" <?php echo ($destinatario->tipo_identificacion == '6') ? "selected" : "" ?>>C.U.I.T.</option>
                                    <option value="7" <?php echo ($destinatario->tipo_identificacion == '7') ? "selected" : "" ?>>EXTRANJEROS</option>
                                    <option value="9" <?php echo ($destinatario->tipo_identificacion == '9') ? "selected" : "" ?>>SIN DOCUMENTO</option>
                                    <option value="10" <?php echo ($destinatario->tipo_identificacion == '10') ? "selected" : "" ?>>C.I.</option>
                                    <option value="11" <?php echo ($destinatario->tipo_identificacion == '11') ? "selected" : "" ?>>C.E.</option>
                                    <option value="12" <?php echo ($destinatario->tipo_identificacion == '12') ? "selected" : "" ?>>C.F.</option>
                                    <option value="13" <?php echo ($destinatario->tipo_identificacion == '13') ? "selected" : "" ?>>C.I.F.</option>
                                    <option value="14" <?php echo ($destinatario->tipo_identificacion == '14') ? "selected" : "" ?>>C.U.I.L.</option>
                                    <option value="15" <?php echo ($destinatario->tipo_identificacion == '15') ? "selected" : "" ?>>EXTRANJEROS NO RESIDENTES</option>
                                    <option value="20" <?php echo ($destinatario->tipo_identificacion == '20') ? "selected" : "" ?>>ORGANISMOS OFICIALES</option>
                                    <option value="30" <?php echo ($destinatario->tipo_identificacion == '30') ? "selected" : "" ?>>VERIFICADORES ESPECIALES</option>

                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="n_identificacion" class="col-sm-2 control-label">Num Identificaci&oacute;n</label>
                            <div class="col-sm-10">
                                <input type="text" name="n_identificacion" maxlength="11" id="n_identificacion" class="form-control"
                                       pattern="^(0|[1-9][0-9]*)$" title="Debe ingresar sólo números"
                                       value="<?php echo $destinatario->n_identificacion ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10 text-center">
                                <button name="cargar_datos" type="button" id="cargar_datos"
                                        class="btn btn-sm btn-primary">Buscar Datos en M@jor
                                </button>
                            </div>
                        </div>

                        <hr/>

                        <div class="form-group">
                            <label for="nombre" class="col-sm-2 control-label">Nombre</label>
                            <div class="col-sm-10">
                                <input type="text" name="nombre" value="<?php echo $destinatario->nombre ?>" maxlength="60" id="nombre"
                                       class="form-control" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="apellido" class="col-sm-2 control-label">Apellido</label>
                            <div class="col-sm-10">
                                <input type="text" name="apellido" value="<?php echo $destinatario->apellido ?>" maxlength="60"
                                       id="apellido" class="form-control" readonly="">
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="domicilio" class="col-sm-2 control-label">Domicilio</label>
                            <div class="col-sm-10">
                                <input type="text" name="domicilio" value="<?php echo $domicilio->direccion ?>" maxlength="60"
                                       id="domicilio" class="form-control" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="altura_domiicilio" class="col-sm-2 control-label">Altura Domicilio</label>
                            <div class="col-sm-10">
                                <input type="text" name="altura_domiicilio" value="<?php echo $domicilio->num ?>" maxlength="60"
                                       id="altura_domiicilio"
                                       class="form-control" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="localidad" class="col-sm-2 control-label">Localidad</label>
                            <div class="col-sm-10">
                                <input type="text" name="localidad" value="<?php echo $domicilio->localidad ?>" maxlength="60"
                                       id="localidad" class="form-control" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="codigo_postal" class="col-sm-2 control-label">Codigo Postal</label>
                            <div class="col-sm-10">
                                <input type="text" name="codigo_postal" value="<?php echo $domicilio->codigo_postal ?>" maxlength="60"
                                       id="codigo_postal" class="form-control"
                                       readonly="">
                            </div>
                        </div>


                    </div>
                </fieldset>

                <fieldset class="group-border">
                    <legend class="group-border">Cedula</legend>

                    <div class="row">
                        <div class="form-group">
                            <label for="oficina_id" class="col-sm-2 control-label">Oficina Origen</label>
                            <div class="col-sm-10">
                                <select name="oficina_id" id="oficina_id" class="form-control selectpicker" title="-- Seleccionar --"
                                        data-selected-text-format="count>1" data-live-search="true">
                                    <?php foreach ($areas as $area): ?>
                                        <option value="<?php echo $area->id; ?>" <?php echo ($area->id == $cedula->oficina_id) ? "selected" : "" ?> ><?php echo $area->codigo . " - " . $area->nombre; ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo $fields['n_cedula']['label']; ?>
                            <?php echo $fields['n_cedula']['form']; ?>
                        </div>

                        <!-- Prioridad -->
                        <div class="form-group">
                            <label for="prioridad" class="col-sm-2 control-label">Prioridad</label>
                            <div class="col-sm-10">
                                <select name="prioridad" id="prioridad" class="form-control selectpicker"
                                        title="-- Seleccionar --" data-selected-text-format="count>1" data-live-search="true">
                                    <option value="14" <?php echo (14 == $cedula->prioridad) ? "selected" : "" ?> >Baja (14 dias)</option>
                                    <option value="7" <?php echo (7 == $cedula->prioridad) ? "selected" : "" ?>>Media (7 dias)</option>
                                    <option value="1" <?php echo (1 == $cedula->prioridad) ? "selected" : "" ?>>URGENTE (24 hs)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Fecha Probable de Entrega -->
                        <div class="form-group">
                            <?php echo $fields['fecha_probable_entrega']['label']; ?>
                            <?php echo $fields['fecha_probable_entrega']['form']; ?>
                        </div>

                        <!-- Tipo Documento -->
                        <div class="form-group">
                            <label for="tipo_documento_id" class="col-sm-2 control-label">Tipo Documento</label>
                            <div class="col-sm-10">
                                <select name="tipo_documento_id" id="tipo_documento_id" class="form-control selectpicker"
                                        title="-- Seleccionar --" data-selected-text-format="count>1" data-live-search="true">
                                    <?php foreach ($tipos_documentos as $tp): ?>
                                        <option value="<?php echo $tp->id; ?>" <?php echo ($tp->id == $cedula->tipo_doc_id) ? "selected" : "" ?> ><?php echo $tp->descripcion; ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo $fields['n_documento']['label']; ?>
                            <?php echo $fields['n_documento']['form']; ?>
                        </div>
                        <div class="form-group">
                            <?php echo $fields['anio']['label']; ?>
                            <?php echo $fields['anio']['form']; ?>
                        </div>
                        <div class="form-group">
                            <?php echo $fields['texto']['label']; ?>
                            <?php echo $fields['texto']['form']; ?>
                        </div>
                        <div class="form-group">
                            <?php echo $fields['observaciones']['label']; ?>
                            <?php echo $fields['observaciones']['form']; ?>
                        </div>

                        <?php echo $adjuntos_view; ?>
                    </div>
                </fieldset>
                <div class="ln_solid"></div>
                <div class="text-center">
                    <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                    <?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $cedula->id) : ''; ?>
                    <a href="notificaciones/cedulas/listar" class="btn btn-default btn-sm">Cancelar</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php echo(!empty($audi_modal) ? $audi_modal : ''); ?>


<!-- Error Messages -->
<div id="validation-alert" class="alert alert-danger alert-dismissible fade in alert-fixed" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <strong>ERROR!</strong>
    <div id="alert-message"></div>
</div>

<script src="js/notificaciones/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    $(document).ready()
    {

        $("#validation-alert").hide();

        var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';

        $("#cargar_datos").click(function () {
            buscar_destinatario($("#tipo_identificacion :selected").val(), $("#n_identificacion").val());
        });

        function buscar_destinatario(tipo, legajo) {
            if (legajo.length > 6 && (legajo.match(/^[0-9]+$/) !== null)) {
                $.ajax({
                    type: "POST",
                    url: "notificaciones/cedulas/buscar",
                    dataType: "json",
                    data: {tipo: tipo, legajo: legajo, csrf_mlc2: csrfData}
                }).done(function (data) {
                    console.log(data);
                    var destinatario = null;
                    if (data['error'] === undefined) {
                        destinatario = data.destinatario;
                        $("#nombre").val(destinatario['pers_Nombre']);
                        $("#apellido").val(destinatario['pers_Apellido']);
                        $("#domicilio").val(destinatario['pers_Calle']);
                        $("#altura_domiicilio").val(destinatario['pers_Altura']);
                        $("#localidad").val(destinatario['pers_Localidad']);
                        $("#codigo_postal").val(destinatario['pers_CodigoPostal']);
                    } else {
                        limpiar_destinatario();
                        showErrorAlert("No se encontro ningun registro");
                    }
                });
            } else {
                showErrorAlert("NO cumple con los caracteres minimos (7)");
                limpiar_destinatario();
            }
        }


        function limpiar_destinatario() {
            $("#nombre").val("");
            $("#apellido").val("");
            $("#domicilio").val("");
            $("#altura_domiicilio").val("");
            $("#localidad").val("");
            $("#codigo_postal").val("");
        }

        //      $("#oficina_id").select();

        function showErrorAlert(msg) {
            $("#alert-message").html(msg);
            $("#validation-alert").show();
            setTimeout(function () {
                $("#validation-alert").hide();
            }, 2000);

        }

        tinymce.init({
            selector: '#texto',
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons',
            menubar: false,
        });

    }
    ;
</script>

<script>
    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox({
            alwaysShowClose: true
        });
    });

    function eliminar_adjunto(adjunto_id, adjunto_nombre, cedula_id) {
        //var result = undefined;
        var name = 'adjunto_eliminar';
        if (cedula_id !== undefined) {
            name = 'adjunto_eliminar_existente';
        }

        Swal.fire({
            title: 'Confirmar',
            text: "Se eliminará el adjunto",
            type: 'info',
            showCloseButton: true,
            showCancelButton: true,
            focusCancel: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-primary',
            cancelButtonClass: 'btn btn-default',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
                if (result.value) {
                    //$('#adjuntos-container').append("<input type='hidden' name='" + name + "[" + adjunto_id + "]' value='" + adjunto_nombre + "'>");
                    $('#adjunto_' + adjunto_id).remove();

                    $.ajax({
                        type: "POST",
                        url: "notificaciones/adjuntos/eliminar/" + adjunto_id,
                        dataType: "json",
                        data: {adjunto_id: adjunto_id, nombre: adjunto_nombre, cedula_id: cedula_id, csrf_mlc2: csrfData}
                    }).done(function (data) {
                        console.log(data);
                    });
                }
            }
        );
    }

</script>