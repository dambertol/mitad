<!--
	/*
	 * Vista ABM de Documento.
	 * Autor: Leandro
	 * Creado: 10/01/2020
	 * Modificado: 23/01/2020 (Leandro)
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Decretos'; ?></h2>
				<?php if (!empty($audi_modal)): ?>
					<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
						<i class="fa fa-info-circle"></i>
					</button>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = ($txt_btn === 'Anular') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'enctype="multipart/form-data" class="form-horizontal" id="form-documento"'); ?>
				<div class="row">
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="row" id="row-adjuntos">
					<br />
					<h2 class="text-center">
						Galería de Adjuntos
					</h2>
					<div id="adjuntos-container" class="col-sm-12">
						<?php if (!empty($txt_btn) && $txt_btn === 'Editar'): ?>
							<div class="text-center" style="margin-bottom:10px;">
								<a class="btn btn-primary btn-sm" href="gobierno/adjuntos/modal_agregar/documentos/<?php echo $documento->id; ?>" data-remote="false" data-toggle="modal" data-target="#remote_modal" title="Agregar adjunto"><i class="fa fa-plus"></i> Agregar adjunto</a>
							</div>
						<?php elseif (!empty($txt_btn) && $txt_btn === 'Agregar'): ?>
							<div class="text-center" style="margin-bottom:10px;">
								<a class="btn btn-primary btn-sm" href="gobierno/adjuntos/modal_agregar/documentos" data-remote="false" data-toggle="modal" data-target="#remote_modal" title="Agregar adjunto"><i class="fa fa-plus"></i> Agregar adjunto</a>
							</div>
						<?php endif; ?>
						<?php if (!empty($array_adjuntos)): ?>
							<?php foreach ($array_adjuntos as $Adjunto): ?>
								<?php if (!array_key_exists($Adjunto->id, $adjuntos_eliminar_existente_post)): ?>
									<?php if ($Adjunto->extension === 'jpg' || $Adjunto->extension === 'jpeg' || $Adjunto->extension === 'png' || $Adjunto->extension === 'tiff'): ?>
										<?php $preview = '<img style="width: 100%; display: block;" src="' . $Adjunto->ruta . $Adjunto->nombre . '" alt="' . $Adjunto->tipo_adjunto . '">'; ?>
										<?php $extra = ''; ?>
									<?php else: ?>
										<?php $preview = '<object type="application/pdf" data="' . $Adjunto->ruta . $Adjunto->nombre . '#toolbar=0" width="100%" height="170">PDF</object>'; ?>
										<?php $extra = ' data-type="url" data-disable-external-check="true"'; ?>
									<?php endif; ?>
									<div class="col-lg-3 col-md-4 col-sm-6" id="adjunto_<?php echo $Adjunto->id; ?>">
										<div class="thumbnail">
											<div class="image view view-first">
												<?php echo $preview; ?>
												<div class="mask">
													<p>&nbsp;</p>
													<div class="tools tools-bottom">
														<a href="<?php echo $Adjunto->ruta . $Adjunto->nombre; ?>" title="Ver Adjunto" data-toggle="lightbox"<?php echo $extra; ?> data-gallery="documento-gallery" data-title="<?php echo "$Adjunto->tipo_adjunto <span class='small'>$Adjunto->descripcion</span>"; ?>"><i class="fa fa-search"></i></a>
														<?php if (empty($txt_btn)): ?>
															<a href="gobierno/adjuntos/descargar/documentos/<?php echo $Adjunto->id; ?>"  title="Descargar Adjunto"><i class="fa fa-download"></i></a>
														<?php endif; ?>
														<?php if (!empty($txt_btn) && $txt_btn === 'Editar'): ?>
															<a href="javascript:eliminar_adjunto(<?php echo $Adjunto->id; ?>, '<?php echo $Adjunto->nombre; ?>', <?php echo $documento->id; ?>)" title="Eliminar adjunto"><i class="fa fa-remove"></i></a>
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="caption" style="height:60px;">
												<p>
													<b><?php echo $Adjunto->tipo_adjunto; ?></b><br>
													<?php echo $Adjunto->descripcion; ?>
												</p>
											</div>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if (!empty($array_adjuntos_agregar)): ?>
							<?php foreach ($array_adjuntos_agregar as $Adjunto): ?>
								<?php if ($Adjunto->extension === 'jpg' || $Adjunto->extension === 'jpeg' || $Adjunto->extension === 'png' || $Adjunto->extension === 'tiff'): ?>
									<?php $preview = '<img style="width: 100%; display: block;" src="' . $Adjunto->ruta . $Adjunto->nombre . '" alt="' . $Adjunto->tipo_adjunto . '">'; ?>
								<?php else: ?>
									<?php $preview = '<object type="application/pdf" data="' . $Adjunto->ruta . $Adjunto->nombre . '#toolbar=0" width="100%" height="170">PDF</object>'; ?>
								<?php endif; ?>
								<div class="col-lg-3 col-md-4 col-sm-6 adjunto_<?php echo $Adjunto->tipo_id; ?>" id="adjunto_<?php echo $Adjunto->id; ?>">
									<input type='hidden' name='adjunto_agregar[<?php echo $Adjunto->id; ?>]' value='<?php echo $Adjunto->nombre; ?>'>
									<div class="thumbnail">
										<div class="image view view-first">
											<?php echo $preview; ?>
											<div class="mask">
												<p>&nbsp;</p>
												<div class="tools tools-bottom">
													<a href="<?php echo $Adjunto->ruta . $Adjunto->nombre; ?>" title="Ver Adjunto" data-toggle="lightbox" data-gallery="documento-gallery" data-title="<?php echo "$Adjunto->tipo_adjunto <span class='small'>$Adjunto->descripcion</span>"; ?>"><i class="fa fa-search"></i></a>
													<a href="javascript:eliminar_adjunto(<?php echo $Adjunto->id; ?>, '<?php echo $Adjunto->nombre; ?>')" title="Eliminar adjunto"><i class="fa fa-remove"></i></a>
												</div>
											</div>
										</div>
										<div class="caption" style="height:60px;">
											<p>
												<b><?php echo $Adjunto->tipo_adjunto; ?></b><br>
												<?php echo $Adjunto->descripcion; ?>
											</p>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if (!empty($array_adjuntos_eliminar)): ?>
							<?php foreach ($array_adjuntos_eliminar as $Adjunto): ?>
								<input type='hidden' name='adjunto_eliminar[<?php echo $Adjunto->id; ?>]' value='<?php echo $Adjunto->nombre; ?>'>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $documento->id) : ''; ?>
					<a href="gobierno/documentos/<?php echo (!empty($back_url)) ? $back_url : 'listar'; ?>" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>
<script>
	$(document).on('click', '[data-toggle="lightbox"]', function(event) {
		event.preventDefault();
		$(this).ekkoLightbox({
			alwaysShowClose: true
		});
	});
	function eliminar_adjunto(adjunto_id, adjunto_nombre, documento_id) {
		if (documento_id !== undefined) {
			var name = 'adjunto_eliminar_existente';
		} else {
			var name = 'adjunto_eliminar';
		}
		Swal.fire({
			title: 'Confirmar',
			text: "Se eliminará el adjunto",
			type: 'info',
			showCloseButton: true,
			showCancelButton: true,
			focusCancel: true,
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			cancelButtonClass: 'btn btn-default',
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar'
		}).then((result) => {
			if (result.value) {
				$('#adjuntos-container').append("<input type='hidden' name='" + name + "[" + adjunto_id + "]' value='" + adjunto_nombre + "'>");
				$('#adjunto_' + adjunto_id).remove();
			}
		});
	}
</script>