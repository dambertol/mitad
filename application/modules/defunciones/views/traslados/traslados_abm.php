<!--
	/*
	 * Vista ABM de Traslado.
	 * Autor: Leandro
	 * Creado: 05/12/2019
	 * Modificado: 05/12/2019 (Leandro)
	 */
-->
<?php if ($txt_btn === 'Agregar'): ?>
	<script>
		$(document).ready(function() {
			var inicial = $('#ubicacion_destino').selectpicker('val');
			if (inicial === 'agregar' || inicial === '') {
				$('#row-ubicacion :input').attr("disabled", false);
				$('#row-ubicacion').attr("style", "display: block")
				$('#cementerio').attr('required')
				$('#tipo').attr('required')
				$("#cementerio").selectpicker('refresh');
				$("#tipo").selectpicker('refresh');
			} else {
				$('#row-ubicacion').attr("style", "display: none")
				$('#row-ubicacion :input').attr("disabled", true);
				$('#cementerio').removeAttr('required')
				$('#tipo').removeAttr('required')
				$("#cementerio").selectpicker('refresh');
				$("#tipo").selectpicker('refresh');
			}

			$('#ubicacion_destino').on('changed.bs.select', function(e) {
				if (this.value === 'agregar') {
					$('#row-ubicacion :input').attr("disabled", false);
					$('#row-ubicacion').attr("style", "display: block")
					$('#cementerio').attr('required')
					$('#tipo').attr('required')
					$("#cementerio").selectpicker('refresh');
					$("#tipo").selectpicker('refresh');
				} else {
					$('#row-ubicacion').attr("style", "display: none")
					$('#row-ubicacion :input').attr("disabled", true);
					$('#cementerio').removeAttr('required')
					$('#tipo').removeAttr('required')
					$("#cementerio").selectpicker('refresh');
					$("#tipo").selectpicker('refresh');
				}
			});
			cambiarTipoUbicacion();
			$('#tipo').change(function() {
				cambiarTipoUbicacion();
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Traslados'; ?></h2>
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
				<?php if (!empty($fields_solicitante)): ?>
					<div class="row">
						<h2 class="text-center">Datos Solicitante</h2>
						<?php foreach ($fields_solicitante as $field): ?>
							<div class="form-group">
								<?php echo $field['label']; ?> 
								<?php echo $field['form']; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if (!empty($fields_difunto)): ?>
					<div class="row">
						<h2 class="text-center">Datos Difunto</h2>
						<?php foreach ($fields_difunto as $field): ?>
							<div class="form-group">
								<?php echo $field['label']; ?> 
								<?php echo $field['form']; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="row">
					<h2 class="text-center">Datos Traslado</h2>
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if (!empty($fields_ubicacion)): ?>
					<div class="row" id="row-ubicacion">
						<h2 class="text-center">Datos Ubicación</h2>
						<?php foreach ($fields_ubicacion as $field): ?>
							<div class="form-group">
								<?php echo $field['label']; ?> 
								<?php echo $field['form']; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $traslado->id) : ''; ?>
					<a href="defunciones/traslados/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>