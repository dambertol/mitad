<!--
	/*
	 * Vista ABM de Solicitante.
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 18/12/2019 (Leandro)
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Solicitantes'; ?></h2>
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
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $solicitant->id) : ''; ?>
					<a href="defunciones/solicitantes/listar" class="btn btn-default btn-sm">Cancelar</a>
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
					<h2>Operaciones</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<table class="table table-bordered table-condensed table-striped">
						<thead>
							<tr>
								<th style="width:15%;">Fecha Trámite</th>
								<th style="width:30%;">Difunto</th>
								<th style="width:20%;">Tipo Operación</th>
								<th style="width:15%;">Boleta Pago</th>
								<th style="width:15%;">Expediente</th>
								<th style="width:5%;">Ver</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($operaciones)): ?>
								<?php foreach ($operaciones as $Operacion): ?>
									<tr>
										<td style="text-align:right;"><?php echo date_format(new DateTime($Operacion->fecha_tramite), 'd/m/Y'); ?></td>
										<td><?php echo "$Operacion->difunto_nombre $Operacion->difunto_apellido"; ?></td>
										<td>
											<?php
											switch ($Operacion->tipo_operacion)
											{
												case '1':
													$tipo_operacion = "Concesión";
													break;
												case '2':
													$tipo_operacion = "Ornato";
													break;
												case '3':
													$tipo_operacion = "Reducción";
													break;
												case '4':
													$tipo_operacion = "Traslado";
													break;
											}
											echo $tipo_operacion;
											?>
										</td>
										<td><?php echo $Operacion->boleta_pago; ?></td>
										<td><?php echo $Operacion->expediente; ?></td>
										<td><?php echo anchor("defunciones/operaciones/ver/$Operacion->id", 'Ver', 'target="_blank"'); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="6" style="text-align:center; font-weight:bold;">Sin operaciones</td>
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