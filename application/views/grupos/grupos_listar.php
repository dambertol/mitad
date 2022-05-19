<!--
   /*
	* Vista Listado de Grupos
	* Autor: Leandro
	* Creado: 27/01/2017
	* Modificado: 11/09/2018 (Leandro)	
	*/
-->
<script>
	var grupos_table;
	function complete_grupos_table() {
		$('#grupos_table tfoot th').each(function(i) {
			var title = $('#grupos_table thead th').eq(i).text();
			if (title !== '') {
				$(this).html('<input class="form-control input-xs" style="width: 100%;" type="text" placeholder="' + title + '" value="' + grupos_table.column(i).search() + '"/>');
			}
		});
		$('#grupos_table tfoot th').eq(5).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'grupos_table\');"><i class="fa fa-eraser"></i></button>');
		grupos_table.columns().every(function() {
			var column = this;
			$('input,select', grupos_table.table().footer().children[0].children[this[0][0]]).on('change', function() {
				if (column.search() !== this.value) {
					column.search(this.value).draw();
				}
			});
		});
		var r = $('#grupos_table tfoot tr');
		r.find('th').each(function() {
			$(this).css('padding', 5);
		});
		$('#grupos_table thead').append(r);
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Grupos'; ?></h2>
				<?php echo anchor('grupos/agregar', 'Crear grupo', 'class="btn btn-primary btn-sm"') ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php echo $js_table; ?>
				<?php echo $html_table; ?>
			</div>
		</div>
	</div>
</div>