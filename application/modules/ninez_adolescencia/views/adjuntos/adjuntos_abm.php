<!--
	/*
	 * Vista ABM de Adjunto.
	 * Autor: Leandro
	 * Creado: 13/09/2019
	 * Modificado: 17/09/2019 (Leandro)
	 */
-->
<script>
	$(document).ready(function() {
		$("#path").fileinput({
			theme: "fa",
			language: "es",
			maxFileSize: 4096,
			autoReplace: true,
			maxFileCount: 1,
			showRemove: true,
			browseOnZoneClick: true,
			removeClass: "btn btn-danger",
			removeIcon: "<i class=\"glyphicon glyphicon-trash\"></i> ",
			showClose: false,
			showUpload: false,
			allowedFileExtensions: ["pdf"]
		});
	});
</script>
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Adjuntos'; ?></h2>
				<?php if (!empty($audi_modal)): ?>
					<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
						<i class="fa fa-info-circle"></i>
					</button>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'enctype="multipart/form-data" class="form-horizontal"'); ?>
				<div class="row">
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
					<?php if (!empty($adjunto)): ?>
						<div class="form-group">
							<div class="col-sm-12">
								<object data="<?php echo $adjunto->ruta . $adjunto->nombre; ?>" type="application/pdf" width="100%" height="600px">
									alt : <a href="<?php echo $adjunto->ruta . $adjunto->nombre; ?>">Ver PDF</a>
								</object>
							</div>						
						</div>
					<?php endif; ?>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $adjunto->id) : ''; ?>
					<a href="ninez_adolescencia/expedientes/ver/<?php echo $expediente_id; ?>" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>