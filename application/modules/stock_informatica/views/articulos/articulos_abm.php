<!--
	/*
	 * Vista ABM de Artículo.
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 19/02/2020 (Leandro)
	 */
-->
<?php if ($txt_btn === 'Agregar'): ?>
	<script>
		$(document).ready(function() {
			$("#categoria").selectpicker('val', <?php echo $categoria_id; ?>);
			$("#categoria").on('changed.bs.select', function(e) {
				var categoria = $("#categoria option:selected").val();
				window.location.replace(CI.base_url + "stock_informatica/articulos/<?php echo $txt_btn === 'Agregar' ? 'agregar' : 'editar'; ?>/" + categoria);
			});
		});
	</script>
<?php endif; ?>
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Artículos'; ?></h2>
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
				<div class="row">
					<div class="col-xs-12">
						<section class="panel">
							<div class="x_title">
								<h2>Atributos</h2>
								<div class="clearfix"></div>
							</div>
							<div class="panel-body">
								<div class="row">
									<table class="table table-bordered table-condensed table-striped">
										<thead>
											<tr>
												<th style="width:50%;">Nombre</th>
												<th style="width:50%;">Valor</th>
											</tr>
										</thead>
										<tbody>
											<?php $cant_rows_nro = 0; ?>
											<?php foreach ($fields_detalle_array as $Fields_detalle): ?>
												<?php $cant_rows_nro++; ?>
												<tr id="detalle_<?php echo $cant_rows_nro; ?>">
													<?php foreach ($Fields_detalle as $Field): ?>
														<?php if (isset($Field['type']) && $Field['type'] == 'hidden'): ?>
															<?php echo $Field['form']; ?>
														<?php else: ?>
															<td><?php echo $Field['form']; ?></td>
														<?php endif; ?>
													<?php endforeach; ?>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</section>
					</div>
				</div>
				<div class="ln_solid"></div>
				<div class="text-center">
					<?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
					<?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $articulo->id) : ''; ?>
					<a href="stock_informatica/articulos/listar" class="btn btn-default btn-sm">Cancelar</a>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>