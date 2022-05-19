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
                <?php echo form_open(uri_string(), 'class="form-horizontal" id="form-cedulas"'); ?>
                <?php echo form_hidden('id', $cedula->id) ?>

                <!-- Tab links -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item active">
                        <a class="nav-link" id="cedula-tab" data-toggle="tab" href="#cedula" role="tab" aria-controls="cedula"
                           aria-selected="true">Cedula</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="destinatario-tab" data-toggle="tab" href="#destinatario" role="tab"
                           aria-controls="destinatario" aria-selected="true">Destinatario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="notificador-tab" data-toggle="tab" href="#notificador" role="tab"
                           aria-controls="notificador" aria-selected="false">Notificador</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="entrega-tab" data-toggle="tab" href="#entrega" role="tab" aria-controls="entregas"
                           aria-selected="false">Entrega</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="movimientos-tab" data-toggle="tab" href="#movimientos" role="tab"
                           aria-controls="movimientos" aria-selected="false">Movimientos</a>
                    </li>
                </ul>
                <!-- Tab content -->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane active in" id="cedula" role="tabpanel" aria-labelledby="cedula-tab">
                        <fieldset class="group-border">
                            <legend class="group-border">Cedula</legend>

                            <div class="row">
                                <div class="form-group">
                                    <label for="estado_id" class="col-sm-2 control-label">Estado Actual</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="estado_id" value="<?php echo $estado->descripcion; ?>" maxlength="60"
                                               id="estado_id" class="form-control"
                                               readonly="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="oficina_id" class="col-sm-2 control-label">Oficina Origen</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="oficina_id"
                                               value="<?php echo $oficina->codigo . " - " . $oficina->nombre; ?>"
                                               maxlength="60" id="oficina_id" class="form-control"
                                               readonly="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <?php echo $fields['n_cedula']['label']; ?>
                                    <?php echo $fields['n_cedula']['form']; ?>
                                </div>

                                <!-- Tipo Documento -->
                                <div class="form-group">
                                    <label for="tipo_documento_id" class="col-sm-2 control-label">Tipo Documento</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="tipo_documento_id" value="<?php echo $tipo_documento->descripcion; ?>"
                                               maxlength="60" id="tipo_documento_id" class="form-control"
                                               readonly="">
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


                            </div>

                        </fieldset>

                    </div>
                    <div class="tab-pane" id="destinatario" role="tabpanel" aria-labelledby="destinatario-tab">
                        <fieldset class="group-border">
                            <legend class="group-border">Destinatario</legend>
                            <div class="row">

                                <div class="form-group">
                                    <label for="domicilio_alternativo" class="col-sm-2 control-label">Domicilio Actualizado</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="domicilio_alternativo" value="<?php echo $domicilio->alternativo ?>"
                                               maxlength="200" id="domicilio_alternativo" class="form-control" readonly="">
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="tipo_identificacion" class="col-sm-2 control-label">Tipo Identificaci&oacute;n</label>
                                    <div class="col-sm-10">
                                        <select name="tipo_identificacion" id="tipo_identificacion" class="form-control" readonly="">
                                            <option value="1" <?php echo $destinatario->tipo_identificacion == "1" ? "selected" : "" ?> >DNI
                                            </option>
                                            <option value="6" <?php echo $destinatario->tipo_identificacion == "6" ? "selected" : "" ?> >
                                                CUIL/CUIT
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="n_identificacion" class="col-sm-2 control-label">Num Identificaci&oacute;n</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="n_identificacion" value="<?php echo $destinatario->n_identificacion ?>"
                                               maxlength="11" id="n_identificacion"
                                               class="form-control" pattern="^(0|[1-9][0-9]*)$" title="Debe ingresar sólo números"
                                               readonly="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nombre" class="col-sm-2 control-label">Nombre</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="nombre" value="<?php echo $destinatario->nombre ?>" maxlength="60"
                                               id="nombre"
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
                                        <input type="text" name="codigo_postal" value="<?php echo $domicilio->codigo_postal ?>"
                                               maxlength="60"
                                               id="codigo_postal" class="form-control" readonly="">
                                    </div>
                                </div>

                            </div>
                        </fieldset>
                    </div>

                    <div class="tab-pane" id="notificador" role="tabpanel" aria-labelledby="notificador-tab">
                        <fieldset class="group-border">
                            <legend class="group-border">Notificador</legend>
                            <div class="row">

                                <div class="form-group">
                                    <label for="zona_id" class="col-sm-2 control-label">Zona</label>
                                    <div class="col-sm-10">
                                        <select name="zona_id" id="zona_id" class="form-control selectpicker" title="-- Seleccionar --"
                                                data-selected-text-format="count>1" data-live-search="true">
                                            <?php foreach ($zonas as $zona): ?>
                                                <option value="<?php echo $zona->id; ?>" <?php echo ($zona->id == $cedula->zona_id) ? "selected" : "" ?> ><?php echo " - " . $zona->descripcion; ?> </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="notificador_id" class="col-sm-2 control-label">Notificador</label>
                                    <div class="col-sm-10">
                                        <select name="notificador_id" id="notificador_id" class="form-control selectpicker"
                                                title="-- Seleccionar --"
                                                data-selected-text-format="count>1" data-live-search="true">
                                            <?php foreach ($notificadores as $notificador): ?>
                                                <option value="<?php echo $notificador->id; ?>" <?php echo ($notificador->id == $cedula->notificador_id) ? "selected" : "" ?> >
                                                    <?php echo $notificador->id . " - " . $notificador->usuario; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="notificador_suplente_id" class="col-sm-2 control-label">Notificador Suplente</label>
                                    <div class="col-sm-10">
                                        <select name="notificador_suplente_id" id="notificador_suplente_id" class="form-control selectpicker"
                                                title="-- Seleccionar --"
                                                data-selected-text-format="count>1" data-live-search="true">
                                            <option value="0" <?php echo ($cedula->notificador_suplente_id == NULL) ? "selected" : "" ?> >SIN ASIGNAR</option>
                                            <?php foreach ($notificadores as $notificador): ?>
                                                <option value="<?php echo $notificador->id; ?>" <?php echo ($notificador->id == $cedula->notificador_suplente_id) ? "selected" : "" ?> >
                                                    <?php echo $notificador->id . " - " . $notificador->usuario; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>


                <div class="ln_solid"></div>
                <div class="text-center">
                    <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                    <?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $cedula->id) : ''; ?>
                    <a href="notificaciones/cedulas/ver/<?php echo $cedula->id; ?>" class="btn btn-default btn-sm">Cancelar</a>
                    <a href="notificaciones/cedulas/listar" class="btn btn-default btn-sm">Volver al listado</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php echo(!empty($audi_modal) ? $audi_modal : ''); ?>

<script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>

<style>
    form .error {
        color: #ff0000;
    }
</style>

<script type="text/javascript">
    $(document).ready()
    {
        var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';

        $("#cargar_datos").click(function () {
            console.log("combo:" + $("#tipo_identificacion :selected").val());
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
                        console.log("No se encontro ningun registro");
                        $("#nombre").val("");
                        $("#apellido").val("");
                        $("#domicilio").val("");
                        $("#altura_domiicilio").val("");
                        $("#localidad").val("");
                        $("#codigo_postal").val("");
                    }
                });
            } else {
                var msg = "Error, no cumple con los caracteres ";
                alert(msg);
                console.log(msg);
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


        var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        if (window.location.hash) {
            setTimeout(function () {
                console.log(hash);
                $('#' + hash).click();
            }, 1);
        }


        $("#form-cedulas").validate({
            // Specify validation rules
            rules: {
                // The key name on the left side is the name attribute
                // of an input field. Validation rules are defined
                // on the right side
                notificador_id: "required",
                zona_id: "required",

            },
            // Specify validation error messages
            messages: {
                notificador_id: "Seleccione un Notificador",
                zona_id: "Seleccione una Zona",

            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
            submitHandler: function (form) {
                form.submit();

            }
        });
    }

</script>
