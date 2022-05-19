<!DOCTYPE HTML>
<html>
    <head>
        <base href="<?php echo base_url(); ?>" />
        <title>Más Beneficios</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <meta name="description" content="">
        <link type="text/plain" rel="author" href="<?php echo base_url(); ?>humans.txt" />
        <meta name="theme-color" content="#FFD318">
        <!-- Select2 -->
        <link href="vendor/select2/css/select2.css" rel="stylesheet">
        <!-- Font-awesome -->
        <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css" />
        <!-- SweetAlert -->
        <link rel="stylesheet" href="vendor/sweetalert/sweetalert2.min.css" />
        <!-- iziModal -->
        <link rel="stylesheet" href="vendor/iziModal/css/iziModal.min.css" />
        <!-- Front -->
        <link rel="stylesheet" href="<?php echo auto_ver("css/mas_beneficios/front/main.min.css"); ?>" />
        <noscript><link rel="stylesheet" href="css/mas_beneficios/noscript.css" /></noscript>
        <!-- jQuery -->
        <script src="vendor/jquery/jquery-3.4.1.min.js"></script>
    </head>
    <body class="is-preload">
        <!-- Wrapper -->
        <div id="wrapper">
            <header id="header" class="alt">
                <nav>
                    <a href="#menu">Menu</a>
                </nav>
            </header>
            <?php echo $menu; ?>
            <?php echo $content; ?>
            <?php echo $footer; ?>
        </div>
        <div id="bg-loader-ajax" style="display:none;">
            <div id="loader-ajax"><img alt="LC" src="img/generales/logo_lujan_001.png"/></div>
        </div>
        <!-- InputMask -->
        <script src="vendor/inputmask/jquery.inputmask.bundle.min.js"></script>
        <!-- Select2 -->
        <script src="vendor/select2/js/select2.js"></script>
        <script src="vendor/select2/js/i18n/es.js"></script>
        <!-- SweetAlert -->
        <script src="vendor/sweetalert/sweetalert2.min.js"></script>
        <!-- iziModal -->
        <script src="vendor/iziModal/js/iziModal.min.js"></script>
        <!-- Front -->
        <script src="js/mas_beneficios/front/jquery.scrolly.min.js"></script>
        <script src="js/mas_beneficios/front/jquery.scrollex.min.js"></script>
        <script src="js/mas_beneficios/front/browser.min.js"></script>
        <script src="js/mas_beneficios/front/breakpoints.min.js"></script>
        <script src="js/mas_beneficios/front/util.min.js"></script>
        <script src="<?php echo auto_ver("js/mas_beneficios/front/main.min.js"); ?>"></script>
        <!-- Base -->
        <script src="js/base.js"></script>
        <?php if (ENVIRONMENT === 'production'): ?>
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-46335422-4"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());

                gtag('config', 'UA-46335422-4');
            </script>
        <?php endif; ?>
    </body>
</html>