<!--
	/*
	 * Vista Imprimir Vales
	 * Autor: Leandro
	 * Creado: 15/11/2017
	 * Modificado: 22/01/2019 (Leandro)
	 */
-->
<script>
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Vale'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
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
					<?php echo form_button(array('onClick' => 'recargar_vales();', 'name' => 'refresh', 'class' => 'btn btn-primary btn-sm', 'title' => $txt_btn, 'content' => $txt_btn)); ?>
					<a href="vales_combustible/vales/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php $i = 0; ?>
<?php if (!empty($vales)): ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2><?php echo (!empty($title_view)) ? $title_view : 'Vale'; ?></h2>
					<?php echo form_button(array('onClick' => 'imprimir_vales_pdf();', 'name' => 'print_pdf', 'class' => 'btn btn-primary btn-sm pull-right', 'title' => 'Imprimir PDF', 'content' => '<i class="fa fa-file-pdf-o"></i> PDF')); ?>
					<?php echo form_button(array('onClick' => 'imprimir_vales();', 'name' => 'print', 'class' => 'btn btn-primary btn-sm pull-right', 'title' => 'Imprimir', 'content' => '<i class="fa fa-print"></i> Imprimir')); ?>
					<div class="clearfix"></div>
				</div>
				<div class="x_content" id="div-imprimir">
					<h2 style="text-align:center;">Impresión de Vales</h2>
					<h4 style="text-align:center;">Desde: <?php echo 'VC' . str_pad($desde, 6, '0', STR_PAD_LEFT); ?> - Hasta: <?php echo 'VC' . str_pad($hasta, 6, '0', STR_PAD_LEFT); ?></h4>
					<?php $area_actual = 0; ?>
					<?php foreach ($vales as $vale): ?>
						<?php if ($area_actual !== $vale->area_codigo): ?>
							<?php $area_actual = $vale->area_codigo; ?>
							<?php if ($i % 2 === 1): ?>
								<?php $i++; ?>
							<?php endif; ?>
							<?php if ($i % 6 === 0 && $i !== 0): ?>
								<div class="page-break">&nbsp;</div>
							<?php endif; ?>
							<table style="width:100%; margin:1px; font-size:14px; font-weight:bold;">
								<tbody>
									<tr>
										<td><?php echo "$vale->area_codigo-$vale->area_nombre"; ?></td>
									</tr>
								</tbody>
							</table>
						<?php endif; ?>
						<?php $i++; ?>
						<table class="tabla_impresion_vales">
							<tbody>
								<tr>
									<td style="border:1px solid; width:100px;">
										<img style="width:100px;" src="img/vales_combustible/logo_lujan_vale.png" alt="Luján de Cuyo"/>
									</td>
									<td colspan="2" style="text-align:center;">
										<span style="font-size:16px;">VALE DE COMBUSTIBLE</span><br/>
										Vale por <?php echo $vale->metros_cubicos . ' ' . ($vale->tipo_combustible === 'GNC' ? 'm3' : 'lts') . ' de ' . $vale->tipo_combustible; ?>
									</td>
								</tr>
								<tr>
									<td colspan="3" style="font-weight:bold; text-align:center; font-size:16px; text-transform:uppercase;">ESTACIÓN <?php echo $vale->estacion; ?></td>
								</tr>
								<tr>
									<td colspan="3" style="font-weight:bold; font-size:16px;">
										<div style="float:left; width:50%;">N° Vale: <?php echo 'VC' . str_pad($vale->id, 6, '0', STR_PAD_LEFT); ?></div>
										<div style="float:right; width:50%;">Vencimiento: <?php echo date_format(new DateTime($vale->vencimiento), 'd/m/Y'); ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="3" style="font-size:16px; font-weight:bold;">
										<span class="area_impresion_vales">
											Para el Área: <span style="font-size:10px;"><?php echo "$vale->area_codigo-$vale->area_nombre"; ?></span>
										</span>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="border:1px solid; min-width:130px;">
										N° de patente, o<br/>nombre maquinaria<br/>
										<span style="overflow:hidden; white-space:nowrap; text-overflow:ellipsis; width:120px; display:inline-block;">
											<?php $carga = !empty($vale->forma_carga) && $vale->forma_carga === 'Bidón' ? ' (Bidón)' : ''; ?>
											<?php echo!empty($vale->dominio) ? $vale->dominio . $carga : (!empty($vale->vehiculo) ? $vale->vehiculo . $carga : '.....................' ); ?>
										</span>
									</td>
									<td>
										Firma:...............................................<br/>
										<?php echo!empty($vale->persona_major) ? $vale->persona_major : 'Aclaración: .......................................'; ?><br/>
										<?php echo!empty($vale->persona_id) ? $vale->persona_id : 'DNI: .................................................'; ?>
									</td>
								</tr>
								<tr style="height:55px;">
									<td colspan="3"></td>
								</tr>
								<tr>
									<td colspan="3" style="text-align:center; font-size:12px;">No se aceptarán vales con tachaduras y/o enmiendas</td>
								</tr>
							</tbody>
						</table>
						<?php if ($i % 6 === 0 && $i !== 0): ?>
							<div class="page-break">&nbsp;</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>










