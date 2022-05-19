<!--
   /*
	* Vista Listado de Usuarios
	* Autor: Leandro
	* Creado: 26/01/2017
	* Modificado: 23/10/2019 (Leandro)
	*/
-->
<script>
	var usuarios_table;
	$(document).ready(function() {
		$('#grupo').on('change', function() {
			usuarios_table.ajax.reload(null, false);
		});
	});
	function complete_usuarios_table() {
		$('#usuarios_table tfoot th').each(function(i) {
			var title = $('#usuarios_table thead th').eq(i).text();
			if (title !== '') {
				if (title === 'Estado') {
					$(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_estados)); ?>);
					$(this).find('select').val(usuarios_table.column(i).search());
				} else {
					$(this).html('<input class="form-control input-xs" style="width: 100%;" type="text" placeholder="' + title + '" value="' + usuarios_table.column(i).search() + '"/>');
				}
			}
		});
		$('#usuarios_table tfoot th').eq(6).html('');
		$('#usuarios_table tfoot th').eq(7).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'usuarios_table\');"><i class="fa fa-eraser"></i></button>');
		usuarios_table.columns().every(function() {
			var column = this;
			$('input,select', usuarios_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
				if (column.search() !== this.value) {
					column.search(this.value).draw();
				}
			});
		});
		var r = $('#usuarios_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#usuarios_table thead').append(r);
	}
	function activar_usuario(usuario_id) {
		Swal.fire({
			title: 'Confirmar',
			text: "Se activará el usuario seleccionado",
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
				window.location.href = CI.base_url + 'usuarios/activar/' + usuario_id;
			}
		})
	}
	function desactivar_usuario(usuario_id) {
		Swal.fire({
			title: 'Confirmar',
			text: "Se desactivará el usuario seleccionado",
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
				window.location.href = CI.base_url + 'usuarios/desactivar/' + usuario_id;
			}
		})
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Usuarios'; ?></h2>
				<?php echo anchor('usuarios/agregar', 'Crear usuario', 'class="btn btn-primary btn-sm"') ?>
				<?php echo anchor('usuarios/desbloquear', 'Desbloquear usuarios', 'class="btn btn-primary btn-sm pull-right"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="form-horizontal">
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<br />
				<div class="col-lg-12">
					<?php echo $js_table; ?>
					<?php echo $html_table; ?>
				</div>
			</div>
		</div>
	</div>
</div>