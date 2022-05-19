<!--
	/*
	 * Vista ABM de Resolución.
	 * Autor: Leandro
	 * Creado: 29/11/2017
	 * Modificado: 07/06/2019 (Leandro)
	 */
-->
<script>
	$(document).ready(function() {
		toggleFormato();
		$("#formato").change(function() {
			toggleFormato();
		});
		tinymce.init({
			language: 'es',
			selector: 'textarea',
			height: 400,
			menubar: true,
<?php echo (empty($txt_btn) || $txt_btn === 'Anular') ? 'readonly: true,' : ''; ?>
			plugins: [
				'advlist autolink lists link image charmap print preview anchor textcolor',
				'searchreplace visualblocks code fullscreen',
				'insertdatetime media table contextmenu powerpaste nonbreaking'
			],
			toolbar: 'insert | copy paste | undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent firstoutdent firstindent | removeformat | preview ',
			nonbreaking_force_tab: true,
			init_instance_callback: function(editor) {
				editor.on('KeyPress', function(e) {
					if (e.keyCode === 9) { // tab pressed
						e.preventDefault();
						e.stopPropagation();
						return false;
					}
				});
			},
			setup: function(editor) {
				editor.on('init', function() {
					editor.formatter.register('textindent_format', {
						selector: 'p',
						styles: {'text-indent': '%value'}
					});
				});
				editor.addButton('firstoutdent', {
					text: '1°',
					tooltip: 'Disminuir sangría 1° línea',
					icon: 'outdent',
					onclick: function() {
						var node = editor.selection.getNode();
						var tamaño = (tinyMCE.DOM.getStyle(node, 'text-indent', true)).slice(0, -2);
						if (tamaño >= 30) {
							editor.formatter.apply('textindent_format', {value: (parseInt(tamaño) - 30) + 'px'});
						}
					}
				});
				editor.addButton('firstindent', {
					text: '1°',
					tooltip: 'Incrementar sangría 1° línea',
					icon: 'indent',
					onclick: function() {
						var node = editor.selection.getNode();
						var tamaño = (tinyMCE.DOM.getStyle(node, 'text-indent', true)).slice(0, -2);
						editor.formatter.apply('textindent_format', {value: (parseInt(tamaño) + 30) + 'px'});
					}
				});
			}
		});
<?php if ($txt_btn === 'Editar') : ?>
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
<?php endif; ?>
	});
	function toggleFormato() {
		if ($("#formato").val() === 'A') {
			$("#texto").closest('.form-group').hide();
<?php if ($txt_btn === 'Editar') : ?>
				$("#aviso-texto").closest('.form-group').hide();
				$("#path").closest('.form-group').show();
				$("#aviso-archivo").closest('.form-group').show();
<?php endif; ?>
		} else if ($("#formato").val() === 'T') {
			$("#texto").closest('.form-group').show();
<?php if ($txt_btn === 'Editar') : ?>
				$("#aviso-texto").closest('.form-group').show();
				$("#path").closest('.form-group').hide();
				$("#aviso-archivo").closest('.form-group').hide();
<?php endif; ?>
		}
	}
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Resoluciones'; ?></h2>
				<?php if (!empty($audi_modal)): ?>
					<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
						<i class="fa fa-info-circle"></i>
					</button>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = ($txt_btn === 'Anular') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'enctype="multipart/form-data" class="form-horizontal"'); ?>
				<div class="row">
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
					<?php if ($txt_btn === 'Editar') : ?>
						<div class="form-group">
							<div class="col-sm-10 col-sm-offset-2">
								<span id="aviso-archivo" class="red">Al subir un archivo se reemplazará por el archivo o texto actual</span>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-10 col-sm-offset-2">
								<span id="aviso-texto" class="red">Al modificar el texto se reemplazará por el archivo o texto actual</span>
							</div>
						</div>
					<?php endif; ?>
					<?php if (!empty($archivo)): ?>
						<div class="form-group">
							<label class="col-sm-2 control-label">Archivo actual</label> 
							<div class="col-sm-10">
								<object data="<?php echo $archivo->ruta . $archivo->nombre; ?>" type="application/pdf" width="100%" height="600px">
									alt : <a href="<?php echo $archivo->ruta . $archivo->nombre; ?>">Ver PDF</a>
								</object>
							</div>						
						</div>
					<?php endif; ?>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Anular') ? form_hidden('id', $resolucion->id) : ''; ?>
					<a href="resoluciones/resoluciones/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>