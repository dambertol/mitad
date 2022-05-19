<!--
	/*
	 * Vista ABM de Adulto Responsable.
	 * Autor: Leandro
	 * Creado: 13/09/2019
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Adultos Responsables'; ?></h2>
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
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $adultos_responsabl->id) : ''; ?>
					<a href="ninez_adolescencia/expedientes/ver/<?php echo $expediente_id; ?>" class="btn btn-default btn-sm">Cancelar</a>
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
<?php elseif ($txt_btn === 'Editar'): ?>
			//PERSONA
			$('#carga_domicilio').on('changed.bs.select', function(e) {
				domicilio_row('Editar');
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
	});
</script>