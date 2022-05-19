<!--
	/*
	 * Vista ABM de Remito
	 * Autor: Leandro
	 * Creado: 10/11/2017
	 * Modificado: 12/06/2018 (Leandro)
	 */
-->
<script>
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	var call = 0;
	var call_costo = 0;
<?php if ($txt_btn === 'Agregar' || $txt_btn === 'Editar'): ?>
		var total_lts = 0;
		var vales_litros = <?php echo $vales_litros; ?>;
		var last_value_move = 0;
		var vales_vencimientos = <?php echo $vales_vencimientos; ?>;
<?php endif; ?>
	$(document).ready(function() {
<?php if ($txt_btn === 'Agregar'): ?>
			buscar_costo(++call_costo);
			$("#tipo_combustible").change(function() {
				buscar_costo(++call_costo);
				get_facturas_tipo();
				return false;
			});
			$("#litros").keyup(function() {
				buscar_costo(++call_costo);
				return false;
			});
			$("#fecha").change(function() {
				buscar_costo(++call_costo);
				return false;
			});
<?php endif; ?>
		buscar_persona(++call);
		$("#persona").keyup(function() {
			buscar_persona(++call);
			return false;
		});

<?php if ($txt_btn === 'Editar'): ?>
			$("#tipo_combustible").change(function() {
				get_facturas_tipo();
				return false;
			});
<?php endif; ?>
<?php if ($txt_btn === 'Agregar' || $txt_btn === 'Editar'): ?>
			$('#guardar').click(function() {
				total_lts = 0;
				lts_remito = parseFloat($('#litros').val());
				$('#vales').find(":selected").each(function(ind, sel) {
					total_lts += parseFloat(vales_litros[this.value]);
				});
				if (total_lts < lts_remito) {
					$('#lts_tipo').html('MENOR');
					$('#lts_vales').html(total_lts.toFixed(2));
					$('#lts_remito').html(lts_remito.toFixed(2));
					var $btn = $('<button type="button" class="btn btn-primary" onclick="javascript:confirmar();">Confirmar</button>');
					$btn.prependTo($("#buttons-litros-modal"));
					$('#litros-modal').modal('show');
				} else {
					confirmar();
				}
			});
<?php else: ?>
			$('#guardar').click(function() {
				confirmar();
			});
<?php endif; ?>
<?php if ($txt_btn === 'Agregar' || $txt_btn === 'Editar'): ?>
			$('#vales').bootstrapDualListbox({
				moveOnSelect: false,
				nonSelectedListLabel: 'Disponibles',
				selectedListLabel: 'Asignados'
			});

			$('#vales').on('change', function() {
				total_lts = 0;
				lts_remito = parseFloat($('#litros').val());
				fecha_remito = $("#fecha").data("DateTimePicker").date().startOf('day');
				venc_vale = moment(vales_vencimientos[last_value_move]).startOf('day');
				if (last_value_move !== 0 && fecha_remito && fecha_remito > venc_vale) {
					last_value_move = 0;
					$('#venc_vales').html(venc_vale.format('DD/MM/YYYY'));
					$('#vencimiento-modal').modal('show');
				}
				$('#vales').find(":selected").each(function(ind, sel) {
					total_lts += parseFloat(vales_litros[this.value]);
				});
				if (total_lts > lts_remito) {
					$('#lts_tipo').html('MAYOR');
					$('#lts_vales').html(total_lts.toFixed(2));
					$('#lts_remito').html(lts_remito.toFixed(2));
					$('#litros-modal').modal('show');
				}
			});
<?php endif; ?>
	});
	function addZ(n) {
		return n < 10 ? '0' + n : '' + n;
	}
	function confirmar() {
		$('#remito-form').submit();
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Remito'; ?></h2>
				<?php if (!empty($audi_modal)): ?>
					<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
						<i class="fa fa-info-circle"></i>
					</button>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_button = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn, 'id' => 'guardar') : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn, 'id' => 'guardar'); ?>
				<?php echo form_open(uri_string(), 'class="form-horizontal" id="remito-form"'); ?>
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
					<?php echo (!empty($txt_btn)) ? form_button($data_button, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $remito->id) : ''; ?>
					<a href="vales_combustible/remitos/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php if ($txt_btn !== 'Agregar' && $txt_btn !== 'Editar'): ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Vales asignados</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<table class="table table-bordered table-condensed table-striped">
						<thead>
							<tr>
								<th style="width:20%;">Número</th>
								<th style="width:20%;">Tipo</th>
								<th style="width:20%;">M³/Litros</th>
								<th style="width:40%;">Area</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($vales_asignados)): ?>
								<?php $total_litros = 0; ?>
								<?php foreach ($vales_asignados as $Vale): ?>
									<tr>
										<td><a href="vales_combustible/vales/ver/<?php echo $Vale->id; ?>" target="_blank"><?php echo "VC" . str_pad($Vale->id, 6, '0', STR_PAD_LEFT); ?></a></td>
										<td><?php echo $Vale->tipo_combustible; ?></td>
										<td style="text-align:right;"><?php echo number_format($Vale->metros_cubicos, 2, ',', '.'); ?></td>
										<td><?php echo $Vale->area; ?></td>
									</tr>
									<?php $total_litros += $Vale->metros_cubicos; ?>
								<?php endforeach; ?>
								<tr style="text-align:center; font-weight:bold;">
									<td colspan="2">TOTAL</td>
									<td style="text-align:right;"><?php echo number_format($total_litros, 2, ',', '.'); ?></td>
									<td style="text-align:right;"></td>
								</tr>
							<?php else: ?>
								<tr>
									<td colspan="4" style="text-align:center; font-weight:bold;">Sin vales</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>
<div class="modal fade" id="litros-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="completar-perfil-modalLabel">Atención!</h4>
			</div>
			<div class="modal-body">
				<p>La cantidad de M³/Litros de los vales es <b><span id="lts_tipo"></b> a la cantidad del remito.</p>
				<p><b>M³/Litros Vales : </b><span id="lts_vales"></span></p>
				<p><b>M³/Litros Remito: </b><span id="lts_remito"></span></p>
			</div>
			<div id="buttons-litros-modal" class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="vencimiento-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="completar-perfil-modalLabel">Atención!</h4>
			</div>
			<div class="modal-body">
				<p>El vale se encuentra <b>VENCIDO</b>.</p>
				<p><b>Vencimiento: </b><span id="venc_vales"></span></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>