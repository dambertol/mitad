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
                        <label for="n_identificacion" class="col-sm-2 control-label">Num Identificaci&oacute;n</label>
                        <div class="col-sm-10">
                            <input type="text" name="n_identificacion"
                                   value=" <?php echo $destinatario->tipo_identificacion == "1" ? "DNI" : "CUIL/CUIT" ?>: <?php echo $destinatario->n_identificacion ?>"
                                   id="n_identificacion" class="form-control" readonly="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label">Nombre</label>
                        <div class="col-sm-10">
                            <input type="text" name="nombre" value="<?php echo $destinatario->nombre ?>" maxlength="60"
                                   id="nombre" class="form-control" readonly="">
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
                        <label for="zona_id" class="col-sm-2 control-label">Domicilio Major</label>
                        <div class="col-sm-10">
                            <input type="text"
                                   value="<?php echo $domicilio->direccion . " " . $domicilio->num . ", " . $domicilio->localidad . " (CP: " . $domicilio->codigo_postal . " )"; ?>"
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
                    <legend class="group-border">Causas de la Devolucion de la Cedula erronea</legend>
                    <br>
                    <div class="form-group">
                        <label for="tipo_devolucion_id" class="col-sm-2 control-label">Motivo de la devolucion</label>
                        <div class="col-sm-10">
                            <select name="tipo_devolucion_id" id="tipo_devolucion_id" class="form-control selectpicker"
                                    title="-- Seleccionar --"
                                    data-selected-text-format="count>1" data-live-search="true">
                                <option value="1">Destinatario Erroneo</option>
                                <option value="2">Domicilio Inexistente</option>
                                <option value="other_reason">Otro motivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="field_otro_motivo_devolucion" style="display: none;">
                        <label for="otro_motivo_devolucion" class="col-sm-2 control-label">Otro Motivo</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="5" name="otro_motivo_devolucion"
                                      id="otro_motivo_devolucion" disabled></textarea>
                        </div>
                    </div>


                </fieldset>

                <div class="ln_solid"></div>
                <div class="text-center">
                    <?php echo form_submit(array('class' => 'btn btn-warning btn-sm', 'title' => 'Realizar Devolucion'), 'Realizar Devolucion'); ?>
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
                otro_motivo_devolucion: {
                    required: true,
                    minlength: 10
                }
            },
            // Specify validation error messages
            messages: {
                tipo_devolucion_id: "Seleccione un Motivo de Devolucion",
                otro_motivo_devolucion: "Escriba una Observacion de Devolucion",

            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
            submitHandler: function (form) {
                form.submit();
            }
        });

        $('#tipo_devolucion_id').change(function (e) {
            if ($(this).val() !== "other_reason") {
                $("#otro_motivo_devolucion").prop("disabled", true);
                $("#field_otro_motivo_devolucion").fadeOut(500);
            }
            else {
                $("#otro_motivo_devolucion").prop("disabled", false);
                $("#field_otro_motivo_devolucion").fadeIn(500);
            }
        });


    }

</script>

