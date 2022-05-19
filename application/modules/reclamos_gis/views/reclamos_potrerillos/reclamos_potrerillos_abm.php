<!--
/**
  * Vista ABM de Reclamo Potrerillos.
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
								<?php echo $fields['n_orden']['label']; ?> 
								<?php echo $fields['n_orden']['form']; ?> 
							</div>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="form-group">
								<?php echo $fields['agente']['label']; ?>
								<?php echo $fields['agente']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['nomenclatura']['label']; ?>
								<?php echo $fields['nomenclatura']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['padron']['label']; ?>
								<?php echo $fields['padron']['form']; ?>
							</div>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="form-group">
								<?php echo $fields['fecha']['label']; ?>
								<?php echo $fields['fecha']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['n_nota']['label']; ?>
								<?php echo $fields['n_nota']['form']; ?>
							</div>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="form-group">
								<?php echo $fields['telefono_contacto']['label']; ?>
								<?php echo $fields['telefono_contacto']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['tipo']['label']; ?>
								<?php echo $fields['tipo']['form']; ?>
							</div>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="form-group">
								<?php echo $fields['estado']['label']; ?>
								<?php echo $fields['estado']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['correccion_capa']['label']; ?>
								<?php echo $fields['correccion_capa']['form']; ?>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<?php echo $fields['inspeccion']['label']; ?>
								<?php echo $fields['inspeccion']['form']; ?>
							</div>
						</div>
					</div>
					<div class="row change_col">
						<div class="col-xs-12">
							<div class="col-md-6">
								<fieldset class="group-border">
									<legend class="group-border">Existente</legend>
									<div class="form-group">
										<?php echo $fields['cubierta_existente']['label']; ?>
										<?php echo $fields['cubierta_existente']['form']; ?>
									</div>
									<div class="form-group">
										<?php echo $fields['pileta_existente']['label']; ?>
										<?php echo $fields['pileta_existente']['form']; ?>
									</div>
									<div class="form-group">
										<?php echo $fields['cubierta_gis_existente']['label']; ?>
										<?php echo $fields['cubierta_gis_existente']['form']; ?>
									</div>
									<div class="form-group">
										<?php echo $fields['pileta_gis_existente']['label']; ?>
										<?php echo $fields['pileta_gis_existente']['form']; ?>
									</div>
								</fieldset>
							</div>
							<div class="col-md-6">
								<fieldset class="group-border">
									<legend class="group-border">Resoluci&oacute;n</legend>
									<div class="form-group">
										<?php echo $fields['cubierta_gis_nueva']['label']; ?>
										<?php echo $fields['cubierta_gis_nueva']['form']; ?>
									</div>
									<div class="form-group">
										<?php echo $fields['pileta_gis_nueva']['label']; ?>
										<?php echo $fields['pileta_gis_nueva']['form']; ?>
									</div>
									<div class="form-group">
										<?php echo $fields['cubierta_declarada']['label']; ?>
										<?php echo $fields['cubierta_declarada']['form']; ?>
									</div>
									<div class="form-group">
										<?php echo $fields['pileta_declarada']['label']; ?>
										<?php echo $fields['pileta_declarada']['form']; ?>
									</div>
								</fieldset>
							</div>
						</div>
					</div>                     

					<div class="col-xs-12">                        
						<fieldset class="group-border">
							<legend class="group-border">Observaciones</legend>
							<div class="form-group obs">
								<br>
								<?php echo $fields['observaciones']['form']; ?>
							</div>   
						</fieldset>
					</div>                        

				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $reclamo->id) : ''; ?>
					<a href="reclamos_gis/reclamos_potrerillos/listar" class="btn btn-default btn-sm">Cancelar</a>
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