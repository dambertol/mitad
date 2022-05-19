<!--
   /*
	* Vista Perfil de Usuario
	* Autor: Leandro
	* Creado: 26/01/2017
	* Modificado: 17/07/2018 (Leandro)
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
				<h2>Mi Perfil</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="form-horizontal">
					<div class="row">
						<div class="form-group">
							<?php echo lang('edit_user_username_label', 'username', array('class' => "col-sm-2 control-label")); ?>
							<div class="col-sm-10">
								<?php echo form_input($username, '', 'class="form-control"'); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo lang('edit_user_fname_label', 'nombre', array('class' => "col-sm-2 control-label")); ?>
							<div class="col-sm-10">
								<?php echo form_input($nombre, '', 'class="form-control"'); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo lang('edit_user_lname_label', 'apellido', array('class' => "col-sm-2 control-label")); ?>
							<div class="col-sm-10">
								<?php echo form_input($apellido, '', 'class="form-control"'); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo lang('edit_user_email_label', 'email', array('class' => "col-sm-2 control-label")); ?>
							<div class="col-sm-10">
								<?php echo form_input($email, '', 'class="form-control"'); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo lang('edit_user_groups_label', 'groups', array('class' => "col-sm-2 control-label")); ?>
							<div class="col-sm-10">
								<?php echo form_input($grupos, '', 'class="form-control"'); ?>
							</div>
						</div>
					</div>
					<div class="ln_solid"></div>
					<div class="text-center">
						<a href="escritorio" class="btn btn-default btn-sm">Volver</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>