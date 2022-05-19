<!--
	/*
	 * Vista ABM de Persona.
	 * Autor: Leandro
	 * Creado: 01/06/2018
	 * Modificado: 23/10/2019 (Leandro)
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Personas'; ?></h2>
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
				<div class="row" id="row-persona">
					<h2 class="text-center">Datos personales</h2>
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
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $persona->id) : ''; ?>
					<a href="personas/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>
<script>
	$(document).ready(function() {
		domicilio_row();
		$('#cuil').inputmask({
			mask: '99-99999999-9',
			removeMaskOnSubmit: true
		});
		$('#cuil').blur(function() {
			var input = this;
			var cuil = input.value;
			var resul = validaCuil(cuil);
			if (!resul) {
				Swal.fire({
					type: 'error',
					title: 'Error.',
					text: 'CUIL inválido',
					buttonsStyling: false,
					confirmButtonClass: 'btn btn-primary'
				}).then(function() {
					Swal.close();
					input.focus()
				});
			}
		});
<?php if ($txt_btn === 'Agregar' || $txt_btn === 'Editar'): ?>
			$('#carga_domicilio').on('changed.bs.select', function(e) {
				domicilio_row();
			});
			function domicilio_row() {
				if ($('#carga_domicilio').selectpicker('val') === 'SI') {
					$('#row-domicilio :input').attr("disabled", false);
					$("#localidad").selectpicker('refresh');
					$("#row-domicilio").show();
				} else {
					$('#row-domicilio :input').attr("disabled", true);
					$("#localidad").selectpicker('refresh');
					$("#row-domicilio").hide();
				}
			}
<?php else: ?>
			function domicilio_row() {
				if ('<?php echo $persona->carga_domicilio; ?>' === 'SI') {
					$("#row-domicilio").show();
				} else {
					$("#row-domicilio").hide();
				}
			}
<?php endif; ?>
	});
</script>