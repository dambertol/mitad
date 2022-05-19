<!--
   /*
	* Vista Formularios
	* Autor: Leandro
	* Creado: 15/11/2019
	* Modificado: 15/11/2019 (Leandro)	
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Formularios'; ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="clearfix"></div>
				<?php if (!empty($formularios)) : ?>
					<?php foreach ($formularios as $Formulario) : ?>
						<div class="col-md-4 col-sm-4">
							<div class="well profile_view">
								<div class="col-sm-12">
									<div class="left col-sm-9">
										<h2><?php echo $Formulario->nombre; ?></h2>
										<p><strong>Formato: </strong><?php echo $Formulario->formato; ?></p>
										<p><strong>Tamaño: </strong><?php echo $Formulario->tamanio; ?> KB</p>
									</div>
									<div class="right col-sm-3 text-center">
										<div class="icon">
											<i class="fa <?php echo $Formulario->icono; ?>"></i>
										</div>
									</div>
								</div>
								<div class="text-center">
									<div class="col-sm-12 emphasis">
										<a class="btn btn-primary btn-sm" href="<?php echo $Formulario->ruta; ?>" target="_blank">
											<i class="fa fa-download"> </i> Descargar
										</a>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>