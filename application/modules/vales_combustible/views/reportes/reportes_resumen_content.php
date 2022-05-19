<!--
	/*
	 * Vista de Reporte Resumen
	 * Autor: Leandro
	 * Creado: 10/01/2019
	 * Modificado: 28/01/2019 (Leandro)
	 */
-->
<script>
	$(document).ready(function() {
		$("#button_exportar").click(function() {
			$("#form_reporte").data('submitted', false);
			$("#form_reporte").submit();
		});
		$('#desde').datetimepicker({
			locale: 'es',
			format: 'L',
			useCurrent: false,
			showClear: true,
			showTodayButton: true,
			showClose: true,
			daysOfWeekDisabled: [0, 1, 3, 4, 5, 6]
		});
		$("#desde").on("dp.change", function(e) {
			if (e.date.weekday() !== 1) {	//No es Martes
				$('#desde').val(e.date.startOf('week').add(1, 'days').format("DD/MM/YYYY"));
			}
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Reporte'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = array('id' => 'button_exportar', 'class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'id="form_reporte" class="form-horizontal"'); ?>
				<div class="row">
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
					<a href="vales_combustible/reportes/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php if (!empty($resumen)) : ?>
	<?php foreach ($resumen as $Area => $datos): ?>
		<div class="row">
			<div class="col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2><?php echo $Area; ?></h2>
						<div class="clearfix"></div>
					</div>
					<div class="x_content">
						<table class="table table-bordered table-condensed" style="table-layout:fixed;">
							<thead>
								<tr>
									<th>Estado/Tipo</th>
									<?php foreach ($tipos_combustible as $Tipo): ?>
										<th><?php echo $Tipo; ?></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><b>CUPO SEMANAL</b></td>
									<?php foreach ($tipos_combustible as $Tipo): ?>
										<?php if (!empty($resumen_cupos[$Area][$Tipo])) : ?>
											<td><?php echo $resumen_cupos[$Area][$Tipo]; ?></td>
										<?php else: ?>
											<td>0</td>
										<?php endif; ?>
									<?php endforeach; ?>
								</tr>
								<?php $array_estados = array('Aprobado', 'Pendiente'); ?>
								<?php foreach ($array_estados as $Estado): ?>
									<tr>
										<td><b><?php echo $Estado; ?></b></td>
										<?php foreach ($tipos_combustible as $Tipo): ?>
											<?php if (!empty($datos[$Tipo][$Estado])) : ?>
												<td><?php echo $datos[$Tipo][$Estado]; ?></td>
											<?php else: ?>
												<td>0</td>
											<?php endif; ?>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
