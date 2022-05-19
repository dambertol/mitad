<!--
	/*
	 * Vista ABM de Legajo.
	 * Autor: Leandro
	 * Creado: 02/02/2017
	 * Modificado: 11/12/2019 (Leandro)
	 */
-->
<?php if ($txt_btn === 'Agregar'): ?>
	<script>
		var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
		$(document).ready(function() {
			$("#cargar_datos").click(function() {
				buscar_legajo($("#legajo").val());
			});
		});
	</script>
<?php endif; ?>
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Legajos'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'class="form-horizontal"'); ?>
				<div class="row">
					<?php if ($txt_btn === 'Agregar'): ?>
						<div class="form-group">
							<?php echo form_label('Legajo *', 'legajo', array('class' => "col-sm-2 control-label")); ?>
							<div class="col-sm-7">
								<?php echo form_input($legajo, '', 'class="form-control" required'); ?>
							</div>
							<div class="col-sm-3 text-center">
								<?php echo form_button('cargar_datos', 'Cargar Datos', array('id' => 'cargar_datos', 'class' => 'btn btn-sm btn-primary')); ?>
							</div>
						</div>
					<?php endif; ?>
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $legajo->id) : ''; ?>
					<a href="recursos_humanos/legajos/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>