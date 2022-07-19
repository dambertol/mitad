<!--
/**
  * Vista ABM de Intermediacion.
  * Autor: Leandro
  * Creado: 14/03/2019 (Leandro)
  * Modificado: 05/04/2019 (Leandro)
  */
-->
<style> legend.group-border {
		width: inherit;
		/* Or auto */
		padding: 0 10px;
		/* To give a bit of padding on the left and right */
		border-bottom: none;
		margin-bottom: 0px;
	}
	fieldset.group-border {
		border: 1px groove #ddd !important;
		padding: 0 1.4em 1.4em 1.4em !important;
		margin: 0 0 1.5em 0 !important;
		-webkit-box-shadow: 0px 0px 0px 0px #000;
		box-shadow: 0px 0px 0px 0px #000;
	}
</style>
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Reclamos'; ?></h2>
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
					<div class="row change_col">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['cuit']['label']; ?> 
								<?php echo $fields['cuit']['form']; ?> 
							</div>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="form-group">
								<?php echo $fields['cantidad_personas']['label']; ?>
								<?php echo $fields['cantidad_personas']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['razon_social']['label']; ?>
								<?php echo $fields['razon_social']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['domicilio']['label']; ?>
								<?php echo $fields['domicilio']['form']; ?>
							</div>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="form-group">
								<?php echo $fields['genero']['label']; ?>
								<?php echo $fields['genero']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['distrito']['label']; ?>
								<?php echo $fields['distrito']['form']; ?>
							</div>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="form-group">
								<?php echo $fields['rango_edad']['label']; ?>
								<?php echo $fields['rango_edad']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['telefono_empresa']['label']; ?>
								<?php echo $fields['telefono_empresa']['form']; ?>
							</div>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="form-group">
								<?php echo $fields['estudios']['label']; ?>
								<?php echo $fields['estudios']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['email']['label']; ?>
								<?php echo $fields['email']['form']; ?>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['nivel_estudios']['label']; ?>
								<?php echo $fields['nivel_estudios']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-xs-12">
							<div class="col-md-6">
									<div class="form-group">
										<?php echo $fields['tipo_solicitud']['label']; ?>
										<?php echo $fields['tipo_solicitud']['form']; ?>
									</div>
									<div class="form-group">
										<?php echo $fields['fecha']['label']; ?>
										<?php echo $fields['fecha']['form']; ?>
									</div>
									<div class="form-group">
										<?php echo $fields['puesto_requerido']['label']; ?>
										<?php echo $fields['puesto_requerido']['form']; ?>
									</div>
							</div>
							<div class="col-md-6">
									<div class="form-group">
										<?php echo $fields['carrera']['label']; ?>
										<?php echo $fields['carrera']['form']; ?>
									</div>
									<div class="form-group">
										<?php echo $fields['experiencia_requerida']['label']; ?>
										<?php echo $fields['experiencia_requerida']['form']; ?>
									</div>
									<div class="form-group">
										<?php echo $fields['tareas_realizar']['label']; ?>
										<?php echo $fields['tareas_realizar']['form']; ?>
									</div>
									<div class="form-group">
										<?php echo $fields['datos_adicionales']['label']; ?>
										<?php echo $fields['datos_adicionales']['form']; ?>
									</div>
							</div>
						</div>
					</div>                                          
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $empleo->id) : ''; ?>
					<a href="oficina_de_empleo/intermediacion/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>
<script>
	$(document).ready(function() {
		//Modificar el style por defecto
		$('form .change_col').find('div.col-sm-10').removeClass('col-sm-10').addClass('col-sm-9');
		$('form .change_col').find('label.col-sm-2').removeClass('col-sm-2').addClass('col-sm-3');
		$('.obs').find('div.col-sm-10').removeClass('col-sm-10').addClass('col-sm-12');
	});
</script>