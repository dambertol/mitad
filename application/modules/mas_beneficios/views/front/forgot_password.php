<!--
        /*
         * Vista Forgot Password
         * Autor: Leandro
         * Creado: 19/07/2018
         * Modificado: 21/12/2020 (Leandro)
         */
-->
<section id="banner" class="major">
    <div class="inner">
        <header class="major">
            <a href="mas_beneficios/front/inicio">
                <h3 style="text-align:center;">
                    <img src="img/mas_beneficios/beneficios_001_03.png"  alt="Más Beneficios" height="150" title="Más Beneficios"/>
                </h3>
            </a>
        </header>
    </div>
</section>
<div id="main">
    <section id="one">
        <div class="inner">
            <header class="major">
                <h2>Ingresá tu documento y te enviaremos una nueva clave a tu mail</h2>
            </header>
            <?php echo form_open("auth/forgot_password", 'id="forgot-form"'); ?>
            <div class="row gtr-uniform">
                <div class="col-12">
                    <?php echo form_input($identity); ?>
                </div>
                <?php echo $recaptcha_widget; ?>
                <div class="col-12 sin-borde">
                    <ul class="actions">
                        <li><input value="Enviar" class="primary fit" type="submit"></li>
                    </ul>
                </div>
            </div>
            <?php echo form_close(); ?>
            <p><a href="auth/login">Volver</a></p>
        </div>
    </section>
</div>
<?php echo $recaptcha_script; ?>
<script type="text/javascript">
    $(document).ready(function() {
<?php if (!empty($error) && $error !== 'null') : ?>
            Swal.fire({
                title: "Error",
                html: <?php echo $error; ?>,
                type: 'error',
                background: '#676567',
                customClass: 'front-modal',
                buttonsStyling: false,
                confirmButtonClass: 'primary',
                width: '36rem'
            });
<?php endif; ?>
<?php if (!empty($message) && $message !== 'null') : ?>
            Swal.fire({
                title: "Ok",
                html: <?php echo $message; ?>,
                type: 'success',
                background: '#676567',
                customClass: 'front-modal',
                buttonsStyling: false,
                confirmButtonClass: 'primary',
                width: '36rem'
            });
<?php endif; ?>
    });
    var forgot_form = document.getElementById('forgot-form');
    $('#forgot-form').submit(function(event) {
        event.preventDefault();
        grecaptcha.execute();
    });
    function submitForm(response) {
        forgot_form.submit();
    }
</script>
