<!--
	/*
	 * Vista Alta de Trámite.
	 * Autor: Leandro
	 * Creado: 21/05/2018
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Trámites'; ?></h2>
				<?php if (!empty($audi_modal)): ?>
					<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
						<i class="fa fa-info-circle"></i>
					</button>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = array('id' => 'btn-iniciar', 'class' => 'btn btn-primary btn-sm disabled', 'title' => $txt_btn, 'disabled' => true); ?>
				<?php echo form_open(uri_string(), 'enctype="multipart/form-data" class="form-horizontal" id="form-tramite"'); ?>
				<div id="smartwizard">
					<ul>
						<li><a href="#paso-0">1. Transferencia<br /><small>Datos generales</small></a></li>
						<li><a href="#paso-1">2. Escribano<br /><small>Datos del escribano</small></a></li>
						<li><a href="#paso-2">3. Transmitente<br /><small>Datos del transmitente</small></a></li>
						<li><a href="#paso-3">4. Adquirente<br /><small>Datos del adquirente</small></a></li>
						<li><a href="#paso-4">5. Inmueble<br /><small>Datos del inmueble</small></a></li>
					</ul>
					<div>
						<div id="paso-0" class="">
							<br />
							<div id="form-paso-0" role="form" data-toggle="validator">
								<div style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
									<div style="padding:5px 15px;">
										<h2 class="text-center">Transferencia</h2>
									</div>
									<?php foreach ($fields_tramite as $field_tramite): ?>
										<div class="form-group">
											<?php echo $field_tramite['label']; ?> 
											<?php echo $field_tramite['form']; ?>
										</div>
									<?php endforeach; ?>
									<?php foreach ($fields_adjunto as $field_adjunto): ?>
										<div class="form-group">
											<?php echo $field_adjunto['label']; ?> 
											<?php echo $field_adjunto['form']; ?>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
						<div id="paso-1" class="">
							<br />
							<div id="form-paso-1" role="form" data-toggle="validator">
								<div class="alert alert-info alert-dismissible fade in" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
									</button>
									<i class="fa fa-info"></i>INFORMACIÓN<br>
									En caso de que sus datos no sean correctos, contactarse con la Dirección de Catastro antes de iniciar el trámite
								</div>
								<div style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
									<div style="padding:5px 15px;">
										<h2 class="text-center">Escribano</h2>
									</div>
									<?php foreach ($fields_escribano as $field_escribano): ?>
										<div class="form-group">
											<?php echo $field_escribano['label']; ?> 
											<?php echo $field_escribano['form']; ?>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
						<div id="paso-2" class="">
							<br />
							<div id="form-paso-2" role="form" data-toggle="validator">
								<?php $cant_v = 1; ?>
								<?php foreach ($fields_vendedores as $fields_vendedor): ?>
									<div id="vendedor_<?php echo $cant_v; ?>" class="vendedor" style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
										<div style="padding:5px 15px;">
											<h2 class="text-center">Transmitente <span id="titulo_vendedor_1">1</span></h2>
										</div>
										<?php foreach ($fields_vendedor as $field_vendedor): ?>
											<div class="form-group">
												<?php echo $field_vendedor['label']; ?> 
												<?php echo $field_vendedor['form']; ?>
											</div>
										<?php endforeach; ?>
										<div class="actions" style="min-height:20px; padding:0px 10px;">
											<button type="button" class="agregar-vendedor btn btn-success btn-xs pull-right" title="Agregar transmitente"><i class="fa fa-plus"></i></button> 
											<button type="button" class="quitar-vendedor btn btn-danger btn-xs pull-right" title="Quitar transmitente"><i class="fa fa-minus"></i></button>
										</div>
									</div>
									<?php $cant_v++; ?>
								<?php endforeach; ?>
							</div>
						</div>
						<div id="paso-3" class="">
							<br />
							<div id="form-paso-3" role="form" data-toggle="validator">
								<div class="alert alert-info alert-dismissible fade in" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
									</button>
									<i class="fa fa-info"></i>INFORMACIÓN<br>
									El domicilio informado para el adquirente es el domicilio postal donde recibirá las boletas y notificaciones
								</div>
								<?php $cant_c = 1; ?>
								<?php foreach ($fields_compradores as $fields_comprador): ?>
									<div id="comprador_<?php echo $cant_c; ?>" class="comprador" style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
										<div style="padding:5px 15px;">
											<h2 class="text-center">Adquirente <span id="titulo_comprador_1">1</span></h2>
										</div>
										<?php foreach ($fields_comprador as $field_comprador): ?>
											<div class="form-group">
												<?php echo $field_comprador['label']; ?> 
												<?php echo $field_comprador['form']; ?>
											</div>
										<?php endforeach; ?>
										<div class="actions" style="min-height:20px; padding:0px 10px;">
											<button type="button" class="agregar-comprador btn btn-success btn-xs pull-right" title="Agregar adquirente"><i class="fa fa-plus"></i></button> 
											<button type="button" class="quitar-comprador btn btn-danger btn-xs pull-right" title="Quitar adquirente"><i class="fa fa-minus"></i></button>
										</div>
									</div>
									<?php $cant_c++; ?>
								<?php endforeach; ?>
							</div>
						</div>
						<div id="paso-4" class="">
							<br />
							<div id="form-paso-4" role="form" data-toggle="validator">
								<div style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
									<div style="padding:5px 15px;">
										<h2 class="text-center">Inmueble</h2>
									</div>
									<?php foreach ($fields_inmueble as $field_inmueble): ?>
										<div class="form-group">
											<?php echo $field_inmueble['label']; ?> 
											<?php echo $field_inmueble['form']; ?>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo form_input(array('name' => 'cant_v', 'type' => 'hidden', 'id' => 'cant_v'), sizeof($fields_vendedores)); ?>
					<?php echo form_input(array('name' => 'cant_c', 'type' => 'hidden', 'id' => 'cant_c'), sizeof($fields_compradores)); ?>
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<a href="transferencias/tramites/<?php echo $back_url; ?>" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>
<script>
	var regex = /^(.+?)(\d+)$/i;
	var cloneIndexV = $(".vendedor").length + 1;
	var cloneIndexC = $(".comprador").length + 1;
	var cant_v = <?php echo sizeof($fields_vendedores); ?>;
	var cant_c = <?php echo sizeof($fields_compradores); ?>;
	$(document).ready(function() {
		$('#smartwizard').smartWizard({
			theme: 'arrows',
			transitionEffect: 'fade',
			keyNavigation: false,
			useURLhash: false,
			showStepURLhash: false,
			lang: {
				next: 'Siguiente',
				previous: 'Anterior'
			},
			anchorSettings: {
				markDoneStep: true,
				markAllPreviousStepsAsDone: true,
				removeDoneStepOnNavigateBack: true,
				enableAnchorOnDoneStep: true
			}
		});
		$("#smartwizard").on("leaveStep", function(e, anchorObject, stepNumber, stepDirection) {
			var elmForm = $("#form-paso-" + stepNumber);
			if (stepDirection === 'forward' && elmForm) {
				if (stepNumber === 0) {
					if ($("#tipo").selectpicker('val') === '') {
						Swal.fire({
							type: 'error',
							title: 'Error.',
							text: 'Seleccione el tipo de trámite.',
							buttonsStyling: false,
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: 'Aceptar'
						});
						return false;
					}
				}
				if (stepNumber === 3) {
					var collection = $("#form-paso-3").find(".selectpicker");
					var errorLocalidad = false;
					collection.each(function() {
						if ($(this).selectpicker('val') === '') {
							errorLocalidad = true;
						}
					});
					if (errorLocalidad) {
						Swal.fire({
							type: 'error',
							title: 'Error.',
							text: 'Seleccione la localidad del adquirente.',
							buttonsStyling: false,
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: 'Aceptar'
						});
						return false;
					}
				}
				if (stepNumber === 2 || stepNumber === 3) {
					var totalPorcentaje = 0;
					var error = false;
					elmForm.find('.porcentaje').each(function(index, element) {
						totalPorcentaje += parseFloat($(element).val());
						if (parseFloat($(element).val()) <= 0) {
							Swal.fire({
								type: 'error',
								title: 'Error.',
								text: 'El porcentaje no puede ser igual a 0%.',
								buttonsStyling: false,
								confirmButtonClass: 'btn btn-primary',
								confirmButtonText: 'Aceptar'
							});
							error = true;
						}
					});
					if (error) {
						return false;
					}
					if (totalPorcentaje > 100.00) {
						Swal.fire({
							type: 'error',
							title: 'Error.',
							text: 'La suma de porcentajes no puede ser mayor a 100%.',
							buttonsStyling: false,
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: 'Aceptar'
						});
						return false;
					}
				}
				elmForm.validator('validate');
				var elmErr = elmForm.find('.has-error');
				var filesCount = $('#certificado_catastral').fileinput('getFilesCount');
				if ((elmErr && elmErr.length > 0) || filesCount === 0) {
					Swal.fire({
						type: 'error',
						title: 'Error.',
						text: 'Revise los campos antes de pasar al próximo paso.',
						buttonsStyling: false,
						confirmButtonClass: 'btn btn-primary',
						confirmButtonText: 'Aceptar'
					});
					return false;
				}
			}
			return true;
		});
		$("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
			if (stepNumber === 4) {
				$('#btn-iniciar').removeClass('disabled');
				$('#btn-iniciar').prop('disabled', false);
			} else {
				$('#btn-iniciar').addClass('disabled');
				$('#btn-iniciar').prop('disabled', true);
			}
		});
		$('#btn-iniciar').on('click', function() {
			if (!$(this).hasClass('disabled')) {
				var elmForm = $("#form-tramite");
				if (elmForm) {
					elmForm.validator('validate');
					var elmErr = elmForm.find('.has-error');
					if (elmErr && elmErr.length > 0) {
						Swal.fire({
							type: 'error',
							title: 'Error.',
							text: 'Revise los campos antes de iniciar el trámite.',
							buttonsStyling: false,
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: 'Aceptar'
						});
						return false;
					} else {
						elmForm.submit();
						return false;
					}
				}
			}
		});
		$("#plano_mensura").fileinput({
			theme: "fa",
			language: "es",
			dropZoneEnabled: false,
			maxFileSize: 8192,
			autoReplace: true,
			required: false,
			maxFileCount: 1,
			showRemove: true,
			removeClass: "btn btn-danger",
			removeIcon: "<i class=\"glyphicon glyphicon-trash\"></i> ",
			showClose: false,
			showUpload: false,
			allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf']
		});
		$("#certificado_catastral").fileinput({
			theme: "fa",
			language: "es",
			dropZoneEnabled: false,
			maxFileSize: 8192,
			autoReplace: true,
			required: true,
			maxFileCount: 1,
			showRemove: true,
			removeClass: "btn btn-danger",
			removeIcon: "<i class=\"glyphicon glyphicon-trash\"></i> ",
			showClose: false,
			showUpload: false,
			allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf']
		});
		$("#otros").fileinput({
			theme: "fa",
			language: "es",
			dropZoneEnabled: false,
			maxFileSize: 8192,
			autoReplace: true,
			required: false,
			maxFileCount: 10,
			showRemove: true,
			removeClass: "btn btn-danger",
			removeIcon: "<i class=\"glyphicon glyphicon-trash\"></i> ",
			showClose: false,
			showUpload: false,
			allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf']
		});
		$('#cuil').inputmask({
			mask: '99-99999999-9',
			removeMaskOnSubmit: true
		});
		for (var i = 1; i <= cant_v; i++) {
			$('#cuil_v_' + i).inputmask({
				mask: '99-99999999-9',
				removeMaskOnSubmit: true
			});
			$('#cuil_v_' + i).blur(function() {
				var input = this;
				var cuil = input.value;
				var resul = validaCuil(cuil);
				if (!resul) {
					Swal.fire({
						type: 'error',
						title: 'Error.',
						text: 'CUIL inválido.',
						buttonsStyling: false,
						confirmButtonClass: 'btn btn-primary',
						confirmButtonText: 'Aceptar'
					}).then(function() {
						Swal.close();
						input.focus();
					});
				}
			});
		}
		for (var i = 1; i <= cant_c; i++) {
			$('#cuil_c_' + i).inputmask({
				mask: '99-99999999-9',
				removeMaskOnSubmit: true
			});
			$('#cuil_c_' + i).blur(function() {
				var input = this;
				var cuil = input.value;
				var resul = validaCuil(cuil);
				if (!resul) {
					Swal.fire({
						type: 'error',
						title: 'Error.',
						text: 'CUIL inválido.',
						buttonsStyling: false,
						confirmButtonClass: 'btn btn-primary',
						confirmButtonText: 'Aceptar'
					}).then(function() {
						Swal.close();
						input.focus();
					});
				}
			});
		}
		$(".agregar-vendedor").on("click", agregarVendedor);
		$(".quitar-vendedor").on("click", quitarVendedor);
		$(".agregar-comprador").on("click", agregarComprador);
		$(".quitar-comprador").on("click", quitarComprador);

		toggleQuitarVendedor();
		toggleQuitarComprador();
	});

	//VENDEDOR
	function agregarVendedor() {
		$(this).parents(".vendedor").clone()
						.appendTo("#form-paso-2")
						.attr("id", "vendedor_" + cloneIndexV)
						.find("*")
						.each(function() {
							var id = this.id || "";
							var match = id.match(regex) || [];
							if (match.length === 3) {
								this.id = match[1] + (cloneIndexV);
								this.name = match[1] + (cloneIndexV);
								this.value = "";
								if (match[1] === 'titulo_vendedor_') {
									this.textContent = cloneIndexV;
								}
							}
						})
						.on('click', '.agregar-vendedor', agregarVendedor)
						.on('click', '.quitar-vendedor', quitarVendedor);
		$('#porcentaje_v_' + cloneIndexV).inputmask('decimal', {
			radixPoint: ',',
			unmaskAsNumber: true,
			digits: 2,
			autoUnmask: true,
			digitsOptional: false,
			placeholder: '',
			removeMaskOnSubmit: true,
			rightAlign: false,
			positionCaretOnClick: 'select',
			onBeforeMask: function(value, opts) {
				processedValue = parseFloat(value).toFixed(2).replace(".", ",");
				return processedValue;
			}
		});
		$('#cuil_v_' + cloneIndexV).inputmask({
			mask: '99-99999999-9',
			removeMaskOnSubmit: true
		});
		$('#cuil_v_' + cloneIndexV).blur(function() {
			var input = this;
			var cuil = input.value;
			var resul = validaCuil(cuil);
			if (!resul) {
				Swal.fire({
					type: 'error',
					title: 'Error.',
					text: 'CUIL inválido.',
					buttonsStyling: false,
					confirmButtonClass: 'btn btn-primary',
					confirmButtonText: 'Aceptar'
				}).then(function() {
					Swal.close();
					input.focus()
				});
			}
		});
		toggleQuitarVendedor();
		$('#cant_v').val(cloneIndexV);
		cloneIndexV++;
	}
	function quitarVendedor() {
		var id = $(this).parents(".vendedor").attr("id") || "";
		var match = id.match(regex) || [];
		if (match.length === 3) {
			$(this).parents(".vendedor").remove();
			cloneIndexV--;
			renumerarVendedor(match[2]);
		}
		$('#cant_v').val(cloneIndexV - 1);
		toggleQuitarVendedor();
	}
	function renumerarVendedor(idDesde) {
		var i = idDesde;
		$(".vendedor").each(function() {
			var id = this.id || "";
			var match = id.match(regex) || [];
			if (match.length === 3) {
				if (match[2] > idDesde) {
					$("#" + id).attr("id", "vendedor_" + i)
									.find("*")
									.each(function() {
										var id = this.id || "";
										var match = id.match(regex) || [];
										if (match.length === 3) {
											this.id = match[1] + (i);
											this.name = match[1] + (i);
											if (match[1] === 'titulo_vendedor_') {
												this.textContent = i;
											}
										}
									});
					i++;
				}
			}
		});
	}
	function toggleQuitarVendedor() {
		if ($(".vendedor").length === 1) {
			$('.quitar-vendedor').hide();
		} else {
			$('.quitar-vendedor').show();
		}
	}

	//COMPRADOR
	function agregarComprador() {
		$(this).parents(".comprador")
						.clone()
						.appendTo("#form-paso-3")
						.attr("id", "comprador_" + cloneIndexC)
						.find("*")
						.each(function() {
							var id = this.id || "";
							var match = id.match(regex) || [];
							if (match.length === 3) {
								this.id = match[1] + (cloneIndexC);
								this.name = match[1] + (cloneIndexC);
								this.value = "";
								if (match[1] === 'titulo_comprador_') {
									this.textContent = cloneIndexC;
								}
							}
						})
						.on('click', '.agregar-comprador', agregarComprador)
						.on('click', '.quitar-comprador', quitarComprador);

		$('#porcentaje_c_' + cloneIndexC).inputmask('decimal', {
			radixPoint: ',',
			unmaskAsNumber: true,
			digits: 2,
			autoUnmask: true,
			digitsOptional: false,
			placeholder: '',
			removeMaskOnSubmit: true,
			rightAlign: false,
			positionCaretOnClick: 'select',
			onBeforeMask: function(value, opts) {
				processedValue = parseFloat(value).toFixed(2).replace(".", ",");
				return processedValue;
			}
		});
		$('#comprador_' + cloneIndexC).find('.bootstrap-select').replaceWith(function() {
			return $('select', this);
		});
		$('#localidad_c_' + cloneIndexC).find('.bs-title-option').remove();
		$('#localidad_c_' + cloneIndexC).selectpicker('refresh');
		$('#cuil_c_' + cloneIndexC).inputmask({
			mask: '99-99999999-9',
			removeMaskOnSubmit: true
		});
		$('#cuil_c_' + cloneIndexC).blur(function() {
			var input = this;
			var cuil = input.value;
			var resul = validaCuil(cuil);
			if (!resul) {
				Swal.fire({
					type: 'error',
					title: 'Error.',
					text: 'CUIL inválido.',
					buttonsStyling: false,
					confirmButtonClass: 'btn btn-primary',
					confirmButtonText: 'Aceptar'
				}).then(function() {
					Swal.close();
					input.focus()
				});
			}
		});
		toggleQuitarComprador();
		$('#cant_c').val(cloneIndexC);
		cloneIndexC++;
	}
	function quitarComprador() {
		var id = $(this).parents(".comprador").attr("id") || "";
		var match = id.match(regex) || [];
		if (match.length === 3) {
			$(this).parents(".comprador").remove();
			cloneIndexC--;
			renumerarComprador(match[2]);
		}
		$('#cant_c').val(cloneIndexC - 1);
		toggleQuitarComprador();
	}
	function renumerarComprador(idDesde) {
		var i = idDesde;
		$(".comprador").each(function() {
			var id = this.id || "";
			var match = id.match(regex) || [];
			if (match.length === 3) {
				if (match[2] > idDesde) {
					$("#" + id).attr("id", "comprador_" + i)
									.find("*")
									.each(function() {
										var id = this.id || "";
										var match = id.match(regex) || [];
										if (match.length === 3) {
											this.id = match[1] + (i);
											this.name = match[1] + (i);
											if (match[1] === 'titulo_comprador_') {
												this.textContent = i;
											}
										}
									});
					i++;
				}
			}
		});
	}
	function toggleQuitarComprador() {
		if ($(".comprador").length === 1) {
			$('.quitar-comprador').hide();
		} else {
			$('.quitar-comprador').show();
		}
	}
</script>