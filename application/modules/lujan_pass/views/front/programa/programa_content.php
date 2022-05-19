<!--
        /*
         * Vista content de Programa.
         * Autor: Leandro
         * Creado: 22/08/2018
         * Modificado: 30/12/2020 (Leandro)
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
                <h2>Programa Luján Pass</h2>
            </header>
            <div class="fondo-blanco">
                <p>Luján Pass es un programa de beneficios en servicios turísticos destinado a visitantes de nuestro departamento en el que podrán obtener descuentos, promociones y tarifas especiales para disfrutar de alojamiento, gastronomía y numerosos servicios turísticos.</p>
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
    });
</script>