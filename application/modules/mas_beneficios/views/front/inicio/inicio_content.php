<!--
        /*
         * Vista content Inicio.
         * Autor: Leandro
         * Creado: 12/07/2018
         * Modificado: 03/03/2021 (Leandro)
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
        <div class="content">
            <div id="buscador" class="" method="post">
                <input type="text" class="main-input" id="main-input" name="nombre" placeholder="Buscar..." value="" />
                <button type="button" class="main-btn">
                    <p class="search-small">BUSCAR POR</p>
                    <p class="search-large">NOMBRE</p>
                </button>
                <ul class="search-description">
                    <li id="search-description-categoria">CATEGORIA</li>
                    <li id="search-description-nombre">NOMBRE</li>
                </ul>
                <input id="main-submit" class="primary" type="submit" value="Buscar" />
                <input id="main-clear" class="primary" type="submit" value="Limpiar" />
            </div>
        </div>
        <div class="content">
            <div class="row" style="width:100%;">
                <div class="col-6">
                    <div style="text-align:center;">
                        <a href="mas_beneficios/front/inicio/index/1" class="button<?= $agrupamiento_id === $agrupamiento_id_comercio ? ' a-active' : '' ?>">COMERCIOS</a>
                    </div>
                </div>
                <div class="col-6">
                    <div style="text-align:center;">
                        <a href="mas_beneficios/front/inicio/index/promo" class="button<?= $agrupamiento_id === 'promo' ? ' a-active' : '' ?>">DESCUENTOS</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div id="main">
    <section id="one" class="tiles">
        <?php if (!empty($categorias)) : ?>
            <?php foreach ($categorias as $Categoria) : ?>
                <article class="<?= $Categoria->estilo ?>">
                    <header class="major">
                        <h3><a id="<?= $Categoria->id ?>" title="Filtrar sólo <?= $Categoria->nombre ?>" href="#" class="link"><?= $Categoria->nombre ?></a></h3>
                    </header>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
    <section id="two">
        <div class="inner">
            <header class="major">
                <h2 id="title_comercios"></h2>
            </header>
            <div class="row" id='comercios-row'></div>
        </div>
    </section>
</div>
<div id="modal"></div>
<div id="modal-inicio" class="iziModal">
    <a href="mas_beneficios/front/inicio/index/promo/2">
        <img src="img/mas_beneficios/popups/dia_del_ninio.jpg" style="width:100%;" alt=""/>
    </a>
</div>
<script type="text/javascript">
    var csrfData = '<?php echo $this->security->get_csrf_hash(); ?>';
    var comercioID = 0;
    var tipoBusqueda = 'nombre';
<?php if ($agrupamiento_id === 'promo') : ?>
        var url = "mas_beneficios/front/inicio/get_promociones";
        var url_part = "mas_beneficios/front/inicio/get_promocion/";
<?php else: ?>
        var url = "mas_beneficios/front/inicio/get_comercios";
        var url_part = "mas_beneficios/front/inicio/get_comercio/";
<?php endif; ?>
    $(document).ready(function() {
<?php if ($agrupamiento_id !== 'promo') : ?>
            $("#modal-inicio").iziModal({
                title: ' ',
                headerColor: '#676567',
                radius: 0,
                transitionIn: 'bounceInDown',
                transitionOut: 'bounceOutUp'
            });
<?php endif; ?>
        $(document).on('click', '.beneficio', function(event) {
            event.preventDefault();
            comercioID = event.currentTarget.id.substr(event.currentTarget.id.indexOf('-') + 1);
            $('#modal').iziModal('open');
        });
        $("#modal").iziModal({
            title: ' ',
            headerColor: '#676567',
            radius: 0,
            transitionIn: 'bounceInDown',
            transitionOut: 'bounceOutUp',
            onOpening: function(modal) {
                modal.startLoading();
                $.post(url_part, {csrf_mlc2: csrfData, comercio_id: comercioID}, function(data) {
                    $("#modal .iziModal-content").html(data);
                    modal.stopLoading();
                });
            }
        });
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
<?php if (!empty($categoria_id)) : ?>
            $('#search-description-categoria').click();
            $('#main-input').val($('#' + <?php echo $categoria_id; ?>).text());
            get_comercios(<?php echo $categoria_id; ?>);
<?php else: ?>
            $('#search-description-nombe').click();
            $('#main-input').val('');
            get_comercios();
<?php endif; ?>

        var $tiles = $('.tiles > article');
        $tiles.each(function() {
            var $this = $(this);
            $link = $this.find('.link');
            $id = $link.attr('id');
            if ($link.length > 0) {
                $x = $link.clone().text('').addClass('primary').appendTo($this);
                $link = $link.add($x);
                $link.click(tiles_callback($id));
            }
        });
        $('#main-submit').click(buscar_callback());
        $('#main-clear').click(tiles_callback('0'));
    });
<?php if ($agrupamiento_id !== 'promo' && FALSE) : ?>
        $(window).on("load", function() {
            setTimeout(function() {
                $('#modal-inicio').iziModal('open');
            }, 1000);
        });
<?php endif; ?>
    function tiles_callback(id) {
        return function(event) {
            event.stopPropagation();
            event.preventDefault();
            get_comercios(id);
            if (id !== '0') {
                $('#search-description-categoria').click();
                $('#main-input').val($('#' + id).text());
            } else {
                $('#search-description-nombe').click();
                $('#main-input').val('');
            }
        };
    }

    function buscar_callback() {
        return function(event) {
            event.stopPropagation();
            event.preventDefault();
            var texto = $('#main-input').val();
            get_comercios(undefined, texto);
        };
    }

    function get_comercios(categoria_id, texto) {
        if ((categoria_id !== undefined && categoria_id !== '0') || texto !== undefined) {
            $('#bg-loader-ajax').show();
            document.getElementById("comercios-row").style.opacity = "0";
        }
        var data;
        if (categoria_id !== undefined && categoria_id !== '0') {
            data = {csrf_mlc2: csrfData, categoria_id: categoria_id};
        } else if (texto !== undefined) {
            data = {csrf_mlc2: csrfData, tipo: tipoBusqueda, texto: texto};
        } else {
            var elmnt = document.getElementById("title_comercios");
            elmnt.innerText = '';
            $('#comercios-row').html('');
            return;
        }
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: data
        }).done(function(data) {
            var html = '';
            if (data['error'] === undefined) {
                $.each(data['comercios'], function(key, value) {
                    html += '<div class="col-3 col-4-medium col-6-small col-12-xsmall" style="position: relative;">';
                    html += '<div class="beneficio style0" id="beneficio-' + value.id + '" style="padding: 0;">';
                    html += '<span class="label label-default rank-label">' + value.categoria + '</span>';
                    html += '<img alt="' + value.comercio + '" style="width: 100%" src="' + value.imagen_url + '">';
                    html += '<div class="beneficio-txt">';
                    html += '<h3>' + value.comercio + '</h3>';
                    html += '<p>' + value.comentarios + '</p>';
                    html += '</div>';
                    html += '</div>';
                    html += '<div class="row row-redes">';
                    html += '<div class="col-3" style="text-align: center;">';
                    if (value.twitter !== null && value.twitter !== '') {
                        html += '<a href="https://' + value.twitter + '" target="_blank" title="Twitter" class="icon alt fa fa-twitter"></a>';
                    } else {
                        html += '<div class="icon alt disabled fa fa-twitter"></div>';
                    }
                    html += '</div>';
                    html += '<div class="col-3" style="text-align: center;">';
                    if (value.facebook !== null && value.facebook !== '') {
                        html += '<a href="https://' + value.facebook + '" target="_blank" title="Facebook" class="icon alt fa fa-facebook"></a>';
                    } else {
                        html += '<div class="icon alt disabled fa fa-facebook"></div>';
                    }
                    html += '</div>';
                    html += '<div class="col-3" style="text-align: center;">';
                    if (value.instagram !== null && value.instagram !== '') {
                        html += '<a href="https://' + value.instagram + '" target="_blank" title="Instagram" class="icon alt fa fa-instagram"></a>';
                    } else {
                        html += '<div class="icon alt disabled fa fa-instagram"></div>';
                    }
                    html += '</div>';
                    html += '<div class="col-3" style="text-align: center;">';
                    if (value.web !== null && value.web !== '') {
                        html += '<a href="https://' + value.web + '" target="_blank" title="Web" class="icon alt fa fa-globe"></a>';
                    } else {
                        html += '<div class="icon alt disabled fa fa-globe"></div>';
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });
            }
            if (categoria_id !== undefined || texto !== undefined) {
                window.setTimeout(function() {
                    $('#comercios-row').html(html);
                    document.getElementById("comercios-row").style.opacity = "1";
                    $('#bg-loader-ajax').hide();
                }, 1000);
                var elmnt = document.getElementById("title_comercios");
                if (categoria_id !== undefined && categoria_id !== '0') {
                    elmnt.innerText = 'Comercios en categoría "' + $('#' + categoria_id).text() + '"';
                } else if (texto !== undefined) {
                    elmnt.innerText = 'Comercios que contienen "' + texto + '"';
                }
                elmnt.scrollIntoView(true);
            } else {
                $('#comercios-row').html(html);
            }
        });
    }

    $('.main-btn').click(function() {
        $('.search-description').slideToggle(100);
    });

    $('.search-description li').click(function() {
        var target = $(this).html();
        var toRemove = '';
        var newTarget = target.replace(toRemove, '');
        newTarget = newTarget.replace(/\s/g, '');
        $(".search-large").html(newTarget);
        $('.search-description').hide();
        newTarget = newTarget.toLowerCase();
        tipoBusqueda = newTarget;
    });

    $('#main-submit-mobile').click(function() {
        $('#main-submit').trigger('click');
    });
</script>