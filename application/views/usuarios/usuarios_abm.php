<!--
   /*
	* Vista ABM de Usuarios
	* Autor: Leandro
	* Creado: 30/01/2017
	* Modificado: 23/10/2019 (Leandro)
	*/
-->
<?php if ($txt_btn === 'Agregar') : ?>
	<script>
		var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
		$(document).ready(function() {
			var inicial = $('#persona').selectpicker('val');
			if (inicial === 'agregar') {
	//				limpiar_persona_usuario();
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
			//			buscar_empleado_usuario($("#dni").val());
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
						confirmButtonClass: 'btn btn-primary'
					}).then(function() {
						Swal.close();
						input.focus()
					});
				}
			});
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
					<a href="usuarios/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>