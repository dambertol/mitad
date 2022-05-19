<!--
	/*
	 * Vista ABM de Documento.
	 * Autor: Leandro
	 * Creado: 20/02/2017
	 * Modificado: 11/02/2020 (Leandro)
	 */
-->
<script>
	$(document).ready(function() {
		$("#ruta").fileinput({
			theme: "fa",
			language: "es",
			maxFileSize: 4096,
			autoReplace: true,
			maxFileCount: 1,
			browseOnZoneClick: true,
			showRemove: true,
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Documentos'; ?></h2>
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
					<?php if (isset($documentos_legajo)): ?>
						<div class="form-group">
							<label for="ruta" class="col-sm-2 control-label">Archivo</label>
							<div class="col-sm-10">
								<object data="<?php echo "$documentos_legajo->ruta/$documentos_legajo->nombre"; ?>" type="application/pdf" width="100%" height="600px">
									alt : <a href="<?php echo "$documentos_legajo->ruta/$documentos_legajo->nombre"; ?>">Ver PDF</a>
								</object>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $documentos_legajo->id) : ''; ?>
					<?php if (!empty($back_url) && !empty($back_id)): ?>
						<a href="recursos_humanos/<?php echo $back_url; ?>/ver/<?php echo $back_id; ?>" class="btn btn-default btn-sm">Cancelar</a>
					<?php else: ?>
						<a href="recursos_humanos/documentos_legajo/listar" class="btn btn-default btn-sm">Cancelar</a>
					<?php endif; ?>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>