<!--
	/*
	 * Vista Imprimir Vales PDF
	 * Autor: Leandro
	 * Creado: 15/11/2017
	 * Modificado: 22/01/2019 (Leandro)
	 */
-->
<?php $i = 0; ?>
<?php if (!empty($vales)): ?>
	<h2 style="text-align:center;">Impresión de Vales</h2>
	<h4 style="text-align:center;">Desde: <?php echo 'VC' . str_pad($desde, 6, '0', STR_PAD_LEFT); ?> - Hasta: <?php echo 'VC' . str_pad($hasta, 6, '0', STR_PAD_LEFT); ?></h4>
	<?php $area_actual = 0; ?>
	<?php foreach ($vales as $vale): ?>
		<?php if ($area_actual !== $vale->area_codigo): ?>
			<?php $area_actual = $vale->area_codigo; ?>
			<?php if ($i % 2 === 1): ?>
				<?php $i++; ?>
				<?php if ($i % 6 === 0 && $i !== 0): ?>
					<pagebreak />
				<?php endif; ?>
			<?php endif; ?>
			<div style="width:750px; float:left; margin:1px;"> 
				<table style="font-size:14px; font-weight:bold;">
					<tbody>
						<tr>
							<td><?php echo "$vale->area_codigo-$vale->area_nombre"; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
		<?php $i++; ?>
		<div style="width:375px; float:left; margin:1px;">
			<table style="border:1px solid; border-collapse:collapse; width:100%; font-family:serif; margin:2px;">
				<tbody>
					<tr>
						<td style="border:1px solid; width:100px;">
							<img style="width:100px;" src="img/vales_combustible/logo_lujan_vale.png" alt="Luján de Cuyo"/>
						</td>
						<td colspan="2" style="border:1px solid; text-align:center;">
							<span style="font-size:16px;">VALE DE COMBUSTIBLE</span><br/>
							Vale por <?php echo $vale->metros_cubicos . ' ' . ($vale->tipo_combustible === 'GNC' ? 'm3' : 'lts') . ' de ' . $vale->tipo_combustible; ?>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="border:1px solid; font-weight:bold; text-align:center; font-size:16px; text-transform:uppercase;">ESTACIÓN <?php echo $vale->estacion; ?></td>
					</tr>
					<tr>
						<td colspan="2" style="font-weight:bold; font-size:16px;">
							N° Vale: <?php echo 'VC' . str_pad($vale->id, 6, '0', STR_PAD_LEFT); ?>
						</td>
						<td style="font-weight:bold; font-size:16px; text-align:right; width:230px;">
							Vencimiento: <?php echo date_format(new DateTime($vale->vencimiento), 'd/m/Y'); ?>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="border:1px solid; font-size:16px; font-weight:bold;">
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
					<tr>
						<td colspan="3" style="height:60px; border:1px solid;"></td>
					</tr>
					<tr>
						<td colspan="3" style="border:1px solid; text-align:center; font-size:12px;">No se aceptarán vales con tachaduras y/o enmiendas</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php if ($i % 6 === 0 && $i !== 0): ?>
			<pagebreak />
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>