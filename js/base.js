$(document).ready(function () {
    $('form').preventDoubleSubmission();

    $('.close').click(function (event) {
        $('.alert').alert('close');
    });

    $('form').find('input[type=text],textarea,select').filter(':enabled:first:not(.no-autofocus):not(button)').focus();

    aplicar_formatos();
});

function aplicar_formatos() {
    // Aplica la funcion datetimepicker a todos los imputs con clase dateFormat
    $('.dateFormat').each(function (index, element) {
        $(element).datetimepicker({
            locale: 'es',
            format: 'L',
            useCurrent: false,
            showClear: true,
            showTodayButton: true,
            showClose: true
        });
        $(element).attr("autocomplete", "off");
    });

    // Aplica la funcion datetimepicker a todos los imputs con clase dateTimeFormat
    $('.dateTimeFormat').each(function (index, element) {
        $(element).datetimepicker({
            locale: 'es',
            useCurrent: false,
            showClear: true,
            showTodayButton: true,
            showClose: true
        });
        $(element).attr("autocomplete", "off");
    });

    $('.precioFormat,.numberFormat').each(function (index, element) {
        $(element).inputmask('decimal', {
            radixPoint: ',',
            unmaskAsNumber: true,
            digits: 2,
            autoUnmask: true,
            digitsOptional: false,
            placeholder: '',
            removeMaskOnSubmit: true,
            rightAlign: false,
            positionCaretOnClick: 'select',
            onBeforeMask: function (value, opts) {
                processedValue = parseFloat(value).toFixed(2).replace(".", ",");
                return processedValue;
            }
        });
    });

    $('.telefonoFormat').each(function (index, element) {
        $(element).inputmask({
            mask: '999 999 9999',
            unmaskAsNumber: true,
            autoUnmask: true,
            removeMaskOnSubmit: true,
            rightAlign: false
        });
    });
}

$(document).on('focus', 'input[readonly]', function () {
    this.blur();
});

$('form input:not(textarea):not([type=submit]):not(.submit-enter)').on('keypress', function (e) {
    /* ENTER PRESSED*/
    if (e.keyCode == 13) {
        /* FOCUS ELEMENT */
        var inputs = $(this).parents("form").eq(0).find(":input:enabled:visible");
        var idx = inputs.index(this);

        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
//			inputs[idx + 1].select();
        }
        e.preventDefault();
        return false;
    }
});

// jQuery plugin to prevent double submission of forms
jQuery.fn.preventDoubleSubmission = function () {
    $(this).on('submit', function (e) {
        var $form = $(this);
        if ($form.data('submitted') === true) {
            // Previously submitted - don't submit again
            e.preventDefault();
        } else {
            // Mark it so that the next submit can be ignored
            $form.data('submitted', true);
        }
    });
    // Keep chainability
    return this;
};

function agregar_filtros(id, table, columns) {
    $('#' + id + ' tfoot th').each(function (i) {
        var clase = '';
        var tdclass = $('#' + id + ' thead th').eq(i)[0]['attributes']['class']['value'];
        if (tdclass.indexOf("dt-body-right") >= 0) {
            clase = ' text-right';
        }
        var title = $('#' + id + ' thead th').eq(i).text();
        if (title !== '') {
            $(this).html('<input class="form-control input-xs' + clase + '" style="width: 100%;" type="text" placeholder="' + title + '" value="' + table.column(i).search() + '"/>');
        }
    });
    $('#' + id + ' tfoot th').eq(columns).html('<button type="button" class="btn btn-xs btn-primary" onclick="limpiar_filtro(\'' + id + '\');" title="Limpiar filtros"><i class="fa fa-eraser"></i></button>');
    table.columns().every(function () {
        var column = this;
        $('input,select', table.table().footer().children[0].children[this[0][0]]).on('change keypress', function (e) {
            if (e.type === 'change' || e.which === 13) {
                if (column.search() !== this.value) {
                    column.search(this.value).draw();
                }
                e.preventDefault();
            }
        });
    });
    var r = $('#' + id + ' tfoot tr');
    r.find('th').each(function () {
        $(this).css('padding', '5px 2px');
    });
    $('#' + id + ' thead').append(r);
    $('#search_0').css('text-align', 'center');
}

function limpiar_filtro(id) {
    localStorage.removeItem('DataTables_' + id + '_' + window.location.pathname);
    if (typeof (url_listar) == 'undefined') {
        location.reload();
    } else {
        window.location.replace(url_listar);
    }
}

function validaCuil(cuil) {
    if (typeof (cuil) == 'undefined') {
        return true;
    }
    cuil = cuil.toString().replace(/[-_]/g, "");
    if (cuil == '') {
        return true;
    }
    if (cuil.length != 11) {
        return false;
    } else {
        var mult = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        var total = 0;
        for (var i = 0; i < mult.length; i++) {
            total += parseInt(cuil[i]) * mult[i];
        }
        var mod = total % 11;
        var digito = mod == 0 ? 0 : mod == 1 ? 9 : 11 - mod;
    }
    return digito == parseInt(cuil[10]);
}