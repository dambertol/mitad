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
                <?php echo anchor('notificaciones/adjuntos/descargar/modelo_cedula.docx', 'Descargar Modelo Cedula', 'class="btn btn-info btn-sm" target="_blank"') ?>
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
                <?php echo form_open(uri_string(), 'enctype="multipart/form-data" class="form-horizontal" id="form-cedulas"'); ?>

                <fieldset class="group-border">
                    <legend class="group-border">Destinatario</legend>
                    <div class="row">

                        <div class="form-group">
                            <label for="domicilio_alternativo" class="col-sm-2 control-label">Domicilio Actualizado</label>
                            <div class="col-sm-10">
                                <input type="text" name="domicilio_alternativo" value="" maxlength="200" id="domicilio_alternativo"
                                       class="form-control" placeholder="Calle Numero, Localidad">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tipo_identificacion" class="col-sm-2 control-label">Tipo Identificaci&oacute;n</label>
                            <div class="col-sm-10">
                                <select name="tipo_identificacion" id="tipo_identificacion" class="form-control">
                                    <option value="1">DNI</option>
                                    <option value="2">L.C.</option>
                                    <option value="3">L.E.</option>
                                    <option value="4">L.F.</option>
                                    <option value="5">PASAPORTE</option>
                                    <option value="6">C.U.I.T.</option>
                                    <option value="7">EXTRANJEROS</option>
                                    <option value="9">SIN DOCUMENTO</option>
                                    <option value="10">C.I.</option>
                                    <option value="11">C.E.</option>
                                    <option value="12">C.F.</option>
                                    <option value="13">C.I.F.</option>
                                    <option value="14">C.U.I.L.</option>
                                    <option value="15">EXTRANJEROS NO RESIDENTES</option>
                                    <option value="20">ORGANISMOS OFICIALES</option>
                                    <option value="30">VERIFICADORES ESPECIALES</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="n_identificacion" class="col-sm-2 control-label">Num Identificaci&oacute;n</label>
                            <div class="col-sm-10">
                                <input type="text" name="n_identificacion" value="" maxlength="20" id="n_identificacion"
                                       class="form-control" title="Debe ingresar sólo números">
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
                                <input type="text" name="nombre" value="" maxlength="60" id="nombre"
                                       class="form-control" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="apellido" class="col-sm-2 control-label">Apellido</label>
                            <div class="col-sm-10">
                                <input type="text" name="apellido" value="" maxlength="60" id="apellido" class="form-control" readonly="">
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="domicilio" class="col-sm-2 control-label">Domicilio</label>
                            <div class="col-sm-10">
                                <input type="text" name="domicilio" value="" maxlength="60" id="domicilio" class="form-control" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="altura_domiicilio" class="col-sm-2 control-label">Altura Domicilio</label>
                            <div class="col-sm-10">
                                <input type="text" name="altura_domiicilio" value="" maxlength="60" id="altura_domiicilio"
                                       class="form-control" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="localidad" class="col-sm-2 control-label">Localidad</label>
                            <div class="col-sm-10">
                                <input type="text" name="localidad" value="" maxlength="60" id="localidad" class="form-control" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="codigo_postal" class="col-sm-2 control-label">Codigo Postal</label>
                            <div class="col-sm-10">
                                <input type="text" name="codigo_postal" value="" maxlength="60" id="codigo_postal" class="form-control"
                                       readonly="">
                            </div>
                        </div>


                    </div>
                </fieldset>

                <fieldset class="group-border">
                    <legend class="group-border">Cedula</legend>

                    <div class="row">
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
                                    <option value="14" selected>Baja (14 dias)</option>
                                    <option value="7">Media (7 dias)</option>
                                    <option value="1">URGENTE (24 hs)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Ofiicina Origen -->
                        <div class="form-group">
                            <label for="oficina_id" class="col-sm-2 control-label">Oficina Origen</label>
                            <div class="col-sm-10">
                                <select name="oficina_id" id="oficina_id" class="form-control selectpicker" title="-- Seleccionar --"
                                        data-selected-text-format="count>1" data-live-search="true">
                                    <?php foreach ($areas as $area): ?>
                                        <?php if (!is_null($areas_usuario)): ?>
                                            <?php if (in_array($area->id, $areas_usuario)): ?>
                                                <option value="<?php echo $area->id; ?>"><?php echo $area->codigo . " - " . $area->nombre; ?> </option>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <option value="<?php echo $area->id; ?>"><?php echo $area->codigo . " - " . $area->nombre; ?> </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tipo Documento -->
                        <div class="form-group">
                            <label for="tipo_documento_id" class="col-sm-2 control-label">Tipo Documento</label>
                            <div class="col-sm-10">
                                <select name="tipo_documento_id" id="tipo_documento_id" class="form-control selectpicker"
                                        title="-- Seleccionar --" data-selected-text-format="count>1" data-live-search="true">
                                    <?php foreach ($tipos_documentos as $tp): ?>
                                        <option value="<?php echo $tp->id; ?>"><?php echo $tp->descripcion; ?> </option>
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
                            <label for="chars_left" class="col-sm-2 control-label">Caracteres Restantes:</label>
                            <div class="col-sm-10">
                                <span id="chars_left" class="form-control" readonly=""></span>
                            </div>
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


