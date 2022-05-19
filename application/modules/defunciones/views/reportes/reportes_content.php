<!--
	/*
	 * Vista de Reportes
	 * Autor: Leandro
	 * Creado: 28/11/2019
	 * Modificado: 04/12/2019 (Leandro)
	 */
-->
<script>
	$(document).ready(function() {
		$("#button_exportar").click(function() {
			$("#form_reporte").data('submitted', false);
			$("#form_reporte").submit();
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Reporte'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = array('id' => 'button_exportar', 'class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
				<?php echo form_open(uri_string(), 'id="form_reporte" class="form-horizontal"'); ?>
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
					<a href="defunciones/reportes/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php if (!empty($graficos_data)) : ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Operaciones Realizadas</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="row">
						<div class="chart-responsive">
							<div id="chart_usuarios" style="height:300px;"></div>
						</div>

					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Operaciones Realizadas</h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="row">
						<div class="chart-responsive">
							<div id="chart_usuarios_t" style="height:300px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			var AR = d3.formatLocale({
				"decimal": ",",
				"thousands": ".",
				"grouping": [3],
				"currency": ["$", ""]
			});
			var entero = AR.format(",.0f");
			var porcentaje = AR.format(".2%");
			chart_usuarios = c3.generate({
				bindto: '#chart_usuarios',
				data: {
					type: 'line',
					x: 'x',
					labels: {
						format: entero
					},
					columns: <?php echo $graficos_data['grafico_usuarios']; ?>
				},
				axis: {
					x: {
						tick: {
							rotate: -60
						},
						type: 'category'
					},
					y: {
						label: {
							text: 'Operaciones',
							position: 'outer-middle'
						},
						padding: {
							top: 20,
							bottom: 0
						},
						tick: {
							format: entero
						}
					}
				},
				grid: {
					y: {
						show: true
					}
				},
				tooltip: {
					format: entero
				}
			});
			chart_usuarios_t = c3.generate({
				bindto: '#chart_usuarios_t',
				data: {
					type: 'pie',
					columns: <?php echo $graficos_data['grafico_usuarios_t']; ?>
				},
				color: {
					pattern: ['#9467bd', '#8c564b', '#c49c94', '#bcbd22', '#e377c2', '#f7b6d2', '#c7c7c7', '#7f7f7f', '#dbdb8d', '#17becf', '#9edae5', '#c5b0d5']
				},
				pie: {
					label: {
						format: function(value, ratio, id) {
							return entero(value);
						}
					}
				},
				tooltip: {
					format: {
						value: function(value, ratio, id) {
							var tt = entero(value) + ' (' + porcentaje(ratio) + ')';
							return tt;
						}
					}
				}
			});
		});
	</script>
<?php endif; ?>