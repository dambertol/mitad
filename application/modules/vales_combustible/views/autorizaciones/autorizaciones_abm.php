<!--
	/*
	 * Vista ABM de Autorización.
	 * Autor: Leandro
	 * Creado: 17/11/2017
	 * Modificado: 04/07/2019 (Leandro)
	 */
-->
<script>
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	var call = 0;
	$(document).ready(function() {
		buscar_persona(++call);
		toggle_litros();
<?php if (!empty($txt_btn) && $txt_btn !== 'Cargar' && $txt_btn !== 'Anular') : ?>
			$('#vehiculo').on('changed.bs.select', function(e) {
				buscar_combustible_vehiculo();
			});
			$('#lleno').on('changed.bs.select', function(e) {
				toggle_litros();
			});
			$("#persona").keyup(function() {
				buscar_persona(++call);
				return false;
			});
<?php endif; ?>
	});
	function toggle_litros() {
		if ($('#lleno').val() === 'SI') {
			$('#litros_autorizados').val('0,00');
			$('#litros_autorizados').parent().parent().hide();
		} else {
			$('#litros_autorizados').parent().parent().show();
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Autorización'; ?></h2>
				<?php if (!empty($audi_modal)): ?>
					<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
						<i class="fa fa-info-circle"></i>
					</button>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'class="form-horizontal"'); ?>
				<div class="row">
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Anular' || $txt_btn === 'Cargar') ? form_hidden('id', $autorizacion->id) : ''; ?>
					<?php if ($txt_btn === 'Cargar') : ?>
						<a href="vales_combustible/autorizaciones/listar_pendientes" class="btn btn-default btn-sm">Cancelar</a>
					<?php else: ?>
						<a href="vales_combustible/autorizaciones/listar" class="btn btn-default btn-sm">Cancelar</a>
					<?php endif; ?>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>