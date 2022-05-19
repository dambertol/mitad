<!DOCTYPE html>
<html lang="es">
    <body>
        <?php if (!empty($padron)): ?>
            <?php $padron = $padron[0]; ?>
            <table>
                <tr class="medidas">
                    <td style="width:14%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:16%;"></td>
                    <td style="width:4%;"></td>
                    <td style="width:16%;"></td>
                    <td style="width:16%;"></td>
                    <td style="width:24%;"></td>
                </tr>
                <tr>
                    <td colspan="7" style="padding-top:180px;"></td>
                </tr>
                <tr>
                    <td colspan="7" style="font-size:14px; text-align:right; font-weight:bold;">
                        N° PADRÓN: <?php echo (int) $padron->trib_Cuenta; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="font-size:14px;">
                        <b>
                            <?php echo "$padron->pers_Apellido, $padron->pers_Nombre"; ?>
                            <?php echo!empty($padron->adj_Apellido) ? "ADJ: $padron->adj_Apellido, $padron->adj_Nombre" : ""; ?>
                        </b>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="font-size:12px; height:84px;">
                        <?php
                        if (empty($padron->zent_Calle))
                        {
                            echo "$padron->pers_Calle $padron->pers_Altura ";
                            if (!empty($padron->pers_Piso) && $padron->pers_Piso != ' ')
                                echo "PISO: $padron->pers_Piso ";
                            echo "";
                            if (!empty($padron->pers_Depto) && $padron->pers_Depto != ' ')
                                echo "DEPTO: $padron->pers_Depto ";
                            if (!empty($padron->pers_Lote) && $padron->pers_Lote != ' ')
                                echo "LOTE:$padron->pers_Lote ";
                            if (!empty($padron->pers_Manzana) && $padron->pers_Manzana != ' ')
                                echo "M:$padron->pers_Manzana ";
                            if (!empty($padron->pers_Fraccion) && $padron->pers_Fraccion != ' ')
                                echo "F:$padron->pers_Fraccion ";
                            if (!empty($padron->pers_EntreCalle1) && $padron->pers_EntreCalle1 != ' ')
                                echo "Entre:$padron->pers_EntreCalle1 ";
                    //        if (!empty($padron->pers_EntreCalle2) && $padron->pers_EntreCalle2 != ' ')
                    //            echo "y:$padron->pers_EntreCalle2 ";    
                            echo "<br />";
                            echo "($padron->pers_CodigoPostal) - $padron->pers_Localidad - $padron->pers_Provincia";
                        }
                        else
                        {
                            echo "$padron->zent_Calle $padron->zent_Altura ";
                            if (!empty($padron->zent_Piso) && $padron->zent_Piso != ' ')
                                echo "PISO: $padron->zent_Piso ";
                            if (!empty($padron->zent_Depto) && $padron->zent_Depto != ' ')
                                echo "DEPTO: $padron->zent_Depto ";
                            if (!empty($padron->zent_Lote) && $padron->zent_Lote != ' ')
                                echo "LOTE:$padron->zent_Lote ";
                            if (!empty($padron->zent_Manzana) && $padron->zent_Manzana != ' ')
                                echo "M:$padron->zent_Manzana ";
                            if (!empty($padron->zent_Fraccion) && $padron->zent_Fraccion != ' ')
                                echo "F:$padron->zent_Fraccion ";
                            if (!empty($padron->zent_EntreCalle1) && $padron->zent_EntreCalle1 != ' ')
                                echo "Entre:$padron->zent_EntreCalle1 ";
                    //        if (!empty($padron->zent_EntreCalle2) && $padron->zent_EntreCalle2 != ' ')
                    //            echo "y:$padron->zent_EntreCalle2 ";    
                            echo "<br />";
                            echo "($padron->zent_CodigoPostal) - $padron->zent_Localidad - $padron->zent_Provincia";
                        }
                        echo "<br />";
                        echo "UBICACIÓN DE LA PROPIEDAD: $padron->fren_Calle $padron->fren_Altura $padron->fren_Piso LOTE:$padron->fren_Lote M:$padron->fren_Manzana - ($padron->fren_CodigoPostal) $padron->fren_Localidad - MENDOZA";
                        echo "<br />";
                        ?>
                    </td>
                </tr>
            </table>
            <table>
                <tr class="medidas">
                    <td style="width:10%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:10%;"></td>
                </tr>
                <tr>
                    <td colspan="10" style="font-size:12px; padding-top:34px; text-align:center;">
                        <?php
                        echo "NOMENCLATURA CATASTRAL: " .
                        str_pad($padron->Loc, 2, "0", STR_PAD_LEFT) . "" .
                        str_pad($padron->Dist, 2, "0", STR_PAD_LEFT) . "" .
                        str_pad($padron->Sec, 2, "0", STR_PAD_LEFT) . "" .
                        str_pad($padron->Manz, 4, "0", STR_PAD_LEFT) . "" .
                        str_pad($padron->Parc, 6, "0", STR_PAD_LEFT) . "" .
                        str_pad($padron->SubPar, 6, "0", STR_PAD_LEFT) . "" .
                        str_pad($padron->SubDiv, 4, "0", STR_PAD_LEFT) . "" .
                        str_pad($padron->UComp, 3, "0", STR_PAD_LEFT);
                        echo "<br />";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:9px; text-align:center; border:1px solid #4e4c4e; background-color:#EBE9E9;">CLASE</td>
                    <td style="font-size:9px; text-align:center; border:1px solid #4e4c4e; background-color:#EBE9E9;">ZONA</td>
                    <td style="font-size:9px; text-align:center; border:1px solid #4e4c4e; background-color:#EBE9E9;">CATEGORÍA</td>
                    <td style="font-size:9px; text-align:center; border:1px solid #4e4c4e; background-color:#EBE9E9;">SUPERFICIE</td>
                    <td style="font-size:9px; text-align:center; border:1px solid #4e4c4e; background-color:#EBE9E9;">CUBIERTA</td>
                    <td style="font-size:9px; text-align:center; border:1px solid #4e4c4e; background-color:#EBE9E9;">FRENTE</td>
                    <td style="font-size:9px; text-align:center; border:1px solid #4e4c4e; background-color:#EBE9E9;">LOCATIVO</td>
                    <td style="font-size:9px; text-align:center; border:1px solid #4e4c4e; background-color:#EBE9E9;">CUBIERTA GIS</td>
                    <td style="font-size:9px; text-align:center; border:1px solid #4e4c4e; background-color:#EBE9E9;">PILETA</td>
                    <td style="font-size:9px; text-align:center; border:1px solid #4e4c4e; background-color:#EBE9E9;">PILETA GIS</td>
                </tr>
                <tr>
                    <td style="font-size:10px; text-align:center; border:1px solid;"><?php echo $padron->ttas_SubTasa; ?></td>
                    <td style="font-size:10px; text-align:center; border:1px solid;"><?php echo $padron->zont_Codigo; ?></td>
                    <td style="font-size:10px; text-align:center; border:1px solid;"><?php echo $padron->tviv_Codigo; ?></td>
                    <td style="font-size:10px; text-align:center; border:1px solid;"><?php echo $padron->sup_total; ?></td>
                    <td style="font-size:10px; text-align:center; border:1px solid;"><?php echo $padron->sup_cubierta; ?></td>
                    <td style="font-size:10px; text-align:center; border:1px solid;"><?php echo $padron->fren_Metros; ?></td>
                    <td style="font-size:10px; text-align:center; border:1px solid;"><?php echo number_format($padron->valu_Valor, 5); ?></td>
                    <td style="font-size:10px; text-align:center; border:1px solid;"><?php echo $padron->sup_cubierta_gis; ?></td>
                    <td style="font-size:10px; text-align:center; border:1px solid;"><?php echo $padron->sup_pileta; ?></td>
                    <td style="font-size:10px; text-align:center; border:1px solid;"><?php echo $padron->sup_pileta_gis; ?></td>
                </tr>
            </table>
        <?php endif; ?>
        <div style="margin-top:2px; width:100%;">
            <div style="font-size:12px; text-align:left; width:50%; float:left; font-weight:bold;">
                CÓDIGO DE PAGO ELECTRÓNICO: <?php echo "1" . str_pad((int) $padron->trib_Cuenta, 6, "0", STR_PAD_LEFT); ?>
            </div>
            <div style="font-size:12px; text-align:right; width:50%; float:left; font-weight:bold;">
                <?php echo $padron->usua_Clave; ?>
            </div>
        </div>
        <div style="width:100%;">
            <?php if ($deuda <= 10): ?>
                <div style="font-size:18px; text-align:center; width:45%; float:left; font-weight:bold; padding:10px 0 12px 0; ">
                    ¡Gracias por estar al día!
                </div>
                <div style="font-size:18px; text-align:center; width:10%; float:left; font-weight:bold; padding:-4px 0 0 -3px;">
                    <img src="img/major/feliz.png" alt=")" style="width:50%;"/>
                </div>
                <div style="font-size:18px; text-align:center; width:45%; float:left; font-weight:bold; padding:10px 0 12px 0; ">
                    <?php if ($descuento_en_termino > 0): ?>
                        Obtuviste un descuento de $<?= $descuento_en_termino; ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div style="font-size:18px; text-align:center; width:45%; float:left; font-weight:bold; padding:10px 0 12px 0; ">
                    ¡Tu padrón registra deuda!
                </div>
                <div style="font-size:18px; text-align:center; width:10%; float:left; font-weight:bold; padding:-4px 0 0 -3px;">
                    <img src="img/major/triste.png" alt=")" style="width:50%;"/>
                </div>
                <div style="font-size:18px; text-align:center; width:45%; float:left; font-weight:bold; padding:10px 0 12px 0; ">
                    $<?= number_format($deuda, 2, ',', '.'); ?> al 31/12/2020
                </div>
            <?php endif; ?>
        </div>
        <div style="float:left; width:49%;">
            <?php if (!empty($boletas['A'])): ?>
                <div style="height:150px;">
                    <table style="border:1px solid;">
                        <tr class="medidas">
                            <td style="width:35%;"></td>
                            <td style="width:15%;"></td>
                            <td style="width:35%;"></td>
                            <td style="width:15%;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="border-bottom:1px solid; font-size:17px; text-align:center; vertical-align:middle; padding:0; font-weight:bold;">
                                Servicios A
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-right:1px solid; text-align:center; vertical-align:middle; font-weight:bold;">Pago Anual</td>
                            <td colspan="2" style="border-right:1px solid; text-align:center; vertical-align:middle; font-weight:bold;">Pago Bimestral</td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right;">Agua Corriente</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual['A'][0]; ?></td>
                            <td style="font-size:8px; text-align:right;">Agua Corriente</td> 
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion['A'][0]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right;">Servicios Sanitarios</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual['A'][1]; ?></td>
                            <td style="font-size:8px; text-align:right;">Servicios Sanitarios</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion['A'][1]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right;">Bomberos</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual['A'][3]; ?></td>
                            <td style="font-size:8px; text-align:right;">Bomberos</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion['A'][3]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right;">Fondo Sostenimiento Redes Servicio Sanitario</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual['A'][4]; ?></td>
                            <td style="font-size:8px; text-align:right;">Fondo Sostenimiento Redes Servicio Sanitario</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion['A'][4]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right;">Consumo Excedente Superficies Jardines</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual['A'][5]; ?></td>
                            <td style="font-size:8px; text-align:right;">Consumo Excedente Superficies Jardines</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion['A'][5]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;">Total</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;"><?php echo $facturacion_anual['A'][6]; ?></td>
                            <td style="font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;">Total</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;"><?php echo $facturacion['A'][6]; ?></td>
                        </tr>
                    </table>
                </div>
                <div style="float:left; width:100%; padding:0px; text-align:center;">
                    Opción pago anual
                </div>
                <?php foreach ($boletas['A'] as $boleta): ?>
                    <div style="float:left; width:100%; padding:2px;">
                        <table>
                            <tr class="medidas">
                                <td style="width:50%;"></td>
                                <td style="width:50%;"></td>
                            </tr>
                            <tr>
                                <?php
                                $vencimiento = new DateTime($boleta->pago_VtoTermino);
                                $codigo = '425' .
                                        str_pad(number_format($boleta->pago_Importe, 2, '', ''), 8, '0', STR_PAD_LEFT) .
                                        str_pad($vencimiento->format('z') + 1, 3, '0', STR_PAD_LEFT) .
                                        str_pad($boleta->pago_CodigoDelegacion, 13, '0', STR_PAD_LEFT) .
                                        $boleta->pago_Numero . $vencimiento->format('y') . $boleta->pago_DigitoVerificador;
                                ?>
                                <td rowspan="2" style="border:1px solid; font-size:13px; text-align:center; vertical-align:middle; padding:0; font-weight:bold;">
                                    <?php if ($boleta->pago_CodigoDelegacion === 85): ?>
                                        <?php echo substr($boleta->pago_Cuota, 2, 1) . '-' . $boleta->pago_Periodo . ' Anual'; ?>
                                    <?php else: ?>
                                        <?php echo substr($boleta->pago_Cuota, -2) . '-' . $boleta->pago_Periodo . ' Cuota ' . substr($boleta->pago_Cuota, -1) . ' de 6'; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="border:1px solid; font-size:11px; text-align:center; vertical-align:middle; padding:0;">
                                    <?php echo 'Vencimiento Único'; ?>
                                </td>	
                            </tr>
                            <tr>
                                <td style="border:1px solid; font-size:11px; text-align:center; vertical-align:middle; padding:0;">
                                    <?php echo $vencimiento->format('d/m/Y'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:1px solid; font-size:12px; text-align:center; font-weight:bold;">
                                    IMPORTE
                                </td>
                                <td style="border:1px solid; font-size:12px; text-align:center; font-weight:bold;">
                                    $<?php echo number_format($boleta->pago_Importe, 2, ',', '.'); ?>
                                </td>
                            </tr>
                            <tr style="border:1px solid;">
                                <td colspan="2" style="font-size:12px; text-align:center; padding-top:5px;">
                            <barcode code="<?php echo $codigo; ?>" type="I25" class="barcode" size="0.65" height="1.0" />
                            <?php echo $codigo; ?>
                            </td>
                            </tr>
                        </table>
                    </div>
                    <?php if ($boleta->pago_CodigoDelegacion === 85): ?>
                        <div style="float:left; width:100%; padding:1px; text-align:center;">
                            Opción pago bimestral
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div style="float:right; width:49%;">
            <?php if (!empty($boletas['B'])): ?>
                <div style="height:150px;">
                    <table style="border:1px solid;">
                        <tr class="medidas">
                            <td style="width:35%;"></td>
                            <td style="width:15%;"></td>
                            <td style="width:35%;"></td>
                            <td style="width:15%;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="border-bottom:1px solid; font-size:17px; text-align:center; vertical-align:middle; padding:0; font-weight:bold;">
                                Servicios B
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-right:1px solid; text-align:center; vertical-align:middle; font-weight:bold;">Pago Anual</td>
                            <td colspan="2" style="border-right:1px solid; text-align:center; vertical-align:middle; font-weight:bold;">Pago Bimestral</td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right;">Servicios Generales</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual['B'][0]; ?></td>
                            <td style="font-size:8px; text-align:right;">Servicios Generales</td> 
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion['B'][0]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right;">Bomberos</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual['B'][2]; ?></td>
                            <td style="font-size:8px; text-align:right;">Bomberos</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion['B'][2]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right;">Conservación de Calles</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual['B'][5]; ?></td>
                            <td style="font-size:8px; text-align:right;">Conservación de Calles</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion['B'][5]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right;">Recolección de Residuos Verdes</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual['B'][1]; ?></td>
                            <td style="font-size:8px; text-align:right;">Recolección de Residuos Verdes</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion['B'][1]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right;">Fondo sostenim. biblioteca</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual['B'][3]; ?></td>
                            <td style="font-size:8px; text-align:right;">Fondo sostenim. biblioteca</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion['B'][3]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right;">Tasa Mínima</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion_anual['B'][4]; ?></td>
                            <td style="font-size:8px; text-align:right;">Tasa Mínima</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right;"><?php echo $facturacion['B'][4]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:6px; text-align:right;">&nbsp;</td>
                            <td style="border-right:1px solid; font-size:6px; text-align:right;">&nbsp;</td>
                            <td style="font-size:6px; text-align:right;">&nbsp;</td>
                            <td style="border-right:1px solid; font-size:6px; text-align:right;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;">Total</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;"><?php echo $facturacion_anual['B'][6]; ?></td>
                            <td style="font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;">Total</td>
                            <td style="border-right:1px solid; font-size:8px; text-align:right; border-top: solid 1px #000; font-weight: bold;"><?php echo $facturacion['B'][6]; ?></td>
                        </tr>
                    </table>
                </div>
                <div style="float:left; width:100%; padding:0px; text-align:center;">
                    Opción pago anual
                </div>
                <?php foreach ($boletas['B'] as $boleta): ?>
                    <div style="float:left; width:100%; padding:2px;">
                        <table>
                            <tr class="medidas">
                                <td style="width:50%;"></td>
                                <td style="width:50%;"></td>
                            </tr>
                            <tr>
                                <?php
                                $vencimiento = new DateTime($boleta->pago_VtoTermino);
                                $codigo = '425' .
                                        str_pad(number_format($boleta->pago_Importe, 2, '', ''), 8, '0', STR_PAD_LEFT) .
                                        str_pad($vencimiento->format('z') + 1, 3, '0', STR_PAD_LEFT) .
                                        str_pad($boleta->pago_CodigoDelegacion, 13, '0', STR_PAD_LEFT) .
                                        $boleta->pago_Numero . $vencimiento->format('y') . $boleta->pago_DigitoVerificador;
                                ?>
                                <td rowspan="2" style="border:1px solid; font-size:13px; text-align:center; vertical-align:middle; padding:0; font-weight:bold;">
                                    <?php if ($boleta->pago_CodigoDelegacion === 85): ?>
                                        <?php echo substr($boleta->pago_Cuota, 2, 1) . '-' . $boleta->pago_Periodo . ' Anual'; ?>
                                    <?php else: ?>
                                        <?php echo substr($boleta->pago_Cuota, -2) . '-' . $boleta->pago_Periodo . ' Cuota ' . substr($boleta->pago_Cuota, -1) . ' de 6'; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="border:1px solid; font-size:11px; text-align:center; vertical-align:middle; padding:0;">
                                    <?php echo 'Vencimiento Único'; ?>
                                </td>	
                            </tr>
                            <tr>
                                <td style="border:1px solid; font-size:11px; text-align:center; vertical-align:middle; padding:0;">
                                    <?php echo $vencimiento->format('d/m/Y'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:1px solid; font-size:12px; text-align:center; font-weight:bold;">
                                    IMPORTE
                                </td>
                                <td style="border:1px solid; font-size:12px; text-align:center; font-weight:bold;">
                                    $<?php echo number_format($boleta->pago_Importe, 2, ',', '.'); ?>
                                </td>
                            </tr>
                            <tr style="border:1px solid;">
                                <td colspan="2" style="font-size:12px; text-align:center; padding-top:5px;">
                            <barcode code="<?php echo $codigo; ?>" type="I25" class="barcode" size="0.65" height="1.0" />
                            <?php echo $codigo; ?>
                            </td>
                            </tr>
                        </table>
                    </div>
                    <?php if ($boleta->pago_CodigoDelegacion === 85): ?>
                        <div style="float:left; width:100%; padding:2px; text-align:center;">
                            Opción pago bimestral
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </body>
</html>