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
                <?php echo form_open(uri_string(), 'class="form-horizontal"'); ?>


                <!-- Tab links -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item active">
                        <a class="nav-link" id="cedula-tab" data-toggle="tab" href="#cedula" role="tab" aria-controls="cedula"
                           aria-selected="false">Cedula</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="adjuntos-tab" data-toggle="tab" href="#adjuntos" role="tab"
                           aria-controls="adjuntos" aria-selected="true"><span
                                    class="label label-info"><?php echo $cantidad_adjuntos; ?></span> Adjuntos</a>
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
                    <div class="tab-pane active" id="cedula" role="tabpanel" aria-labelledby="cedula-tab">
                        <fieldset class="group-border">
                            <legend class="group-border">Cedula</legend>

                            <div class="row">
                                <?php if (!is_null($cedula->fecha_delete)): ?>
                                    <div class="form-group">
                                        <label for="fecha_delete" class="col-sm-2 control-label red">FECHA DE ELIMINACION</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="fecha_delete" value="<?php echo $cedula->fecha_delete; ?>"
                                                   id="fecha_delete" class="form-control red"
                                                   readonly="">
                                        </div>
                                    </div>
                                <?php endif; ?>

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

                                <!-- Prioridad -->
                                <div class="form-group">
                                    <label for="prioridad" class="col-sm-2 control-label">Prioridad</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="prioridad_id"
                                               value="<?php if ($cedula->prioridad == 14): echo "Baja (14 dias)";
                                               elseif ($cedula->prioridad == 7): echo "Media (7 dias)";
                                               elseif ($cedula->prioridad == 1): echo "URGENTE (24 hs)";
                                               else:
                                                   echo "Sin Datos";
                                               endif;
                                               ?>"
                                               maxlength="60" id="prioridad_id" class="form-control"
                                               readonly="">
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

                    <div class="tab-pane" id="adjuntos" role="tabpanel" aria-labelledby="adjuntos-tab">
                        <fieldset class="group-border">
                            <legend class="group-border">Adjuntos</legend>
                            <div class="row">

                                <div id="adjuntos-container" class="col-sm-12">

                                    <?php if (!empty($array_adjuntos)): ?>
                                        <?php foreach ($array_adjuntos as $Adjunto): ?>
                                            <?php if ($Adjunto->extension === 'jpg' || $Adjunto->extension === 'jpeg' || $Adjunto->extension === 'png'): ?>
                                                <?php $preview = '<img style="width: 100%; display: block;" src="' . $Adjunto->ruta . $Adjunto->nombre . '" alt="' . $Adjunto->tipo_adjunto . '">'; ?>
                                                <?php $extra = ''; ?>
                                            <?php else: ?>
                                                <?php $preview = '<object type="application/pdf" data="' . $Adjunto->ruta . $Adjunto->nombre . '#toolbar=0" width="100%" height="170">PDF</object>'; ?>
                                                <?php $extra = ' data-type="url" data-disable-external-check="true"'; ?>
                                            <?php endif; ?>
                                            <div class="col-lg-3 col-md-4 col-sm-6 adjunto_<?php echo $Adjunto->tipo_id; ?>"
                                                 id="adjunto_<?php echo $Adjunto->id; ?>">
                                                <div class="thumbnail">
                                                    <div class="image view view-first">
                                                        <?php echo $preview; ?>
                                                        <div class="mask">
                                                            <p>&nbsp;</p>
                                                            <div class="tools tools-bottom">
                                                                <a href="<?php echo $Adjunto->ruta . $Adjunto->nombre; ?>"
                                                                   target="_blank"
                                                                   title="Ver Adjunto" data-toggle="lightbox"<?php echo $extra; ?>
                                                                   data-gallery="cedula-gallery"
                                                                   data-title="<?php echo "$Adjunto->tipo_adjunto <span class='small'>$Adjunto->descripcion</span>"; ?>"><i
                                                                            class="fa fa-search"></i></a>
                                                                <a href="notificaciones/adjuntos/descargar/cedulas/<?php echo $Adjunto->id; ?>"
                                                                   title="Descargar Adjunto"><i class="fa fa-download"></i></a>
                                                                <?php if (!empty($txt_btn) && $txt_btn === 'Editar'): ?>
                                                                    <!--                                                                    <a href="javascript:eliminar_adjunto(--><?php //echo $Adjunto->id; ?><!--, '--><?php //echo $Adjunto->nombre; ?><!--', --><?php //echo $cedula->id; ?><!--)"-->
                                                                    <!--                                                                       title="Eliminar adjunto"><i class="fa fa-remove"></i></a>-->
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="caption" style="height:60px;">
                                                        <p>
                                                            <b><?php echo $Adjunto->tipo_adjunto; ?></b><br>
                                                            <?php echo $Adjunto->descripcion; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($array_adjuntos_eliminar)): ?>
                                        <?php foreach ($array_adjuntos_eliminar as $Adjunto): ?>
                                            <input type='hidden' name='adjunto_eliminar[<?php echo $Adjunto->id; ?>]'
                                                   value='<?php echo $Adjunto->nombre; ?>'>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
                                    <label for="notificador_id" class="col-sm-2 control-label">Notificador</label>
                                    <div class="col-sm-10">
                                        <?php if ($notificador): ?>
                                            <input type="text" name="notificador_id"
                                                   value="<?php echo $notificador->id . " - " . $notificador->usuario; ?>"
                                                   maxlength="60" id="notificador_id" class="form-control" readonly="">
                                        <?php else: ?>
                                            <input type="text" name="notificador_id" value="NO ASIGANDO" maxlength="60"
                                                   id="notificador_id" class="form-control" readonly="">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="notificador_id" class="col-sm-2 control-label">Notificador Suplente</label>
                                    <div class="col-sm-10">
                                        <?php if ($notificador_suplente): ?>
                                            <input type="text" name="notificador_suplente_id"
                                                   value="<?php echo $notificador_suplente->id . " - " . $notificador_suplente->usuario; ?>"
                                                   maxlength="60" id="notificador_suplente_id" class="form-control" readonly="">
                                        <?php else: ?>
                                            <input type="text" name="notificador_suplente_id" value="NO ASIGANDO" maxlength="60"
                                                   id="notificador_suplente_id" class="form-control" readonly="">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="zona_id" class="col-sm-2 control-label">Zona</label>
                                    <div class="col-sm-10">
                                        <?php if ($zona): ?>
                                            <input type="text" name="zona_id" value="<?php echo $zona->descripcion; ?>" maxlength="60"
                                                   id="zona_id" class="form-control" readonly="">
                                        <?php else: ?>
                                            <input type="text" name="zona" value="NO ASIGANDA" maxlength="60"
                                                   id="zona" class="form-control" readonly="">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>


                    </div>

                    <div class="tab-pane" id="entrega" role="tabpanel" aria-labelledby="entrega-tab">
                        <fieldset class="group-border">
                            <legend class="group-border">Entrega</legend>
                            <div class="row">
                                <?php if (!is_null($devolucion)): ?>
                                    <div class="form-group">
                                        <label for="tipo_devolucion_id" class="col-sm-2 control-label">Entrega</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="tipo_devolucion_id" value="<?php echo $devolucion->tipo_devolucion; ?>"
                                                   maxlength="60" id="tipo_devolucion_id" class="form-control" readonly="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="observaciones_devolucion" class="col-sm-2 control-label">Observaciones</label>
                                        <div class="col-sm-10">
                                <textarea class="form-control" rows="5" name="observaciones_devolucion"
                                          id="observaciones_devolucion" readonly=""><?php echo $devolucion->observaciones; ?></textarea>

                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <label for="" class="control-label">No hay datos para mostrar</label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </fieldset>
                    </div>
                    <div class="tab-pane" id="movimientos" role="tabpanel" aria-labelledby="movimientos-tab">
                        <fieldset class="group-border">
                            <legend class="group-border">Movimientos</legend>

                            <div class="row">
                                <table class="table table-hover table-condensed ">
                                    <thead>
                                    <tr>
                                        <th class="col-md-1 text-center">ID</th>
                                        <th class="col-md-2 text-center">Descripci&oacute;n</th>
                                        <th class="col-md-5 text-center">Observaciones</th>
                                        <th class="col-md-2 text-center">Usuario</th>
                                        <th class="col-md-2 text-center">Fecha</th>
                                    </tr>
                                    </thead>


                                    <?php foreach ($movimientos as $movimiento): ?>
                                        <tr class="<?php echo ($movimiento->id == $movimiento_actual->id) ? "red" : "" ?>">
                                            <td class="text-center">
                                                <?php echo $movimiento->id; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo notificaciones_cedulas_movimientos_tipo_desc($movimiento->tipo_movimiento_id); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $movimiento->observaciones; ?>
                                            </td>
                                            <td class="text-center">
                                                <span data-toggle="tooltip" data-placement="top"
                                                      title="<?php echo $movimiento->usuario; ?>">
                                                    <?php echo $movimiento->usuario_id; ?>
                                                </span>
                                            </td>
                                            <td class="text-center"><?php echo $movimiento->fecha; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </fieldset>
                    </div>

                </div>


                <div class="ln_solid"></div>
                <div class="text-center">

                    <?php if (is_null($cedula->fecha_delete)): ?>

                        <?php if ($txt_tipo_user === 'OFICINA_EXTERNA'): ?>
                            <?php if ($txt_btn === 'Editar'): ?>
                                <a href="notificaciones/cedulas/editar/<?php echo $cedula->id ?>" class="btn btn-primary btn-sm">Editar
                                    Cedula</a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($txt_tipo_user === 'OFICINA_NOTIFICACIONES'): ?>
                            <?php if ($txt_btn === 'Realizada'): ?>
                                <a href="notificaciones/cedulas/aceptar/<?php echo $cedula->id ?>" class="btn btn-primary btn-sm">Aceptar
                                    Cedula</a>
                                <a href="notificaciones/cedulas/erronea/<?php echo $cedula->id ?>" class="btn btn-warning btn-sm">Cedula
                                    con Errores</a>
                            <?php elseif ($txt_btn === 'Asignar'): ?>
                                <a href="notificaciones/cedulas/asignar/<?php echo $cedula->id ?>#notificador-tab"
                                   class="btn btn-warning btn-sm">Asignar
                                    Notificador</a>
                            <?php elseif ($txt_btn === 'Imprimir'): ?>
                                <a href="notificaciones/cedulas/asignar/<?php echo $cedula->id ?>#notificador-tab"
                                   class="btn btn-warning btn-sm">Cambiar
                                    Notificador</a>
                                <a href="notificaciones/cedulas/despachar/<?php echo $cedula->id ?>" class="btn btn-info btn-sm">Despachar
                                    Cedula</a>
                            <?php elseif ($txt_btn === 'Entrega'): ?>
                                <a href="notificaciones/cedulas/asignar/<?php echo $cedula->id ?>#notificador-tab"
                                   class="btn btn-warning btn-sm">Cambiar
                                    Notificador</a>
                                <a href="notificaciones/cedulas/vista_previa_impresion/<?php echo $cedula->id ?>"
                                   class="btn btn-info btn-sm">Vista
                                    previa Impresion</a>
                                <a href="notificaciones/cedulas/entrega/<?php echo $cedula->id ?>" class="btn btn-success btn-sm">Cargar
                                    Entrega</a>
                            <?php endif; ?>
                        <?php endif; ?>


                        <?php if ($txt_tipo_user === 'NOTIFICADOR'): ?>
                            <?php if ($txt_btn === 'Imprimir'): ?>
                                <a href="notificaciones/cedulas/despachar/<?php echo $cedula->id ?>" class="btn btn-info btn-sm">Despachar
                                    Cedula</a>
                            <?php elseif ($txt_btn === 'Entrega'): ?>
                                <a href="notificaciones/cedulas/vista_previa_impresion/<?php echo $cedula->id ?>"
                                   class="btn btn-info btn-sm">Vista
                                    previa Impresion</a>
                                <a href="notificaciones/cedulas/entrega/<?php echo $cedula->id ?>" class="btn btn-success btn-sm">Cargar
                                    Entrega</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a href="notificaciones/cedulas/listar" class="btn btn-default btn-sm">Volver al Listado</a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php echo(!empty($audi_modal) ? $audi_modal : ''); ?>

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

        //      $("#oficina_id").select();
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
        swal({
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