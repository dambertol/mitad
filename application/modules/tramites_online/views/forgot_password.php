<!DOCTYPE html>
<html lang="es">
    <head>
        <base href="<?php echo base_url(); ?>" />
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link type="text/plain" rel="author" href="https://sistemamlc.lujandecuyo.gob.ar/v2/humans.txt" />
        <link rel="icon" href="<?php echo base_url(); ?>favicon.ico">
        <title>Trámites a Distancia</title>
        <!-- Bootstrap Core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom styles -->
        <link href="css/tramites_online/login.css" rel="stylesheet">
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
    <body class="main">
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
        <div class="login-screen"></div>
        <div class="login-center">
            <div class="container min-height" style="margin-top:30px;">
                <div class="row">
                    <div class="col-lg-3 col-lg-offset-8 col-md-4 col-md-offset-8 col-xs-12">
                        <div class="login" id="card">
                            <div class="front signin_form">
                                <img class="center-block" src="img/generales/logo_lujan_002.png" alt="Luján de Cuyo" />
                                <br />
                                <p style="text-align:center; font-size:18px;">TRÁMITES A DISTANCIA</p>
                                <p style="text-align:center;">
                                    Ingresa tu documento y recibirás un correo electrónico para recuperar tu contraseña.
                                </p>
                                        <?php echo form_open('auth/forgot_password', 'class="login-form", id="forgot-form"'); ?>
                                <div class="form-group">
                                    <div class="input-group">
<?php echo form_input($identity); ?>
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-user"></i>
                                        </span>
                                    </div>
                                </div>
                                <button class="btn btn-lg btn-primary btn-block" style="margin-top:69px;" type="submit">RECUPERAR</button>
                                <p>
                                    <a href="auth/login" class="forgot">Volver</a>
                                </p>
<?php echo $recaptcha_widget; ?>
<?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bootstrap Core JavaScript -->
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<?php echo $recaptcha_script; ?>
        <script type="text/javascript">
            var forgot_form = document.getElementById('forgot-form');
            $('#forgot-form').submit(function(event) {
                event.preventDefault();
                grecaptcha.execute();
            });
            function submitForm(response) {
                forgot_form.submit();
            }
        </script>
    </body>
</html>