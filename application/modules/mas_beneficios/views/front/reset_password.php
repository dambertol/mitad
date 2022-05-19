<!--
        /*
         * Vista Reset Password
         * Autor: Leandro
         * Creado: 30/07/2018
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
                <h2>Creá tu nueva contraseña</h2>
            </header>
            <?php echo form_open('auth/reset_password/' . $code); ?>
            <div class="row gtr-uniform">
                <div class="col-12">
                    <?php echo form_input($new_password); ?>
                </div>
                <div class="col-12">
                    <?php echo form_input($new_password_confirm); ?>
                </div>
                <div class="col-12 sin-borde">
                    <ul class="actions">
                        <li><?php echo form_submit('submit', lang('reset_password_submit_btn'), 'class="primary fit"'); ?></li>
                    </ul>
                </div>
            </div>
            <?php echo form_input($user_id); ?>
            <?php echo form_hidden($csrf); ?>
            <?php echo form_close(); ?>
        </div>
    </section>
</div>
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
</script>
