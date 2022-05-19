<div class="row" id="row-adjuntos">
    <br/>
    <h2 class="text-center">
        Galería de Adjuntos
    </h2>
    <div id="adjuntos-container" class="col-sm-12">
        <?php if (!empty($txt_btn) && $txt_btn === 'Editar'): ?>
            <div class="text-center" style="margin-bottom:10px;">
                <a class="btn btn-primary btn-sm" href="notificaciones/adjuntos/modal_agregar/cedulas/<?php echo $cedula->id; ?>"
                   data-remote="false" data-toggle="modal" data-target="#remote_modal" title="Agregar adjunto"><i class="fa fa-plus"></i>
                    Agregar adjunto</a>
            </div>
        <?php elseif (!empty($txt_btn) && $txt_btn === 'Solicitar'): ?>
            <div class="text-center" style="margin-bottom:10px;">
                <a class="btn btn-primary btn-sm" href="notificaciones/adjuntos/modal_agregar/cedulas" data-remote="false"
                   data-toggle="modal" data-target="#remote_modal" title="Agregar adjunto"><i class="fa fa-plus"></i> Agregar adjunto</a>
            </div>
        <?php endif; ?>
        <?php if (!empty($array_adjuntos)): ?>
            <?php foreach ($array_adjuntos as $Adjunto): ?>
                <?php if (!array_key_exists($Adjunto->id, $adjuntos_eliminar_existente_post)): ?>
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
                                        <a href="<?php echo $Adjunto->ruta . $Adjunto->nombre; ?>" title="Ver Adjunto"
                                           target="_blank" data-toggle="lightbox"<?php echo $extra; ?> data-gallery="cedulas-gallery"
                                           data-title="<?php echo "$Adjunto->tipo_adjunto <span class='small'>$Adjunto->descripcion</span>"; ?>"><i
                                                    class="fa fa-search"></i></a>
                                        <?php if (empty($txt_btn)): ?>
                                            <a href="notificaciones/adjuntos/descargar/cedula/<?php echo $Adjunto->id; ?>"
                                               title="Descargar Adjunto"><i class="fa fa-download"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($txt_btn) && $txt_btn === 'Editar'): ?>
                                            <a href="javascript:eliminar_adjunto(<?php echo $Adjunto->id; ?>, '<?php echo $Adjunto->nombre; ?>', <?php echo $cedula->id; ?>)"
                                               title="Eliminar adjunto"><i class="fa fa-remove"></i></a>
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
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($array_adjuntos_agregar)): ?>
            <?php foreach ($array_adjuntos_agregar as $Adjunto): ?>
                <?php if ($Adjunto->extension === 'jpg' || $Adjunto->extension === 'jpeg' || $Adjunto->extension === 'png'): ?>
                    <?php $preview = '<img style="width: 100%; display: block;" src="' . $Adjunto->ruta . $Adjunto->nombre . '" alt="' . $Adjunto->tipo_adjunto . '">'; ?>
                <?php else: ?>
                    <?php $preview = '<object type="application/pdf" data="' . $Adjunto->ruta . $Adjunto->nombre . '#toolbar=0" width="100%" height="170">PDF</object>'; ?>
                <?php endif; ?>
                <div class="col-lg-3 col-md-4 col-sm-6 adjunto_<?php echo $Adjunto->tipo_id; ?>" id="adjunto_<?php echo $Adjunto->id; ?>">
                    <input type='hidden' name='adjunto_agregar[<?php echo $Adjunto->id; ?>]' value='<?php echo $Adjunto->nombre; ?>'>
                    <div class="thumbnail">
                        <div class="image view view-first">
                            <?php echo $preview; ?>
                            <div class="mask">
                                <p>&nbsp;</p>
                                <div class="tools tools-bottom">
                                    <a href="<?php echo $Adjunto->ruta . $Adjunto->nombre; ?>" title="Ver Adjunto" data-toggle="lightbox"
                                       data-gallery="cedulas-gallery"
                                       data-title="<?php echo "$Adjunto->tipo_adjunto <span class='small'>$Adjunto->descripcion</span>"; ?>"><i
                                                class="fa fa-search"></i></a>
                                    <a href="javascript:eliminar_adjunto(<?php echo $Adjunto->id; ?>, '<?php echo $Adjunto->nombre; ?>')"
                                       title="Eliminar adjunto"><i class="fa fa-remove"></i></a>
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
                <input type='hidden' name='adjunto_eliminar[<?php echo $Adjunto->id; ?>]' value='<?php echo $Adjunto->nombre; ?>'>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>


