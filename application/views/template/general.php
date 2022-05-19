<!DOCTYPE html>
<html lang="es">
	<head>
		<base href="<?php echo base_url(); ?>" />
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link type="text/plain" rel="author" href="http://sistemamlc.lujandecuyo.gob.ar/v2/humans.txt" />
		<link rel="icon" href="<?php echo base_url(); ?>favicon.ico">
		<title><?php echo (isset($title)) ? $title : TITLE; ?></title>
		<!-- Bootstrap-Select -->
		<link href="vendor/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">
		<!-- Bootstrap Core CSS -->
		<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<!-- Custom CSS -->
		<link href="vendor/gentelella/css/custom.min.css" rel="stylesheet">
		<!-- Custom Fonts -->
		<link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
		<!-- DataTables -->
		<link href="vendor/datatables/css/datatables.min.css" rel="stylesheet">
		<!-- DateTimePicker -->
		<link href="vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
		<!-- SweetAlert -->
		<link href="vendor/sweetalert/sweetalert2.min.css" rel="stylesheet">
		<!-- iCheck -->
		<link href="vendor/icheck/skins/flat/blue.css" rel="stylesheet">
		<!-- Sistema MLC 2 -->
		<link href="css/base.css" rel="stylesheet">
		<?php
		if (!empty($css))
		{
			if (is_array($css))
			{
				foreach ($css as $C)
				{
					if (substr($C, 0, 4) !== 'http')
					{
						echo '<link rel="stylesheet" href="' . auto_ver($C) . '">';
					}
					else
					{
						echo '<link rel="stylesheet" href="' . $C . '">';
					}
				}
			}
			else
			{
				if (substr($css, 0, 4) !== 'http')
				{
					echo '<link rel="stylesheet" href="' . auto_ver($css) . '">';
				}
				else
				{
					echo '<link rel="stylesheet" href="' . $css . '">';
				}
			}
		}
		?>
		<script>
			var CI = {'base_url': '<?php echo base_url(); ?>'};
		</script>
		<!-- jQuery -->
		<script src="vendor/jquery/jquery-3.4.1.min.js"></script>
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
                <?php if (ENVIRONMENT === 'production') : ?>
                        <!-- Global site tag (gtag.js) - Google Analytics -->
                        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-46335422-2"></script>
                        <script>
                          window.dataLayer = window.dataLayer || [];
                          function gtag(){dataLayer.push(arguments);}
                          gtag('js', new Date());

                          gtag('config', 'UA-46335422-2');
                        </script>
                <?php endif; ?>
	</head>
	<body class="<?php echo ($menu_collapse === '1') ? 'nav-sm' : 'nav-md'; ?>">
		<div class="container body">
			<div class="main_container">
				<?php echo $nav; ?>
				<div class="right_col" role="main">
					<?php echo $content; ?>
				</div>
				<footer>
					<div class="pull-left">
						Copyright &copy; <?php echo (date("Y") === "2012") ? "2012" : "2012 - " . date("Y"); ?> Municipalidad de Luján de Cuyo - Dirección de Informática y Comunicaciones
					</div>
					<div class="pull-right">
						Versión 2.3.0
					</div>
					<div class="clearfix"></div>
				</footer>
			</div>
			<div id="bg-loader-ajax" class="bg-loader-ajax" style="display:none;">
				<div id="loader-ajax" class="loader-ajax"><img alt="LC" src="img/generales/logo_lujan_001.png"/></div>
			</div>
		</div>
		<div class="modal fade" id="remote_modal" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
				</div>
			</div>
		</div>
		<!-- Bootstrap Core JavaScript -->
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
		<!-- FastClick -->
		<script src="vendor/fastclick/lib/fastclick.min.js"></script>
		<!-- Custom Theme JavaScript -->
		<script src="vendor/gentelella/js/custom.min.js"></script>
		<!-- Moment -->
		<script src="vendor/moment/moment-with-locales.min.js"></script>
		<script>
			moment.updateLocale('es', {
				months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
				monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
			});
		</script>
		<!-- DataTables -->
		<script src="vendor/datatables/js/datatables.min.js"></script>
		<script src="vendor/datatables/plugins/sorting/datetime-moment.js"></script>
		<!-- DateTimePicker -->
		<script src="vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
		<!-- Bootstrap Select -->
		<script src="vendor/bootstrap-select/js/bootstrap-select.min.js"></script>
		<script src="vendor/bootstrap-select/js/i18n/defaults-es_ES.min.js"></script>
		<!-- SweetAlert -->
		<script src="vendor/sweetalert/sweetalert2.min.js"></script>
		<!-- iCheck -->
		<script src="vendor/icheck/icheck.min.js"></script>
		<!-- InputMask -->
		<script src="vendor/inputmask/jquery.inputmask.bundle.min.js"></script>
		<!-- PrintThis -->
		<script src="vendor/printThis/printThis.js"></script>
		<!-- Sistema MLC 2 -->
		<script src="js/base.js"></script>
                <!-- Trámites Online -->
		<script src="js/tramites_online/base.js"></script>
		<?php
		if (!empty($js))
		{
			if (is_array($js))
			{
				foreach ($js as $J)
				{
					if (substr($J, 0, 4) !== 'http')
					{
						echo '<script src="' . auto_ver($J) . '"></script>';
					}
					else
					{
						echo '<script src="' . $J . '"></script>';
					}
				}
			}
			else
			{
				if (substr($js, 0, 4) !== 'http')
				{
					echo '<script src="' . auto_ver($js) . '"></script>';
				}
				else
				{
					echo '<script src="' . $js . '"></script>';
				}
			}
		}
		?>
		<script>
			$(document).ready(function() {
				$("#menu_toggle").on('click', function(e) {
					if ($('body').hasClass('nav-md')) {
						set_menu_collapse(0);
					} else {
						set_menu_collapse(1);
					}
				});
				$("#remote_modal").on("show.bs.modal", function(e) {
					if (typeof e.relatedTarget !== 'undefined') {
						var link = $(e.relatedTarget);
						$(this).find(".modal-content").load(link.attr("href"));
					}
				});
				$('#remote_modal').on("hidden.bs.modal", function(e) {
					$(this).find(".modal-content").empty();
				});
				update_alertas();
				setInterval(function() {
					update_alertas();
				}, 300000);	//5 MIN
			});
			function set_menu_collapse(val) {
				$.ajax({
					type: 'POST',
					url: 'ajax/set_menu_collapse',
					data: {value: val, <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'},
					dataType: 'json'
				});
			}
			function update_alertas() {
				$.ajax({
					url: "ajax/update_alertas",
					dataType: "json",
					success: function(data) {
						$("#alertas-count").html(data.count);
						$("#alertas-menu").html(data.message);
					}
				});
			}
		</script>
	</body>
</html>
