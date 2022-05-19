<?php setlocale(LC_TIME, "esp"); ?>
<div style="margin: 0 10px 0 10px;">
    <div style="text-align: center;">
        <img src="img/generales/logo_lujan_001.png" alt="Luján de Cuyo" />
        <p style="font-size: 16px; font-weight: bold;">
            MUNICIPALIDAD DE LUJÁN DE CUYO<br />
            DIRECCIÓN DE DEFUNCIONES
        </p>
    </div>
    <div class="fichada_data">
        <p style="text-align: right; font-weight:bold;">
            Luján de Cuyo, <?php echo strftime('%d de %B de %Y', date_timestamp_get(new DateTime($operacion->fecha_tramite))); ?>
        </p>
        <br />
        <?php if ($operacion->tipo_operacion == '3' || $operacion->tipo_operacion == '4'): ?>
            <div style="text-align: center; border: solid 1px #000;">
                <p style="font-size: 14px; font-weight: bold;">
                    ATENCIÓN: El retiro de lápidas y trabajos de ornato de sepulturas y/o nichos deben ser retirados por los propietarios o por constructores autorizados para trabajar dentro del Cementerio, con 24 horas de anticipación a la realización de la reducción y/o traslado.
                </p>
            </div>
        <?php endif; ?>
        <p style="font-weight: bold;">
            Señor:<br />
            Intendente Municipal<br />
            S________//________D<br />
        </p>
        <p style="text-align:justify; text-indent:6em;">
            El que suscribe <?php echo $operacion->s_nombre; ?> D.N.I. <?php echo $operacion->s_dni; ?>.
            Con domicilio en calle <?php echo $operacion->s_domicilio; ?><?php if (!empty($operacion->s_domicilio_alt)) echo ' (Domicilio alternativo: ' . $operacion->s_domicilio_alt . ')'; ?><?php if (!empty($operacion->s_telefono)) echo '. Teléfono ' . $operacion->s_telefono; ?><?php if (!empty($operacion->s_telefono_alt)) echo ' (Teléfono alternativo: ' . $operacion->s_telefono_alt . ')'; ?>. Solicita:<br />
        </p>
        <?php
        if (!empty($detalle_operacion))
            switch ($operacion->tipo_operacion)
            {
                case '1': //CONCESION
                    $tipo_operacion = "concesiones";
                    switch ($detalle_operacion->u_tipo)
                    {
                        case 'Nicho':
                            echo "<p>";
                            if ($detalle_operacion->tipo_concesion === 'Alquiler')
                            {
                                echo "<span style='font-weight: bold; text-decoration: underline;'>" . (($detalle_operacion->ingreso === '1') ? 'ALQUILER' : 'RENOVACIÓN') . " NICHO</span>";
                                echo "<br />";
                                echo "<b>POR</b> " . (!empty($detalle_operacion->tiempo_concesion) ? $detalle_operacion->tiempo_concesion : "...........................................") . "<br />";
                            }
                            else
                            {
                                echo "<span style='font-weight: bold; text-decoration: underline;'>INGRESO A NICHO PERPETUO</span>";
                                echo "<br />";
                                echo "<b>DURACIÓN</b> Perpetua<br />";
                            }
                            echo "<b>NICHO</b> " . (!empty($detalle_operacion->u_nicho) ? $detalle_operacion->u_nicho : ".........") . " <b>F</b> " . (!empty($detalle_operacion->u_fila) ? $detalle_operacion->u_fila : "............") . " <br />";
                            echo "<b>SECTOR</b> " . (!empty($detalle_operacion->u_sector) ? $detalle_operacion->u_sector : "..................................") . " <br />";
                            echo "<b>DESDE EL:</b> " . (!empty($detalle_operacion->inicio) ? strftime('%d/%m/%Y', date_timestamp_get(new DateTime($detalle_operacion->inicio))) : "..............................") . " <br />";
                            if ($detalle_operacion->tipo_concesion === 'Alquiler')
                                echo "<b>HASTA EL:</b> " . (!empty($detalle_operacion->fin) ? strftime('%d/%m/%Y', date_timestamp_get(new DateTime($detalle_operacion->fin))) : "..............................") . " <br />";
                            echo "</p>";
                            break;
                        case 'Tierra':
                            echo "<p>";
                            if ($detalle_operacion->tipo_concesion === 'Alquiler')
                            {
                                echo "<span style='font-weight: bold; text-decoration: underline;'>" . (($detalle_operacion->ingreso === 1) ? 'ALQUILER' : 'RENOVACIÓN') . " TIERRA</span>";
                                echo "<br />";
                                echo "<b>POR</b> " . (!empty($detalle_operacion->tiempo_concesion) ? $detalle_operacion->tiempo_concesion : "...........................................") . "<br />";
                            }
                            else
                            {
                                echo "<span style='font-weight: bold; text-decoration: underline;'>INGRESO A TIERRA PERPETUO</span>";
                                echo "<br />";
                                echo "<b>DURACIÓN</b> Perpetua<br />";
                            }
                            echo "<b>PARCELA</b> " . (!empty($detalle_operacion->u_nicho) ? $detalle_operacion->u_nicho : ".........") . " <b>F</b> " . (!empty($detalle_operacion->u_fila) ? $detalle_operacion->u_fila : "............") . " <br />";
                            echo "<b>CUADRO</b> " . (!empty($detalle_operacion->u_cuadro) ? $detalle_operacion->u_cuadro : "..................................") . " <br />";
                            echo "<b>SECTOR</b> " . (!empty($detalle_operacion->u_sector) ? $detalle_operacion->u_sector : "..................................") . " <br />";
                            echo "<b>DESDE EL:</b> " . (!empty($detalle_operacion->inicio) ? strftime('%d/%m/%Y', date_timestamp_get(new DateTime($detalle_operacion->inicio))) : "..............................") . " <br />";
                            if ($detalle_operacion->tipo_concesion === 'Alquiler')
                                echo "<b>HASTA EL:</b> " . (!empty($detalle_operacion->fin) ? strftime('%d/%m/%Y', date_timestamp_get(new DateTime($detalle_operacion->fin))) : "..............................") . " <br />";
                            echo "</p>";
                            break;
                        case 'Pileta':
                            echo "<p>";
                            echo "<span style='font-weight: bold; text-decoration: underline;'>INGRESO A PILETA</span>";
                            echo "<br />";
                            echo "<b>PARCELA</b> " . (!empty($detalle_operacion->u_nicho) ? $detalle_operacion->u_nicho : ".........") . " <b>F</b> " . (!empty($detalle_operacion->u_fila) ? $detalle_operacion->u_fila : "............") . " <br />";
                            echo "<b>CUADRO</b> " . (!empty($detalle_operacion->u_cuadro) ? $detalle_operacion->u_cuadro : "................") . " <br />";
                            echo "<b>SECTOR</b> " . (!empty($detalle_operacion->u_sector) ? $detalle_operacion->u_sector : "..................................") . " <br />";
                            echo "<b>DENOMINACIÓN</b> " . (!empty($detalle_operacion->u_denominacion) ? $detalle_operacion->u_denominacion : "..................................") . " <br />";
                            echo "<b>DESDE EL:</b> " . (!empty($detalle_operacion->inicio) ? strftime('%d/%m/%Y', date_timestamp_get(new DateTime($detalle_operacion->inicio))) : "..............................") . " <br />";
                            if ($detalle_operacion->tipo_concesion === 'Alquiler')
                                echo "<b>HASTA EL:</b> " . (!empty($detalle_operacion->fin) ? strftime('%d/%m/%Y', date_timestamp_get(new DateTime($detalle_operacion->fin))) : "..............................") . " <br />";
                            else
                                echo "<b>DURACIÓN</b> Perpetua<br />";
                            echo "</p>";
                            break;
                        case 'Mausoleo':
                            echo "<p>";
                            echo "<span style='font-weight: bold; text-decoration: underline;'>INGRESO A MAUSOLEO</span>";
                            echo "<br />";
                            echo "<b>PARCELA</b> " . (!empty($detalle_operacion->u_nicho) ? $detalle_operacion->u_nicho : ".........") . " <b>F</b> " . (!empty($detalle_operacion->u_fila) ? $detalle_operacion->u_fila : "............") . " <br />";
                            echo "<b>CUADRO</b> " . (!empty($detalle_operacion->u_cuadro) ? $detalle_operacion->u_cuadro : "................") . " <br />";
                            echo "<b>SECTOR</b> " . (!empty($detalle_operacion->u_sector) ? $detalle_operacion->u_sector : "..................................") . " <br />";
                            echo "<b>DENOMINACIÓN</b> " . (!empty($detalle_operacion->u_denominacion) ? $detalle_operacion->u_denominacion : "..................................") . " <br />";
                            echo "<b>DESDE EL:</b> " . (!empty($detalle_operacion->inicio) ? strftime('%d/%m/%Y', date_timestamp_get(new DateTime($detalle_operacion->inicio))) : "..............................") . " <br />";
                            if ($detalle_operacion->tipo_concesion === 'Alquiler')
                                echo "<b>HASTA EL:</b> " . (!empty($detalle_operacion->fin) ? strftime('%d/%m/%Y', date_timestamp_get(new DateTime($detalle_operacion->fin))) : "..............................") . " <br />";
                            else
                                echo "<b>DURACIÓN</b> Perpetua<br />";
                            echo "</p>";
                            break;
                        case 'Nicho Urna':
                            echo "<p>";
                            echo "<span style='font-weight: bold; text-decoration: underline;'>" . (($detalle_operacion->ingreso === 1) ? 'ALQUILER' : 'RENOVACIÓN') . " NICHO URNA</span>";
                            echo "<br />";
                            if ($detalle_operacion->tipo_concesion === 'Alquiler')
                                echo "<b>POR</b> " . (!empty($detalle_operacion->tiempo_concesion) ? $detalle_operacion->tiempo_concesion : ".......................................") . "<br />";
                            else
                                echo "<b>DURACIÓN</b> Perpetua<br />";
                            echo "<b>NICHO</b> " . (!empty($detalle_operacion->u_nicho) ? $detalle_operacion->u_nicho : "...............") . " <b>F</b> " . (!empty($detalle_operacion->u_fila) ? $detalle_operacion->u_fila : "...............") . " <br />";
                            echo "<b>ING. 2° CAD</b> ......................... <br />";
                            echo "<b>DESDE EL:</b> " . (!empty($detalle_operacion->inicio) ? strftime('%d/%m/%Y', date_timestamp_get(new DateTime($detalle_operacion->inicio))) : "..............................") . " <br />";
                            if ($detalle_operacion->tipo_concesion === 'Alquiler')
                                echo "<b>HASTA EL:</b> " . (!empty($detalle_operacion->fin) ? strftime('%d/%m/%Y', date_timestamp_get(new DateTime($detalle_operacion->fin))) : "..............................") . " <br />";
                            echo "</p>";
                            break;
                    }
                    break;
                case '2': //ORNATO
                    echo "<p>";
                    echo "<span style='font-weight: bold; text-decoration: underline;'>TRABAJO ORNATO</span>";
                    echo "<br />";
                    switch ($detalle_operacion->u_tipo)
                    {
                        case 'Nicho':
                            echo "<b>NICHO</b> " . (!empty($detalle_operacion->u_nicho) ? $detalle_operacion->u_nicho : ".........") . " <b>F</b> " . (!empty($detalle_operacion->u_fila) ? $detalle_operacion->u_fila : "............") . " <br />";
                            echo "<b>SECTOR</b> " . (!empty($detalle_operacion->u_sector) ? $detalle_operacion->u_sector : "..................................") . " <br />";
                            break;
                        case 'Tierra':
                            echo "<b>PARCELA</b> " . (!empty($detalle_operacion->u_nicho) ? $detalle_operacion->u_nicho : ".........") . " <b>F</b> " . (!empty($detalle_operacion->u_fila) ? $detalle_operacion->u_fila : "............") . " <br />";
                            echo "<b>CUADRO</b> " . (!empty($detalle_operacion->u_cuadro) ? $detalle_operacion->u_cuadro : "..................................") . " <br />";
                            echo "<b>SECTOR</b> " . (!empty($detalle_operacion->u_sector) ? $detalle_operacion->u_sector : "..................................") . " <br />";
                            break;
                        case 'Pileta':
                            echo "<b>PILETA</b><br />";
                            echo "........................................ <br />";
                            echo "<b>CUADRO N°</b> " . (!empty($detalle_operacion->u_cuadro) ? $detalle_operacion->u_cuadro : "................") . " <br />";
                            echo "........................................ <br />";
                            break;
                        case 'Mausoleo':
                            echo "<b>MAUSOLEO</b><br />";
                            echo "........................................ <br />";
                            echo "<b>CUADRO N°</b> " . (!empty($detalle_operacion->u_cuadro) ? $detalle_operacion->u_cuadro : "................") . " <br />";
                            echo "........................................ <br />";
                            break;
                        case 'Nicho Urna':
                            echo "<b>NICHO URNA</b> " . (!empty($detalle_operacion->u_nicho) ? $detalle_operacion->u_nicho : "...............") . " <b>F</b> " . (!empty($detalle_operacion->u_fila) ? $detalle_operacion->u_fila : "...............") . " <br />";
                            break;
                    }
                    echo "<b>CONST.</b> " . $detalle_operacion->c_nombre . " <br />";
                    echo "<b>TIPO ORNATO</b> " . $detalle_operacion->tipo_ornato . " <br />";
                    echo "</p>";
                    break;
                case '3': //REDUCCION
                    echo "<p>";
                    echo "<span style='font-weight: bold; text-decoration: underline;'>REDUCCIÓN</span>";
                    echo "<br />";
                    switch ($detalle_operacion->u_tipo)
                    {
                        case 'Nicho':
                            echo "<b>NICHO</b> " . (!empty($detalle_operacion->u_nicho) ? $detalle_operacion->u_nicho : ".........") . " <b>F</b> " . (!empty($detalle_operacion->u_fila) ? $detalle_operacion->u_fila : "............") . " <br />";
                            echo "<b>SECTOR</b> " . (!empty($detalle_operacion->u_sector) ? $detalle_operacion->u_sector : "..................................") . " <br />";
                            break;
                        case 'Tierra':
                            echo "<b>PARCELA</b> " . (!empty($detalle_operacion->u_nicho) ? $detalle_operacion->u_nicho : ".........") . " <b>F</b> " . (!empty($detalle_operacion->u_fila) ? $detalle_operacion->u_fila : "............") . " <br />";
                            echo "<b>CUADRO</b> " . (!empty($detalle_operacion->u_cuadro) ? $detalle_operacion->u_cuadro : "..................................") . " <br />";
                            echo "<b>SECTOR</b> " . (!empty($detalle_operacion->u_sector) ? $detalle_operacion->u_sector : "..................................") . " <br />";
                            break;
                        case 'Pileta':
                            echo "<b>PILETA</b><br />";
                            echo "........................................ <br />";
                            echo "<b>CUADRO N°</b> " . (!empty($detalle_operacion->u_cuadro) ? $detalle_operacion->u_cuadro : "................") . " <br />";
                            echo "........................................ <br />";
                            break;
                        case 'Mausoleo':
                            echo "<b>MAUSOLEO</b><br />";
                            echo "........................................ <br />";
                            echo "<b>CUADRO N°</b> " . (!empty($detalle_operacion->u_cuadro) ? $detalle_operacion->u_cuadro : "................") . " <br />";
                            echo "........................................ <br />";
                            break;
                        case 'Nicho Urna':
                            echo "<b>NICHO URNA</b> " . (!empty($detalle_operacion->u_nicho) ? $detalle_operacion->u_nicho : "...............") . " <b>F</b> " . (!empty($detalle_operacion->u_fila) ? $detalle_operacion->u_fila : "...............") . " <br />";
                    }
                    echo "<b>FECHA REALIZACIÓN</b> " . (!empty($detalle_operacion->fecha_realizacion) ? strftime('%d/%m/%Y %H:%M', date_timestamp_get(new DateTime($detalle_operacion->fecha_realizacion))) : "..............................") . " <br />";
                    echo "</p>";
                    break;
                case '4': //TRASLADOS
                    switch ($detalle_operacion->tipo_o)
                    {
                        case 'Nicho':
                            $descripcionO = "S: $detalle_operacion->sector_o - F: $detalle_operacion->fila_o - N: $detalle_operacion->nicho_o";
                            break;
                        case 'Tierra':
                            $descripcionO = "S: $detalle_operacion->sector_o - C: $detalle_operacion->cuadro_o - F: $detalle_operacion->fila_o - P: $detalle_operacion->nicho_o";
                            break;
                        case 'Mausoleo':
                            $descripcionO = "C: $detalle_operacion->cuadro_o - D: $detalle_operacion->denominacion_o";
                            break;
                        case 'Pileta':
                            $descripcionO = "C: $detalle_operacion->cuadro_o - D: $detalle_operacion->denominacion_o";
                            break;
                        case 'Nicho Urna':
                            $descripcionO = "S: $detalle_operacion->sector_o - F: $detalle_operacion->fila_o - N: $detalle_operacion->nicho_o";
                            break;
                    }
                    switch ($detalle_operacion->tipo_d)
                    {
                        case 'Nicho':
                            $descripcionD = "S: $detalle_operacion->sector_d - F: $detalle_operacion->fila_d - N: $detalle_operacion->nicho_d";
                            break;
                        case 'Tierra':
                            $descripcionD = "S: $detalle_operacion->sector_d - C: $detalle_operacion->cuadro_d - F: $detalle_operacion->fila_d - P: $detalle_operacion->nicho_d";
                            break;
                        case 'Mausoleo':
                            $descripcionD = "C: $detalle_operacion->cuadro_d - D: $detalle_operacion->denominacion_d";
                            break;
                        case 'Pileta':
                            $descripcionD = "C: $detalle_operacion->cuadro_d - D: $detalle_operacion->denominacion_d";
                            break;
                        case 'Nicho Urna':
                            $descripcionD = "S: $detalle_operacion->sector_d - F: $detalle_operacion->fila_d - N: $detalle_operacion->nicho_d";
                            break;
                    }
                    echo "<p>";
                    echo "<span style='font-weight: bold; text-decoration: underline;'>TRASLADO</span>";
                    echo "<br />";
                    echo "<b>TIPO TRASLADO</b> " . $detalle_operacion->tipo_traslado . " <br />";
                    echo "<b>UBICACIÓN ORIGEN</b> " . $detalle_operacion->cementerio_o . " " . $descripcionO . " <br />";
                    echo "<b>UBICACIÓN DESTINO</b> " . $detalle_operacion->cementerio_d . " " . $descripcionD . " <br />";
                    echo "<b>FECHA REALIZACIÓN</b> " . (!empty($detalle_operacion->fecha_realizacion) ? strftime('%d/%m/%Y %H:%M', date_timestamp_get(new DateTime($detalle_operacion->fecha_realizacion))) : "..............................") . " <br />";
                    echo "<b>COCHERÍA</b> " . (!empty($detalle_operacion->cocheria) ? $detalle_operacion->cocheria : "....................................................") . " <br />";
                    echo "</p>";
                    break;
            }
        ?>
        <p style="text-align:justify; text-indent:6em;">
            Para los restos de <?php echo $operacion->d_nombre . ' ' . $operacion->d_apellido; ?><?php if (!empty($operacion->d_dni)) echo ', D.N.I.: ' . $operacion->d_dni; ?><?php if (!empty($operacion->d_nacimiento)) echo ', nacido el día ' . strftime('%d de %B de %Y', date_timestamp_get(new DateTime($operacion->d_nacimiento))); ?>. Fallecido el día <?php echo strftime('%d de %B de %Y', date_timestamp_get(new DateTime($operacion->d_defuncion))); ?> a la edad de <?php echo $operacion->d_edad; ?>. <?php echo!empty($operacion->d_causa) ? 'Causa de muerte: ' . $operacion->d_causa . '.' : ''; ?><br /><br />
            <?php if ($operacion->tipo_operacion === '1' && $detalle_operacion->ingreso === '1') : ?>
                <?php if (!empty($operacion->d_observaciones)) echo "<b>OBSERVACIONES:</b> " . $operacion->d_observaciones . " <br />"; ?>
                <?php if (!empty($detalle_operacion->hora_ingreso)) echo "<b>INGRESO:</b> " . strftime('%d/%m/%Y %H:%M', date_timestamp_get(new DateTime($detalle_operacion->hora_ingreso))) . " <br />"; ?>
                <?php if (!empty($operacion->cocheria_difunto)) echo "<b>COCHERÍA:</b> " . $operacion->cocheria_difunto . " <br />"; ?>
            <?php endif; ?>
            <?php if ($operacion->tipo_operacion === 3) : ?>
                <?php if (!empty($detalle_operacion->fecha_realizacion)) echo "<b>REALIZACIÓN:</b> " . strftime('%d/%m/%Y %H:%M', date_timestamp_get(new DateTime($detalle_operacion->fecha_realizacion))) . " <br />"; ?>
            <?php endif; ?>
        </p>
        <p>
            <span style="font-weight: bold; text-decoration: underline;">
                FORMA DE PAGO
            </span>
            <br />
            a) Al contado la suma de pesos ................................................................................. ($ ........................................ ) <br />
            b) En el acto la suma de pesos ................................................................................... ($ ........................................ ) <br />
            y el saldo en (..........) cuotas iguales de pesos ........................................................... ($ ........................................ ) <br />
            cada una con mas el 1,6% de interés mensual sobre el saldo.
        </p>
        <br />
        <br />
        <br />
        <p style="text-align: right;">
            Firma y Aclaración
        </p>
        <p>
            FICHA N° <?php echo $operacion->d_ficha; ?>
        </p>
    </div>
    <pagebreak />
    <p style="text-align: justify;">
        El aforo que antecede concuerda con lo determinado en la ORDENANZA GENERAL TRIBUTARIA VIGENTE.
    </p>
    <br />
    <br />
    <br />
    <p style="text-align: right;">
        <?php echo!empty($operacion->agente) ? $operacion->agente : "Firma y Sello"; ?>
    </p>
    <br />
    <br />
    <br />
    <table style="width:100%;">
        <tr>
            <td>PAGO</td>
            <td>.........................</td>
            <td>CUOTA N°</td>
            <td>.........................</td>
            <td>CUOTA N°</td>
            <td>.........................</td>
        </tr>
        <tr>
            <td>BOLETA N°</td>
            <td>.........................</td>
            <td>BOLETA N°</td>
            <td>.........................</td>
            <td>BOLETA N°</td>
            <td>.........................</td>
        </tr>
        <tr>
            <td>IMPORTE $</td>
            <td>.........................</td>
            <td>IMPORTE $</td>
            <td>.........................</td>
            <td>IMPORTE $</td>
            <td>.........................</td>
        </tr>
        <tr>
            <td>SEPELIO $</td>
            <td>.........................</td>
            <td>INTERESES $</td>
            <td>.........................</td>
            <td>INTERESES $</td>
            <td>.........................</td>
        </tr>
        <tr>
            <td>INTERESES $</td>
            <td>.........................</td>
            <td>FECHA</td>
            <td>.........................</td>
            <td>FECHA</td>
            <td>.........................</td>
        </tr>
        <tr>
            <td>SELLADO $</td>
            <td>.........................</td>
            <td>FIRMA</td>
            <td>.........................</td>
            <td>FIRMA</td>
            <td>.........................</td>
        </tr>
        <tr>
            <td>FECHA</td>
            <td>.........................</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>FIRMA</td>
            <td>.........................</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <br />
    <br />
    <br />
    <table style="width:100%;">
        <tr>
            <td>CUOTA N°</td>
            <td>.........................</td>
            <td>CUOTA N°</td>
            <td>.........................</td>
            <td>CUOTA N°</td>
            <td>.........................</td>
        </tr>
        <tr>
            <td>BOLETA N°</td>
            <td>.........................</td>
            <td>BOLETA N°</td>
            <td>.........................</td>
            <td>BOLETA N°</td>
            <td>.........................</td>
        </tr>
        <tr>
            <td>IMPORTE $</td>
            <td>.........................</td>
            <td>IMPORTE $</td>
            <td>.........................</td>
            <td>IMPORTE $</td>
            <td>.........................</td>
        </tr>
        <tr>
            <td>INTERESES $</td>
            <td>.........................</td>
            <td>INTERESES $</td>
            <td>.........................</td>
            <td>INTERESES $</td>
            <td>.........................</td>
        </tr>
        <tr>
            <td>FECHA</td>
            <td>.........................</td>
            <td>FECHA</td>
            <td>.........................</td>
            <td>FECHA</td>
            <td>.........................</td>
        </tr>
        <tr>
            <td>FIRMA</td>
            <td>.........................</td>
            <td>FIRMA</td>
            <td>.........................</td>
            <td>FIRMA</td>
            <td>.........................</td>
        </tr>
    </table>
    <br />
    <br />
    <br />
    <p style="text-align:right; font-weight:bold;">
        Luján de Cuyo, <?php echo strftime('%d de %B de %Y', date_timestamp_get(new DateTime($operacion->fecha_tramite))); ?>
    </p>
    <br />
    <p style="text-align: justify;">
        El recurrente hizo efectiva la suma de pesos .......................................................................... ($ .............................)
        según boleta de Ingresos N° ......................................................... de fecha ..................................................................
    </p>
    <br />
    <br />
    <br />
    <p style="text-align: right;">
        <?php echo!empty($operacion->agente) ? $operacion->agente : "Firma y Sello"; ?>
    </p>
</div>