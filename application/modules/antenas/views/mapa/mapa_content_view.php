<!--
	/*
	 * Vista mapa de Torres.
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 21/03/2019 (Leandro)
	 */
-->
<script>
//	var input_figuras = <?php // echo $figuras;   ?>;
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	$(document).ready(function() {
		$('#proveedor').change(function() {
			filtrarFiguras();
		});
		$('#distrito').change(function() {
			filtrarFiguras();
		});
	})
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Antenas'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="form-horizontal">
					<div class="row">
						<?php foreach ($fields as $field): ?>
							<div class="form-group">
								<?php echo $field['label']; ?> 
								<?php echo $field['form']; ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="row">
						<div id="map" style="height:640px;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
