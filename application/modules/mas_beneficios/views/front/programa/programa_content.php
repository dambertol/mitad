<!--
        /*
         * Vista content de Programa.
         * Autor: Leandro
         * Creado: 22/08/2018
         * Modificado: 02/12/2020 (Leandro)
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
                <h2>Programa Más Beneficios</h2>
            </header>
            <p>El Programa Más Beneficios es una herramienta digital que promueve el Desarrollo Económico de Luján a través de soluciones concretas y efectivas para la industria, el comercio, los emprendedores y prestadores de servicios.</p>
            <h4>TU COMERCIO + CERCA</h4>
            <p>La municipalidad de Lujan de Cuyo genera la Plataforma Tu Comercio + Cerca  para que los comercios adheridos ofrezcan sus productos y canales de venta a los vecinos de Luján para que fácilmente encuentren  el producto o servicio que necesitan.</p>
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