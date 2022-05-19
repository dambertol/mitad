<?php echo form_open(uri_string(), array('data-toggle' => 'validator', 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data')); ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title"><?php echo $title; ?></h4>
</div>
<div class="modal-body">
	<div class="row">
		<?php foreach ($fields as $field): ?>
			<div class="form-group">
				<?php echo $field['label']; ?> 
				<?php echo $field['form']; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php if (!empty($adjuntos)): ?>
		<?php foreach ($adjuntos as $Adjunto): ?>
			<div class="form-group">
				<label for="<?php echo $Adjunto->id; ?>" class="col-sm-2 control-label">Adjuntos</label> 
				<div class="col-sm-10">
					<div class="control-label left">	
						<?php echo anchor_popup($Adjunto->ruta . $Adjunto->nombre, 'Ver Archivo'); ?>
					</div>
				</div>								
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo isset($txt_btn) ? 'Cancelar' : 'Cerrar'; ?></button>
	<?php echo (!empty($txt_btn)) ? form_submit(array('class' => 'btn btn-' . ($txt_btn === 'Agregar' ? 'primary' : ($txt_btn === 'Editar' ? 'warning' : 'danger')) . ' pull-right', 'title' => $txt_btn), $txt_btn) : ''; ?>
	<?php echo (!empty($txt_btn) && ($txt_btn === 'Editar' || $txt_btn === 'Eliminar')) ? form_hidden('id', $pase->id) : ''; ?>
</div>
<?php echo form_close(); ?>