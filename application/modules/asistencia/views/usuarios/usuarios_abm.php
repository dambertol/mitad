<!--
   /*
	* Vista ABM de Usuarios
	* Autor: Leandro
	* Creado: 19/09/2016
	* Modificado: 19/02/2020 (Leandro)
	*/
-->
<?php if ($txt_btn === 'Agregar') : ?>
	<script>
		var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
		$(document).ready(function() {
			var inicial = $('#persona').selectpicker('val');
			if (inicial === 'agregar' || inicial === '') {
				$('#row-persona :input').attr("disabled", false);
				$("#sexo").selectpicker('refresh');
				$("#nacionalidad").selectpicker('refresh');
			} else {
				buscar_persona_usuario(inicial);
				$('#row-persona :input').attr("disabled", true);
				$("#sexo").selectpicker('refresh');
				$("#nacionalidad").selectpicker('refresh');
			}
			$('#persona').on('changed.bs.select', function(e) {
				if (this.value === 'agregar') {
					limpiar_persona_usuario();
					$('#row-persona :input').attr("disabled", false);
					$("#sexo").selectpicker('refresh');
					$("#nacionalidad").selectpicker('refresh');
				} else {
					buscar_persona_usuario(this.value);
					$('#row-persona :input').attr("disabled", true);
					$("#sexo").selectpicker('refresh');
					$("#nacionalidad").selectpicker('refresh');
				}
			});
			$("#cargar_datos").click(function() {
				buscar_empleado_usuario($("#dni").val());
			});
			$('#cuil').inputmask({
				mask: '99-99999999-9',
				removeMaskOnSubmit: true
			});
			$('#cuil').blur(function() {
				var input = this;
				var cuil = input.value;
				var resul = validaCuil(cuil);
				if (!resul) {
					Swal.fire({
						type: 'error',
						title: 'Error.',
						text: 'CUIL inválido',
						buttonsStyling: false,
						confirmButtonClass: 'btn btn-primary',
						confirmButtonText: 'Aceptar'
					}).then(function() {
						Swal.close();
						input.focus()
					});
				}
			});
	<?php if ($txt_btn === 'Editar' && $usuario_otros_modulos) : ?>
				$('#email').prop('readonly', true);
				$("#password").val('********');
				$('#password').prop('readonly', true);
				$("#password_confirm").val('********');
				$('#password_confirm').prop('readonly', true);
				$('#active').prop('disabled', true);
				$('#aviso-usuario-modalFooter').html('<button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Aceptar</button>');
				$('#aviso-usuario-modalBody').html('<p style="color:#E74C3C; font-weight:bold;">El usuario existe en el sistema con permisos en otros Módulos.</p><p>Solo podrá modificar el grupo deseado para el Módulo Asistencia. En caso de querer modificar datos (email, contraseña, etc.) deberá contactarse con el administrador.</p><p>Muchas gracias!</p>');
				$('#aviso-usuario-modal').modal('show');
	<?php endif; ?>
		});
	</script>
<?php endif; ?>
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
				<?php if ($txt_btn === 'Editar' && $usuario_otros_modulos): ?>
					<div class="alert alert-info alert-dismissible fade in" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
						</button>
						<i class="fa fa-info"></i>INFORMACIÓN<br>
						El Usuario tiene otros Módulos asociados, sólo podrá restablecerle su contraseña, para cambiar su estado o sus grupos contactar al administrador.
					</div>
				<?php endif; ?>
				<?php $data_submit = array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'class="form-horizontal"'); ?>
				<div class="row" id="row-usuario">
					<h2 class="text-center">Datos Usuario</h2>
					<?php foreach ($fields as $key => $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
					<?php if (!empty($fields_oficina)): ?>
						<?php foreach ($fields_oficina as $field_oficina): ?>
							<div class="form-group">
								<?php echo $field_oficina['label']; ?> 
								<?php echo $field_oficina['form']; ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<div class="row" id="row-persona">
					<br />
					<h2 class="text-center">Datos Persona</h2>
					<?php foreach ($fields_persona as $key => $field_persona): ?>
						<div class="form-group">
							<?php echo $field_persona['label']; ?> 
							<?php echo $field_persona['form']; ?>
						</div>
						<?php if ($txt_btn === 'Agregar' && $key === 'dni'): ?>
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-10 text-center">
									<?php echo form_button('cargar_datos', 'Cargar Datos M@jor', array('id' => 'cargar_datos', 'class' => 'btn btn-sm btn-primary')); ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $usuario->id) : ''; ?>
					<a href="asistencia/usuarios/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="aviso-usuario-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="aviso-usuario-modalLabel">Advertencia!</h4>
			</div>
			<div class="modal-body" id="aviso-usuario-modalBody"></div>
			<div class="modal-footer" id="aviso-usuario-modalFooter"></div>
		</div>
	</div>
</div>