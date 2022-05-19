<!--
	/*
	 * Vista ABM de Constructor.
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 22/11/2019 (Leandro)
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Constructores'; ?></h2>
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
					<br />
					<h2 class="text-center">Datos Constructor</h2>
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if (!empty($fields_permiso)): ?>
					<div class="row">
						<br />
						<h2 class="text-center">Datos Permiso</h2>
						<?php foreach ($fields_permiso as $field): ?>
							<div class="form-group">
								<?php echo $field['label']; ?> 
								<?php echo $field['form']; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $constructor->id) : ''; ?>
					<a href="defunciones/constructores/listar" class="btn btn-default btn-sm">Cancelar</a>
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
					<h2>Permisos Actuales</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<table class="table table-bordered table-condensed table-striped">
						<thead>
							<tr>
								<th style="width:35%;">Fecha de Pago</th>
								<th style="width:30%;">Boleta de Pago</th>
								<th style="width:35%;">Vencimiento</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($permisos)): ?>
								<?php foreach ($permisos as $Permiso): ?>
									<tr>
										<td style="text-align:right;"><?php echo date_format(new DateTime($Permiso->fecha_pago), 'd/m/Y H:i:m'); ?></td>
										<td style="text-align:right;"><?php echo $Permiso->boleta_pago; ?></td>
										<td style="text-align:right;"><?php echo date_format(new DateTime($Permiso->vencimiento), 'd/m/Y'); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="3" style="text-align:center; font-weight:bold;">Sin permisos</td>
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