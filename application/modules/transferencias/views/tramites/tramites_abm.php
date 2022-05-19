<!--
	/*
	 * Vista ABM de Trámite.
	 * Autor: Leandro
	 * Creado: 21/05/2018
	 * Modificado: 08/10/2018 (Leandro)
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
				<?php $data_submit = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'enctype="multipart/form-data" class="form-horizontal"'); ?>
				<div id="smartwizard">
					<ul>
						<li><a href="#paso-1">1. Transferencia<br /><small>Datos generales</small></a></li>
						<li><a href="#paso-2">2. Escribano<br /><small>Datos del escribano</small></a></li>
						<li><a href="#paso-3">3. Transmitente<br /><small>Datos del transmitente</small></a></li>
						<li><a href="#paso-4">4. Adquirente<br /><small>Datos del adquirente</small></a></li>
						<li><a href="#paso-5">5. Inmueble<br /><small>Datos del inmueble</small></a></li>
					</ul>
					<div>
						<div id="paso-1" class="">
							<br />
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
						<div id="paso-2" class="">
							<br />
							<div class="alert alert-info alert-dismissible fade in" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
								</button>
								<i class="fa fa-info"></i>INFORMACIÓN<br>
								En caso de que sus datos no sean correctos, contactarse con la Dirección de Catastro antes de iniciar el trámite
							</div>
							<?php foreach ($fields_escribano as $field_escribano): ?>
								<div class="form-group">
									<?php echo $field_escribano['label']; ?> 
									<?php echo $field_escribano['form']; ?>
								</div>
							<?php endforeach; ?>
						</div>
						<div id="paso-3" class="">
							<br />
							<?php $cant_v = 1; ?>
							<?php foreach ($fields_vendedores as $fields_vendedor): ?>
								<div id="vendedor_<?php echo $cant_v; ?>" class="vendedor" style="border-radius:5px; border:1px solid #ddd; padding-bottom:10px; margin-bottom:15px;">
									<div style="padding:5px 15px;">
										<h2 class="text-center">Transmitente</h2>
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
						<div id="paso-4" class="">
							<br />
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
										<h2 class="text-center">Adquirente</h2>
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
						<div id="paso-5" class="">
							<br />
							<?php foreach ($fields_inmueble as $field_inmueble): ?>
								<div class="form-group">
									<?php echo $field_inmueble['label']; ?> 
									<?php echo $field_inmueble['form']; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo form_input(array('name' => 'cant_v', 'type' => 'hidden', 'id' => 'cant_v'), sizeof($fields_vendedores)); ?>
					<?php echo form_input(array('name' => 'cant_c', 'type' => 'hidden', 'id' => 'cant_c'), sizeof($fields_compradores)); ?>
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $tramite->id) : ''; ?>
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
			keyNavigation: false,
			useURLhash: false,
			showStepURLhash: false,
			lang: {
				next: 'Siguiente',
				previous: 'Anterior'
			},
			anchorSettings: {
				enableAllAnchors: true
			}
		});
<?php if ($txt_btn === 'Iniciar Trámite') : ?>
			$("#plano_mensura").fileinput({
				theme: "fa",
				language: "es",
				dropZoneEnabled: false,
				maxFileSize: 4096,
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
				maxFileSize: 4096,
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
				maxFileSize: 4096,
				autoReplace: true,
				required: false,
				maxFileCount: 4,
				showRemove: true,
				removeClass: "btn btn-danger",
				removeIcon: "<i class=\"glyphicon glyphicon-trash\"></i> ",
				showClose: false,
				showUpload: false,
				allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf']
			});
			for (var i = 1; i <= cant_v; i++) {
				$('#cuil_v_' + i).inputmask({
					mask: '99-99999999-9',
					removeMaskOnSubmit: true
				});
				$('#cuil_v_' + i).blur(function() {
					var cuil = this.value;
					var resul = validaCuil(cuil);
					if (!resul) {
						swal({
							type: 'error',
							title: 'Error.',
							text: 'CUIL inválido',
							buttonsStyling: false,
							confirmButtonClass: 'btn btn-primary'
						}).then(function() {
							swal.close();
							$('#cuil_v_' + i).focus()
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
					var cuil = this.value;
					var resul = validaCuil(cuil);
					if (!resul) {
						swal({
							type: 'error',
							title: 'Error.',
							text: 'CUIL inválido',
							buttonsStyling: false,
							confirmButtonClass: 'btn btn-primary'
						}).then(function() {
							swal.close();
							$('#cuil_c_' + i).focus()
						});
					}
				});
			}
<?php endif; ?>
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
						.appendTo("#paso-3")
						.attr("id", "vendedor_" + cloneIndexV)
						.find("*")
						.each(function() {
							var id = this.id || "";
							var match = id.match(regex) || [];
							if (match.length === 3) {
								this.id = match[1] + (cloneIndexV);
								this.name = match[1] + (cloneIndexV);
								this.value = "";
							}
						})
						.on('click', '.agregar-vendedor', agregarVendedor)
						.on('click', '.quitar-vendedor', quitarVendedor);
		$('#cuil_v_' + cloneIndexV).inputmask({
			mask: '99-99999999-9',
			removeMaskOnSubmit: true
		});
		$('#cuil_v_' + cloneIndexV).blur(function() {
			var cuil = this.value;
			var resul = validaCuil(cuil);
			if (!resul) {
				swal({
					type: 'error',
					title: 'Error.',
					text: 'CUIL inválido',
					buttonsStyling: false,
					confirmButtonClass: 'btn btn-primary'
				}).then(function() {
					swal.close();
					$('#cuil_v_' + cloneIndexV).focus()
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
						.appendTo("#paso-4")
						.attr("id", "comprador_" + cloneIndexC)
						.find("*")
						.each(function() {
							var id = this.id || "";
							var match = id.match(regex) || [];
							if (match.length === 3) {
								this.id = match[1] + (cloneIndexC);
								this.name = match[1] + (cloneIndexC);
								this.value = "";
							}
						})
						.on('click', '.agregar-comprador', agregarComprador)
						.on('click', '.quitar-comprador', quitarComprador);

		$('#comprador_' + cloneIndexC).find('.bootstrap-select').replaceWith(function() {
			return $('select', this);
		});
		$('#localidad_c_' + cloneIndexC).selectpicker('refresh');

		$('#cuil_c_' + cloneIndexC).inputmask({
			mask: '99-99999999-9',
			removeMaskOnSubmit: true
		});
		$('#cuil_c_' + cloneIndexC).blur(function() {
			var cuil = this.value;
			var resul = validaCuil(cuil);
			if (!resul) {
				swal({
					type: 'error',
					title: 'Error.',
					text: 'CUIL inválido',
					buttonsStyling: false,
					confirmButtonClass: 'btn btn-primary'
				}).then(function() {
					swal.close();
					$('#cuil_c_' + cloneIndexC).focus()
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