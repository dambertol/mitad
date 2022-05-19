<!--
        /*
         * Vista content de Comercios.
         * Autor: Leandro
         * Creado: 12/07/2018
         * Modificado: 25/02/2021 (Leandro)
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
                <h2>Registrate como COMERCIO para comunicar tus productos y servicios</h2>
            </header>
            <?php echo form_open(uri_string(), 'id="comercio-form"'); ?>
            <div class="row gtr-uniform">
                <div class="col-12 sin-borde">
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
                    <?php echo form_dropdown($sexo, $sexo_opt, $sexo_opt_selected); ?>
                </div>
                <div class="col-6 col-12-medium">
                    <?php echo form_input($celular); ?>
                </div>
                <div class="col-6 col-12-medium">
                    <?php echo form_input($email); ?>
                </div>
                <div class="col-12 col-12-medium">
                    <?php echo form_dropdown($localidad, $localidad_opt, $localidad_opt_selected); ?>
                </div>
                <div class="col-12 col-12-medium sin-borde">
                    <input type="checkbox" id="terminos" name="terminos" required>
                    <label for="terminos">He leído y acepto los términos y condiciones</label>
                    <a href="mas_beneficios/front/condiciones" target="_blank">Ver términos y condiciones</a>
                </div>
                <?php echo $recaptcha_widget; ?>
                <div class="col-12 sin-borde">
                    <ul class="actions">
                        <li><input value="Solicitar Adhesión" class="primary fit" type="submit"></li>
                    </ul>
                    <br>
                </div>
            </div>
            <?php echo form_close(); ?>
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
        var myCheckbox = document.getElementById("terminos");
        var myCheckboxMsg = "Por favor aceptá los Términos y Condiciones";
        myCheckbox.setCustomValidity(myCheckboxMsg);
        myCheckbox.addEventListener("change", function() {
            this.setCustomValidity(this.validity.valueMissing ? myCheckboxMsg : "");
        }, false);

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
                    title: 'Error.',
                    text: 'CUIL inválido',
                    type: 'error',
                    background: '#676567',
                    customClass: 'front-modal',
                    buttonsStyling: false,
                    confirmButtonClass: 'primary',
                    width: '36rem'
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
        $.fn.select2.defaults.set('language', 'es');
        $("#localidad").select2({
            placeholder: "Seleccionar Localidad"
        });
        $("#sexo").select2({
            placeholder: "Seleccionar Sexo"
        });
    });

    var comercio_form = document.getElementById('comercio-form');
    $('#comercio-form').submit(function(event) {
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