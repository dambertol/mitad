<!--
	/*
	 * Vista ABM de Parentezco.
	 * Autor: Leandro
	 * Creado: 09/09/2019
	 * Modificado: 23/10/2019 (Leandro)
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Parentezcos'; ?></h2>
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
				<div class="row" id="row-parentezco">
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row" id="row-persona">
							<br />
							<h2 class="text-center">Datos Persona</h2>
							<?php foreach ($fields_persona as $field_persona): ?>
								<div class="form-group">
									<?php echo $field_persona['label']; ?> 
									<?php echo $field_persona['form']; ?>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="row" id="row-domicilio">
							<br />
							<h2 class="text-center">Datos Domilicio Persona</h2>
							<?php foreach ($fields_domicilio as $field_domicilio): ?>
								<div class="form-group">
									<?php echo $field_domicilio['label']; ?> 
									<?php echo $field_domicilio['form']; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row" id="row-pariente">
							<br />
							<h2 class="text-center">Datos Pariente</h2>
							<?php foreach ($fields_pariente as $field_pariente): ?>
								<div class="form-group">
									<?php echo $field_pariente['label']; ?> 
									<?php echo $field_pariente['form']; ?>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="row" id="row-domicilio-pariente">
							<br />
							<h2 class="text-center">Datos Domilicio Pariente</h2>
							<?php foreach ($fields_domicilio_pariente as $field_domicilio_pariente): ?>
								<div class="form-group">
									<?php echo $field_domicilio_pariente['label']; ?> 
									<?php echo $field_domicilio_pariente['form']; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $parentezcos_persona->id) : ''; ?>
					<a href="ninez_adolescencia/parentezcos_personas/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>
<script>
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	$(document).ready(function() {
<?php if ($txt_btn === 'Agregar'): ?>
			//PERSONA
			var inicial = $('#persona').selectpicker('val');
			if (inicial === 'agregar' || inicial === '') {
				$('#row-persona :input').attr("disabled", false);
				$('#row-domicilio :input').attr("disabled", false);
				$("#sexo").selectpicker('refresh');
				$("#nacionalidad").selectpicker('refresh');
				$("#localidad").selectpicker('refresh');
			} else {
				buscar_persona(inicial);
				$('#row-persona :input').attr("disabled", true);
				$('#row-domicilio :input').attr("disabled", true);
				$("#sexo").selectpicker('refresh');
				$("#nacionalidad").selectpicker('refresh');
				$("#localidad").selectpicker('refresh');
			}
			$('#persona').on('changed.bs.select', function(e) {
				if (this.value === 'agregar') {
					$('#row-persona :input').attr("disabled", false);
					$('#row-domicilio :input').attr("disabled", false);
					$("#sexo").selectpicker('refresh');
					$("#nacionalidad").selectpicker('refresh');
					$("#carga_domicilio").selectpicker('refresh');
					$("#localidad").selectpicker('refresh');
				} else {
					buscar_persona(this.value);
					$('#row-persona :input').attr("disabled", true);
					$('#row-domicilio :input').attr("disabled", true);
					$("#sexo").selectpicker('refresh');
					$("#nacionalidad").selectpicker('refresh');
					$("#carga_domicilio").selectpicker('refresh');
					$("#localidad").selectpicker('refresh');
				}
			});
			$('#carga_domicilio').on('changed.bs.select', function(e) {
				domicilio_row('Agregar');
			});

			//PARIENTE
			var pa_inicial = $('#pariente').selectpicker('val');
			if (pa_inicial === 'agregar' || pa_inicial === '') {
				$('#row-pariente :input').attr("disabled", false);
				$('#row-domicilio-pariente :input').attr("disabled", false);
				$("#pa_sexo").selectpicker('refresh');
				$("#pa_nacionalidad").selectpicker('refresh');
				$("#pa_localidad").selectpicker('refresh');
			} else {
				buscar_pariente(pa_inicial);
				$('#row-pariente :input').attr("disabled", true);
				$('#row-domicilio-pariente :input').attr("disabled", true);
				$("#pa_sexo").selectpicker('refresh');
				$("#pa_nacionalidad").selectpicker('refresh');
				$("#pa_localidad").selectpicker('refresh');
			}
			$('#pariente').on('changed.bs.select', function(e) {
				if (this.value === 'agregar') {
					$('#row-pariente :input').attr("disabled", false);
					$('#row-domicilio-pariente :input').attr("disabled", false);
					$("#pa_sexo").selectpicker('refresh');
					$("#pa_nacionalidad").selectpicker('refresh');
					$("#pa_carga_domicilio").selectpicker('refresh');
					$("#pa_localidad").selectpicker('refresh');
				} else {
					buscar_pariente(this.value);
					$('#row-pariente :input').attr("disabled", true);
					$('#row-domicilio-pariente :input').attr("disabled", true);
					$("#pa_sexo").selectpicker('refresh');
					$("#pa_nacionalidad").selectpicker('refresh');
					$("#pa_carga_domicilio").selectpicker('refresh');
					$("#pa_localidad").selectpicker('refresh');
				}
			});
			$('#pa_carga_domicilio').on('changed.bs.select', function(e) {
				domicilio_pariente_row('Agregar');
			});
<?php elseif ($txt_btn === 'Editar'): ?>
			//PERSONA
			$('#carga_domicilio').on('changed.bs.select', function(e) {
				domicilio_row('Editar');
			});

			//PARIENTE
			$('#pa_carga_domicilio').on('changed.bs.select', function(e) {
				domicilio_pariente_row('Editar');
			});
<?php endif; ?>
		//PERSONA
		domicilio_row('<?php echo $txt_btn; ?>');
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

		//PARIENTE
		domicilio_pariente_row('<?php echo $txt_btn; ?>');
		$('#pa_cuil').inputmask({
			mask: '99-99999999-9',
			removeMaskOnSubmit: true
		});
		$('#pa_cuil').blur(function() {
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
	});
</script>