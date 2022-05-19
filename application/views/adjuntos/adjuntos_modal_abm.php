<!--
 /*
	* Vista Adjunto Modal
	* Autor: Leandro
	* Creado: 16/11/2017
	* Modificado: 08/05/2019 (Leandro)
	*/
-->
<?php echo form_open_multipart(uri_string()); ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></h4>
</div>
<div class="modal-body">
	<div class="form-horizontal">
		<div class="row">
			<?php foreach ($fields as $field): ?>
				<div class="form-group">
					<?php echo $field['label']; ?> 
					<?php echo $field['form']; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<div class="modal-footer">
	<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $entidad_id) : ''; ?>
	<?php if ($txt_btn === 'Eliminar'): ?>
		<?php echo form_button(array('id' => 'btn-confirmar', 'class' => 'btn btn-danger btn-sm', 'title' => 'Eliminar', 'content' => 'Eliminar')); ?>
	<?php else: ?>
		<?php echo (!empty($txt_btn)) ? form_button(array('id' => 'btn-confirmar', 'class' => 'btn btn-primary btn-sm', 'title' => $txt_btn, 'content' => $txt_btn)) : ''; ?>
	<?php endif; ?>
	<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
</div>
<?php echo form_close(); ?>
<script>
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	$(document).ready(function() {
		$('.selectpicker').selectpicker();
		$("#path").fileinput({
			uploadUrl: '<?php echo $accion_url; ?>',
			uploadAsync: false,
			uploadExtraData: function(previewId, index) {
				var info = {
					tipo_adjunto: $("#tipo_adjunto").val(),
					descripcion: $("#descripcion").val(),
					csrf_mlc2: csrfData
				};
				return info;
			},
			browseOnZoneClick: true,
			showCaption: false,
			showRemove: false,
			showBrowse: false,
			fileActionSettings: {
				showUpload: false,
				showDrag: false
			},
			theme: "fa",
			language: "es",
			required: true,
			maxFileSize: 4096,
			showClose: false,
			showUpload: false,
			dropZoneEnabled: true,
			msgErrorClass: 'alert alert-block alert-danger',
			allowedFileExtensions: <?php echo $extensiones; ?>,
			maxFileCount: 1
		});
		$('#path').on('filebatchuploadsuccess', function(event, data, previewId, index) {
			if (data.response.adjunto.extension === 'jpg' || data.response.adjunto.extension === 'jpeg' || data.response.adjunto.extension === 'png') {
				var preview = "<img style='width:100%; display:block;' src='" + data.response.adjunto.archivo + "' alt='" + data.response.adjunto.tipo + "'>";
				var extra = '';
				var ver = "<a href='" + data.response.adjunto.archivo + "' title='Ver Adjunto' data-toggle='lightbox'" + extra + " data-gallery='adjunto-gallery' data-title='" + data.response.adjunto.tipo + " <span class=\"small\">" + data.response.adjunto.descripcion + "</span>'  title='Ver adjunto'><i class='fa fa-search'></i></a>"
			} else if (data.response.adjunto.extension === 'pdf') {
				var preview = "<object type='application/pdf' data='" + data.response.adjunto.archivo + "' width='100%' height='170'>PDF</object>";
				var extra = ' data-type="url" data-disable-external-check="true"';
				var ver = "<a href='" + data.response.adjunto.archivo + "' title='Ver Adjunto' data-toggle='lightbox'" + extra + " data-gallery='adjunto-gallery' data-title='" + data.response.adjunto.tipo + " <span class=\"small\">" + data.response.adjunto.descripcion + "</span>'  title='Ver adjunto'><i class='fa fa-search'></i></a>"
			} else {
				var preview = '<img src="img/generales/nopreview.png" alt="Sin Vista Previa">';
				var extra = '';
				var ver = '';
			}
			var adjunto = "<div class='col-lg-3 col-md-4 col-sm-6 adjunto_" + data.response.adjunto.tipo_id + "' id='adjunto_" + data.response.adjunto.id + "'>\n\
										<input type='hidden' name='adjunto_agregar[" + data.response.adjunto.id + "]' value='" + data.response.adjunto.nombre + "'>\n\
										<div class='thumbnail'>\n\
											<div class='image view view-first'>\n\
												" + preview + "\n\
												<div class='mask'>\n\
													<p>&nbsp;</p>\n\
													<div class='tools tools-bottom'>\n\
														" + ver + "\n\
														<a href='javascript:eliminar_adjunto(\"" + data.response.adjunto.id + "\", \"" + data.response.adjunto.nombre + "\")' title='Eliminar adjunto'><i class='fa fa-remove'></i></a>\n\
													</div>\n\
												</div>\n\
											</div>\n\
											<div class='caption' style='height:60px;'>\n\
												<p>\n\
													<b>" + data.response.adjunto.tipo + "</b><br>\n\
													" + data.response.adjunto.descripcion + "\n\
												</p>\n\
											</div>\n\
										</div>\n\
									</div>";
			$('#adjuntos-container').append(adjunto);
			$('#remote_modal').modal('hide');
		});
		$("#btn-confirmar").on('click', function() {
			$('#path').fileinput('upload');
		});
	});
</script>