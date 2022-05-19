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
        <!-- Bootstrap-Select -->
        <link href="vendor/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">
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
            <?php echo form_open(current_url(), 'class="form-horizontal" id="formulario-form"'); ?>
            <h3>
                <img class="center-block" style="max-width:100%;" src="img/generales/publico/formularios/formulario.png" alt="Luján de Cuyo" />
            </h3>
            <br>
            <h3 class="text-center red">IMPORTANTE: Extensión del plazo para el Sorteo - Fecha Viernes 16/10/2020</h3>
            <br>
            <h4 class="text-center">Datos del adulto</h4>
            <?php foreach ($fields as $field): ?>
                <?php if (isset($field['name']) && $field['name'] === 'nombre_ninio') : ?>
                    <br>
                    <h4 class="text-center">Datos del niño/a</h4>
                <?php endif; ?>
                <div class="form-group">
                    <?php echo $field['label']; ?>
                    <?php echo $field['form']; ?>
                </div>
                <?php if (isset($field['name']) && $field['name'] === 'apellido_ninio') : ?>
                    <br>
                    <h4 class="text-center">Firmas del diploma</h4>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="form-group">
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                    <input type="checkbox" id="bases" name="terminos" required>
                    <label for="bases">He leído y acepto las <a href="img/generales/publico/formularios/bases.pdf" target="_blank">Bases y Condiciones</a></label>
                </div>
            </div>
            <?php echo $recaptcha_widget; ?>
            <br>
            <div class="text-center">
                <button class="btn btn-lg btn-primary" type="submit">ENVIAR</button>
            </div>
            <?php echo form_close(); ?>
        </div> <!-- /container -->
        <!-- Bootstrap Core JavaScript -->
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
        <!-- FastClick -->
        <script src="vendor/fastclick/lib/fastclick.min.js"></script>
        <!-- Bootstrap Select -->
        <script src="vendor/bootstrap-select/js/bootstrap-select.min.js"></script>
        <script src="vendor/bootstrap-select/js/i18n/defaults-es_ES.min.js"></script>
        <!-- InputMask -->
        <script src="vendor/inputmask/jquery.inputmask.bundle.min.js"></script>
        <!-- Sistema MLC 2 -->
        <script src="js/base.js"></script>
        <?php echo $recaptcha_script; ?>
        <script type="text/javascript">
                var formulario_form = document.getElementById('formulario-form');
                $('#formulario-form').submit(function (event) {
                    event.preventDefault();
                    if (validateForm()) {
                        grecaptcha.execute();
                    }
                });
                function validateForm() {
                    if (!$('#bases').prop('checked')) {
                        Swal.fire({
                            title: "Error",
                            text: 'Por favor aceptá las Bases y Condiciones',
                            type: 'error',
                            background: '#676567',
                            customClass: 'front-modal',
                            buttonsStyling: false,
                            confirmButtonClass: 'primary',
                            width: '36rem'
                        });
                        $('#bases').focus();
                        return false;
                    }
                    return true;
                }
                function submitForm(response) {
                    formulario_form.submit();
                }
                $(document).ready(function () {
                    var myCheckbox = document.getElementById("bases");
                    var myCheckboxMsg = "Por favor aceptá las Bases y Condiciones";
                    myCheckbox.setCustomValidity(myCheckboxMsg);
                    myCheckbox.addEventListener("change", function () {
                        this.setCustomValidity(this.validity.valueMissing ? myCheckboxMsg : "");
                    }, false);
                    $('#dni').inputmask({
                        mask: '99999999',
                        removeMaskOnSubmit: true
                    });
                    $('#telefono').inputmask({
                        mask: '9999999999',
                        removeMaskOnSubmit: true
                    });
                });
        </script>
    </body>
</html>