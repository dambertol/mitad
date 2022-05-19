<!--
        /*
         * Vista content de Comercios.
         * Autor: Leandro
         * Creado: 12/07/2018
         * Modificado: 25/04/2020 (Leandro)
         */
-->
<section id="banner" class="major">
    <div class="inner">
        <span class="image">
            <img src="<?php echo $image; ?>" alt="" />
        </span>
        <header class="major">
            <h1>Comercios</h1>
        </header>
        <div class="content">
            <p>TUS PRODUCTOS Y SERVICIOS SE COMUNICARÁN A TRAVÉS DE ESTE SITIO WEB POR EL PERIODO DE TIEMPO Y CON LA INFORMACIÓN ENVIADA.</p>
        </div>
    </div>
</section>
<div id="main">
    <section id="one">
        <div class="inner">
            <header class="major">
                <h2>Sumá tu comercio</h2>
            </header>
            <?php echo form_open(uri_string(), 'id="comercio-form"'); ?>
            <div class="row gtr-uniform">
                <div class="col-12">
                    <h3>Datos Personales</h3>
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
                    <?php echo form_input($celular); ?>
                </div>
                <div class="col-12 col-12-medium">
                    <?php echo form_input($email); ?>
                </div>
                <div class="col-12 col-12-medium">
                    <input type="checkbox" id="terminos" name="terminos" required>
                    <label for="terminos">He leído y acepto los términos y condiciones</label>
                    <a href="mas_beneficios/front/condiciones" target="_blank">Ver términos y condiciones</a>
                </div>
                <?php echo $recaptcha_widget; ?>
                <div class="col-12">
                    <ul class="actions">
                        <li><input value="Solicitar Adhesión" class="primary fit" type="submit"></li>
                    </ul>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </section>
</div>
<?php echo $recaptcha_script; ?>
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
        var myCheckbox = document.getElementById("terminos");
        var myCheckboxMsg = "Por favor aceptá los Términos y Condiciones";
        myCheckbox.setCustomValidity(myCheckboxMsg);
        myCheckbox.addEventListener("change", function () {
            this.setCustomValidity(this.validity.valueMissing ? myCheckboxMsg : "");
        }, false);

        $('#cuil').inputmask({
            mask: '99-99999999-9',
            removeMaskOnSubmit: true
        });
        $('#cuil').blur(function () {
            var input = this;
            var cuil = input.value;
            var resul = validaCuil(cuil);
            if (!resul) {
                Swal.fire({
                    title: 'Error.',
                    text: 'CUIL inválido',
                    type: 'error',
                    background: '#676567',
                    customClass: 'front-modal',
                    buttonsStyling: false,
                    confirmButtonClass: 'primary',
                    width: '36rem'
                }).then(function () {
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

    var comercio_form = document.getElementById('comercio-form');
    $('#comercio-form').submit(function (event) {
        event.preventDefault();
        if (validateForm()) {
            grecaptcha.execute();
        }
    });
    function validateForm() {
        if (!validaCuil($('#cuil').val())) {
            Swal.fire({
                title: "Error",
                text: 'CUIL inválido',
                type: 'error',
                background: '#676567',
                customClass: 'front-modal',
                buttonsStyling: false,
                confirmButtonClass: 'primary',
                width: '36rem'
            });
            $('#cuil').focus();
            return false;
        }
        if (!$('#terminos').prop('checked')) {
            Swal.fire({
                title: "Error",
                text: 'Por favor aceptá los Términos y Condiciones',
                type: 'error',
                background: '#676567',
                customClass: 'front-modal',
                buttonsStyling: false,
                confirmButtonClass: 'primary',
                width: '36rem'
            });
            $('#terminos').focus();
            return false;
        }
        return true;
    }
    function submitForm(response) {
        comercio_form.submit();
    }
</script>