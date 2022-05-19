<!DOCTYPE html>
<html lang="es">
    <head>
        <base href="<?php echo base_url(); ?>" />
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link type="text/plain" rel="author" href="https://sistemamlc.lujandecuyo.gob.ar/v2/humans.txt" />
        <link rel="icon" href="<?php echo base_url(); ?>favicon.ico">
        <title><?php echo TITLE; ?></title>
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
        <div class="container">
            <?php echo form_open(current_url(), 'class="form-signin" id="boleta-form" style="max-width: 480px;"'); ?>
            <h3 class="form-signin-heading">
                <img class="center-block" src="img/generales/logo_lujan_001.png" alt="Luján de Cuyo" />
                <span class="center-block" style="text-align: center; color:#4E4C4E; margin: 10px 0;">Imprimí tu boleto</span>
            </h3>
            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                </button>
                <i class="fa fa-info"></i>IMPORTANTE<br>
                Para la correcta impresión de tu boleto, utilizá papel formato A4. 
            </div>
            <div>
                <label for="servicio" class="sr-only">Servicio</label>
                <?php echo form_dropdown('servicio', $servicio_opt, $servicio_opt_selected, 'id="servicio" class="form-control" required'); ?>
            </div>
            <div>
                <label for="padron" class="sr-only">Padrón</label>
                <?php echo form_input($padron); ?>
            </div>
            <?php echo $recaptcha_widget; ?>
            <br>
            <button class="btn btn-lg btn-primary btn-block" type="submit">ACEPTAR</button>
            <?php echo form_close(); ?>
        </div> <!-- /container -->
        <div style="cursor: pointer; max-width: 450px; margin: 0 auto;" onclick="window.open('https://pagomiscuentas.com/como-pago', '_blank');">
            <img class="center-block" src="img/generales/publico/como_pagar_004_01.png" alt="PagoMisCuentas" />
        </div>
        <br>
        <div style="cursor: pointer; max-width: 450px; margin: 0 auto;" onclick="window.open('https://www.redlink.com.ar/link_pagos_y_cobranzas.html#como_y_donde_pagar', '_blank');">
            <img class="center-block" src="img/generales/publico/como_pagar_004_02.png" alt="PagosLink" />
        </div>
        <br>
        <div style="cursor: pointer; max-width: 450px; margin: 0 auto;" onclick="window.open('https://www.mercadopago.com.ar/servicios', '_blank');">
            <img class="center-block" src="img/generales/publico/como_pagar_004_03.png" alt="MercadoPago" />
        </div>
        <br>
        <div style="cursor: pointer; max-width: 450px; margin: 0 auto;" onclick="window.open('https://www.epagos.com.ar/ayuda.php', '_blank');">
            <img class="center-block" src="img/generales/publico/como_pagar_004_04.png" alt="MercadoPago" />
        </div>
        <!-- Bootstrap Core JavaScript -->
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
        <!-- FastClick -->
        <script src="vendor/fastclick/lib/fastclick.min.js"></script>
        <!-- Sistema MLC 2 -->
        <script src="js/base.js"></script>
        <?php echo $recaptcha_script; ?>
        <script type="text/javascript">
            var boleta_form = document.getElementById('boleta-form');
            $('#boleta-form').submit(function(event) {
                event.preventDefault();
                grecaptcha.execute();
            });
            function submitForm(response) {
                boleta_form.submit();
            }
        </script>
    </body>
</html>