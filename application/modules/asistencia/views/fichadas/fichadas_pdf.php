<div style="width: 43%; float: left; height: 100px;"></div>
<div style="width: 57%; float: left; height: 100px;">
	<img src="img/generales/logo_lujan_001.png" alt="Luján de Cuyo" />
</div>
<div class="clearfix"></div>
<div class="content-fichadas">
	<p>Planilla de control de asistencia y puntualidad</p>
	<p><?php echo $mes_planilla; ?></p>
	<b>Empleador:</b> MUNICIPALIDAD DE LUJÁN DE CUYO (C.U.I.T. 30999087600)<br />
	<b>Empleado:</b> <?php if (!empty($empleado->pers_Apellido) && !empty($empleado->pers_Nombre)) echo $empleado->pers_Apellido . ', ' . $empleado->pers_Nombre; ?> (<?php if (!empty($empleado->tper_Nombre)) echo $empleado->tper_Nombre . ' '; ?><?php if (!empty($empleado->labo_Codigo)) echo $empleado->labo_Codigo; ?><?php if (!empty($empleado->labo_CUIL)) echo ' - C.U.I.L. ' . $empleado->labo_CUIL; ?>)<br />
	<b>Legajo:</b> <?php if (!empty($empleado->labo_Codigo)) echo $empleado->labo_Codigo; ?><br />
	<b>Oficina:</b> <?php if (!empty($empleado->ofi_Oficina)) echo $empleado->ofi_Oficina . " - "; ?><?php if (!empty($empleado->ofi_Descripcion)) echo $empleado->ofi_Descripcion; ?> <?php if (!empty($empleado->prtn_Codigo)){ if ($empleado->prtn_Codigo === '07') echo " (Locación)"; elseif ($empleado->prtn_Codigo === '10') echo " (Plan)";} ?><br />
	<b>Horario:</b> <?php if (!empty($empleado->hora_Descripcion)) echo $empleado->hora_Descripcion; ?><br />
	<table class="table table-condensed table-bordered table-fichadas">
		<thead>
			<tr>
				<th style="width:60px;text-align:center;">Día</th>
				<th style="width:140px;text-align:center;">Fecha</th>
				<th style="width:80px;text-align:center;">Entrada</th>
				<th style="width:80px;text-align:center;">Salida</th>
				<th style="width:80px;text-align:center;">Entrada</th>
				<th style="width:80px;text-align:center;">Salida</th>
				<th style="width:80px;text-align:center;">Hs. Día</th>
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
				<td style="font-weight:bold;text-align:center;"><?php echo $day; ?></td>
				<td style="font-weight:bold;text-align:center;"><?php echo (array_key_exists((new DateTime($fecha))->format('dmY'), $feriados)) ? $fecha . ' (Feriado)' : $fecha; ?></td>
				<?php if (!empty($Contenido['A'])): ?>
					<td colspan="5" style="background-color:#ddd; text-align:center;"><?php if (!empty($Contenido['A'])) echo $Contenido['A']; ?></td>
				<?php else: ?>
					<td style="text-align:center;"><?php if (!empty($Contenido['E'][0])) echo $Contenido['E'][0] . ' (' . $Contenido['R_E'][0] . ')'; ?></td>
					<td style="text-align:center;"><?php if (!empty($Contenido['S'][0])) echo $Contenido['S'][0] . ' (' . $Contenido['R_S'][0] . ')'; ?></td>
					<td style="text-align:center;"><?php if (!empty($Contenido['E'][1])) echo $Contenido['E'][1] . ' (' . $Contenido['R_E'][1] . ')'; ?></td>
					<td style="text-align:center;"><?php if (!empty($Contenido['S'][1])) echo $Contenido['S'][1] . ' (' . $Contenido['R_S'][1] . ')'; ?></td>
					<td style="font-weight:bold;text-align:center;"><?php if (!empty($Contenido['total'])) echo $Contenido['total']; ?></td>
				<?php endif; ?>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	Planilla generada el: <?php echo date_format(new DateTime(), 'd/m/Y h:i:s'); ?><br />
	Relojes: (1)Taboada 1, (2)Centro Cívico 2, (3)Hacienda, (4)Obrador, (5)Policia Vial, (6)Polideportivo,<br />
	(7)Deleg Chacras, (8)Deleg Carrodilla, (9)Santa Elena, (10)Desarrollo Social, (11)Planta Potabilizadora,<br />
	(12)Centro Cívico 1, (13)Estación Ferri, (14)Cementerio, (15)Deleg Perdriel, (16)Deleg Agrelo,<br />
	(17)Deleg Ugarteche, (18)Deleg Carrizal, (19)Biblioteca, (20)Deleg Compuertas, (21)Deleg Pedemonte,<br />
	(22)Deleg Drummond, (23)Centro Cívico, (24)Playón Este, (25)Polid. Carrodilla
</div>