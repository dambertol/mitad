<!--
   /*
	* Vista Change Password
	* Autor: Leandro
	* Creado: 26/01/2017
	* Modificado: 26/01/2017 (Leandro)
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
				<h2>Cambiar contraseña</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo form_open("auth/change_password", 'class="form-horizontal"'); ?>
				<div class="row">
					<div class="form-group">
						<?php echo lang('change_password_old_password_label', 'old', array('class' => "col-sm-2 control-label")); ?>
						<div class="col-sm-10">
							<?php echo form_input($old_password, '', 'class="form-control"'); ?>
						</div>
					</div>
					<div class="form-group">
						<label for="new" class="col-sm-2 control-label"><?php echo sprintf(lang('change_password_new_password_label'), $min_password_length); ?></label> 
						<div class="col-sm-10">
							<?php echo form_input($new_password, '', 'class="form-control"'); ?>
						</div>
					</div>
					<div class="form-group">
						<?php echo lang('change_password_new_password_confirm_label', 'new_confirm', array('class' => "col-sm-2 control-label")); ?>
						<div class="col-sm-10">
							<?php echo form_input($new_password_confirm, '', 'class="form-control"'); ?>
						</div>
					</div>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo form_input($user_id); ?>
					<?php echo form_submit('submit', lang('change_password_submit_btn'), 'class="btn btn-primary btn-sm"'); ?>
					<a href="escritorio" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>