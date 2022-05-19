<!--
	/*
	 * Vista ABM de Hoja de Ruta.
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 02/07/2019 (GENERATOR_MLC)
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Hojas de Ruta'; ?></h2>
                <?php if (!empty($audi_modal)): ?>
                    <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
                        <i class="fa fa-info-circle"></i>
                    </button>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php $data_submit = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
                <?php echo form_open(uri_string(), 'class="form-horizontal"'); ?>
                <div class="row">

                    <div class="form-group">
                        <label for="notificador_id" class="col-sm-2 control-label">Notificador</label>
                        <div class="col-sm-10">
                            <select name="notificador_id" id="notificador_id" class="form-control selectpicker"
                                    title="-- Seleccionar --"
                                    data-selected-text-format="count>1" data-live-search="true">
                                <?php foreach ($notificadores as $notificador): ?>
                                    <option value="<?php echo $notificador->id; ?>">
                                        <?php echo $notificador->id . " - " . $notificador->usuario; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $fields['fecha_limite']['label']; ?>
                        <?php echo $fields['fecha_limite']['form']; ?>
                    </div>

                    <div class="form-group">
                        <label for="estado_id" class="col-sm-2 control-label">Estado</label>
                        <div class="col-sm-10">
                            <?php if ($estado_hoja_ruta): ?>
                                <input type="text" name="estado_id"
                                       value="<?php echo $estado_hoja_ruta->id . " - " . $estado_hoja_ruta->desc; ?>"
                                       maxlength="60" id="estado_id" class="form-control" readonly="">
                            <?php else: ?>
                                <input type="text" name="estado_id" value="NO ASIGANDO" maxlength="60"
                                       id="estado_id" class="form-control" readonly="">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cedulas_id" class="col-sm-2 control-label">Cedulas</label>
                        <div class="col-sm-10" id="cedulas_chk">
                        </div>
                    </div>

                </div>
                <div class="ln_solid"></div>
                <div class="text-center">
                    <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                    <?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $hojas_ruta->id) : ''; ?>
                    <a href="notificaciones/hojas_rutas/listar" class="btn btn-default btn-sm">Cancelar</a>
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

        $("#form-cedulas").validate({
            // Specify validation rules
            rules: {
                // The key name on the left side is the name attribute
                // of an input field. Validation rules are defined
                // on the right side
                notificador_id: "required",
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

        $('#notificador_id').change(function (e) {
            $.ajax({
                type: "POST",
                url: "notificaciones/hojas_rutas/cargar_cedulas",
                dataType: "json",
                data: {n_id: $("#notificador_id :selected").val(), csrf_mlc2: csrfData}
            }).done(function (data) {
                console.log(data);
                var cedulas = null;
                $("#cedulas_chk").html("");
                if (data['error'] === undefined) {
                    cedulas = data.cedulas;

                    cedulas.forEach(function (cedula) {
                        var html = "<div class='checkbox'>";
                        html += "<label>";
                        html += "<input type='checkbox' name='cedulas[]' value='" + cedula.id + "'>";
                        html += "Ced Nº: " + cedula.n_cedula + " - " + cedula.apellido + ", " + cedula.nombre + " - ";
                        html += cedula.direccion + " " + cedula.num + " (" + cedula.localidad + ")";
                        html += "</label>";
                        html += "</div>";

                        $("#cedulas_chk").append(html);
                    });
                } else {
                    console.log("No se encontro ningun registro");
                }
            });
        });
    }

</script>

