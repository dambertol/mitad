<!--
	/*
	 * Vista ABM de Documento.
	 * Autor: Leandro
	 * Creado: 08/01/2020
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Documentos'; ?></h2>
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
				<div class="row" id="row-documento">
					<br />
					<h2 class="text-center">Datos Documento</h2>
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="row" id="row-parte">
					<br />
					<h2 class="text-center">Datos Parte</h2>
					<?php foreach ($fields_parte as $field_parte): ?>
						<div class="form-group">
							<?php echo $field_parte['label']; ?> 
							<?php echo $field_parte['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if (!isset($documento) || isset($documento->persona_id)): ?>
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
					<?php if (!isset($documento) || isset($documento->domicilio_id)): ?>
						<div class="row" id="row-domicilio">
							<br />
							<h2 class="text-center">Datos Domilicio</h2>
							<?php foreach ($fields_domicilio as $field_domicilio): ?>
								<div class="form-group">
									<?php echo $field_domicilio['label']; ?> 
									<?php echo $field_domicilio['form']; ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
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
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	$(document).on('click', '[data-toggle="lightbox"]', function(event) {
		event.preventDefault();
		$(this).ekkoLightbox({
			alwaysShowClose: true
		});
	});
	$(document).ready(function() {
<?php if ($txt_btn === 'Agregar' || $txt_btn === 'Editar'): ?>
			var inicial_parte = $('#parte').selectpicker('val');
			var inicial_persona = $('#persona').selectpicker('val');
			if (inicial_parte === 'agregar' || inicial_parte === '') {
				$('#row-parte :input').attr("disabled", false);
				$("#row-parte").show();
				if (inicial_persona === 'agregar') {
					$('#row-persona :input').attr("disabled", false);
					$('#row-domicilio :input').attr("disabled", false);
					$("#row-persona").show();
					$("#row-domicilio").show();
				} else if (inicial_persona === 'sin_persona' || inicial_persona === '') {
					$('#row-persona :input').attr("disabled", true);
					$('#row-domicilio :input').attr("disabled", true);
					$("#row-persona").hide();
					$("#row-domicilio").hide();
				} else {
					buscar_persona(inicial_persona);
					$('#row-persona :input').attr("disabled", true);
					$('#row-domicilio :input').attr("disabled", true);
					$("#row-persona").show();
					$("#row-domicilio").show();
				}
			} else {
				$('#row-parte :input').attr("disabled", true);
				buscar_parte(inicial_parte);
			}

			$('#parte').on('changed.bs.select', function(e) {
				if (this.value === 'agregar') {
					$('#row-parte :input').attr("disabled", false);
					limpiar_parte();
				} else {
					$('#row-parte :input').attr("disabled", true);
					buscar_parte(this.value);
				}
			});

			$('#persona').on('changed.bs.select', function(e) {
				if (this.value === 'agregar') {
					$('#row-persona :input').attr("disabled", false);
					$("#row-persona").show();
					limpiar_persona();
				} else if (this.value === 'sin_persona') {
					$('#row-persona :input').attr("disabled", true);
					$("#row-persona").hide();
					limpiar_persona();
				} else {
					$('#row-persona :input').attr("disabled", true);
					$("#row-persona").show();
					buscar_persona(this.value);
				}
			});

			$('#carga_domicilio').on('changed.bs.select', function(e) {
				domicilio_row('Agregar');
			});
<?php endif; ?>
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