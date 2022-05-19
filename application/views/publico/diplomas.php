<!DOCTYPE html>
<html lang="es">
    <head>
        <base href="<?php echo base_url(); ?>" />
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link type="text/plain" rel="author" href="https://sistemamlc.lujandecuyo.gob.ar/v2/humans.txt" />
        <link rel="icon" href="<?php echo base_url(); ?>favicon.ico">
        <title><?php echo $titulo; ?></title>
        <!-- Bootstrap Core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="vendor/gentelella/css/custom.min.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="css/signin.css" rel="stylesheet">
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
                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());

                gtag('config', 'UA-46335422-2');
            </script>
        <?php endif; ?>
    </head>
    <body>
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
        <div class="container text-center">
            <?php if (!empty($image)) : ?>
				<h3>
					<a href="data:image/png;base64, <?= $image; ?>" download="dilpoma.png">
						<img id="diploma" src="data:image/png;base64, <?= $image; ?>" alt="Diploma" title="Haga click en la imagen para descargarla" style="width:90%; max-width:1280px;"/>
					</a>
				</h3>
            <?php endif; ?>
            <br>
            <div class="text-center">
                <a class="btn btn-lg btn-primary" onClick="$('#diploma').click()">DESCARGAR</a>
                <a class="btn btn-lg btn-primary" href="publico/formularios">VOLVER</a>
            </div>
            <?php echo form_close(); ?>
        </div> <!-- /container -->
        <!-- Bootstrap Core JavaScript -->
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
        <!-- FastClick -->
        <script src="vendor/fastclick/lib/fastclick.min.js"></script>
        <!-- Sistema MLC 2 -->
        <script src="js/base.js"></script>
    </body>
</html>