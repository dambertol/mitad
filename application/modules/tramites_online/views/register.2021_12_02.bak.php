<!DOCTYPE html>
<html lang="es">
    <head>
        <base href="<?php echo base_url(); ?>" />
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link type="text/plain" rel="author" href="http://sistemamlc.lujandecuyo.gob.ar/v2/humans.txt" />
        <link rel="icon" href="<?php echo base_url(); ?>favicon.ico">
        <title>Trámites OnLine</title>
        <!-- Bootstrap-Select -->
        <link href="vendor/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">
        <!-- Bootstrap Core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- SweetAlert -->
        <link href="vendor/sweetalert/sweetalert2.min.css" rel="stylesheet">
        <!-- Sistema MLC 2 -->
        <link href="css/base.css" rel="stylesheet">
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
                    <div class="col-lg-5 col-lg-offset-6 col-md-6 col-md-offset-6 col-xs-12">
                        <div class="login" id="card">
                            <div class="front signin_form"> 
                                <img class="center-block" src="img/generales/logo_lujan_002.png" alt="Luján de Cuyo" />
                                <br />
                                <p style="text-align:center; font-size:18px;">TRÁMITES ONLINE</p>
                                <?php echo form_open(current_url(), 'class="login-form" id="register-form"'); ?>
                                <div class="form-group">
                                    <div class="input-group">
                                        <?php echo form_input($cuil); ?>
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-user"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <?php echo form_input($nombre); ?>
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-user"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <?php echo form_input($apellido); ?>
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-user"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <?php echo form_dropdown($sexo, $sexo_opt, $sexo_opt_selected); ?>
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-user"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <?php echo form_input($email); ?>
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-envelope"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <?php echo form_input($celular); ?>
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-phone"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <?php echo form_dropdown($localidad, $localidad_opt, $localidad_opt_selected); ?>
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-map-marker"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
									<div class="col-sm-2"></div>
									<div class="col-sm-10">
									<input type="checkbox" id="terminos" name="terminos" required>
									<label for="bases">He leído y acepto los <a href="img/generales/publico/formularios/TERMINOS Y CONDICIONES TAD.pdf" target="_blank">Términos y Condiciones</a></label>
									</div>
								</div>
                                <button class="btn btn-lg btn-primary btn-block" type="submit">REGISTRATE</button>
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
        <!-- Bootstrap Select -->
        <script src="vendor/bootstrap-select/js/bootstrap-select.min.js"></script>
        <script src="vendor/bootstrap-select/js/i18n/defaults-es_ES.min.js"></script>
        <!-- SweetAlert -->
        <script src="vendor/sweetalert/sweetalert2.min.js"></script>
        <!-- InputMask -->
        <script src="vendor/inputmask/jquery.inputmask.bundle.min.js"></script>
        <!-- Sistema MLC 2 -->
        <script src="js/base.js"></script>
        <?php echo $recaptcha_script; ?>
        <script>
                var register_form = document.getElementById('register-form');
                $('#register-form').submit(function(event) {
                    event.preventDefault();
                    grecaptcha.execute();
                });
                function submitForm(response) {
                    register_form.submit();
                }
                $(document).ready(function() {
                    $('#cuil').inputmask({
                        mask: '99-99999999-9',
                        removeMaskOnSubmit: true
                    });
                    $('#cuil').blur(function() {
                        var input = this;
                        var cuil = input.value;
                        var resul = validaCuil(cuil);
                        if (!resul) {
                            Swal.fire({
                                type: 'error',
                                title: 'Error.',
                                text: 'CUIL inválido',
                                buttonsStyling: false,
                                confirmButtonClass: 'btn btn-primary'
                            }).then(function() {
                                Swal.close();
                                input.focus()
                            });
                        }
                    });
                    $('#celular').inputmask({
                        mask: '999 999 9999',
                        removeMaskOnSubmit: true
                    });
                });
        </script>
    </body>
</html>