<script src="js/notificaciones/tinymce/tinymce.min.js"></script>
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
        var search_button_major_pressed = false;
        $("#cargar_datos").click(function () {
            console.log("Buscando datos...");
            //console.log("combo:" + $("#tipo_identificacion :selected").val());
            buscar_destinatario($("#tipo_identificacion :selected").val(), $("#n_identificacion").val());
        });

        function buscar_destinatario(tipo, legajo) {
            // if (legajo.length > 6 && (legajo.match(/^[0-9]+$/) !== null)) {
            if (legajo.length > 6) {
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
                        search_button_major_pressed = true;
                    } else {

                        console.log("No se encontro ningun registro");
                        alert("No se encontro ningun registro");
                        limpiar_destinatario();
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
            search_button_major_pressed = false;
            $("#nombre").val("");
            $("#apellido").val("");
            $("#domicilio").val("");
            $("#altura_domiicilio").val("");
            $("#localidad").val("");
            $("#codigo_postal").val("");
        }

        //      $("#oficina_id").select();

        var max_chars = 2600; //max characters
        var max_for_html = 300; //max characters for html tags
        var allowed_keys = [8, 13, 16, 17, 18, 20, 33, 34, 35, 36, 37, 38, 39, 40, 46];
        var chars_without_html = 0;

        function alarmChars() {
            if (chars_without_html > (max_chars - 25)) {
                $('#chars_left').css('color', 'red');
            } else {
                $('#chars_left').css('color', 'gray');
            }
        }


        tinymce.init({
            selector: '#texto',
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons',
            menubar: false,

            statusbar: false,
            setup: function (ed) {
                ed.on("KeyDown", function (ed, evt) {
                    chars_without_html = $.trim(tinyMCE.activeEditor.getContent().replace(/(<([^>]+)>)/ig, "")).length;
                    chars_with_html = tinyMCE.activeEditor.getContent().length;
                    var key = ed.keyCode;

                    $('#chars_left').html(max_chars - chars_without_html);

                    if (allowed_keys.indexOf(key) != -1) {
                        alarmChars();
                        return;
                    }

                    if (chars_with_html > (max_chars + max_for_html)) {
                        ed.stopPropagation();
                        ed.preventDefault();
                    } else if (chars_without_html > max_chars - 1 && key != 8 && key != 46) {
                        alert('Limite de Caracteres!');
                        ed.stopPropagation();
                        ed.preventDefault();
                    }
                    alarmChars();
                });
            },
        });

        chars_without_html = $.trim($("#texto").text().replace(/(<([^>]+)>)/ig, "")).length;
        $('#chars_left').html(max_chars - chars_without_html);
        alarmChars();

        $("#form-cedulas").validate({
            // Specify validation rules
            rules: {
                // The key name on the left side is the name attribute
                // of an input field. Validation rules are defined
                // on the right side
                n_identificacion: "required",
                n_documento: "required",
                prioridad: "required",
                texto: {
                    required: true,
                    maxlength: 2500
                },
                oficina_id: "required",
                observaciones: {
                    maxlength: 1000
                },
                tipo_documento_id: "required",

                anio: {
                    required: true,
                    minlength: 4
                }
            },
            // Specify validation error messages
            messages: {
                n_identificacion: "Debe ingresar el numero de identificacion",
                prioridad: "Seleccione una prioridad",
                n_documento: "Ingrese el numero del Documento",
                texto: {
                    required: true,
                    maxlength: 2500
                },
                oficina_id: "Seleccione una oficina",
                observaciones: {
                    maxlength: 1000
                },
                tipo_documento_id: "Seleccione un tipo de Documento",

                anio: {
                    required: "Ingrese el Año",
                    minlength: 4
                }
            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
            submitHandler: function (form) {
                if (search_button_major_pressed) {
                    form.submit();
                }
                else {
                    alert("No se ha buscado un destinatario");
                }
            }
        });

    }
    ;
</script>
