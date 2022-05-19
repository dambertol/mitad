<!--
	/*
	 * Vista listado de Consumos Líneas Fijas.
	 * Autor: Leandro
	 * Creado: 05/09/2019
	 * Modificado: 05/09/2019 (Leandro)
	 */
-->
<script>
	$(document).ready(function() {
		$("#periodo").on("keyup change", function() {
			var periodo = $("#periodo option:selected").val();
			window.location.replace(CI.base_url + "telefonia/lineas_fijas_consumos/listar/" + periodo);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Consumos Líneas Fijas'; ?></h2>
				<?php echo anchor("telefonia/lineas_fijas_consumos/cargar/$periodo_id", 'Cargar Consumos del Periodo', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="form-horizontal">
					<div class="row">
						<div class="form-group">
							<?php echo form_label('Periodo *', 'periodo', array('class' => 'col-sm-2 control-label')); ?>
							<div class="col-sm-10">
								<?php echo form_dropdown('periodo', $periodo_opt, $periodo_id, 'class="form-control selectpicker" id="periodo" title="-- Seleccionar --" data-live-search="true"'); ?>
							</div>
						</div>
					</div>
				</div>
				<br />
				<table class="table table-striped table-hover">
					<?php if (!empty($consumos)): ?>
						<tr>
							<th>Línea</th>
							<th>Domicilio</th>
							<th>Observaciones</th>
							<th>Área</th>
							<th>Monto</th>
							<th>Estado</th>
						</tr>
						<?php $total = 0; ?>
						<?php foreach ($consumos as $consumo): ?>
							<?php $total += $consumo->monto; ?>
							<tr>
								<td style="font-weight:bold; text-align:right;"><?php echo $consumo->linea; ?></td>
								<td><?php echo $consumo->domicilio; ?></td>
								<td><?php echo $consumo->observaciones; ?></td>
								<td><?php echo $consumo->area; ?></td>
								<td style="font-weight:bold; text-align:right;">$ <?php echo number_format($consumo->monto, 2, ',', '.'); ?></td>
								<td><?php echo $consumo->estado; ?></td>
							</tr>
						<?php endforeach; ?>
						<tr>
							<td colspan="4" style="font-weight:bold; text-align:right;">TOTAL</td>
							<td style="font-weight:bold; text-align:right;">$ <?php echo number_format($total, 2, ',', '.'); ?></td>
							<td></td>
						</tr>
					<?php else: ?>
						<tr>
							<td style="font-weight:bold; text-align:center;">No hay datos para el periodo seleccionado</td>
						</tr>
					<?php endif; ?>
				</table>
			</div>
		</div>
	</div>
</div>