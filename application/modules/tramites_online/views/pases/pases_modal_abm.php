<?php echo form_open(uri_string(), array('data-toggle' => 'validator', 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data')); ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title"><?php echo $title; ?></h4>
</div>
<div class="modal-body">

    <div class="row">
    
    <ul><i class="fa fa-clock-o"></i>Historial
    <?php foreach ($fields as $field): ?>
            <div class="form-group">
                    <?php echo $field['label']; ?>
                    <?php echo $field['form']; ?>
            </div>
            <?php endforeach; ?>            
    </ul>
        <ul><i class="fa fa-clock-o"></i>Datos de Solicitante
        <?php foreach ($fields_solicitante as $field): ?>
            <div class="form-group">
            <?php echo $field['label'] ?>
            <?php echo $field['form']; ?>          
            </div>
            <?php endforeach; ?>
    </ul>
    
    </div>
    <ul class="nav nav-tabs" role="tablist">
        <?php $step = 0; ?>
        <?php foreach ($fields_group as $paso_key => $paso): ?>
            <li role="presentation" class="<?= $step === 0 ? 'active' : ''; ?>"><a href="#tab_<?= $paso_key; ?>"
                                                                                   aria-controls="tab_<?= $paso_key; ?>" role="tab"
                                                                                   data-toggle="tab"><i
                            class="fa fa-clock-o"></i> <?= $paso['nombre']; ?></a></li>
            <?php $step++; ?>
        <?php endforeach; ?>
    </ul>
    <div class="tab-content">
        <?php $step = 0; ?>
        <?php foreach ($fields_group as $paso_key => $paso): ?>
            <div role="tabpanel" class="tab-pane <?= $step === 0 ? 'active' : ''; ?>" id="tab_<?= $paso_key; ?>">
                <br/>
                <?php if (!empty($paso['mensaje'])) : ?>
                    <div class="alert alert-info alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">??</span>
                        </button>
                        <i class="fa fa-info"></i>IMPORTANTE<br>
                        <?= $paso['mensaje']; ?>
                    </div>
                <?php endif; ?>
                <div id="form-paso-<?= $step; ?>" role="form" data-toggle="validator">
                    <?php $cant = 1; ?>
                    <?php echo form_input(array('name' => "cant_$paso_key", 'type' => 'hidden', 'id' => "cant_$paso_key"), sizeof($paso['allFields'])); ?>
                    <?php foreach ($paso['allFields'] as $fields): ?>
                        <div id="<?= "$paso_key-$cant"; ?>" class="<?= $paso_key; ?>"
                             style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
                            <div style="padding:5px 15px;">
                                <h2 class="text-center"><?= $paso['nombre'] . " " . $cant; ?> <span id="titulo_<?= $paso_key; ?>_1"></span>
                                </h2>
                            </div>
                            <?php foreach ($fields as $Field): ?>
                                <div class="form-group">
                                    <?php if (isset($Field['type']) && $Field['type'] == 'h3'): ?>
                                        <h3 class="text-center">
                                            <?php echo $Field['value']; ?>
                                        </h3>
                                    <?php elseif (isset($Field['type']) && $Field['type'] == 'h4'): ?>
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10">
                                            <h4 class="">
                                                <?php echo $Field['value']; ?>
                                            </h4>
                                        </div>
                                    <?php elseif (isset($Field['type']) && $Field['type'] == 'textarea'): ?>
                                        <?php echo $Field['label']; ?>
                                        <div class="col-sm-10">
                                            <textarea id="<?php echo $Field['id']; ?>" name="<?php echo $Field['name']; ?>"
                                                      class="<?php echo $Field['class']; ?>" rows="5"><?php echo $Field['value']; ?></textarea>
                                        </div>
                                    <?php else: ?>
                                        <?php echo $Field['label']; ?>
                                        <?php echo $Field['form']; ?>
                                    <?php endif; ?>
                                </div>
                                <?php if ($txt_btn === 'Iniciar Tr??mite' && $paso_key === 'nomenclatura'): ?>
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10 text-center">
                                            <?php echo form_button('cargar_datos', 'Cargar Datos M@jor', array('id' => 'cargar_datos', 'class' => 'btn btn-sm btn-primary')); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php $cant++; ?>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($adjuntos_tramite)): ?>
                    <?php foreach ($adjuntos_tramite as $adjunto_tramite): ?>
                        <div class="form-group">
                            <label for="<?php echo $adjunto_tramite->id; ?>"
                                   class="col-sm-2 control-label"><?php echo $adjunto_tramite->tipo; ?></label>
                            <div class="col-sm-10">
                                <div class="control-label left">
                                    <?php echo anchor_popup($adjunto_tramite->ruta . $adjunto_tramite->nombre, 'Ver Archivo'); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php $step++; ?>
        <?php endforeach; ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo isset($txt_btn) ? 'Cancelar' : 'Cerrar'; ?></button>
    <?php echo (!empty($txt_btn)) ? form_submit(array('class' => 'btn btn-' . ($txt_btn === 'Agregar' ? 'primary' : ($txt_btn === 'Editar' ? 'warning' : 'danger')) . ' pull-right', 'title' => $txt_btn), $txt_btn) : ''; ?>
    <?php echo (!empty($txt_btn) && ($txt_btn === 'Editar' || $txt_btn === 'Eliminar')) ? form_hidden('id', $pase->id) : ''; ?>
</div>
<?php echo form_close(); ?>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker('refresh');
    });
</script>