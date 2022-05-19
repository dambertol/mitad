<!--
	/*
	 * Vista ABM de Factura
	 * Autor: Leandro
	 * Creado: 08/11/2017
	 * Modificado: 19/03/2018 (Leandro)
	 */
-->
<script>
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	$(document).ready(function() {
<?php if ($txt_btn === 'Agregar' || $txt_btn === 'Editar'): ?>
			$("#tipo_combustible").change(function() {
				get_ordenes_tipo();
				return false;
			});
<?php endif; ?>
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Factura'; ?></h2>
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
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $factura->id) : ''; ?>
					<a href="vales_combustible/facturas/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
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
					<h2>Remitos asignados</h2>
					<?php echo anchor("vales_combustible/remitos/agregar/$factura->id", 'Crear Remito', 'class="btn btn-primary btn-sm"'); ?>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<table class="table table-bordered table-condensed table-striped">
						<thead>
							<tr>
								<th style="width:25%;">Número</th>
								<th style="width:25%;">Tipo</th>
								<th style="width:25%;">M³/Litros</th>
								<th style="width:25%;">Costo</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($remitos_asignados)): ?>
								<?php $total_litros = 0; ?>
								<?php $total_costo = 0; ?>
								<?php foreach ($remitos_asignados as $Remito): ?>
									<tr>
										<td style="text-align:right;"><a href="vales_combustible/remitos/ver/<?php echo $Remito->id; ?>" target="_blank"><?php echo $Remito->remito; ?></a></td>
										<td><?php echo $Remito->tipo_combustible; ?></td>
										<td style="text-align:right;"><?php echo number_format($Remito->litros, 2, ',', '.'); ?></td>
										<td style="text-align:right;"><?php echo '$ ' . number_format($Remito->costo, 2, ',', '.'); ?></td>
									</tr>
									<?php $total_litros += $Remito->litros; ?>
									<?php $total_costo += $Remito->costo; ?>
								<?php endforeach; ?>
								<tr style="text-align:center; font-weight:bold;">
									<td colspan="2">TOTAL</td>
									<td style="text-align:right;"><?php echo number_format($total_litros, 2, ',', '.'); ?></td>
									<td style="text-align:right;"><?php echo '$ ' . number_format($total_costo, 2, ',', '.'); ?></td>
								</tr>
							<?php else: ?>
								<tr>
									<td colspan="4" style="text-align:center; font-weight:bold;">Sin remitos</td>
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