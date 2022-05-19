<!DOCTYPE html>
<html lang="es">
	<head>
		<base href="<?php echo base_url(); ?>" />
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link type="text/plain" rel="author" href="http://sistemamlc.lujandecuyo.gob.ar/v2/humans.txt" />
		<link rel="icon" href="<?php echo base_url(); ?>favicon.ico">
		<title>Expedientes - Visualizar</title>
		<!-- Custom CSS -->
		<link href="vendor/gentelella/css/custom.min.css" rel="stylesheet">
		<!-- Custom Fonts -->
		<link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
		<!-- Visor CSS -->
		<link rel="stylesheet" href="css/ninez_adolescencia/visor.css">
		<!-- Sistema MLC 2 -->
		<link href="css/base.css" rel="stylesheet">
		<!-- jQuery -->
		<script src="vendor/jquery/jquery-3.4.1.min.js"></script>
		<!-- PDFJS -->
		<script type="text/javascript" src="vendor/pdfjs/pdf.js"></script>
		<!-- TurnJS -->
		<script type="text/javascript" src="vendor/turnjs/turn.js"></script>
		<script type="text/javascript" src="vendor/turnjs/hash.js"></script>
		<script type="text/javascript" src="vendor/turnjs/zoom.min.js"></script>
		<script type="text/javascript" src="vendor/turnjs/magazine.js"></script>
		<!-- Visor JS -->
		<script type="text/javascript" src="js/ninez_adolescencia/visor.js"></script>
	</head>
	<body onload="load()">
		<div class="container body">
			<div class="main_container">
				<div>
					<div id="indice">
						<div id="title">ÍNDICE</div>
					</div>
				</div>
				<div class="right_col" role="main" style="padding:0px !important;">
					<div id="canvas">
						<div class="zoom-icon zoom-icon-in"><i class="fa fa-search"></i></div>
						<div class="magazine-viewport">
							<div class="container">
								<div class="previous-button"></div>
								<div class="magazine">
									<div>
										<p style="text-align:center; padding-top:100px;">
											<img src="img/generales/reportes/logo_escudo_bn.jpg" alt="Luján de Cuyo" width="105" height="113" />
										</p>
										<h1 style="text-align:center; padding-top:100px;">NIÑEZ Y ADOLESCENCIA</h1>
										<h4 style="text-align:center; padding-top:100px;">EXPEDIENTE N° <?php echo $expediente_id; ?></h4>
									</div>
								</div>
								<div class="next-button"></div>
								<div class="pag">
									<ul class="pagination">
										<li class="paginate_button first" id="first">
											<a href="#" onclick="onFirstPage();return false;" tabindex="0"> &lt;&lt; </a>
										</li>
										<li class="paginate_button previous" id="prev">
											<a href="#" onclick="onPrevPage();return false;" tabindex="0"> &lt; </a>
										</li>
										<li class="paginate_button active">
											<a href="#" tabindex="0"> <span>Página: <span id="page_num"></span> / <span id="page_count"></span></span> </a>
										</li>
										<li class="paginate_button next" id="next">
											<a href="#" onclick="onNextPage();return false;" tabindex="0"> &gt; </a>
										</li>
										<li class="paginate_button last" id="last">
											<a href="#" onclick="onLastPage();return false;" tabindex="0"> &gt;&gt; </a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    <script type="text/javascript">
			var pdfDocs = [],
							urls = <?php echo $urls; ?>,
							descripciones = <?php echo $descripciones; ?>,
							current = {},
							loadedCount = 0,
							totalPageCount = 0,
							pageNum = 1,
							pageRendering = false,
							pageNumPending = null,
							scale = 1.5;
		</script>
	</body>
</html>