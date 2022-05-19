<!--
   /*
	 * Vista Buscador de Personas en Expedientes
	 * Autor: Leandro
	 * Creado: 17/09/2019
	 * Modificado: 23/10/2019 (Leandro)
	 */
-->
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Buscador'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<?php $data_submit = array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
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
					<?php echo form_submit($data_submit, $txt_btn, 'id="btn-buscar"'); ?>
				</div>
				<?php if (!empty($personas)) : ?>
					<div class="row">
						<?php foreach ($personas as $Persona) : ?>
							<div class="col-md-3 col-xs-12 widget widget_tally_box">
								<div class="x_panel">
									<div class="x_content">
										<div class="flex">
											<ul class="list-inline widget_profile_box" style="background-color:#FFD318; color:#4E4C4E;">
												<li style="font-size:20px; width:75%; line-height:36px; text-align:center;">
													<?php echo $Persona->dni; ?>
												</li>
												<li>
													<img src="img/generales/user.png" alt="..." class="img-circle profile_img">
												</li>
											</ul>
										</div>
										<h3 class="name"><?php echo $Persona->apellido; ?> <?php echo $Persona->nombre; ?></h3>
										<div class="flex">
											<ul class="list-inline count2">
												<li></li>
												<li>
													<a href="#" onclick='verExpedientes(<?php echo json_encode($Persona->expedientes); ?>);return false;'>
														<h3><?php echo $Persona->exp_count; ?></h3>
														<span>Expedientes</span>
													</a>
												</li>
												<li></li>
											</ul>
										</div>
										<p style="text-align:left;">
											<b>Teléfono:</b> <?php echo $Persona->telefono; ?><br/>
											<b>Celular:</b> <?php echo $Persona->celular; ?><br/>
											<b>Mail:</b> <?php echo $Persona->email; ?><br/>
											<b>Fecha Nacimiento:</b> <?php echo $Persona->fecha_nacimiento; ?><br/>
											<b>Nacionalidad:</b> <?php echo $Persona->nacionalidad; ?><br/>
										</p>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<script>
	var base_tr;
	var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
	function verExpedientes(ids) {
		console.log(ids);
		var html = '';
		for (i in ids) {
			if (ids.hasOwnProperty(i)) {
				html += '<p><a href="ninez_adolescencia/expedientes/ver/' + i + '" target="_blank">Expediente Nro ' + ids[i] + '</a></p>';
			}
		}
		Swal.fire({
			type: 'info',
			title: 'Listado de Expedientes',
			html: html,
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			confirmButtonText: 'Aceptar'
		});
	}
</script>

