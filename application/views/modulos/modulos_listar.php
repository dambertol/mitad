<!--
   /*
	* Vista Listado de Módulos
	* Autor: Leandro
	* Creado: 17/03/2017
	* Modificado: 11/09/2018 (Leandro)	
	*/
-->
<script>
	var modulos_table;
	function complete_modulos_table() {
		$('#modulos_table tfoot th').each(function(i) {
			var title = $('#modulos_table thead th').eq(i).text();
			if (title !== '') {
				$(this).html('<input class="form-control input-xs" style="width: 100%;" type="text" placeholder="' + title + '" value="' + modulos_table.column(i).search() + '"/>');
			}
		});
		$('#modulos_table tfoot th').eq(4).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'modulos_table\');"><i class="fa fa-eraser"></i></button>');
		modulos_table.columns().every(function() {
			var column = this;
			$('input,select', modulos_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
				if (column.search() !== this.value) {
					column.search(this.value).draw();
				}
			});
		});
		var r = $('#modulos_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#modulos_table thead').append(r);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Módulos'; ?></h2>
				<?php echo anchor('modulos/agregar', 'Crear módulo', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>