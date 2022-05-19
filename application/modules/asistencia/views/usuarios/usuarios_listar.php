<!--
   /*
        * Vista Listado de Usuarios
        * Autor: Leandro
        * Creado: 23/09/2016
        * Modificado: 04/08/2020 (Leandro)
        */
-->
<script>
    var users_table;
    function complete_users_table() {
        $('#users_table tfoot th').each(function (i) {
            var clase = '';
            var tdclass = $('#users_table thead th').eq(i)[0]['attributes']['class']['value'];
            if (tdclass.indexOf("dt-body-right") >= 0) {
                clase = ' text-right';
            }
            var title = $('#users_table thead th').eq(i).text();
            var indice = $('#users_table thead th').eq(i).index();
            if (title !== '') {
                if (indice === 4) {
                    $(this).html(<?php echo json_encode(form_dropdown(array('class' => 'input-xs form-control', 'style' => 'width:100%;'), $array_estados)); ?>);
                    $(this).find('select').val(users_table.column(i).search());
                } else {
                    $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + users_table.column(i).search() + '"/>');
                }
            }
        });
        $('#users_table tfoot th').eq(5).html('');
        $('#users_table tfoot th').eq(6).html('<button class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'users_table\');"><i class="fa fa-eraser"></i></button>');
        users_table.columns().every(function () {
            var column = this;
            $('input,select', users_table.table().footer().children[0].children[this[0][0]]).on('change keypress', function (e) {
                if (e.type === 'change' || e.which === 13) {
                    if (column.search() !== this.value) {
                        column.search(this.value).draw();
                    }
                    e.preventDefault();
                }
            });
        });
        var r = $('#users_table tfoot tr');
        r.find('th').each(function () {
            $(this).css('padding', 5);
        });
        $('#users_table thead').append(r);
    }
    function activar_usuario(usuario_id) {
        Swal.fire({
            title: 'Confirmar',
            text: "Se activará el usuario seleccionado",
            type: 'info',
            showCloseButton: true,
            showCancelButton: true,
            focusCancel: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-primary',
            cancelButtonClass: 'btn btn-default',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                window.location.href = CI.base_url + 'asistencia/usuarios/activar/' + usuario_id;
            }
        })
    }
    function desactivar_usuario(usuario_id) {
        Swal.fire({
            title: 'Confirmar',
            text: "Se desactivará el usuario seleccionado",
            type: 'info',
            showCloseButton: true,
            showCancelButton: true,
            focusCancel: true,
            buttonsStyling: false,
            confirmButtonClass: 'btn btn-primary',
            cancelButtonClass: 'btn btn-default',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                window.location.href = CI.base_url + 'asistencia/usuarios/desactivar/' + usuario_id;
            }
        })
    }
</script>
<?php if (!empty($error)) : ?>
    <div class="alert alert-danger alert-dismissible fade in alert-fixed" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>ERROR!</strong><?php echo $error; ?>
    </div>
<?php endif; ?>
<?php if (!empty($message)) : ?>
    <div class="alert alert-success alert-dismissible fade in alert-fixed" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>OK!</strong><?php echo $message; ?>
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Usuarios'; ?></h2>
                <button class="btn btn-primary btn-sm pull-right" onclick="window.open('asistencia/usuarios/reporte')"><i class="fa fa-file-excel-o"></i> REPORTE</button>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php echo $js_table; ?>
                <?php echo $html_table; ?>
            </div>
        </div>
    </div>
</div>