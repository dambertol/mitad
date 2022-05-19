<!--
	/*
	 * Vista ABM de Acta.
	 * Autor: Leandro
	 * Creado: 24/10/2019
	 * Modificado: 30/12/2019 (Leandro)
	 */
-->
<script>
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	$(document).ready(function() {
		$("#cargar_datos").click(function() {
			buscar_datos_padron($("#padron_municipal").val());
		});
	});
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
	<div class="col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Actas'; ?></h2>
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
					<br />
					<h2 class="text-center">Datos acta</h2>
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="row" id="row-domicilio">
					<br />
					<h2 class="text-center">Datos domilicio</h2>
					<?php if ($txt_btn === 'Agregar'): ?>
						<div class="form-group">
							<div class="col-sm-12 text-center">
								<?php echo form_button('cargar_datos', 'Cargar Dirección de M@jor', array('id' => 'cargar_datos', 'class' => 'btn btn-sm btn-primary')); ?>
							</div>
						</div>
					<?php endif; ?>
					<?php foreach ($fields_domicilio as $field_domicilio): ?>
						<div class="form-group">
							<?php echo $field_domicilio['label']; ?> 
							<?php echo $field_domicilio['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $acta->id) : ''; ?>
					<a href="actasisp/actas/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>