<!--
        /*
         * Vista Informe de Deuda Comercio.
         * Autor: Leandro
         * Creado: 29/01/2019
         * Modificado: 08/04/2021 (Leandro)
         */
-->
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
                <h2><?php echo (!empty($title_view)) ? $title_view : 'Deudas'; ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="container-table">
                        <?php if (!empty($padron[0])): ?>
                            <table id="datos-padron">
                                <tr style="font-size:18px; font-weight:bold;">
                                    <td colspan="6">MUNICIPALIDAD DE LUJÁN DE CUYO</td>
                                </tr>
                                <tr style="border-bottom:2px solid; font-size:12px; font-weight:bold;">
                                    <td colspan="6">INFORME DE DEUDA</td>
                                </tr>
                                <tr>
                                    <td style="width:18%;" class="tbl_deudas_title">Fecha de Emisión:</td>
                                    <td style="width:23%;" class="tbl_deudas_content"><?php echo date_format(new DateTime(), 'd/m/Y h:i'); ?></td>
                                    <td style="width:10%;"></td>
                                    <td style="width:22%;"></td>
                                    <td style="width:10%;" class="tbl_deudas_title">Cuenta:</td>
                                    <td style="width:17%;" class="tbl_deudas_content"><?php echo $padron[0]->trib_Cuenta; ?></td>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_title">Nombre de Fantasía:</td>
                                    <td class="tbl_deudas_content" colspan="3">
                                        <?php echo $padron[0]->come_NombreFantasia; ?>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_title">Titular:</td>
                                    <td class="tbl_deudas_content" colspan="3"><?php echo $padron[0]->pers_Apellido . " " . $padron[0]->pers_Nombre; ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_title">Dom.Contrib:</td>
                                    <td class="tbl_deudas_content" colspan="3">
                                        <?php echo $padron[0]->pers_Calle . " " . $padron[0]->pers_Altura . " " . $padron[0]->pers_Piso . " " . $padron[0]->pers_Depto; ?>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_title">Dom.Comercio:</td>
                                    <td class="tbl_deudas_content" colspan="3">
                                        <?php echo $padron[0]->come_Calle . " " . $padron[0]->come_Altura; ?>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_title">Localidad:</td>
                                    <td class="tbl_deudas_content" colspan="3"><?php echo $padron[0]->come_Localidad; ?></td>
                                    <td class="tbl_deudas_title">Barrio:</td>
                                    <td class="tbl_deudas_content"><?php echo ''; ?></td>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_title">Manzana:</td>
                                    <td class="tbl_deudas_content"><?php echo ''; ?></td>
                                    <td class="tbl_deudas_title">Lote:</td>
                                    <td class="tbl_deudas_content"><?php echo ''; ?></td>
                                    <td class="tbl_deudas_title">Zona:</td>
                                    <td class="tbl_deudas_content"><?php echo ''; ?></td>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_title">Esquina:</td>
                                    <td class="tbl_deudas_content"><?php echo ''; ?></td>
                                    <td class="tbl_deudas_title">Tipo Viv.:</td>
                                    <td class="tbl_deudas_content" colspan="3"><?php echo ''; ?></td>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_title">Sup.Total:</td>
                                    <td class="tbl_deudas_content"><?php echo ''; ?></td>
                                    <td class="tbl_deudas_title">Sup.Callejón:</td>
                                    <td></td>
                                    <td class="tbl_deudas_title">Sup.Cubierta:</td>
                                    <td class="tbl_deudas_content"><?php echo ''; ?></td>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_title">U. Vivienda:</td>
                                    <td></td>
                                    <td class="tbl_deudas_title">U.Comercio:</td>
                                    <td></td>
                                    <td class="tbl_deudas_title">Mts. Frente:</td>
                                    <td class="tbl_deudas_content"><?php echo ''; ?></td>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_title">Domicilio Postal:</td>
                                    <td class="tbl_deudas_content" colspan="3">
                                        <?php echo $padron[0]->pers_Calle . " " . $padron[0]->pers_Altura . " " . $padron[0]->pers_Piso . " " . $padron[0]->pers_Depto; ?>
                                    </td>
                                    <td class="tbl_deudas_title">Valuación</td>
                                    <td class="tbl_deudas_content"><?php echo ''; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="6">Informe de deuda no apto para el pago</td>
                                </tr>
                            </table>
                            <br />
                        <?php endif; ?>
                        <?php if (!empty($padron[0]->rubros)): ?>
                            <table>
                                <tr style="text-align:center; border-bottom:solid 1px; font-weight:bold;" >
                                    <td colspan="4">Rubros Habilitados</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold; width:11%;">rubl_Codigo</td>
                                    <td style="font-weight:bold; width:46%;">rubl_Descripcion</td>
                                    <td style="font-weight:bold; width:32%;">cubi_Descripcion</td>
                                    <td style="font-weight:bold; width:11%;">rubr_FechaBaja</td>
                                </tr>
                                <?php foreach ($padron[0]->rubros as $Rubro): ?>
                                    <tr>
                                        <td><?php echo $Rubro->rubl_Codigo; ?></td>
                                        <td><?php echo $Rubro->rubl_Descripcion; ?></td>
                                        <td><?php echo $Rubro->cubi_Descripcion; ?></td>
                                        <td style="text-align:right;"><?php echo date_format(new DateTime($Rubro->rubr_FechaBaja), 'd/m/Y'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                            <br />
                        <?php endif; ?>
                        <br />
                        <?php if (!empty($deudas)): ?>
                            <table style="text-align:right;">
                                <?php for ($i = 0; $i <= 8; $i++): ?>
                                    <?php $monto_subtotal[$i] = 0; ?>
                                    <?php $monto_total[$i] = 0; ?>
                                    <?php $monto_total_general[$i] = 0; ?>
                                <?php endfor; ?>
                                <?php $old_tasa = ""; ?>
                                <?php $old_subtasa = ""; ?>
                                <?php $subtasa = ""; ?>
                                <?php $tasa = ""; ?>
                                <tr>
                                    <td style="width:7%"></td>
                                    <td style="width:7%"></td>
                                    <td style="width:7%"></td>
                                    <td style="width:7%"></td>
                                    <td style="width:7%"></td>
                                    <td style="width:7%"></td>
                                    <td style="width:8%"></td>
                                    <td style="width:6%"></td>
                                    <td style="width:6%"></td>
                                    <td style="width:6%"></td>
                                    <td style="width:6%"></td>
                                    <td style="width:6%"></td>
                                    <td style="width:6%"></td>
                                    <td style="width:6%"></td>
                                    <td style="width:8%"></td>
                                </tr>
                                <?php foreach ($deudas as $deuda): ?>
                                    <?php $tasa = "$deuda->ttas_Tasa - $deuda->tasa_Descripcion"; ?>
                                    <?php $subtasa = "$deuda->ttas_Tasa/$deuda->ttas_SubTasa - $deuda->ttas_Descripcion"; ?>
                                    <?php if ($old_subtasa !== $subtasa): ?>
                                        <?php if (!empty($old_subtasa)): ?>
                                            <tr>
                                                <td class="tbl_deudas_totales" colspan="6">
                                                    <?php echo "Subtotal $old_subtasa"; ?>
                                                </td>
                                                <?php for ($i = 0; $i <= 8; $i++): ?>
                                                    <td class="tbl_deudas_totales"><?php echo number_format($monto_subtotal[$i], 2); ?> </td>
                                                    <?php $monto_total[$i] += $monto_subtotal[$i]; ?>
                                                    <?php $monto_subtotal[$i] = 0; ?>
                                                <?php endfor; ?>
                                            </tr>
                                            <?php if ($old_tasa !== $tasa && !empty($old_tasa)): ?>
                                                <tr>
                                                    <td class="tbl_deudas_totales" colspan="6">
                                                        <?php echo "TOTAL TASA $old_tasa"; ?>
                                                    </td>
                                                    <?php for ($i = 0; $i <= 8; $i++): ?>
                                                        <?php if ($i === 0 || $i === 8): ?>
                                                            <td class="tbl_deudas_totales tbl_deudas_bc_ccc"><?php echo number_format($monto_total[$i], 2); ?> </td>
                                                        <?php else: ?>
                                                            <td class="tbl_deudas_totales"><?php echo number_format($monto_total[$i], 2); ?> </td>
                                                        <?php endif; ?>
                                                        <?php $monto_total_general[$i] += $monto_total[$i]; ?>
                                                        <?php $monto_total[$i] = 0; ?>
                                                    <?php endfor; ?>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if (!empty($old_subtasa)): ?>
                                            <tr>
                                                <td colspan="15">&nbsp;</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php $old_subtasa = $subtasa; ?>
                                        <?php $old_tasa = $tasa; ?>
                                        <tr>
                                            <td style="text-align:center; border-top: 1px solid; font-weight: bold;" colspan="15">
                                                <?php echo "TASA $deuda->ttas_Tasa/$deuda->ttas_SubTasa - $deuda->ttas_Descripcion"; ?>
                                            </td>
                                        </tr>
                                        <tr style="border: 1px solid; vertical-align: top;">
                                            <th style="text-align:right;">Período</th>
                                            <th style="text-align:right;">Cuota</th>
                                            <th style="text-align:right;">Venc.</th>
                                            <th style="text-align:right;">Apremio</th>
                                            <th style="text-align:right;">Rubro</th>
                                            <th style="text-align:right;">Cartel</th>
                                            <th style="text-align:right;">Imp.Origen</th>
                                            <th style="text-align:right;">Recargos</th>
                                            <th style="text-align:right;">IVARec</th>
                                            <th style="text-align:right;">ComAdmi nist</th>
                                            <th style="text-align:right;">HonReca udE4</th>
                                            <th style="text-align:right;">HonOfJus tE4</th>
                                            <th style="text-align:right;">HonReca udE5</th>
                                            <th style="text-align:right;">HonOfJus tE5</th>
                                            <th style="text-align:right;">Total</th>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td><?php echo $deuda->ccde_Periodo; ?> </td>
                                        <td><?php echo $deuda->ccde_Cuota; ?> </td>
                                        <td><?php echo date_format(new DateTime($deuda->ccde_FechaVencimiento), 'd/m/y'); ?> </td>
                                        <td><?php echo $deuda->juce_Numero; ?> </td>
                                        <td><?php echo $deuda->rubl_Codigo; ?> </td>
                                        <td><?php echo $deuda->ccde_NumeroCartel; ?> </td>
                                        <td><?php echo number_format(round($deuda->Saldo * $deuda->ValorTipoCantidadSaldo, 2, PHP_ROUND_HALF_EVEN), 2); ?> </td>
                                        <?php $total_fila = round($deuda->Saldo * $deuda->ValorTipoCantidadSaldo, 2, PHP_ROUND_HALF_EVEN); ?>
                                        <?php $monto_subtotal[0] += round($deuda->Saldo * $deuda->ValorTipoCantidadSaldo, 2, PHP_ROUND_HALF_EVEN); ?>
                                        <?php for ($i = 0; $i <= 6; $i++): ?>
                                            <td><?php echo number_format(round($deuda->extras[$i] * $deuda->ValorTipoCantidadSaldo, 2, PHP_ROUND_HALF_EVEN), 2); ?> </td>
                                            <?php $total_fila += round($deuda->extras[$i] * $deuda->ValorTipoCantidadSaldo, 2, PHP_ROUND_HALF_EVEN); ?>
                                            <?php $monto_subtotal[$i + 1] += round($deuda->extras[$i] * $deuda->ValorTipoCantidadSaldo, 2, PHP_ROUND_HALF_EVEN); ?>
                                        <?php endfor; ?>
                                        <?php $monto_subtotal[8] += $total_fila; ?>
                                        <td><?php echo number_format($total_fila, 2); ?> </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td class="tbl_deudas_totales" colspan="6">
                                        <?php echo "Subtotal $old_subtasa"; ?>
                                    </td>
                                    <?php for ($i = 0; $i <= 8; $i++): ?>
                                        <td class="tbl_deudas_totales"><?php echo number_format($monto_subtotal[$i], 2); ?> </td>
                                        <?php $monto_total[$i] += $monto_subtotal[$i]; ?>
                                    <?php endfor; ?>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_totales" colspan="6">
                                        <?php echo "TOTAL TASA $old_tasa"; ?>
                                    </td>
                                    <?php for ($i = 0; $i <= 8; $i++): ?>
                                        <?php if ($i === 0 || $i === 8): ?>
                                            <td class="tbl_deudas_totales tbl_deudas_bc_ccc"><?php echo number_format($monto_total[$i], 2); ?> </td>
                                        <?php else: ?>
                                            <td class="tbl_deudas_totales"><?php echo number_format($monto_total[$i], 2); ?> </td>
                                        <?php endif; ?>
                                        <?php $monto_total_general[$i] += $monto_total[$i]; ?>
                                    <?php endfor; ?>
                                </tr>
                                <tr>
                                    <td class="tbl_deudas_totales tbl_deudas_bc_999" colspan="6">
                                        <?php echo "TOTAL GENERAL DEUDA"; ?>
                                    </td>
                                    <?php for ($i = 0; $i <= 8; $i++): ?>
                                        <?php if ($i === 0 || $i === 8): ?>
                                            <td class="tbl_deudas_totales tbl_deudas_bc_999"><?php echo number_format($monto_total_general[$i], 2); ?> </td>
                                        <?php else: ?>
                                            <td class="tbl_deudas_totales"><?php echo number_format($monto_total_general[$i], 2); ?> </td>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </tr>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="text-center">
                    <button class="btn btn-primary btn-sm" onclick="window.print();" title="Imprimir" type="button" name="Imprimir">Imprimir</button>
                    <a href="major/deudas" class="btn btn-default btn-sm">Volver</a>
                </div>
            </div>
        </div>
    </div>
</div>