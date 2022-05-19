<!--
   /*
	* Vista Asignar de Personal
	* Autor: Leandro
	* Creado: 28/09/2016
	* Modificado: 07/08/2018 (Leandro)
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Usuarios'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = array('class' => 'btn btn-primary btn-sm', 'title' => 'Asignar'); ?>
				<?php echo form_open(uri_string(), 'class="form-horizontal"'); ?>
				<div class="row" id="row-usuario">
					<h2 class="text-center">Datos Usuario</h2>
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
					<?php foreach ($fields_oficina as $field_oficina): ?>
						<div class="form-group">
							<?php echo $field_oficina['label']; ?> 
							<?php echo $field_oficina['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="row" id="row-persona">
					<br />
					<h2 class="text-center">Datos Persona</h2>
					<?php foreach ($fields_persona as $key => $field_persona): ?>
						<div class="form-group">
							<?php echo $field_persona['label']; ?> 
							<?php echo $field_persona['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo form_submit($data_submit, 'Asignar'); ?>
					<a href="asistencia/usuarios/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>