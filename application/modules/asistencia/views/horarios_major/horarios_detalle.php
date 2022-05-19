<!--
   /*
	* Vista Detalle de Horarios Major
	* Autor: Leandro
	* Creado: 26/10/2017
	* Modificado: 09/01/2018 (Leandro)
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Horarios Major'; ?></h2>
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
				<div class="x_title">
					<h2>Secuencia cargada</h2>
					<div class="clearfix"></div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<?php if ($detalle[0]->hora_Tipo === 'N' || $detalle[0]->hora_Tipo === 'F'): ?>
							<table class="table table-condensed table-bordered table-detalle-horario">
								<thead>
									<tr>
										<th>Lunes</th>
										<th>Martes</th>
										<th>Miercoles</th>
										<th>Jueves</th>
										<th>Viernes</th>
										<th>Sábado</th>
										<th>Domingo</th>
										<th>Feriados</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<?php for ($i = 1; $i <= 8; $i++): ?>
											<td>
												<?php if ($detalle[0]->{"hora_DiaSec{$i}Ent"} !== '00:00' || $detalle[0]->{"hora_DiaSec{$i}Sal"} !== '00:00'): ?>
													<?php if ($detalle[0]->hora_Tipo === 'N'): ?>
														<div class="detalle-horario label-success">
															<div class="detalle-horario-texto">Entrada</div>
															<div class="detalle-horario-hora"><?php echo $detalle[0]->{"hora_DiaSec{$i}Ent"}; ?></div>
														</div>
														<div class="detalle-horario label-danger">
															<div class="detalle-horario-texto">Salida</div>
															<div class="detalle-horario-hora"><?php echo $detalle[0]->{"hora_DiaSec{$i}Sal"}; ?></div>
														</div>
													<?php elseif ($detalle[0]->hora_Tipo === 'F'): ?>
														<div class="detalle-horario label-info">
															<div class="detalle-horario-texto">Entrada</div>
															<div class="detalle-horario-hora"><?php echo $detalle[0]->{"hora_DiaSec{$i}Ent"}; ?></div>
														</div>
														<div class="detalle-horario label-danger">
															<div class="detalle-horario-texto">Horas</div>
															<div class="detalle-horario-hora"><?php echo $detalle[0]->{"hora_DiaSec{$i}Cant"}; ?></div>
														</div>
														<div class="detalle-horario label-info">
															<div class="detalle-horario-texto">Salida</div>
															<div class="detalle-horario-hora"><?php echo $detalle[0]->{"hora_DiaSec{$i}Sal"}; ?></div>
														</div>
													<?php endif; ?>
												<?php else: ?>
													<div class="detalle-horario label-default">
														<div class="detalle-horario-texto">No Laborable</div>
													</div>
												<?php endif; ?>
											</td>
										<?php endfor; ?>
									</tr>
								</tbody>
							</table>
						<?php else: ?>
							<table class="table table-condensed table-bordered table-detalle-horario">
								<thead>
									<tr>
										<?php $dia_secuencia = 1; ?>
										<?php for ($i = 1; $i <= 20; $i++): ?>
											<?php if ($detalle[0]->{"hora_DiaSec{$i}Cant"} === 0): ?>
												<?php break; ?>
											<?php else: ?>
												<?php for ($j = 1; $j <= $detalle[0]->{"hora_DiaSec{$i}Cant"}; $j++): ?>
													<th>Día <?php echo $dia_secuencia; ?></th>
													<?php $dia_secuencia++; ?>
												<?php endfor; ?>
											<?php endif; ?>
										<?php endfor; ?>
									</tr>
								</thead>
								<tbody>
									<tr>
										<?php for ($i = 1; $i <= 20; $i++): ?>
											<?php if ($detalle[0]->{"hora_DiaSec{$i}Cant"} === 0): ?>
												<?php break; ?>
											<?php else: ?>
												<?php for ($j = 1; $j <= $detalle[0]->{"hora_DiaSec{$i}Cant"}; $j++): ?>
													<td>
														<?php if ($detalle[0]->{"hora_DiaSec{$i}Ent"} !== '00:00' || $detalle[0]->{"hora_DiaSec{$i}Sal"} !== '00:00'): ?>
															<div class="detalle-horario label-success">
																<div class="detalle-horario-texto">Entrada</div>
																<div class="detalle-horario-hora"><?php echo $detalle[0]->{"hora_DiaSec{$i}Ent"}; ?></div>
															</div>
															<div class="detalle-horario label-danger">
																<div class="detalle-horario-texto">Salida</div>
																<div class="detalle-horario-hora"><?php echo $detalle[0]->{"hora_DiaSec{$i}Sal"}; ?></div>
															</div>
														<?php else: ?>
															<div class="detalle-horario label-default">
																<div class="detalle-horario-texto">No Laborable</div>
															</div>
														<?php endif; ?>
													</td>
													<?php $dia_secuencia++; ?>
												<?php endfor; ?>
											<?php endif; ?>
										<?php endfor; ?>
									</tr>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $categoria->id) : ''; ?>
					<a href="asistencia/horarios_major/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
