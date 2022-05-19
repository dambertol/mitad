<!--
   /*
	* Vista Buscador de Personal
	* Autor: Leandro
	* Creado: 23/02/2017
	* Modificado: 30/05/2017 (Leandro)
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Buscador'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'class="form-horizontal"'); ?>
				<div class="row">
					<div class="form-group">
						<?php echo $fields['legajo']['label']; ?>
						<?php echo $fields['legajo']['form']; ?>
					</div>
					<div class="form-group">
						<?php echo $fields['apellido']['label']; ?>
						<?php echo $fields['apellido']['form']; ?>
					</div>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo form_submit($data_submit, $txt_btn, 'id="btn-buscar"'); ?>
				</div>
				<?php if (!empty($empleados)) : ?>
					<br />
					<?php foreach ($empleados as $Empleado) : ?>
						<div class="row">
							<div class="form-group">
								<div class="col-lg-12">
									<div class="col-sm-8 col-sm-offset-2" style="border: solid #FFD318 2px; padding: 10px;">
										<div class="form-group">
											<div class="col-sm-3"><b>Legajo</b></div>
											<div class="col-sm-9"><?php echo $Empleado->labo_Codigo; ?></div>
										</div>
										<div class="form-group">
											<div class="col-sm-3"><b>Apellido</b></div>
											<div class="col-sm-9"><?php echo $Empleado->pers_Apellido; ?></div>
										</div>
										<div class="form-group">
											<div class="col-sm-3"><b>Nombre</b></div>
											<div class="col-sm-9"><?php echo $Empleado->pers_Nombre; ?></div>
										</div>
										<div class="form-group">
											<div class="col-sm-3"><b>Oficina</b></div>
											<div class="col-sm-9"><?php echo $Empleado->ofi_Oficina . ' - ' . $Empleado->ofi_Descripcion; ?></div>
										</div>
										<div class="form-group">
											<div class="col-sm-3"><b>Horario</b></div>
											<div class="col-sm-9"><?php echo $Empleado->hora_Codigo . ' - ' . $Empleado->hora_Descripcion; ?></div>
										</div>
										<div class="form-group">
											<div class="col-sm-12" style="text-align:center;"><a href="asistencia/fichadas/ver/<?php echo $Empleado->labo_Codigo; ?>" title="Ver fichadas" class="btn btn-primary btn-sm">Ver fichadas</a></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
