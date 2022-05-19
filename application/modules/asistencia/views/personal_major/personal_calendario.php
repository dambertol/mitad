<!--
	/*
	 * Vista Calendario de Horario de personal.
	 * Autor: Leandro
	 * Creado: 07/02/2018
	 * Modificado: 23/10/2019 (Leandro)
	 */
-->
<script>
	document.addEventListener('DOMContentLoaded', function() {
		var calendarEl = document.getElementById('calendar');

		var calendar = new FullCalendar.Calendar(calendarEl, {
			header: {
				left: '',
				center: 'title',
				right: 'prev,next'
			},
			locale: 'es',
			plugins: ['dayGrid'],
			defaultView: 'dayGridMonth',
			contentHeight: 680,
			timeFormat: 'H:mm',
			showNonCurrentDates: false,
			displayEventTime: true,
			eventTimeFormat: {
				hour: '2-digit',
				minute: '2-digit'
			},
			eventSources: [
				{
					url: 'asistencia/personal_major/get_feriados',
					method: 'POST',
					extraParams: {
<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
					},
					success: function(data) {
						var feriados = data;
						var feriadoMoment;
						for (var i = 0; i < feriados.length; i++) {
							feriadoMoment = moment(feriados[i].fecha, 'YYYY-MM-DD');
							$("td[data-date=" + feriadoMoment.format('YYYY-MM-DD') + "]").css('background-color', '#FDC4C9');
						}
					},
					failure: function() {
						Swal.fire({
							type: 'error',
							title: 'Error.',
							text: 'Error al obtener feriados desde Major.',
							buttonsStyling: false,
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: 'Aceptar'
						});
					}
				},
				{
					url: 'asistencia/personal_major/get_horarios',
					method: 'POST',
					extraParams: {
						legajo: <?php echo $personal_horario->legajo; ?>,
<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
					},
					failure: function() {
						Swal.fire({
							type: 'error',
							title: 'Error.',
							text: 'Error al obtener horarios desde Major.',
							buttonsStyling: false,
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: 'Aceptar'
						});
					}
				}
			]
		});

		calendar.render();
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
				<h2><?php echo (!empty($title_view)) ? $title_view : 'Horario de personal'; ?></h2>
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
				<div class="x_title">
					<h2>Detalle de horario</h2>
					<div class="clearfix"></div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div id='calendar'></div>
					</div>
				</div>
				<div class="ln_solid"></div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>