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

                            <?php if ($notificador): ?>
                                <input type="text" name="notificador_id"
                                       value="<?php echo $notificador->id . " - " . $notificador->usuario; ?>"
                                       maxlength="60" id="notificador_id" class="form-control" readonly="">
                            <?php endif; ?>
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
                        <div class="col-sm-10">
                            <?php //dd($cedulas);?>
                            <?php foreach ($cedulas as $cedula): ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="cedulas[]"
                                               value="<?php echo $cedula->id; ?>">
                                        <?php echo $cedula->n_cedula; ?>
                                        <?php echo " - "; ?>
                                        <?php echo $cedula->apellido . "," . $cedula->nombre; ?>
                                        <?php echo " - "; ?>
                                        <?php echo $cedula->direccion . " " . $cedula->num . " " . $cedula->localidad; ?>
                                    </label>

                                </div>
                            <?php endforeach; ?>
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