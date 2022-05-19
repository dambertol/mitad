<!--
	/*
	 * Vista ABM de Vales
	 * Autor: Leandro
	 * Creado: 14/11/2017
	 * Modificado: 28/01/2019 (Leandro)
	 */
-->
<script>
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	var call = 0;
	$(document).ready(function() {
<?php if ($txt_btn === 'Solicitar' || $txt_btn === 'Editar vale') : ?>
			$('#vehiculo').on('changed.bs.select', function(e) {
				buscar_combustible_vehiculo();
			});
<?php endif; ?>
<?php if ($txt_btn !== 'Imprimir Planilla' && $txt_btn !== 'Anular') : ?>
			buscar_persona(++call);
			$("#persona").keyup(function() {
				buscar_persona(++call);
				return false;
			});
<?php endif; ?>
<?php if ($txt_btn === 'Agregar') : ?>
			buscar_combustible_vehiculo(true);
			$('#vehiculo').on('changed.bs.select', function(e) {
				buscar_combustible_vehiculo(true);
			});
<?php endif; ?>
		$('#fecha').datetimepicker({
			locale: 'es',
			format: 'L',
			useCurrent: false,
			showClear: true,
			showTodayButton: true,
			showClose: true,
			daysOfWeekDisabled: [0, 1, 3, 4, 5, 6]
		});
		$("#fecha").on("dp.change", function(e) {
			if (e.date.weekday() !== 1) {	//No es Martes
				$('#fecha').val(e.date.startOf('week').add(1, 'days').format("DD/MM/YYYY"));
			}
		});
		$('#vencimiento').datetimepicker({
			locale: 'es',
			format: 'L',
			useCurrent: false,
			showClear: true,
			showTodayButton: true,
			showClose: true,
			daysOfWeekDisabled: [0, 1, 3, 4, 5, 6]
		});
		$("#vencimiento").on("dp.change", function(e) {
			if (e.date.weekday() !== 1) {	//No es Martes
				$('#vencimiento').val(e.date.startOf('week').add(1, 'days').format("DD/MM/YYYY"));
			}
		});
	});
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Vale'; ?></h2>
				<?php if (!empty($audi_modal)): ?>
					<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
						<i class="fa fa-info-circle"></i>
					</button>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php if ($txt_btn === 'Solicitar') : ?>
					<div class="alert alert-info alert-dismissible fade in" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
						</button>
						<i class="fa fa-info"></i>INFORMACIÓN<br>
						En caso de que no existe el vehículo en el listado, deberá cargarlo en la opción Vehículos y esperar la aprobación de Auditoría<br>
						Sólo podrá utilizar vehículos con el seguro vigente
					</div>
				<?php endif; ?>
				<?php $data_submit = ($txt_btn === 'Anular') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'class="form-horizontal"'); ?>
				<div class="row">
					<?php foreach ($fields as $field): ?>
						<div class="form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
					<?php if ($txt_btn === 'Desanular') : ?>
						<div class="text-center" style="font-size:18px; color:red;">
							Para desanular el Vale se necesita la confirmación de un usuario de Hacienda y un usuario de Contaduría.<br />
							Confirmaciones actuales:
							<?php
							if ($vale->desanula_con == 0 && $vale->desanula_hac == 0)
							{
								echo "<b> Ninguna</b>";
							}
							else
							{
								if ($vale->desanula_con != 0)
								{
									echo "<b> Contaduría</b>";
								}
								if ($vale->desanula_hac != 0)
								{
									echo "<b> Hacienda</b>";
								}
							}
							?>
						</div>
					<?php endif; ?>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Editar vale' || $txt_btn === 'Anular' || $txt_btn === 'Desanular') ? form_hidden('id', $vale->id) : ''; ?>
					<a href="vales_combustible/vales/<?php echo (!empty($back_url)) ? $back_url : 'listar'; ?>" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>