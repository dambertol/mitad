<!--
	/*
	 * Ver Fichadas
	 * Autor: Leandro
	 * Creado: 05/07/2016
	 * Modificado: 20/09/2019 (Leandro)
	 */
-->
<script>
	$(document).ready(function() {
		$('#selector-mes').datetimepicker({
			format: "MMMM YYYY",
			maxDate: moment(),
			locale: "es",
			useCurrent: false
		}).on('dp.change', function(e) {
			window.location.href = CI.base_url + 'asistencia/fichadas/ver/<?php echo $labo_Codigo; ?>/' + moment(e.date._d).format('YYYY/MM');
		});
		$("#button_descargar").click(function() {
			window.location.href = CI.base_url + 'asistencia/fichadas/descargar/<?php echo $labo_Codigo; ?>/<?php echo $anio; ?>/<?php echo $mes; ?>';
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
		<strong>MSJ!</strong><?php echo $message; ?>
	</div>
<?php endif; ?>
<div class="row">
	<div class="col-xs-12">
		<div class="img-fichadas"><img src="img/generales/logo_lujan_001.png" alt="Luján de Cuyo" /></div>
		<div class="content-fichadas">
			<p>Planilla de control de asistencia y puntualidad</p>
			<div class="row">
				<div class="col-xs-6 col-xs-offset-3 col-sm-4 col-sm-offset-4"><input type="text" id="selector-mes" class="form-control text-center no-autofocus" value="<?php echo $mes_planilla; ?>"></div>
			</div>
			<b>Empleador:</b> MUNICIPALIDAD DE LUJÁN DE CUYO (C.U.I.T. 30999087600)<br />
			<b>Empleado:</b> <?php if (!empty($empleado->pers_Apellido) && !empty($empleado->pers_Nombre)) echo $empleado->pers_Apellido . ', ' . $empleado->pers_Nombre; ?> (<?php if (!empty($empleado->tper_Nombre)) echo $empleado->tper_Nombre . ' '; ?><?php if (!empty($empleado->labo_Codigo)) echo $empleado->labo_Codigo; ?><?php if (!empty($empleado->labo_CUIL)) echo ' - C.U.I.L. ' . $empleado->labo_CUIL; ?>)<br />
			<b>Legajo:</b> <?php if (!empty($empleado->labo_Codigo)) echo $empleado->labo_Codigo; ?><br />
			<b>Oficina:</b> <?php if (!empty($empleado->ofi_Oficina)) echo $empleado->ofi_Oficina . " - "; ?><?php if (!empty($empleado->ofi_Descripcion)) echo $empleado->ofi_Descripcion; ?><?php if (!empty($empleado->prtn_Codigo)){ if ($empleado->prtn_Codigo === '07') echo " (Locación)"; elseif ($empleado->prtn_Codigo === '10') echo " (Plan)";} ?><br />
			<b>Horario:</b> <?php if (!empty($empleado->hora_Descripcion)) echo $empleado->hora_Descripcion; ?><br />
			<table class="table table-condensed table-bordered table-fichadas">
				<thead>
					<tr>
						<th>Día</th>
						<th>Fecha</th>
						<th>Entrada</th>
						<th>Salida</th>
						<th>Entrada</th>
						<th>Salida</th>
						<th>Hs. Día</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($planilla))
						foreach ($planilla as $fecha => $Contenido)
						{
							switch (date_format(new DateTime($fecha), 'w'))
							{
								case 0: $day = 'Dom';
									break;
								case 1: $day = 'Lun';
									break;
								case 2: $day = 'Mar';
									break;
								case 3: $day = 'Mié';
									break;
								case 4: $day = 'Jue';
									break;
								case 5: $day = 'Vie';
									break;
								case 6: $day = 'Sáb';
									break;
							}
							if ($day === 'Dom')
								echo '<tr class="division">';
							else
								echo '<tr>';
							?>
						<td><b><?php echo $day; ?></b></td>
						<td><b><?php echo (array_key_exists((new DateTime($fecha))->format('dmY'), $feriados)) ? $fecha . ' (Feriado)' : $fecha; ?></b></td>
		<?php if (!empty($Contenido['A'])): ?>
							<td colspan="5" style="background-color:#ddd;"><?php if (!empty($Contenido['A'])) echo $Contenido['A']; ?></td>
		<?php else: ?>
							<td><?php if (!empty($Contenido['E'][0])) echo $Contenido['E'][0] . ' (' . $Contenido['R_E'][0] . ')'; ?></td>
							<td><?php if (!empty($Contenido['S'][0])) echo $Contenido['S'][0] . ' (' . $Contenido['R_S'][0] . ')'; ?></td>
							<td><?php if (!empty($Contenido['E'][1])) echo $Contenido['E'][1] . ' (' . $Contenido['R_E'][1] . ')'; ?></td>
							<td><?php if (!empty($Contenido['S'][1])) echo $Contenido['S'][1] . ' (' . $Contenido['R_S'][1] . ')'; ?></td>
							<td><?php if (!empty($Contenido['total'])) echo '<b>' . $Contenido['total'] . '</b>'; ?></td>
		<?php endif; ?>
						</tr>
	<?php } ?>
				</tbody>
			</table>
			<b>Planilla generada el:</b> <?php echo date_format(new DateTime(), 'd/m/Y h:i:s'); ?><br />
			<b>Relojes:</b> (1)Taboada 1, (2)Centro Cívico 2, (3)Hacienda, (4)Obrador, (5)Policia Vial, (6)Polideportivo,<br />
			(7)Deleg Chacras, (8)Deleg Carrodilla, (9)Santa Elena, (10)Desarrollo Social, (11)Planta Potabilizadora,<br />
			(12)Centro Cívico 1, (13)Estación Ferri, (14)Cementerio, (15)Deleg Perdriel, (16)Deleg Agrelo,<br />
			(17)Deleg Ugarteche, (18)Deleg Carrizal, (19)Biblioteca, (20)Deleg Compuertas, (21)Deleg Pedemonte,<br />
			(22)Deleg Drummond, (23)Centro Cívico, (24)Playón Este, (25)Polid. Carrodilla
		</div>
			<?php
			$data_button_imprimir = array('class' => 'btn btn-primary btn-sm', 'title' => 'Imprimir', 'onclick' => "javascript:window.print();", 'name' => 'Imprimir');
			$data_button_descargar = array('class' => 'btn btn-primary btn-sm', 'title' => 'Descargar', 'id' => 'button_descargar', 'name' => 'Descargar');
			?>
		<div class="btn-fichadas" style="text-align:center; margin-bottom:20px;">
<?php echo form_button($data_button_imprimir, 'Imprimir'); ?>
<?php echo form_button($data_button_descargar, 'Descargar'); ?>
		</div>
	</div>
</div>