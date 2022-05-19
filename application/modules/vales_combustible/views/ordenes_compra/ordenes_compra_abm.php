<!--
	/*
	 * Vista ABM de Órdenes de Compra
	 * Autor: Leandro
	 * Creado: 13/11/2017
	 * Modificado: 30/01/2018 (Leandro)
	 */
-->
<script>
	var base_tr;
	$(document).ready(function() {
		base_tr = $('#detalle_1').clone();
		$('.costo_total_calculo').on('keyup', function() {
			calcularTotalDetalle(this);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Órden de Compra'; ?></h2>
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
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<section class="panel">
							<div class="x_title">
								<h2>Detalles</h2>
								<div class="clearfix"></div>
							</div>
							<div class="panel-body">
								<div class="row">
									<table class="table table-bordered table-condensed table-striped">
										<thead>
											<tr>
												<th>Tipo</th>
												<th>M³/Litros</th>
												<th>Costo</th>
												<th>Costo Total</th>		
												<th style="width:50px;"></th>
											</tr>
										</thead>
										<tbody>
											<?php $cant_rows_nro = 0; ?>
											<?php foreach ($fields_detalle_array as $Fields_detalle): ?>
												<?php $cant_rows_nro++; ?>
												<tr id="detalle_<?php echo $cant_rows_nro; ?>">
													<?php foreach ($Fields_detalle as $Field): ?>
														<?php if (isset($Field['type']) && $Field['type'] == 'hidden'): ?>
															<?php echo $Field['form']; ?>
														<?php else: ?>
															<td><?php echo $Field['form']; ?></td>
														<?php endif; ?>
													<?php endforeach; ?>
													<td>
														<?php if (FALSE): ?>
															<button name="quitar_detalle_<?php echo $cant_rows_nro; ?>" type="button" id="quitar_detalle_<?php echo $cant_rows_nro; ?>" onclick="quitarDetalle(this)" class="btn btn-danger btn-sm" title="Quitar Detalle">
																<i class="fa fa-remove"></i>
															</button>
														<?php endif; ?>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
									<?php if (FALSE): ?>
										<a href="javascript:void(0);" onclick="insertarDetalle()" title="Agregar Detalles" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Detalles</a>
									<?php endif; ?>
								</div>
							</div>
						</section>
					</div>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $orden_compra->id) : ''; ?>
					<a href="vales_combustible/ordenes_compra/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_input($cant_rows); ?>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php if ($txt_btn !== 'Agregar'): ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Asignaciones</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<table class="table table-bordered table-condensed table-striped">
						<thead>
							<tr>
								<th style="width:20%;">Combustible</th>
								<th style="width:12%;">M³/Litros</th>
								<th style="width:17%;">Asignado Facturas</th>
								<th style="width:17%;">Restante Facturas</th>
								<th style="width:17%;">Asignado Vales</th>
								<th style="width:17%;">Restante Vales</th>	
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($asignados)): ?>
								<?php $total_litros = 0; ?>
								<?php $total_asignado_remitos = 0; ?>
								<?php $total_asignado_vales = 0; ?>
								<?php foreach ($asignados as $Asignado): ?>
									<tr>
										<td><?php echo $Asignado->tipo_combustible; ?></td>
										<td style="text-align:right;"><?php echo number_format($Asignado->litros, 2, ',', '.'); ?></td>
										<td style="text-align:right;"><?php echo number_format($Asignado->asignado_remitos, 2, ',', '.'); ?></td>
										<td style="text-align:right;"><?php echo number_format($Asignado->litros - $Asignado->asignado_remitos, 2, ',', '.'); ?></td>
										<td style="text-align:right;"><?php echo number_format($Asignado->asignado_vales, 2, ',', '.'); ?></td>
										<td style="text-align:right;"><?php echo number_format($Asignado->litros - $Asignado->asignado_vales, 2, ',', '.'); ?></td>
									</tr>
									<?php $total_litros += $Asignado->litros; ?>
									<?php $total_asignado_remitos += $Asignado->asignado_remitos; ?>
									<?php $total_asignado_vales += $Asignado->asignado_vales; ?>
								<?php endforeach; ?>
								<tr style="font-weight:bold;">
									<td>TOTALES</td>
									<td style="text-align:right;"><?php echo number_format($total_litros, 2, ',', '.'); ?></td>
									<td style="text-align:right;"><?php echo number_format($total_asignado_remitos, 2, ',', '.'); ?></td>
									<td style="text-align:right;"><?php echo number_format($total_litros - $total_asignado_remitos, 2, ',', '.'); ?></td>
									<td style="text-align:right;"><?php echo number_format($total_asignado_vales, 2, ',', '.'); ?></td>
									<td style="text-align:right;"><?php echo number_format($total_litros - $total_asignado_vales, 2, ',', '.'); ?></td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
					<table class="table table-bordered table-condensed table-striped">
						<thead>
							<tr>
								<th style="width:20%;">Factura</th>
								<th style="width:40%;">Combustible</th>
								<th style="width:20%;">M³/Litros</th>
								<th style="width:20%;">Costo</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($facturas_asignadas)): ?>
								<?php $total_litros = 0; ?>
								<?php $total_costo = 0; ?>
								<?php foreach ($facturas_asignadas as $Factura): ?>
									<tr>
										<td style="text-align:right;"><a href="vales_combustible/facturas/ver/<?php echo $Factura->id; ?>" target="_blank"><?php echo $Factura->factura; ?></a></td>
										<td><?php echo $Factura->tipo_combustible; ?></td>
										<td style="text-align:right;"><?php echo number_format($Factura->litros, 2, ',', '.'); ?></td>
										<td style="text-align:right;"><?php echo "$ " . number_format($Factura->costo, 2, ',', '.'); ?></td>
									</tr>
									<?php $total_litros += $Factura->litros; ?>
									<?php $total_costo += $Factura->costo; ?>
								<?php endforeach; ?>
								<tr style="font-weight:bold;">
									<td colspan="2">TOTALES</td>
									<td style="text-align:right;"><?php echo number_format($total_litros, 2, ',', '.'); ?></td>
									<td style="text-align:right;"><?php echo "$ " . number_format($total_costo, 2, ',', '.'); ?></td>
								</tr>
							<?php else: ?>
								<tr>
									<td style="text-align:center;" colspan="4">Sin remitos</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
					<table class="table table-bordered table-condensed table-striped">
						<thead>
							<tr>
								<th style="width:20%;">Vale</th>
								<th style="width:40%;">Combustible</th>
								<th style="width:40%;">M³/Litros</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($vales_asignados)): ?>
								<?php $total_litros = 0; ?>
								<?php $total_costo = 0; ?>
								<?php foreach ($vales_asignados as $Vale): ?>
									<tr>
										<td style="text-align:right;"><a href="vales_combustible/vales/ver/<?php echo $Vale->id; ?>" target="_blank"><?php echo $Vale->id; ?></a></td>
										<td><?php echo $Vale->tipo_combustible; ?></td>
										<td style="text-align:right;"><?php echo number_format($Vale->metros_cubicos, 2, ',', '.'); ?></td>
									</tr>
									<?php $total_litros += $Vale->metros_cubicos; ?>
								<?php endforeach; ?>
								<tr style="font-weight:bold;">
									<td colspan="2">TOTALES</td>
									<td style="text-align:right;"><?php echo number_format($total_litros, 2, ',', '.'); ?></td>
								</tr>
							<?php else: ?>
								<tr>
									<td style="text-align:center;" colspan="3">Sin vales</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>