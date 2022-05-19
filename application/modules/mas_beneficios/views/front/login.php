<!--
  /*
         * Vista Login
         * Autor: Leandro
         * Creado: 19/07/2018
         * Modificado: 23/04/2020 (Leandro)
         */
-->
<section id="banner" class="major">
    <div class="inner">
        <span class="image">
            <img src="<?php echo $image; ?>" alt="" />
        </span>
        <header class="major">
            <h1>Ingresá</h1>
        </header>
        <div class="content">
            <p>Si todavía no tenés usuario, Sumá tu Comercio</p>
        </div>
    </div>
</section>
<div id="main">
    <section id="one">
        <div class="inner">
            <header class="major">
                <h2>Ingresá con tu usuario</h2>
            </header>
            <?php echo form_open("auth/login"); ?>
            <div class="row gtr-uniform">
                <div class="col-12">
                    <?php echo form_input($legajo); ?>
                </div>
                <div class="col-12">
                    <?php echo form_input($password); ?>
                </div>
                <div class="col-12">
                    <?php echo form_checkbox('remember', '1', TRUE, 'id="remember" tabIndex="-1"'); ?>
                    <?php echo lang('login_remember_label', 'remember'); ?>
                </div>
                <div class="col-12">
                    <ul class="actions">
                        <li><?php echo form_submit('submit', lang('login_submit_btn'), 'class="primary fit"'); ?></li>
                    </ul>
                </div>
            </div>
            <?php echo form_close(); ?>
            <p><a href="auth/forgot_password"><?php echo lang('login_forgot_password'); ?></a></p>
        </div>
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function () {
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