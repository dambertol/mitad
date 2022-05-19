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
                <?php echo form_open(uri_string(), 'class="form-horizontal" id="form-cedulas"'); ?>
                <?php echo form_hidden('id', $cedula->id); ?>
                <fieldset class="group-border">
                    <legend class="group-border">Cedula</legend>

                    <div class="form-group">
                        <label for="tipo_documento_id" class="col-sm-2 control-label">Nº Cedula</label>
                        <div class="col-sm-10">
                            <input type="text" value="<?php echo $cedula->n_cedula; ?>" class="form-control" readonly="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notificador_id" class="col-sm-2 control-label">Notificador</label>
                        <div class="col-sm-10">
                            <input type="text" name="notificador_id" value="<?php echo $notificador->id . " - " . $notificador->usuario; ?>"
                                   maxlength="60" id="notificador_id" class="form-control" readonly="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="zona_id" class="col-sm-2 control-label">Zona</label>
                        <div class="col-sm-10">
                            <input type="text" value="<?php echo $zona->descripcion; ?>" maxlength="60"
                                   class="form-control" readonly="">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="zona_id" class="col-sm-2 control-label">Domicilio Major</label>
                        <div class="col-sm-10">
                            <input type="text"
                                   value="<?php echo $domicilio->direccion . " " . $domicilio->num . ", " . $domicilio->localidad . " (CP: " . $domicilio->codigo_postal . " )"; ?>"
                                   maxlength="300"
                                   class="form-control" readonly="">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="domicilio_alternativo" class="col-sm-2 control-label">Domicilio Alternativo</label>
                        <div class="col-sm-10">
                            <input type="text" value="<?php echo $domicilio->alternativo; ?>" maxlength="200"
                                   class="form-control" readonly="">

                        </div>
                    </div>
                </fieldset>

                <fieldset class="group-border">
                    <legend class="group-border">Devolucion</legend>


                    <div class="form-group">
                        <label for="tipo_devolucion_id" class="col-sm-2 control-label">Tipo de Entrega</label>
                        <div class="col-sm-10">
                            <select name="tipo_devolucion_id" id="tipo_devolucion_id" class="form-control selectpicker"
                                    title="-- Seleccionar --"
                                    data-selected-text-format="count>1" data-live-search="true">
                                <?php foreach ($tipos_devoluciones as $tipo): ?>
                                    <option value="<?php echo $tipo->id; ?>"><?php echo $tipo->descripcion; ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cambio_domicilio" class="col-sm-2 control-label">Cambio el Domicilio?</label>
                        <div class="col-sm-10">
                            <select name="cambio_domicilio" id="cambio_domicilio" class="form-control selectpicker"
                                    title="-- Seleccionar --"
                                    data-selected-text-format="count>1" data-live-search="true">
                                <option value="SI">Si cambio el domicilio</option>
                                <option value="NO">NO</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="field_cambio_domicilio" style="display: none;">
                        <label for="cambio_domicilio_text" class="col-sm-2 control-label">Nueva Direccion</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="cambio_domicilio_text"
                                      id="cambio_domicilio_text" disabled />
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="observaciones_devolucion" class="col-sm-2 control-label">Observaciones</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="5" name="observaciones_devolucion"
                                      id="observaciones_devolucion"></textarea>
                        </div>
                    </div>


                </fieldset>

                <div class="ln_solid"></div>
                <div class="text-center">
                    <a href="notificaciones/cedulas/vista_previa_impresion/<?php echo $cedula->id ?>" class="btn btn-info btn-sm">Vista
                        previa Impresion</a>
                    <?php echo form_submit(array('class' => 'btn btn-success btn-sm', 'title' => 'Cargar Entrega'), 'Cargar Entrega'); ?>
                    <!--                    <a href="notificaciones/cedulas/entrega/-->
                    <?php //echo $cedula->id ?><!--" class="btn btn-success btn-sm">Cargar Entrega</a>-->
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
                tipo_devolucion_id: "required",
                observaciones_devolucion: "required",
                cambio_domicilio: "required",
                cambio_domicilio_text: {
                    required: true,
                    minlength: 10
                }

            },
            // Specify validation error messages
            messages: {
                tipo_devolucion_id: "Seleccione un Tipo de Entrega",
                observaciones_devolucion: "Escriba una observacion",
                cambio_domicilio: "Seleccione [ SI | NO ]",
                cambio_domicilio_text: "Escriba una direccion",

            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
            submitHandler: function (form) {
                form.submit();

            }
        });

        $('#cambio_domicilio').change(function (e) {
            console.log($(this).val());
            if ($(this).val() !== "SI") {
                $("#cambio_domicilio_text").prop("disabled", true);
                $("#field_cambio_domicilio").fadeOut(500);
            }
            else {
                $("#cambio_domicilio_text").prop("disabled", false);
                $("#field_cambio_domicilio").fadeIn(500);
            }
        });
    }

</script>

