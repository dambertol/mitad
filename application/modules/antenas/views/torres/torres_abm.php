<!--
	/*
	 * Vista ABM de Torre.
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 21/03/2019 (Leandro)
	 */
-->
<script>
<?php if ($txt_btn === 'Editar' || $txt_btn === 'Agregar') : ?>
		var editable = true;
<?php else: ?>
		var editable = false;
<?php endif; ?>
</script>
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
	<div class="col-md-7 col-sm-7 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Torres'; ?></h2>
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
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?>
							<?php if (($txt_btn === 'Editar' || $txt_btn === 'Agregar') && isset($field['name']) && ($field['name'] === 'latitud' || $field['name'] === 'longitud')): ?>
								<div class="col-sm-8">
									<div class="row">
										<?php echo $field['form']; ?>
									</div>
								</div>
								<div class="col-sm-2">
									<?php if ($field['name'] === 'latitud'): ?>
										<?php echo form_button('btn_editar_lat', 'Editar', array('id' => 'btn_editar_lat', 'class' => 'btn btn-sm btn-primary')); ?>
									<?php elseif ($field['name'] === 'longitud'): ?>
										<?php echo form_button('btn_editar_lng', 'Editar', array('id' => 'btn_editar_lng', 'class' => 'btn btn-sm btn-primary')); ?>
									<?php endif; ?>
								</div>
							<?php else: ?>
								<?php echo $field['form']; ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $torr->id) : ''; ?>
					<a href="antenas/torres/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
	<div class="col-md-5 col-sm-5 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Ubicación</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="row">
					<div id="map" style="height:640px;"></div>
				</div>
			</div>
		</div>
		</section>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>