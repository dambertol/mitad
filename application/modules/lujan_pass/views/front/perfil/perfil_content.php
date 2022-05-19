<!--
    /*
     * Vista content de Perfil.
     * Autor: Leandro
     * Creado: 05/01/2021
     * Modificado: 05/01/2021 (Leandro)
     */
-->
<section id="banner" class="major">
    <div class="inner">
        <header class="major">
            <a href="lujan_pass/front/inicio">
                <h3 style="text-align:center;">
                    <img src="img/lujan_pass/lujan_pass_001_03.png" alt="Luján Pass" height="150" title="Luján Pass"/>
                </h3>
            </a>
        </header>
    </div>
</section>
<div id="main">
    <section id="one">
        <div class="inner">
            <header class="major">
                <h2>Mi Perfil</h2>
            </header>
            <div class="form" id="comercio-form">
                <?php if (!empty($administrar)) : ?>
                    <div class="row gtr-uniform fondo-blanco">
                        <div class="col-12 sin-borde">
                            <h3>Mis Comercios</h3>
                        </div>
                        <div class="col-12 sin-borde" style="text-align: center;">
                            <ul class="actions">
                                <li><a href="lujan_pass/escritorio" class="button" type="button">Administrar</a></li>
                            </ul>
                        </div>
                    </div>
                    <br>
                <?php endif; ?>
                <div class="row gtr-uniform fondo-blanco">
                    <div class="col-12 sin-borde">
                        <h3>Mis Datos</h3>
                    </div>
                    <div class="col-6 col-12-medium">
                        <?php echo form_input($nombre); ?>
                    </div>
                    <div class="col-6 col-12-medium">
                        <?php echo form_input($apellido); ?>
                    </div>
                    <div class="col-6 col-12-medium">
                        <?php echo form_input($cuil); ?>
                    </div>
                    <div class="col-6 col-12-medium">
                        <?php echo form_input($sexo); ?>
                    </div>
                    <div class="col-6 col-12-medium">
                        <?php echo form_input($celular); ?>
                    </div>
                    <div class="col-6 col-12-medium">
                        <?php echo form_input($email); ?>
                    </div>
                    <div class="col-12 col-12-medium">
                        <?php echo form_input($localidad); ?>
                    </div>
                </div>
                <br>
                <div class="row gtr-uniform fondo-blanco">
                    <div class="col-12 sin-borde">
                        <h3>Mi Credencial</h3>
                    </div>
                    <div class="col-12 sin-borde" style="text-align: center;">
                        <?php echo '<img src="data:image/png;base64, ' . $tarjeta . '" alt="Tarjeta" style="width:100%; max-width:640px;"/>'; ?>
                        <ul class="actions">
                            <li><?php echo '<a download="tarjeta.png" href="data:image/png;base64, ' . $tarjeta . '" class="button" type="button">Descargar</a>'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
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
        $('#cuil').inputmask({
            mask: '99-99999999-9',
            removeMaskOnSubmit: true
        });
        $('#celular').inputmask({
            mask: '999 999 9999',
            removeMaskOnSubmit: true
        });
    });
</script>