<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Permisos
 * Autor: Leandro
 * Creado: 26/01/2017
 * Modificado: 23/08/2021 (Leandro), 19/7/22 (yoel grosso)
 */
if (!function_exists('groups_names'))
{

    function groups_names($grupos)
    {
        $nombres = array();
        foreach ($grupos as $Grupo)
        {
            array_push($nombres, $Grupo['name']);
        }
        return $nombres;
    }
}

if (!function_exists('in_groups'))
{

    function in_groups($grupos_permitidos, $grupos)
    {
        $result = array_intersect($grupos_permitidos, $grupos);
        return (!empty($result));
    }
}

//REDIRECCIONES SEGÚN PERMISOS
if (!function_exists('redirecciones_general_escritorio'))
{

    function redirecciones_general_escritorio($grupo)
    {
        switch ($grupo)
        {
            case 'actasisp_user': case 'actasisp_inspector': case 'actasisp_consulta_general':
                redirect('actasisp/escritorio', 'refresh');
                break;
            case 'antenas_admin': case 'antenas_consulta_general':
                redirect('antenas/escritorio', 'refresh');
                break;
            case 'asesoria_letrada_admin': case 'asesoria_letrada_user': case 'asesoria_letrada_area': case 'asesoria_letrada_consulta_general':
                redirect('asesoria_letrada/escritorio', 'refresh');
                break;
            case 'asistencia_rrhh': case 'asistencia_control': case 'asistencia_contralor': case 'asistencia_director': case 'asistencia_consulta_general':
                redirect('asistencia/escritorio', 'refresh');
                break;
            case 'asistencia_user':
                redirect('asistencia/fichadas/ver', 'refresh');
                break;
            case 'defunciones_user': case 'defunciones_consulta_general':
                redirect('defunciones/escritorio', 'refresh');
                break;
            case 'desarrollo_social_user': case 'desarrollo_social_consulta_general':
                redirect('desarrollo_social/escritorio', 'refresh');
                break;
            case 'gobierno_user': case 'gobierno_consulta_general':
                redirect('gobierno/escritorio', 'refresh');
                break;
            case 'incidencias_admin': case 'incidencias_user': case 'incidencias_area': case 'incidencias_consulta_general':
                redirect('incidencias/escritorio', 'refresh');
                break;
            case 'lujan_pass_publico': case 'lujan_pass_beneficiario': case 'lujan_pass_control': case 'lujan_pass_consulta_general':
                redirect('lujan_pass/escritorio', 'refresh');
                break;
            case 'major_boletos': case 'major_deudas': case 'major_deudas_masivas': case 'major_solicitudes': case 'major_consulta_general':
                redirect('major/escritorio', 'refresh');
                break;
            case 'mas_beneficios_publico': case 'mas_beneficios_beneficiario': case 'mas_beneficios_control': case 'mas_beneficios_consulta_general':
                redirect('mas_beneficios/escritorio', 'refresh');
                break;
            case 'ninez_adolescencia_admin': case 'ninez_adolescencia_user': case 'ninez_adolescencia_consulta_general':
                redirect('ninez_adolescencia/escritorio', 'refresh');
                break;
            case 'notificaciones_user': case 'notificaciones_areas': case 'notificaciones_notificadores': case 'notificaciones_control':
                redirect('notificaciones/escritorio', 'refresh');
                break;
            case 'obrador_user': case 'obrador_consulta_general':
                redirect('obrador/escritorio', 'refresh');
                break;
            case 'reclamos_major_admin': case 'reclamos_major_consulta_general':
                redirect('reclamos_major/escritorio', 'refresh');
                break;
            case 'reclamos_gis_user':
                redirect('reclamos_gis/escritorio', 'refresh');
                break;
            case 'oficina_empleo': case 'oficina_empleo_general':    //editado por yoel grosso
                    redirect('oficina_de_empleo/escritorio', 'refresh');
                    break;               
            case 'recursos_humanos_admin': case 'recursos_humanos_user': case 'recursos_humanos_publico': case 'recursos_humanos_director': case 'recursos_humanos_consulta_general': case 'recursos_humanos_bonos':
                redirect('recursos_humanos/escritorio', 'refresh');
                break;
            case 'resoluciones_user': case 'resoluciones_consulta_general':
                redirect('resoluciones/escritorio', 'refresh');
                break;
            case 'stock_informatica_user': case 'stock_informatica_consulta_general':
                redirect('stock_informatica/escritorio', 'refresh');
                break;
            case 'telefonia_admin': case 'telefonia_consulta_general':
                redirect('telefonia/escritorio', 'refresh');
                break;
            case 'tablero_consulta_general':
                redirect('tablero/escritorio', 'refresh');
                break;
            case 'toner_admin': case 'toner_consulta_general':
                redirect('toner/escritorio', 'refresh');
                break;
            case 'tramites_online_admin': case 'tramites_online_area': case 'tramites_online_consulta_general': case 'tramites_online_publico':
                redirect('tramites_online/escritorio', 'refresh');
                break;
            case 'transferencias_municipal': case 'transferencias_area': case 'transferencias_consulta_general': case 'transferencias_publico':
                redirect('transferencias/escritorio', 'refresh');
                break;
            case 'vales_combustible_contaduria': case 'vales_combustible_hacienda': case 'vales_combustible_autorizaciones': case 'vales_combustible_obrador': case 'vales_combustible_estacion': case 'vales_combustible_areas': case 'vales_combustible_consulta_general':
                redirect('vales_combustible/escritorio', 'refresh');
                break;
        }
    }
}

//FUNCIONES PARA PERMISOS DEL NAV
if (!function_exists('load_permisos_nav'))
{

    function load_permisos_nav($grupos, $controlador = NULL, $url_actual = NULL, $usuario_sist = NULL)
    {
        $user_menu = '<li class=""><a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><img src="img/generales/user.png" alt="">' . $usuario_sist['nombre'] . " " . $usuario_sist['apellido'] . ' <span class=" fa fa-angle-down"></span></a><ul class="dropdown-menu dropdown-usermenu pull-right"><li><a href="auth/perfil"><i class="fa fa-user-circle-o pull-right"></i> Mi perfil</a></li><li><a href="auth/change_password"><i class="fa fa-lock pull-right"></i> Cambiar contraseña</a></li><li><a href="auth/logout"><i class="fa fa-sign-out pull-right"></i> Salir</a></li></ul></li>';

        $grupos_admin = array('admin', 'consulta_general');
        $grupos_admin_manuales = array('admin', 'admin_manuales', 'consulta_general');
        $grupos_actasisp_user = array('actasisp_user');
        $grupos_actasisp_inspector = array('actasisp_inspector');
        $grupos_actasisp_consulta = array('actasisp_consulta_general');
        $grupos_antenas_admin = array('antenas_admin');
        $grupos_antenas_consulta = array('antenas_consulta_general');
        $grupos_asesoria_letrada_admin = array('asesoria_letrada_admin');
        $grupos_asesoria_letrada_consulta = array('asesoria_letrada_consulta_general');
        $grupos_asesoria_letrada_user = array('asesoria_letrada_user');
        $grupos_asesoria_letrada_area = array('asesoria_letrada_area');
        $grupos_asistencia_consulta = array('asistencia_consulta_general');
        $grupos_asistencia_rrhh = array('asistencia_rrhh');
        $grupos_asistencia_control = array('asistencia_control');
        $grupos_asistencia_contralor = array('asistencia_contralor');
        $grupos_asistencia_director = array('asistencia_director');
        $grupos_asistencia_user = array('asistencia_user');
        $grupos_defunciones_consulta = array('defunciones_consulta_general');
        $grupos_defunciones_user = array('defunciones_user');
        $grupos_desarrollo_social_consulta = array('desarrollo_social_consulta_general');
        $grupos_desarrollo_social_user = array('desarrollo_social_user');
        $grupos_gobierno_consulta = array('gobierno_consulta_general');
        $grupos_gobierno_user = array('gobierno_user');
        $grupos_incidencias_admin = array('incidencias_admin');
        $grupos_incidencias_consulta = array('incidencias_consulta_general');
        $grupos_incidencias_user = array('incidencias_user');
        $grupos_incidencias_area = array('incidencias_area');
        $grupos_lujan_pass_consulta = array('lujan_pass_consulta_general');
        $grupos_lujan_pass_control = array('lujan_pass_control');
        $grupos_lujan_pass_publico = array('lujan_pass_publico');
        $grupos_lujan_pass_beneficiario = array('lujan_pass_beneficiario');
        $grupos_major_consulta = array('major_consulta_general');
        $grupos_major_boletos = array('major_boletos');
        $grupos_major_deudas = array('major_deudas');
        $grupos_major_deudas_masivas = array('major_deudas_masivas');
        $grupos_major_solicitudes = array('major_solicitudes');
        $grupos_mas_beneficios_consulta = array('mas_beneficios_consulta_general');
        $grupos_mas_beneficios_control = array('mas_beneficios_control');
        $grupos_mas_beneficios_publico = array('mas_beneficios_publico');
        $grupos_mas_beneficios_beneficiario = array('mas_beneficios_beneficiario');
        $grupos_ninez_adolescencia_admin = array('ninez_adolescencia_admin');
        $grupos_ninez_adolescencia_consulta = array('ninez_adolescencia_consulta_general');
        $grupos_ninez_adolescencia_user = array('ninez_adolescencia_user');
        $grupos_notificaciones_user = array('notificaciones_user');
        $grupos_notificaciones_oficinas = array('notificaciones_areas');
        $grupos_notificaciones_notificadores = array('notificaciones_notificadores');
        $grupos_notificaciones_control = array('notificaciones_control');
        $grupos_obrador_consulta = array('obrador_consulta_general');
        $grupos_obrador_user = array('obrador_user');
        $grupos_reclamos_major_admin = array('reclamos_major_admin');
        $grupos_reclamos_major_consulta = array('reclamos_major_consulta_general');
        $grupos_reclamos_gis_user = array('reclamos_gis_user');
        $grupos_oficina_de_empleo = array('oficina_empleo, oficina_empleo_general'); //editado por yoel grosso
        $grupos_oficina_de_empleo_general = array('tramites_online_publico'); //editado por yoel grosso      
        $grupos_reclamos_gis_consulta = array('reclamos_gis_consulta_general');
        $grupos_recursos_humanos_consulta = array('recursos_humanos_consulta_general');
        $grupos_recursos_humanos_admin = array('recursos_humanos_admin');
        $grupos_recursos_humanos_user = array('recursos_humanos_user');
        $grupos_recursos_humanos_publico = array('recursos_humanos_publico');
        $grupos_recursos_humanos_director = array('recursos_humanos_director');
        $grupos_recursos_humanos_bonos = array('recursos_humanos_bonos');
        $grupos_resoluciones_consulta = array('resoluciones_consulta_general');
        $grupos_resoluciones_user = array('resoluciones_user');
        $grupos_stock_informatica_consulta = array('stock_informatica_consulta_general');
        $grupos_stock_informatica_user = array('stock_informatica_user');
        $grupos_tablero_consulta = array('tablero_consulta_general');
        $grupos_telefonia_consulta = array('telefonia_consulta_general');
        $grupos_telefonia_admin = array('telefonia_admin');
        $grupos_toner_consulta = array('toner_consulta_general');
        $grupos_toner_admin = array('toner_admin');
        $grupos_transferencias_consulta = array('transferencias_consulta_general');
        $grupos_transferencias_municipal = array('transferencias_municipal');
        $grupos_transferencias_area = array('transferencias_area');
        $grupos_transferencias_publico = array('transferencias_publico');
        $grupos_tramites_online_consulta = array('tramites_online_consulta_general');
        $grupos_tramites_online_admin = array('tramites_online_admin');
        $grupos_tramites_online_area = array('tramites_online_area');
        $grupos_tramites_online_publico = array('tramites_online_publico');
        $grupos_vales_combustible_consulta = array('vales_combustible_consulta_general');
        $grupos_vales_combustible_areas = array('vales_combustible_areas');
        $grupos_vales_combustible_contaduria = array('vales_combustible_contaduria');
        $grupos_vales_combustible_hacienda = array('vales_combustible_hacienda');
        $grupos_vales_combustible_autorizaciones = array('vales_combustible_autorizaciones');
        $grupos_vales_combustible_obrador = array('vales_combustible_obrador');
        $grupos_vales_combustible_estacion = array('vales_combustible_estacion');
//PARA MOSTRAR MANUALES
        $grupos_manuales = array(
            'admin',
            'actasisp_user', 'actasisp_inspector', 'actasisp_consulta_general',
            'antenas_admin', 'antenas_consulta_general',
            'asesoria_letrada_admin', 'asesoria_letrada_consulta_general', 'asesoria_letrada_user', 'asesoria_letrada_area',
            'asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user', 'asistencia_consulta_general',
            'defunciones_user', 'defunciones_consulta_general',
            'desarrollo_social_user', 'desarrollo_social_consulta_general',
            'gobierno_user', 'gobierno_consulta_general',
            'incidencias_admin', 'incidencias_consulta_general', 'incidencias_user', 'incidencias_area',
            'lujan_pass_control', 'lujan_pass_consulta_general',
            'mas_beneficios_control', 'mas_beneficios_consulta_general',
            'major_boletos', 'major_deudas', 'major_deudas_masivas', 'major_solicitudes', 'major_consulta_general',
            'ninez_adolescencia_admin', 'ninez_adolescencia_consulta_general', 'ninez_adolescencia_user',
            'notificaciones_user', 'notificaciones_areas', 'notificaciones_notificadores', 'notificaciones_control',
            'obrador_user', 'obrador_consulta_general',
            'reclamos_major_admin', 'reclamos_major_consulta_general',
            'reclamos_gis_user', 'reclamos_gis_consulta_general',
            'oficina_empleo',  'oficina_empleo_general',     //editado por yoel grosso      
            'recursos_humanos_admin', 'recursos_humanos_user', 'recursos_humanos_director', 'recursos_humanos_publico', 'recursos_humanos_consulta_general',
            'resoluciones_user', 'resoluciones_consulta_general',
            'stock_informatica_user', 'stock_informatica_consulta_general',
            'tablero_consulta_general',
            'telefonia_admin', 'telefonia_consulta_general',
            'toner_admin', 'toner_consulta_general',
            'tramites_online_admin', 'tramites_online_area', 'tramites_online_consulta_general',
            'transferencias_municipal', 'transferencias_area', 'transferencias_consulta_general',
            'vales_combustible_contaduria', 'vales_combustible_hacienda', 'vales_combustible_areas', 'vales_combustible_autorizaciones', 'vales_combustible_obrador', 'vales_combustible_estacion', 'vales_combustible_consulta_general'
        );

        $nav = '';
        $nav .= ($controlador === 'escritorio') ? '<li class="current-page active active-init"><a class="active" href="escritorio"><i class="fa fa-home"></i> Inicio</a></li>' : '<li><a href="escritorio"><i class="fa fa-home"></i> Inicio</a></li>';
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Actas ISP">
        $c_act = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_actasisp_user, $grupos) || in_groups($grupos_actasisp_consulta, $grupos))
        {
            switch ($url_actual)
            {
                case 'actasisp/escritorio':
                case 'actasisp/escritorio/index':
                    $c_act['escritorio'] = ' class="current-page"';
                    break;
                case 'actasisp/actas/listar':
                case 'actasisp/actas/agregar':
                case 'actasisp/actas/editar':
                case 'actasisp/actas/ver':
                case 'actasisp/actas/eliminar':
                    $c_act['actas'] = ' class="current-page"';
                    break;
                case 'actasisp/inspectores/listar':
                case 'actasisp/inspectores/agregar':
                case 'actasisp/inspectores/editar':
                case 'actasisp/inspectores/ver':
                case 'actasisp/inspectores/eliminar':
                    $c_act['inspectores'] = ' class="current-page"';
                    break;
                case 'actasisp/motivos/listar':
                case 'actasisp/motivos/agregar':
                case 'actasisp/motivos/editar':
                case 'actasisp/motivos/ver':
                case 'actasisp/motivos/eliminar':
                    $c_act['motivos'] = ' class="current-page"';
                    break;
                case 'actasisp/reportes/listar':
                case 'actasisp/reportes/actas':
                    $c_act['reportes'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_act))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-file"></i> Actas ISP <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_act['escritorio']) ? '' : $c_act['escritorio']) . '><a href="actasisp/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_act['actas']) ? '' : $c_act['actas']) . '><a href="actasisp/actas/listar">Actas</a></li>';
                $nav .= '<li' . (empty($c_act['inspectores']) ? '' : $c_act['inspectores']) . '><a href="actasisp/inspectores/listar">Inspectores</a></li>';
                $nav .= '<li' . (empty($c_act['motivos']) ? '' : $c_act['motivos']) . '><a href="actasisp/motivos/listar">Motivos</a></li>';
                $nav .= '<li' . (empty($c_act['reportes']) ? '' : $c_act['reportes']) . '><a href="actasisp/reportes/listar">Reportes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-file"></i> Actas ISP <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="actasisp/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="actasisp/actas/listar">Actas</a></li>';
                $nav .= '<li><a href="actasisp/inspectores/listar">Inspectores</a></li>';
                $nav .= '<li><a href="actasisp/motivos/listar">Motivos</a></li>';
                $nav .= '<li><a href="actasisp/reportes/listar">Reportes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_actasisp_inspector, $grupos))
        {
            switch ($url_actual)
            {
                case 'actasisp/escritorio':
                case 'actasisp/escritorio/index':
                    $c_act['escritorio'] = ' class="current-page"';
                    break;
                case 'actasisp/actas/listar':
                case 'actasisp/actas/agregar':
                case 'actasisp/actas/editar':
                case 'actasisp/actas/ver':
                case 'actasisp/actas/eliminar':
                    $c_act['actas'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_act))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-file"></i> Actas ISP <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_act['escritorio']) ? '' : $c_act['escritorio']) . '><a href="actasisp/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_act['actas']) ? '' : $c_act['actas']) . '><a href="actasisp/actas/listar">Actas</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-file"></i> Actas ISP <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="actasisp/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="actasisp/actas/listar">Actas</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Antenas">
        $c_an = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_antenas_consulta, $grupos) || in_groups($grupos_antenas_admin, $grupos))
        {
            switch ($url_actual)
            {
                case 'antenas/escritorio':
                case 'antenas/escritorio/index':
                    $c_an['escritorio'] = ' class="current-page"';
                    break;
                case 'antenas/antenas/listar':
                case 'antenas/antenas/agregar':
                case 'antenas/antenas/editar':
                case 'antenas/antenas/ver':
                case 'antenas/antenas/eliminar':
                    $c_an['antenas'] = ' class="current-page"';
                    break;
                case 'antenas/denuncias/listar':
                case 'antenas/denuncias/agregar':
                case 'antenas/denuncias/editar':
                case 'antenas/denuncias/ver':
                case 'antenas/denuncias/eliminar':
                    $c_an['denuncias'] = ' class="current-page"';
                    break;
                case 'antenas/habilitaciones/listar':
                case 'antenas/habilitaciones/agregar':
                case 'antenas/habilitaciones/editar':
                case 'antenas/habilitaciones/ver':
                case 'antenas/habilitaciones/eliminar':
                    $c_an['habilitaciones'] = ' class="current-page"';
                    break;
                case 'antenas/mapa/ver':
                    $c_an['mapa'] = ' class="current-page"';
                    break;
                case 'antenas/proveedores/listar':
                case 'antenas/proveedores/agregar':
                case 'antenas/proveedores/editar':
                case 'antenas/proveedores/ver':
                case 'antenas/proveedores/eliminar':
                    $c_an['proveedores'] = ' class="current-page"';
                    break;
                case 'antenas/reportes/listar':
                    $c_an['reportes'] = ' class="current-page"';
                    break;
                case 'antenas/torres/listar':
                case 'antenas/torres/agregar':
                case 'antenas/torres/editar':
                case 'antenas/torres/ver':
                case 'antenas/torres/eliminar':
                    $c_an['torres'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_an))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-wifi"></i> Antenas <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_an['escritorio']) ? '' : $c_an['escritorio']) . '><a href="antenas/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_an['antenas']) ? '' : $c_an['antenas']) . '><a href="antenas/antenas/listar">Antenas</a></li>';
                $nav .= '<li' . (empty($c_an['denuncias']) ? '' : $c_an['denuncias']) . '><a href="antenas/denuncias/listar">Denuncias</a></li>';
                $nav .= '<li' . (empty($c_an['habilitaciones']) ? '' : $c_an['habilitaciones']) . '><a href="antenas/habilitaciones/listar">Habilitaciones</a></li>';
                $nav .= '<li' . (empty($c_an['mapa']) ? '' : $c_an['mapa']) . '><a href="antenas/mapa/ver">Mapa</a></li>';
                $nav .= '<li' . (empty($c_an['proveedores']) ? '' : $c_an['proveedores']) . '><a href="antenas/proveedores/listar">Proveedores</a></li>';
                $nav .= '<li' . (empty($c_an['reportes']) ? '' : $c_an['reportes']) . '><a href="antenas/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_an['torres']) ? '' : $c_an['torres']) . '><a href="antenas/torres/listar">Torres</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-wifi"></i> Antenas <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="antenas/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="antenas/antenas/listar">Antenas</a></li>';
                $nav .= '<li><a href="antenas/denuncias/listar">Denuncias</a></li>';
                $nav .= '<li><a href="antenas/habilitaciones/listar">Habilitaciones</a></li>';
                $nav .= '<li><a href="antenas/mapa/ver">Mapa</a></li>';
                $nav .= '<li><a href="antenas/proveedores/listar">Proveedores</a></li>';
                $nav .= '<li><a href="antenas/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="antenas/torres/listar">Torres</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        //<editor-fold defaultstate="collapsed" desc="Permisos Menú Asesoría Letrada">
        $c_al = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_asesoria_letrada_consulta, $grupos) || in_groups($grupos_asesoria_letrada_admin, $grupos))
        {
            switch ($url_actual)
            {
                case 'asesoria_letrada/escritorio':
                case 'asesoria_letrada/escritorio/index':
                    $c_al['escritorio'] = ' class="current-page"';
                    break;
                case 'asesoria_letrada/categorias/listar':
                case 'asesoria_letrada/categorias/agregar':
                case 'asesoria_letrada/categorias/editar':
                case 'asesoria_letrada/categorias/ver':
                case 'asesoria_letrada/categorias/anular':
                    $c_al['categorias'] = ' class="current-page"';
                    break;
                case 'asesoria_letrada/incidencias/listar':
                case 'asesoria_letrada/incidencias/agregar':
                case 'asesoria_letrada/incidencias/editar':
                case 'asesoria_letrada/incidencias/ver':
                case 'asesoria_letrada/incidencias/anular':
                    $c_al['incidencias'] = ' class="current-page"';
                    break;
                case 'asesoria_letrada/reportes/listar':
                case 'asesoria_letrada/reportes/incidencias':
                    $c_al['reportes'] = ' class="current-page"';
                    break;
                case 'asesoria_letrada/sectores/listar':
                case 'asesoria_letrada/sectores/agregar':
                case 'asesoria_letrada/sectores/editar':
                case 'asesoria_letrada/sectores/ver':
                case 'asesoria_letrada/sectores/eliminar':
                    $c_al['sectores'] = ' class="current-page"';
                    break;
                case 'asesoria_letrada/usuarios_areas/listar':
                case 'asesoria_letrada/usuarios_areas/agregar':
                case 'asesoria_letrada/usuarios_areas/editar':
                case 'asesoria_letrada/usuarios_areas/ver':
                case 'asesoria_letrada/usuarios_areas/eliminar':
                    $c_al['usuarios_areas'] = ' class="current-page"';
                    break;
                case 'asesoria_letrada/usuarios_sectores/listar':
                case 'asesoria_letrada/usuarios_sectores/agregar':
                case 'asesoria_letrada/usuarios_sectores/editar':
                case 'asesoria_letrada/usuarios_sectores/ver':
                case 'asesoria_letrada/usuarios_sectores/eliminar':
                    $c_al['usuarios_sectores'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_al))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-book"></i> Asesoría Letrada <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_al['escritorio']) ? '' : $c_al['escritorio']) . '><a href="asesoria_letrada/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_al['categorias']) ? '' : $c_al['categorias']) . '><a href="asesoria_letrada/categorias/listar">Categorías</a></li>';
                $nav .= '<li' . (empty($c_al['incidencias']) ? '' : $c_al['incidencias']) . '><a href="asesoria_letrada/incidencias/listar">Incidencias</a></li>';
                $nav .= '<li' . (empty($c_al['reportes']) ? '' : $c_al['reportes']) . '><a href="asesoria_letrada/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_al['sectores']) ? '' : $c_al['sectores']) . '><a href="asesoria_letrada/sectores/listar">Sectores</a></li>';
                $nav .= '<li' . (empty($c_al['usuarios_areas']) ? '' : $c_al['usuarios_areas']) . '><a href="asesoria_letrada/usuarios_areas/listar">Usuarios por Area</a></li>';
                $nav .= '<li' . (empty($c_al['usuarios_sectores']) ? '' : $c_al['usuarios_sectores']) . '><a href="asesoria_letrada/usuarios_sectores/listar">Usuarios por Sector</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-book"></i> Asesoría Letrada <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="asesoria_letrada/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="asesoria_letrada/categorias/listar">Categorías</a></li>';
                $nav .= '<li><a href="asesoria_letrada/incidencias/listar">Incidencias</a></li>';
                $nav .= '<li><a href="asesoria_letrada/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="asesoria_letrada/sectores/listar">Sectores</a></li>';
                $nav .= '<li><a href="asesoria_letrada/usuarios_areas/listar">Usuarios por Area</a></li>';
                $nav .= '<li><a href="asesoria_letrada/usuarios_sectores/listar">Usuarios por Sector</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_asesoria_letrada_user, $grupos))  //TECNICOS
        {
            switch ($url_actual)
            {
                case 'asesoria_letrada/escritorio':
                case 'asesoria_letrada/escritorio/index':
                    $c_al['escritorio'] = ' class="current-page"';
                    break;
                case 'asesoria_letrada/incidencias/listar_area':
                case 'asesoria_letrada/incidencias/agregar_area':
                case 'asesoria_letrada/incidencias/editar_area':
                case 'asesoria_letrada/incidencias/ver_area':
                    $c_al['incidencias_area'] = ' class="current-page"';
                    break;
                case 'asesoria_letrada/incidencias/listar_tecnico':
                case 'asesoria_letrada/incidencias/agregar_tecnico':
                case 'asesoria_letrada/incidencias/editar_tecnico':
                case 'asesoria_letrada/incidencias/ver_tecnico':
                    $c_al['incidencias'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_al))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-book"></i> Asesoría Letrada <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_al['escritorio']) ? '' : $c_al['escritorio']) . '><a href="asesoria_letrada/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_al['incidencias_area']) ? '' : $c_al['incidencias_area']) . '><a href="asesoria_letrada/incidencias/listar_area">Incidencias Solicitadas</a></li>';
                $nav .= '<li' . (empty($c_al['incidencias']) ? '' : $c_al['incidencias']) . '><a href="asesoria_letrada/incidencias/listar_tecnico">Incidencias Recibidas</a></li>';
                $nav .= '<li' . (empty($c_al['manuales']) ? '' : $c_al['manuales']) . '><a href="asesoria_letrada/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-book"></i> Asesoría Letrada <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="asesoria_letrada/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="asesoria_letrada/incidencias/listar_area">Incidencias Solicitadas</a></li>';
                $nav .= '<li><a href="asesoria_letrada/incidencias/listar_tecnico">Incidencias Recibidas</a></li>';
                $nav .= '<li><a href="asesoria_letrada/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_asesoria_letrada_area, $grupos))
        {
            switch ($url_actual)
            {
                case 'asesoria_letrada/escritorio':
                case 'asesoria_letrada/escritorio/index':
                    $c_al['escritorio'] = ' class="current-page"';
                    break;
                case 'asesoria_letrada/incidencias/listar_area':
                case 'asesoria_letrada/incidencias/agregar_area':
                case 'asesoria_letrada/incidencias/editar_area':
                case 'asesoria_letrada/incidencias/ver_area':
                    $c_al['incidencias'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_al))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-book"></i> Asesoría Letrada <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_al['escritorio']) ? '' : $c_al['escritorio']) . '><a href="asesoria_letrada/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_al['incidencias']) ? '' : $c_al['incidencias']) . '><a href="asesoria_letrada/incidencias/listar_area">Incidencias Solicitadas</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-warning"></i> Incidencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="asesoria_letrada/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="asesoria_letrada/incidencias/listar_area">Incidencias Solicitadas</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // // <editor-fold defaultstate="collapsed" desc="Permisos Menú Asistencia">
        $c_a = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_asistencia_consulta, $grupos) || in_groups($grupos_asistencia_rrhh, $grupos))
        {
            switch ($url_actual)
            {
                case 'asistencia/escritorio':
                case 'asistencia/escritorio/index':
                    $c_a['escritorio'] = ' class="current-page"';
                    break;
                case 'asistencia/personal_major/buscador':
                    $c_a['buscador'] = ' class="current-page"';
                    break;
                case 'asistencia/fichadas/ver':
                    $c_a['fichadas'] = ' class="current-page"';
                    break;
                case 'asistencia/formularios/listar':
                    $c_a['formularios'] = ' class="current-page"';
                    break;
                case 'asistencia/horarios_major/listar':
                case 'asistencia/horarios_major/listar_personal':
                case 'asistencia/horarios_major/ver_detalle':
                    $c_a['horarios_major'] = ' class="current-page"';
                    break;
                case 'asistencia/personal_major/listar':
                case 'asistencia/personal_major/calendario':
                    $c_a['personal_major'] = ' class="current-page"';
                    break;
                case 'asistencia/reportes_major/parte_diario':
                    $c_a['parte_diario'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/parte_diario_horas':
                    $c_a['parte_diario_horas'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/parte_diario_impresion':
                    $c_a['parte_diario_impresion'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/parte_estadistico':
                    $c_a['parte_estadistico'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/parte_relojes':
                    $c_a['parte_relojes'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/parte_novedades':
                    $c_a['parte_novedades'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/reporte_diario':
                    $c_a['reporte_diario'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/usuarios/listar':
                case 'asistencia/usuarios/agregar':
                case 'asistencia/usuarios/editar':
                case 'asistencia/usuarios/ver':
                case 'asistencia/usuarios/asignar':
                    $c_a['usuarios'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_a))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-clock-o"></i> Asistencia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_a['escritorio']) ? '' : $c_a['escritorio']) . '><a href="asistencia/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_a['buscador']) ? '' : $c_a['buscador']) . '><a href="asistencia/personal_major/buscador">Buscador</a></li>';
                $nav .= '<li' . (empty($c_a['fichadas']) ? '' : $c_a['fichadas']) . '><a href="asistencia/fichadas/ver">Fichadas</a></li>';
                $nav .= '<li' . (empty($c_a['formularios']) ? '' : $c_a['formularios']) . '><a href="asistencia/formularios/listar">Formularios</a></li>';
                $nav .= '<li' . (empty($c_a['horarios_major']) ? '' : $c_a['horarios_major']) . '><a href="asistencia/horarios_major/listar">Horarios Major</a></li>';
                $nav .= '<li' . (empty($c_a['personal_major']) ? '' : $c_a['personal_major']) . '><a href="asistencia/personal_major/listar">Personal Major</a></li>';
                $nav .= '<li' . (empty($c_a['reportes_major']) ? '' : $c_a['reportes_major']) . '><a>Reportes Major<span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu' . (empty($c_a['reportes_major']) ? '' : ' child_menu_open') . '">';
                $nav .= '<li class="sub_menu' . (empty($c_a['parte_diario']) ? ' no-current' : $c_a['parte_diario']) . '"><a href="asistencia/reportes_major/parte_diario">Parte Diario</a></li>';
                $nav .= '<li class="sub_menu' . (empty($c_a['parte_diario_horas']) ? ' no-current' : $c_a['parte_diario_horas']) . '"><a href="asistencia/reportes_major/parte_diario_horas">Parte Diario de Horas</a></li>';
                $nav .= '<li class="sub_menu' . (empty($c_a['parte_diario_impresion']) ? ' no-current' : $c_a['parte_diario_impresion']) . '"><a href="asistencia/reportes_major/parte_diario_impresion">Parte Diario Impresión</a></li>';
                $nav .= '<li class="sub_menu' . (empty($c_a['parte_estadistico']) ? ' no-current' : $c_a['parte_estadistico']) . '"><a href="asistencia/reportes_major/parte_estadistico">Parte Estadístico</a></li>';
                $nav .= '<li class="sub_menu' . (empty($c_a['parte_novedades']) ? ' no-current' : $c_a['parte_novedades']) . '"><a href="asistencia/reportes_major/parte_novedades">Parte Novedades</a></li>';
                $nav .= '<li class="sub_menu' . (empty($c_a['parte_relojes']) ? ' no-current' : $c_a['parte_relojes']) . '"><a href="asistencia/reportes_major/parte_relojes">Parte Relojes</a></li>';
                $nav .= '<li class="sub_menu' . (empty($c_a['reporte_diario']) ? ' no-current' : $c_a['reporte_diario']) . '"><a href="asistencia/reportes_major/reporte_diario">Reporte Diario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
                $nav .= '<li' . (empty($c_a['usuarios']) ? '' : $c_a['usuarios']) . '><a href="asistencia/usuarios/listar">Usuarios</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-clock-o"></i> Asistencia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="asistencia/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="asistencia/personal_major/buscador">Buscador</a></li>';
                $nav .= '<li><a href="asistencia/fichadas/ver">Fichadas</a></li>';
                $nav .= '<li><a href="asistencia/formularios/listar">Formularios</a></li>';
                $nav .= '<li><a href="asistencia/horarios_major/listar">Horarios Major</a></li>';
                $nav .= '<li><a href="asistencia/personal_major/listar">Personal Major</a></li>';
                $nav .= '<li><a>Reportes Major<span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_diario">Parte Diario</a></li>';
                $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_diario_horas">Parte Diario de Horas</a></li>';
                $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_diario_impresion">Parte Diario Impresión</a></li>';
                $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_estadistico">Parte Estadístico</a></li>';
                $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_novedades">Parte Novedades</a></li>';
                $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_relojes">Parte Relojes</a></li>';
                $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/reporte_diario">Reporte Diario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
                $nav .= '<li><a href="asistencia/usuarios/listar">Usuarios</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_asistencia_director, $grupos) || in_groups($grupos_asistencia_contralor, $grupos) || in_groups($grupos_asistencia_control, $grupos))
        {
            switch ($url_actual)
            {
                case 'asistencia/escritorio':
                case 'asistencia/escritorio/index':
                    $c_a['escritorio'] = ' class="current-page"';
                    break;
                case 'asistencia/personal_major/buscador':
                    $c_a['buscador'] = ' class="current-page"';
                    break;
                case 'asistencia/fichadas/ver':
                    $c_a['fichadas'] = ' class="current-page"';
                    break;
                case 'asistencia/formularios/listar':
                    $c_a['formularios'] = ' class="current-page"';
                    break;
                case 'asistencia/horarios_major/listar':
                case 'asistencia/horarios_major/listar_personal':
                case 'asistencia/horarios_major/ver_detalle':
                    $c_a['horarios_major'] = ' class="current-page"';
                    break;
                case 'asistencia/personal_major/listar':
                case 'asistencia/personal_major/calendario':
                    $c_a['personal_major'] = ' class="current-page"';
                    break;
                case 'asistencia/reportes_major/parte_diario':
                    $c_a['parte_diario'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/parte_diario_horas':
                    $c_a['parte_diario_horas'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/parte_diario_impresion':
                    $c_a['parte_diario_impresion'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/parte_estadistico':
                    $c_a['parte_estadistico'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/parte_relojes':
                    $c_a['parte_relojes'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/parte_novedades':
                    $c_a['parte_novedades'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
                case 'asistencia/reportes_major/reporte_diario':
                    $c_a['reporte_diario'] = ' current-page'; //Submenu
                    $c_a['reportes_major'] = ' class="current-page active"';
                    break;
            }
            if (!empty($c_a))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-clock-o"></i> Asistencia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_a['escritorio']) ? '' : $c_a['escritorio']) . '><a href="asistencia/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_a['buscador']) ? '' : $c_a['buscador']) . '><a href="asistencia/personal_major/buscador">Buscador</a></li>';
                $nav .= '<li' . (empty($c_a['fichadas']) ? '' : $c_a['fichadas']) . '><a href="asistencia/fichadas/ver">Fichadas</a></li>';
                $nav .= '<li' . (empty($c_a['formularios']) ? '' : $c_a['formularios']) . '><a href="asistencia/formularios/listar">Formularios</a></li>';
                if (in_groups($grupos_asistencia_control, $grupos))
                {
                    $nav .= '<li' . (empty($c_a['horarios_major']) ? '' : $c_a['horarios_major']) . '><a href="asistencia/horarios_major/listar">Horarios Major</a></li>';
                }
                $nav .= '<li' . (empty($c_a['personal_major']) ? '' : $c_a['personal_major']) . '><a href="asistencia/personal_major/listar">Personal Major</a></li>';
                $nav .= '<li' . (empty($c_a['reportes_major']) ? '' : $c_a['reportes_major']) . '><a>Reportes Major<span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu' . (empty($c_a['reportes_major']) ? '' : ' child_menu_open') . '">';
                if (in_groups($grupos_asistencia_control, $grupos) || in_groups($grupos_asistencia_contralor, $grupos))
                {
                    $nav .= '<li class="sub_menu' . (empty($c_a['parte_diario']) ? ' no-current' : $c_a['parte_diario']) . '"><a href="asistencia/reportes_major/parte_diario">Parte Diario</a></li>';
                    $nav .= '<li class="sub_menu' . (empty($c_a['parte_diario_horas']) ? ' no-current' : $c_a['parte_diario_horas']) . '"><a href="asistencia/reportes_major/parte_diario_horas">Parte Diario de Horas</a></li>';
                    $nav .= '<li class="sub_menu' . (empty($c_a['parte_diario_impresion']) ? ' no-current' : $c_a['parte_diario_impresion']) . '"><a href="asistencia/reportes_major/parte_diario_impresion">Parte Diario Impresión</a></li>';
                }
                if (in_groups($grupos_asistencia_control, $grupos))
                {
                    $nav .= '<li class="sub_menu' . (empty($c_a['parte_estadistico']) ? ' no-current' : $c_a['parte_estadistico']) . '"><a href="asistencia/reportes_major/parte_estadistico">Parte Estadístico</a></li>';
                }
                $nav .= '<li class="sub_menu' . (empty($c_a['parte_novedades']) ? ' no-current' : $c_a['parte_novedades']) . '"><a href="asistencia/reportes_major/parte_novedades">Parte Novedades</a></li>';
                if (in_groups($grupos_asistencia_control, $grupos))
                {
                    $nav .= '<li class="sub_menu' . (empty($c_a['parte_relojes']) ? ' no-current' : $c_a['parte_relojes']) . '"><a href="asistencia/reportes_major/parte_relojes">Parte Relojes</a></li>';
                }
                $nav .= '<li class="sub_menu' . (empty($c_a['reporte_diario']) ? ' no-current' : $c_a['reporte_diario']) . '"><a href="asistencia/reportes_major/reporte_diario">Reporte Diario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-clock-o"></i> Asistencia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="asistencia/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="asistencia/personal_major/buscador">Buscador</a></li>';
                $nav .= '<li><a href="asistencia/fichadas/ver">Fichadas</a></li>';
                $nav .= '<li><a href="asistencia/formularios/listar">Formularios</a></li>';
                if (in_groups($grupos_asistencia_control, $grupos))
                {
                    $nav .= '<li><a href="asistencia/horarios_major/listar">Horarios Major</a></li>';
                }
                $nav .= '<li><a href="asistencia/personal_major/listar">Personal Major</a></li>';
                $nav .= '<li><a>Reportes Major<span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                if (in_groups($grupos_asistencia_control, $grupos) || in_groups($grupos_asistencia_contralor, $grupos))
                {
                    $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_diario">Parte Diario</a></li>';
                    $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_diario_horas">Parte Diario de Horas</a></li>';
                    $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_diario_impresion">Parte Diario Impresión</a></li>';
                }
                if (in_groups($grupos_asistencia_control, $grupos))
                {
                    $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_estadistico">Parte Estadístico</a></li>';
                }
                $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_novedades">Parte Novedades</a></li>';
                if (in_groups($grupos_asistencia_control, $grupos))
                {
                    $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/parte_relojes">Parte Relojes</a></li>';
                }
                $nav .= '<li class="sub_menu no-current"><a href="asistencia/reportes_major/reporte_diario">Reporte Diario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_asistencia_user, $grupos))
        {
            switch ($url_actual)
            {
                case 'asistencia/escritorio':
                case 'asistencia/escritorio/index':
                    $c_a['escritorio'] = ' class="current-page"';
                    break;
                case 'asistencia/fichadas/ver':
                    $c_a['fichadas'] = ' class="current-page"';
                    break;
                case 'asistencia/formularios/listar':
                    $c_a['formularios'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_a))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-clock-o"></i> Asistencia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_a['escritorio']) ? '' : $c_a['escritorio']) . '><a href="asistencia/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_a['fichadas']) ? '' : $c_a['fichadas']) . '><a href="asistencia/fichadas/ver">Fichadas</a></li>';
                $nav .= '<li' . (empty($c_a['formularios']) ? '' : $c_a['formularios']) . '><a href="asistencia/formularios/listar">Formularios</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-clock-o"></i> Asistencia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="asistencia/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="asistencia/fichadas/ver">Fichadas</a></li>';
                $nav .= '<li><a href="asistencia/formularios/listar">Formularios</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Defunciones">
        $c_de = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_defunciones_consulta, $grupos) || in_groups($grupos_defunciones_user, $grupos))
        {
            switch ($url_actual)
            {
                case 'defunciones/escritorio':
                case 'defunciones/escritorio/index':
                    $c_de['escritorio'] = ' class="current-page"';
                    break;
                case 'defunciones/cementerios/listar':
                case 'defunciones/cementerios/agregar':
                case 'defunciones/cementerios/editar':
                case 'defunciones/cementerios/eliminar':
                case 'defunciones/cementerios/ver':
                    $c_de['cementerios'] = ' class="current-page"';
                    break;
                case 'defunciones/cocherias/listar':
                case 'defunciones/cocherias/agregar':
                case 'defunciones/cocherias/editar':
                case 'defunciones/cocherias/eliminar':
                case 'defunciones/cocherias/ver':
                    $c_de['cocherias'] = ' class="current-page"';
                    break;
                case 'defunciones/constructores/listar':
                case 'defunciones/constructores/agregar':
                case 'defunciones/constructores/editar':
                case 'defunciones/constructores/eliminar':
                case 'defunciones/constructores/ver':
                case 'defunciones/constructores/renovar_permiso':
                    $c_de['constructores'] = ' class="current-page"';
                    break;
                case 'defunciones/difuntos/listar':
                case 'defunciones/difuntos/agregar':
                case 'defunciones/difuntos/editar':
                case 'defunciones/difuntos/eliminar':
                case 'defunciones/difuntos/ver':
                    $c_de['difuntos'] = ' class="current-page"';
                    break;
                case 'defunciones/expedientes/listar':
                case 'defunciones/expedientes/agregar':
                case 'defunciones/expedientes/editar':
                case 'defunciones/expedientes/eliminar':
                case 'defunciones/expedientes/ver':
                    $c_de['expedientes'] = ' class="current-page"';
                    break;
                case 'defunciones/expedientes_pjm/listar':
                case 'defunciones/expedientes_pjm/agregar':
                case 'defunciones/expedientes_pjm/editar':
                case 'defunciones/expedientes_pjm/eliminar':
                case 'defunciones/expedientes_pjm/ver':
                    $c_de['expedientes_pjm'] = ' class="current-page"';
                    break;
                case 'defunciones/operaciones/listar':
                case 'defunciones/operaciones/agregar':
                case 'defunciones/operaciones/editar':
                case 'defunciones/operaciones/eliminar':
                case 'defunciones/operaciones/ver':
                    $c_de['operaciones_li'] = ' class="current-page active active-init"';
                    $c_de['operaciones_a'] = ' class="active"';
                    $c_de['operaciones_ul'] = ' child_menu_open';
                    $c_de['operaciones'] = ' class="sub_menu current-page"';
                    break;
                case 'defunciones/concesiones/listar':
                case 'defunciones/concesiones/agregar':
                case 'defunciones/concesiones/editar':
                case 'defunciones/concesiones/eliminar':
                case 'defunciones/concesiones/ver':
                    $c_de['operaciones_li'] = ' class="current-page active active-init"';
                    $c_de['operaciones_a'] = ' class="active"';
                    $c_de['operaciones_ul'] = ' child_menu_open';
                    $c_de['concesiones'] = ' class="sub_menu current-page"';
                    break;
                case 'defunciones/ornatos/listar':
                case 'defunciones/ornatos/agregar':
                case 'defunciones/ornatos/editar':
                case 'defunciones/ornatos/eliminar':
                case 'defunciones/ornatos/ver':
                    $c_de['operaciones_li'] = ' class="current-page active active-init"';
                    $c_de['operaciones_a'] = ' class="active"';
                    $c_de['operaciones_ul'] = ' child_menu_open';
                    $c_de['ornatos'] = ' class="sub_menu current-page"';
                    break;
                case 'defunciones/reducciones/listar':
                case 'defunciones/reducciones/agregar':
                case 'defunciones/reducciones/editar':
                case 'defunciones/reducciones/eliminar':
                case 'defunciones/reducciones/ver':
                    $c_de['operaciones_li'] = ' class="current-page active active-init"';
                    $c_de['operaciones_a'] = ' class="active"';
                    $c_de['operaciones_ul'] = ' child_menu_open';
                    $c_de['reducciones'] = ' class="sub_menu current-page"';
                    break;
                case 'defunciones/traslados/listar':
                case 'defunciones/traslados/agregar':
                case 'defunciones/traslados/editar':
                case 'defunciones/traslados/eliminar':
                case 'defunciones/traslados/ver':
                    $c_de['operaciones_li'] = ' class="current-page active active-init"';
                    $c_de['operaciones_a'] = ' class="active"';
                    $c_de['operaciones_ul'] = ' child_menu_open';
                    $c_de['traslados'] = ' class="sub_menu current-page"';
                    break;
                case 'defunciones/tramites/nuevo':
                case 'defunciones/tramites/nuevo_ver_dif':
                case 'defunciones/tramites/nuevo_ver_sol':
                    $c_de['tramite_nuevo'] = ' class="current-page"';
                    break;
                case 'defunciones/tramites/iniciar':
                    $c_de['tramite_iniciar'] = ' class="current-page"';
                    break;
                case 'defunciones/tramites/iniciar_compra':
                    $c_de['tramite_iniciar_compra'] = ' class="current-page"';
                    break;
                case 'defunciones/propietarios/listar':
                case 'defunciones/propietarios/agregar':
                case 'defunciones/propietarios/editar':
                case 'defunciones/propietarios/eliminar':
                case 'defunciones/propietarios/ver':
                    $c_de['propietarios'] = ' class="current-page"';
                    break;
                case 'defunciones/reportes/listar':
                case 'defunciones/reportes/grafico_operaciones':
                case 'defunciones/reportes/sin_boleta':
                case 'defunciones/reportes/sin_licencia':
                    $c_de['reportes'] = ' class="current-page"';
                    break;
                case 'defunciones/solicitantes/listar':
                case 'defunciones/solicitantes/agregar':
                case 'defunciones/solicitantes/editar':
                case 'defunciones/solicitantes/eliminar':
                case 'defunciones/solicitantes/ver':
                    $c_de['solicitantes'] = ' class="current-page"';
                    break;
                case 'defunciones/ubicaciones/listar':
                case 'defunciones/ubicaciones/agregar':
                case 'defunciones/ubicaciones/editar':
                case 'defunciones/ubicaciones/eliminar':
                case 'defunciones/ubicaciones/ver':
                    $c_de['ubicaciones'] = ' class="current-page"';
                    break;
                case 'defunciones/tablero/listar':
                    $c_de['tablero'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_de))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-university"></i> Defunciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_de['escritorio']) ? '' : $c_de['escritorio']) . '><a href="defunciones/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_de['cementerios']) ? '' : $c_de['cementerios']) . '><a href="defunciones/cementerios/listar">Cementerios</a></li>';
                $nav .= '<li' . (empty($c_de['cocherias']) ? '' : $c_de['cocherias']) . '><a href="defunciones/cocherias/listar">Cocherías</a></li>';
                $nav .= '<li' . (empty($c_de['constructores']) ? '' : $c_de['constructores']) . '><a href="defunciones/constructores/listar">Constructores</a></li>';
                $nav .= '<li' . (empty($c_de['difuntos']) ? '' : $c_de['difuntos']) . '><a href="defunciones/difuntos/listar">Difuntos</a></li>';
                $nav .= '<li' . (empty($c_de['expedientes']) ? '' : $c_de['expedientes']) . '><a href="defunciones/expedientes/listar">Expedientes</a></li>';
                $nav .= '<li' . (empty($c_de['expedientes_pjm']) ? '' : $c_de['expedientes_pjm']) . '><a href="defunciones/expedientes_pjm/listar">Expedientes PJM</a></li>';
                $nav .= '	<li' . (empty($c_de['operaciones_li']) ? '' : $c_de['operaciones_li']) . '>
										<a' . (empty($c_de['operaciones_a']) ? '' : $c_de['operaciones_a']) . '>Operaciones<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu' . (empty($c_de['operaciones_ul']) ? '' : $c_de['operaciones_ul']) . '">
                      <li' . (empty($c_de['operaciones']) ? '' : $c_de['operaciones']) . '><a href="defunciones/operaciones/listar">Operaciones</a></li>
                      <li' . (empty($c_de['concesiones']) ? '' : $c_de['concesiones']) . '><a href="defunciones/concesiones/listar">Concesiones</a></li>
                      <li' . (empty($c_de['ornatos']) ? '' : $c_de['ornatos']) . '><a href="defunciones/ornatos/listar">Ornatos</a></li>
                      <li' . (empty($c_de['reducciones']) ? '' : $c_de['reducciones']) . '><a href="defunciones/reducciones/listar">Reducciones</a></li>
                      <li' . (empty($c_de['traslados']) ? '' : $c_de['traslados']) . '><a href="defunciones/traslados/listar">Traslados</a></li>
                    </ul>
									</li>';
                $nav .= '<li' . (empty($c_de['tramite_nuevo']) ? '' : $c_de['tramite_nuevo']) . '><a href="defunciones/tramites/nuevo">Nuevo Trámite</a></li>';
                $nav .= '<li' . (empty($c_de['tramite_iniciar']) ? '' : $c_de['tramite_iniciar']) . '><a href="defunciones/tramites/iniciar">Iniciar Trámite</a></li>';
                $nav .= '<li' . (empty($c_de['propietarios']) ? '' : $c_de['propietarios']) . '><a href="defunciones/propietarios/listar">Propietarios</a></li>';
                $nav .= '<li' . (empty($c_de['reportes']) ? '' : $c_de['reportes']) . '><a href="defunciones/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_de['solicitantes']) ? '' : $c_de['solicitantes']) . '><a href="defunciones/solicitantes/listar">Solicitantes</a></li>';
                $nav .= '<li' . (empty($c_de['ubicaciones']) ? '' : $c_de['ubicaciones']) . '><a href="defunciones/ubicaciones/listar">Ubicaciones</a></li>';
                $nav .= '<li' . (empty($c_de['tablero']) ? '' : $c_de['tablero']) . '><a href="defunciones/tablero/listar">Vencimientos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-university"></i> Defunciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="defunciones/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="defunciones/cementerios/listar">Cementerios</a></li>';
                $nav .= '<li><a href="defunciones/cocherias/listar">Cocherías</a></li>';
                $nav .= '<li><a href="defunciones/constructores/listar">Constructores</a></li>';
                $nav .= '<li><a href="defunciones/difuntos/listar">Difuntos</a></li>';
                $nav .= '<li><a href="defunciones/expedientes/listar">Expedientes</a></li>';
                $nav .= '<li><a href="defunciones/expedientes_pjm/listar">Expedientes PJM</a></li>';
                $nav .= '	<li>
										<a>Operaciones<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li class="sub_menu"><a href="defunciones/operaciones/listar">Operaciones</a></li>
                      <li class="sub_menu"><a href="defunciones/concesiones/listar">Concesiones</a></li>
                      <li class="sub_menu"><a href="defunciones/ornatos/listar">Ornatos</a></li>
                      <li class="sub_menu"><a href="defunciones/reducciones/listar">Reducciones</a></li>
                      <li class="sub_menu"><a href="defunciones/traslados/listar">Traslados</a></li>
                    </ul>
									</li>';
                $nav .= '<li><a href="defunciones/tramites/nuevo">Nuevo Trámite</a></li>';
                $nav .= '<li><a href="defunciones/tramites/iniciar">Iniciar Trámite</a></li>';
                $nav .= '<li><a href="defunciones/propietarios/listar">Propietarios</a></li>';
                $nav .= '<li><a href="defunciones/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="defunciones/solicitantes/listar">Solicitantes</a></li>';
                $nav .= '<li><a href="defunciones/ubicaciones/listar">Ubicaciones</a></li>';
                $nav .= '<li><a href="defunciones/tablero/listar">Vencimientos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Desarrollo Social">
        $c_ds = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_desarrollo_social_consulta, $grupos) || in_groups($grupos_desarrollo_social_user, $grupos))
        {
            switch ($url_actual)
            {
                case 'desarrollo_social/escritorio':
                case 'desarrollo_social/escritorio/index':
                    $c_ds['escritorio'] = ' class="current-page"';
                    break;
                case 'desarrollo_social/articulos/listar':
                case 'desarrollo_social/articulos/agregar':
                case 'desarrollo_social/articulos/editar':
                case 'desarrollo_social/articulos/eliminar':
                case 'desarrollo_social/articulos/ver':
                    $c_ds['articulos'] = ' class="current-page"';
                    break;
                case 'desarrollo_social/compras/listar':
                case 'desarrollo_social/compras/agregar':
                case 'desarrollo_social/compras/editar':
                case 'desarrollo_social/compras/anular':
                case 'desarrollo_social/compras/ver':
                case 'desarrollo_social/compras/imprimir':
                    $c_ds['compras'] = ' class="current-page"';
                    break;
                case 'desarrollo_social/entregas/listar':
                case 'desarrollo_social/entregas/agregar':
                case 'desarrollo_social/entregas/editar':
                case 'desarrollo_social/entregas/anular':
                case 'desarrollo_social/entregas/ver':
                case 'desarrollo_social/entregas/imprimir':
                    $c_ds['entregas'] = ' class="current-page"';
                    break;
                case 'desarrollo_social/beneficiarios/listar':
                case 'desarrollo_social/beneficiarios/agregar':
                case 'desarrollo_social/beneficiarios/editar':
                case 'desarrollo_social/beneficiarios/eliminar':
                case 'desarrollo_social/beneficiarios/ver':
                    $c_ds['beneficiarios'] = ' class="current-page"';
                    break;
                case 'desarrollo_social/lugares/listar':
                case 'desarrollo_social/lugares/agregar':
                case 'desarrollo_social/lugares/editar':
                case 'desarrollo_social/lugares/eliminar':
                case 'desarrollo_social/lugares/ver':
                    $c_ds['lugares'] = ' class="current-page"';
                    break;
                case 'desarrollo_social/proveedores/listar':
                case 'desarrollo_social/proveedores/agregar':
                case 'desarrollo_social/proveedores/editar':
                case 'desarrollo_social/proveedores/eliminar':
                case 'desarrollo_social/proveedores/ver':
                    $c_ds['proveedores'] = ' class="current-page"';
                    break;
                case 'desarrollo_social/reportes/listar':
                case 'desarrollo_social/reportes/stock':
                case 'desarrollo_social/reportes/stock_critico':
                case 'desarrollo_social/reportes/entregas':
                case 'desarrollo_social/reportes/compras':
                case 'desarrollo_social/reportes/entregas_anuladas':
                case 'desarrollo_social/reportes/compras_anuladas':
                    $c_ds['reportes'] = ' class="current-page"';
                    break;
                case 'desarrollo_social/tipos_articulos/listar':
                case 'desarrollo_social/tipos_articulos/agregar':
                case 'desarrollo_social/tipos_articulos/editar':
                case 'desarrollo_social/tipos_articulos/eliminar':
                case 'desarrollo_social/tipos_articulos/ver':
                    $c_ds['tipos_articulos'] = ' class="current-page"';
                    break;
                case 'desarrollo_social/tipos_lugares/listar':
                case 'desarrollo_social/tipos_lugares/agregar':
                case 'desarrollo_social/tipos_lugares/editar':
                case 'desarrollo_social/tipos_lugares/eliminar':
                case 'desarrollo_social/tipos_lugares/ver':
                    $c_ds['tipos_lugares'] = ' class="current-page"';
                    break;
                case 'desarrollo_social/tipos_proveedores/listar':
                case 'desarrollo_social/tipos_proveedores/agregar':
                case 'desarrollo_social/tipos_proveedores/editar':
                case 'desarrollo_social/tipos_proveedores/eliminar':
                case 'desarrollo_social/tipos_proveedores/ver':
                    $c_ds['tipos_proveedores'] = ' class="current-page"';
                    break;
                case 'desarrollo_social/tipos_unidades/listar':
                case 'desarrollo_social/tipos_unidades/agregar':
                case 'desarrollo_social/tipos_unidades/editar':
                case 'desarrollo_social/tipos_unidades/eliminar':
                case 'desarrollo_social/tipos_unidades/ver':
                    $c_ds['tipos_unidades'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_ds))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-universal-access"></i> Desarrollo Social <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_ds['escritorio']) ? '' : $c_ds['escritorio']) . '><a href="desarrollo_social/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_ds['articulos']) ? '' : $c_ds['articulos']) . '><a href="desarrollo_social/articulos/listar">Artículos</a></li>';
                $nav .= '<li' . (empty($c_ds['beneficiarios']) ? '' : $c_ds['beneficiarios']) . '><a href="desarrollo_social/beneficiarios/listar">Beneficiarios</a></li>';
                $nav .= '<li' . (empty($c_ds['compras']) ? '' : $c_ds['compras']) . '><a href="desarrollo_social/compras/listar">Compras</a></li>';
                $nav .= '<li' . (empty($c_ds['entregas']) ? '' : $c_ds['entregas']) . '><a href="desarrollo_social/entregas/listar">Entregas</a></li>';
                $nav .= '<li' . (empty($c_ds['lugares']) ? '' : $c_ds['lugares']) . '><a href="desarrollo_social/lugares/listar">Lugares</a></li>';
                $nav .= '<li' . (empty($c_ds['proveedores']) ? '' : $c_ds['proveedores']) . '><a href="desarrollo_social/proveedores/listar">Proveedores</a></li>';
                $nav .= '<li' . (empty($c_ds['reportes']) ? '' : $c_ds['reportes']) . '><a href="desarrollo_social/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_ds['tipos_articulos']) ? '' : $c_ds['tipos_articulos']) . '><a href="desarrollo_social/tipos_articulos/listar">Tipos de Artículos</a></li>';
                $nav .= '<li' . (empty($c_ds['tipos_lugares']) ? '' : $c_ds['tipos_lugares']) . '><a href="desarrollo_social/tipos_lugares/listar">Tipos de Lugares</a></li>';
                $nav .= '<li' . (empty($c_ds['tipos_proveedores']) ? '' : $c_ds['tipos_proveedores']) . '><a href="desarrollo_social/tipos_proveedores/listar">Tipos de Proveedores</a></li>';
                $nav .= '<li' . (empty($c_ds['tipos_unidades']) ? '' : $c_ds['tipos_unidades']) . '><a href="desarrollo_social/tipos_unidades/listar">Tipos de Unidades</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-universal-access"></i> Desarrollo Social <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="desarrollo_social/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="desarrollo_social/articulos/listar">Artículos</a></li>';
                $nav .= '<li><a href="desarrollo_social/beneficiarios/listar">Beneficiarios</a></li>';
                $nav .= '<li><a href="desarrollo_social/compras/listar">Compras</a></li>';
                $nav .= '<li><a href="desarrollo_social/entregas/listar">Entregas</a></li>';
                $nav .= '<li><a href="desarrollo_social/lugares/listar">Lugares</a></li>';
                $nav .= '<li><a href="desarrollo_social/proveedores/listar">Proveedores</a></li>';
                $nav .= '<li><a href="desarrollo_social/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="desarrollo_social/tipos_articulos/listar">Tipos de Artículos</a></li>';
                $nav .= '<li><a href="desarrollo_social/tipos_lugares/listar">Tipos de Lugares</a></li>';
                $nav .= '<li><a href="desarrollo_social/tipos_proveedores/listar">Tipos de Proveedores</a></li>';
                $nav .= '<li><a href="desarrollo_social/tipos_unidades/listar">Tipos de Unidades</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Gobierno">
        $c_go = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_gobierno_consulta, $grupos))
        {
            switch ($url_actual)
            {
                case 'gobierno/escritorio':
                case 'gobierno/escritorio/index':
                    $c_go['escritorio'] = ' class="current-page"';
                    break;
                case 'gobierno/documentos/listar_decretos':
                case 'gobierno/documentos/agregar_decreto':
                case 'gobierno/documentos/editar_decreto':
                case 'gobierno/documentos/anular_decreto':
                case 'gobierno/documentos/ver_decreto':
                    $c_go['decretos'] = ' class="current-page"';
                    break;
                case 'gobierno/documentos/listar':
                case 'gobierno/documentos/agregar':
                case 'gobierno/documentos/editar':
                case 'gobierno/documentos/anular':
                case 'gobierno/documentos/ver':
                    $c_go['documentos'] = ' class="current-page"';
                    break;
                case 'gobierno/numeraciones/listar':
                case 'gobierno/numeraciones/agregar':
                case 'gobierno/numeraciones/editar':
                case 'gobierno/numeraciones/eliminar':
                case 'gobierno/numeraciones/ver':
                    $c_go['numeraciones'] = ' class="current-page"';
                    break;
                case 'gobierno/partes/listar':
                case 'gobierno/partes/agregar':
                case 'gobierno/partes/editar':
                case 'gobierno/partes/eliminar':
                case 'gobierno/partes/ver':
                    $c_go['partes'] = ' class="current-page"';
                    break;
                case 'gobierno/tipos_documentos/listar':
                case 'gobierno/tipos_documentos/agregar':
                case 'gobierno/tipos_documentos/editar':
                case 'gobierno/tipos_documentos/eliminar':
                case 'gobierno/tipos_documentos/ver':
                    $c_go['tipos_documentos'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_go))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-file-text-o"></i> Gobierno <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_go['escritorio']) ? '' : $c_go['escritorio']) . '><a href="gobierno/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_go['decretos']) ? '' : $c_go['decretos']) . '><a href="gobierno/documentos/listar_decretos">Decretos</a></li>';
                $nav .= '<li' . (empty($c_go['documentos']) ? '' : $c_go['documentos']) . '><a href="gobierno/documentos/listar">Documentos</a></li>';
                $nav .= '<li' . (empty($c_go['numeraciones']) ? '' : $c_go['numeraciones']) . '><a href="gobierno/numeraciones/listar">Numeraciones</a></li>';
                $nav .= '<li' . (empty($c_go['partes']) ? '' : $c_go['partes']) . '><a href="gobierno/partes/listar">Partes</a></li>';
                $nav .= '<li' . (empty($c_go['tipos_documentos']) ? '' : $c_go['tipos_documentos']) . '><a href="gobierno/tipos_documentos/listar">Tipos de Documentos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-file-text-o"></i> Gobierno <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="gobierno/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="gobierno/documentos/listar_decretos">Decretos</a></li>';
                $nav .= '<li><a href="gobierno/documentos/listar">Documentos</a></li>';
                $nav .= '<li><a href="gobierno/numeraciones/listar">Numeraciones</a></li>';
                $nav .= '<li><a href="gobierno/partes/listar">Partes</a></li>';
                $nav .= '<li><a href="gobierno/tipos_documentos/listar">Tipos de Documentos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_gobierno_user, $grupos))
        {
            switch ($url_actual)
            {
                case 'gobierno/escritorio':
                case 'gobierno/escritorio/index':
                    $c_go['escritorio'] = ' class="current-page"';
                    break;
                case 'gobierno/documentos/listar_decretos':
                case 'gobierno/documentos/agregar_decreto':
                case 'gobierno/documentos/editar_decreto':
                case 'gobierno/documentos/anular_decreto':
                case 'gobierno/documentos/ver_decretos':
                    $c_go['decretos'] = ' class="current-page"';
                    break;
                case 'gobierno/documentos/listar':
                case 'gobierno/documentos/agregar':
                case 'gobierno/documentos/editar':
                case 'gobierno/documentos/anular':
                case 'gobierno/documentos/ver':
                    $c_go['documentos'] = ' class="current-page"';
                    break;
                case 'gobierno/numeraciones/listar':
                case 'gobierno/numeraciones/agregar':
                case 'gobierno/numeraciones/editar':
                case 'gobierno/numeraciones/eliminar':
                case 'gobierno/numeraciones/ver':
                    $c_go['numeraciones'] = ' class="current-page"';
                    break;
                case 'gobierno/partes/listar':
                case 'gobierno/partes/agregar':
                case 'gobierno/partes/editar':
                case 'gobierno/partes/eliminar':
                case 'gobierno/partes/ver':
                    $c_go['partes'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_go))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-file-text-o"></i> Gobierno <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_go['escritorio']) ? '' : $c_go['escritorio']) . '><a href="gobierno/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_go['decretos']) ? '' : $c_go['decretos']) . '><a href="gobierno/documentos/listar_decretos">Decretos</a></li>';
                $nav .= '<li' . (empty($c_go['documentos']) ? '' : $c_go['documentos']) . '><a href="gobierno/documentos/listar">Documentos</a></li>';
                $nav .= '<li' . (empty($c_go['numeraciones']) ? '' : $c_go['numeraciones']) . '><a href="gobierno/numeraciones/listar">Numeraciones</a></li>';
                $nav .= '<li' . (empty($c_go['partes']) ? '' : $c_go['partes']) . '><a href="gobierno/partes/listar">Partes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-file-text-o"></i> Gobierno <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="gobierno/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="gobierno/documentos/listar_decretos">Decretos</a></li>';
                $nav .= '<li><a href="gobierno/documentos/listar">Documentos</a></li>';
                $nav .= '<li><a href="gobierno/numeraciones/listar">Numeraciones</a></li>';
                $nav .= '<li><a href="gobierno/partes/listar">Partes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Incidencias">
        $c_in = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_incidencias_consulta, $grupos) || in_groups($grupos_incidencias_admin, $grupos))
        {
            switch ($url_actual)
            {
                case 'incidencias/escritorio':
                case 'incidencias/escritorio/index':
                    $c_in['escritorio'] = ' class="current-page"';
                    break;
                case 'incidencias/categorias/listar':
                case 'incidencias/categorias/agregar':
                case 'incidencias/categorias/editar':
                case 'incidencias/categorias/ver':
                case 'incidencias/categorias/anular':
                    $c_in['categorias'] = ' class="current-page"';
                    break;
                case 'incidencias/incidencias/listar':
                case 'incidencias/incidencias/agregar':
                case 'incidencias/incidencias/editar':
                case 'incidencias/incidencias/ver':
                case 'incidencias/incidencias/anular':
                    $c_in['incidencias'] = ' class="current-page"';
                    break;
                case 'incidencias/reportes/listar':
                case 'incidencias/reportes/incidencias':
                    $c_in['reportes'] = ' class="current-page"';
                    break;
                case 'incidencias/sectores/listar':
                case 'incidencias/sectores/agregar':
                case 'incidencias/sectores/editar':
                case 'incidencias/sectores/ver':
                case 'incidencias/sectores/eliminar':
                    $c_in['sectores'] = ' class="current-page"';
                    break;
                case 'incidencias/usuarios_areas/listar':
                case 'incidencias/usuarios_areas/agregar':
                case 'incidencias/usuarios_areas/editar':
                case 'incidencias/usuarios_areas/ver':
                case 'incidencias/usuarios_areas/eliminar':
                    $c_in['usuarios_areas'] = ' class="current-page"';
                    break;
                case 'incidencias/usuarios_sectores/listar':
                case 'incidencias/usuarios_sectores/agregar':
                case 'incidencias/usuarios_sectores/editar':
                case 'incidencias/usuarios_sectores/ver':
                case 'incidencias/usuarios_sectores/eliminar':
                    $c_in['usuarios_sectores'] = ' class="current-page"';
                    break;
                case 'incidencias/manuales':
                case 'incidencias/manuales/index':
                    $c_in['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_in))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-warning"></i> Incidencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_in['escritorio']) ? '' : $c_in['escritorio']) . '><a href="incidencias/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_in['categorias']) ? '' : $c_in['categorias']) . '><a href="incidencias/categorias/listar">Categorías</a></li>';
                $nav .= '<li' . (empty($c_in['incidencias']) ? '' : $c_in['incidencias']) . '><a href="incidencias/incidencias/listar">Incidencias</a></li>';
                $nav .= '<li' . (empty($c_in['reportes']) ? '' : $c_in['reportes']) . '><a href="incidencias/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_in['sectores']) ? '' : $c_in['sectores']) . '><a href="incidencias/sectores/listar">Sectores</a></li>';
                $nav .= '<li' . (empty($c_in['usuarios_areas']) ? '' : $c_in['usuarios_areas']) . '><a href="incidencias/usuarios_areas/listar">Usuarios por Area</a></li>';
                $nav .= '<li' . (empty($c_in['usuarios_sectores']) ? '' : $c_in['usuarios_sectores']) . '><a href="incidencias/usuarios_sectores/listar">Usuarios por Sector</a></li>';
                $nav .= '<li' . (empty($c_in['manuales']) ? '' : $c_in['manuales']) . '><a href="incidencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-warning"></i> Incidencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="incidencias/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="incidencias/categorias/listar">Categorías</a></li>';
                $nav .= '<li><a href="incidencias/incidencias/listar">Incidencias</a></li>';
                $nav .= '<li><a href="incidencias/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="incidencias/sectores/listar">Sectores</a></li>';
                $nav .= '<li><a href="incidencias/usuarios_areas/listar">Usuarios por Area</a></li>';
                $nav .= '<li><a href="incidencias/usuarios_sectores/listar">Usuarios por Sector</a></li>';
                $nav .= '<li><a href="incidencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_incidencias_user, $grupos))  //TECNICOS
        {
            switch ($url_actual)
            {
                case 'incidencias/escritorio':
                case 'incidencias/escritorio/index':
                    $c_in['escritorio'] = ' class="current-page"';
                    break;
                case 'incidencias/incidencias/listar_area':
                case 'incidencias/incidencias/agregar_area':
                case 'incidencias/incidencias/editar_area':
                case 'incidencias/incidencias/ver_area':
                    $c_in['incidencias_area'] = ' class="current-page"';
                    break;
                case 'incidencias/incidencias/listar_tecnico':
                case 'incidencias/incidencias/agregar_tecnico':
                case 'incidencias/incidencias/editar_tecnico':
                case 'incidencias/incidencias/ver_tecnico':
                    $c_in['incidencias'] = ' class="current-page"';
                    break;
                case 'incidencias/manuales':
                case 'incidencias/manuales/index':
                    $c_in['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_in))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-warning"></i> Incidencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_in['escritorio']) ? '' : $c_in['escritorio']) . '><a href="incidencias/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_in['incidencias_area']) ? '' : $c_in['incidencias_area']) . '><a href="incidencias/incidencias/listar_area">Incidencias Solicitadas</a></li>';
                $nav .= '<li' . (empty($c_in['incidencias']) ? '' : $c_in['incidencias']) . '><a href="incidencias/incidencias/listar_tecnico">Incidencias Recibidas</a></li>';
                $nav .= '<li' . (empty($c_in['manuales']) ? '' : $c_in['manuales']) . '><a href="incidencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-warning"></i> Incidencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="incidencias/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="incidencias/incidencias/listar_area">Incidencias Solicitadas</a></li>';
                $nav .= '<li><a href="incidencias/incidencias/listar_tecnico">Incidencias Recibidas</a></li>';
                $nav .= '<li><a href="incidencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_incidencias_area, $grupos))
        {
            switch ($url_actual)
            {
                case 'incidencias/escritorio':
                case 'incidencias/escritorio/index':
                    $c_in['escritorio'] = ' class="current-page"';
                    break;
                case 'incidencias/incidencias/listar_area':
                case 'incidencias/incidencias/agregar_area':
                case 'incidencias/incidencias/editar_area':
                case 'incidencias/incidencias/ver_area':
                    $c_in['incidencias'] = ' class="current-page"';
                    break;
                case 'incidencias/manuales':
                case 'incidencias/manuales/index':
                    $c_in['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_in))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-warning"></i> Incidencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_in['escritorio']) ? '' : $c_in['escritorio']) . '><a href="incidencias/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_in['incidencias']) ? '' : $c_in['incidencias']) . '><a href="incidencias/incidencias/listar_area">Incidencias Solicitadas</a></li>';
                $nav .= '<li' . (empty($c_in['manuales']) ? '' : $c_in['manuales']) . '><a href="incidencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-warning"></i> Incidencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="incidencias/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="incidencias/incidencias/listar_area">Incidencias Solicitadas</a></li>';
                $nav .= '<li><a href="incidencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        //<editor-fold defaultstate="collapsed" desc="Permisos Menú Luján Pass">
        $c_lp = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_lujan_pass_consulta, $grupos))
        {
            switch ($controlador)
            {
                case 'lujan_pass/escritorio':
                case 'lujan_pass/escritorio/index':
                    $c_lp['escritorio'] = ' class="current-page"';
                    break;
                case 'lujan_pass/campanias':
                    $c_lp['campanias'] = ' class="current-page"';
                    break;
                case 'lujan_pass/categorias':
                    $c_lp['categorias'] = ' class="current-page"';
                    break;
                case 'lujan_pass/comercios':
                    $c_lp['comercios'] = ' class="current-page"';
                    break;
                case 'lujan_pass/promociones':
                    $c_lp['promociones'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_lp))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-map-marker"></i> Luján Pass <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_lp['escritorio']) ? '' : $c_lp['escritorio']) . '><a href="lujan_pass/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="lujan_pass/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li' . (empty($c_lp['campanias']) ? '' : $c_lp['campanias']) . '><a href="lujan_pass/campanias/listar">Campañas</a></li>';
                $nav .= '<li' . (empty($c_lp['categorias']) ? '' : $c_lp['categorias']) . '><a href="lujan_pass/categorias/listar">Categorias</a></li>';
                $nav .= '<li' . (empty($c_lp['comercios']) ? '' : $c_lp['comercios']) . '><a href="lujan_pass/comercios/listar">Comercios</a></li>';
                $nav .= '<li' . (empty($c_lp['promociones']) ? '' : $c_lp['promociones']) . '><a href="lujan_pass/promociones/listar">Descuentos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-map-marker"></i> Luján Pass <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="lujan_pass/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="lujan_pass/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li><a href="lujan_pass/campanias/listar">Campañas</a></li>';
                $nav .= '<li><a href="lujan_pass/categorias/listar">Categorías</a></li>';
                $nav .= '<li><a href="lujan_pass/comercios/listar">Comercios</a></li>';
                $nav .= '<li><a href="lujan_pass/promociones/listar">Descuentos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_lujan_pass_control, $grupos))
        {
            switch ($controlador)
            {
                case 'lujan_pass/escritorio':
                case 'lujan_pass/escritorio/index':
                    $c_lp['escritorio'] = ' class="current-page"';
                    break;
                case 'lujan_pass/comercios':
                    $c_lp['comercios'] = ' class="current-page"';
                    break;
                case 'lujan_pass/promociones':
                    $c_lp['promociones'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_lp))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-map-marker"></i> Luján Pass <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_lp['escritorio']) ? '' : $c_lp['escritorio']) . '><a href="lujan_pass/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="lujan_pass/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li' . (empty($c_lp['comercios']) ? '' : $c_lp['comercios']) . '><a href="lujan_pass/comercios/listar">Comercios</a></li>';
                $nav .= '<li' . (empty($c_lp['promociones']) ? '' : $c_lp['promociones']) . '><a href="lujan_pass/promociones/listar">Descuentos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-map-marker"></i> Luján Pass <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="lujan_pass/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="lujan_pass/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li><a href="lujan_pass/comercios/listar">Comercios</a></li>';
                $nav .= '<li><a href="lujan_pass/promociones/listar">Descuentos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_lujan_pass_publico, $grupos))
        {
            switch ($controlador)
            {
                case 'lujan_pass/escritorio':
                case 'lujan_pass/escritorio/index':
                    $c_lp['escritorio'] = ' class="current-page"';
                    break;
                case 'lujan_pass/comercios':
                    $c_lp['comercios'] = ' class="current-page"';
                    break;
                case 'lujan_pass/promociones':
                    $c_lp['promociones'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_lp))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-map-marker"></i> Luján Pass <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_lp['escritorio']) ? '' : $c_lp['escritorio']) . '><a href="lujan_pass/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="lujan_pass/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li' . (empty($c_lp['comercios']) ? '' : $c_lp['comercios']) . '><a href="lujan_pass/comercios/listar">Comercios</a></li>';
                $nav .= '<li' . (empty($c_lp['promociones']) ? '' : $c_lp['promociones']) . '><a href="lujan_pass/promociones/listar">Descuentos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-map-marker"></i> Luján Pass <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="lujan_pass/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="lujan_pass/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li><a href="lujan_pass/comercios/listar">Comercios</a></li>';
                $nav .= '<li><a href="lujan_pass/promociones/listar">Descuentos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_lujan_pass_beneficiario, $grupos))
        {
            switch ($controlador)
            {
                case 'lujan_pass/escritorio':
                case 'lujan_pass/escritorio/index':
                    $c_lp['escritorio'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_lp))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-map-marker"></i> Luján Pass <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_lp['escritorio']) ? '' : $c_lp['escritorio']) . '><a href="lujan_pass/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="lujan_pass/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-map-marker"></i> Luján Pass <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="lujan_pass/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="lujan_pass/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        //<editor-fold defaultstate="collapsed" desc="Permisos Menú M@jor">
        $c_maj = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_major_boletos, $grupos) || in_groups($grupos_major_deudas, $grupos) || in_groups($grupos_major_consulta, $grupos))
        {
            switch ($controlador)
            {
                case 'major/escritorio':
                case 'major/escritorio/index':
                    $c_maj['escritorio'] = ' class="current-page"';
                    break;
                case 'major/boletos':
                    $c_maj['boletos'] = ' class="current-page"';
                    break;
                case 'major/deudas':
                    $c_maj['deudas'] = ' class="current-page"';
                    break;
                case 'major/deudas_masivas':
                    $c_maj['deudas_masivas'] = ' class="current-page"';
                    break;
                case 'major/solicitudes':
                    $c_maj['solicitudes'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_maj))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-at"></i> M@jor <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_maj['escritorio']) ? '' : $c_maj['escritorio']) . '><a href="major/escritorio">Inicio</a></li>';
                if (in_groups($grupos_admin, $grupos) || in_groups($grupos_major_boletos, $grupos) || in_groups($grupos_major_consulta, $grupos))
                {
                    $nav .= '<li' . (empty($c_maj['boletos']) ? '' : $c_maj['boletos']) . '><a href="major/boletos">Boletos</a></li>';
                }
                if (in_groups($grupos_admin, $grupos) || in_groups($grupos_major_deudas, $grupos) || in_groups($grupos_major_consulta, $grupos))
                {
                    $nav .= '<li' . (empty($c_maj['deudas']) ? '' : $c_maj['deudas']) . '><a href="major/deudas">Deudas</a></li>';
                }
                if (in_groups($grupos_admin, $grupos) || in_groups($grupos_major_deudas_masivas, $grupos) || in_groups($grupos_major_consulta, $grupos))
                {
                    $nav .= '<li' . (empty($c_maj['deudas_masivas']) ? '' : $c_maj['deudas_masivas']) . '><a href="major/deudas_masivas">Deudas Masivas</a></li>';
                }
                if (in_groups($grupos_admin, $grupos) || in_groups($grupos_major_solicitudes, $grupos) || in_groups($grupos_major_consulta, $grupos))
                {
                    $nav .= '<li' . (empty($c_maj['solicitudes']) ? '' : $c_maj['solicitudes']) . '><a href="major/solicitudes/listar">Solicitudes</a></li>';
                }
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-at"></i> M@jor <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="major/escritorio">Inicio</a></li>';
                if (in_groups($grupos_admin, $grupos) || in_groups($grupos_major_boletos, $grupos) || in_groups($grupos_major_consulta, $grupos))
                {
                    $nav .= '<li><a href="major/boletos">Boletos</a></li>';
                }
                if (in_groups($grupos_admin, $grupos) || in_groups($grupos_major_deudas, $grupos) || in_groups($grupos_major_consulta, $grupos))
                {
                    $nav .= '<li><a href="major/deudas">Deudas</a></li>';
                }
                if (in_groups($grupos_admin, $grupos) || in_groups($grupos_major_deudas_masivas, $grupos) || in_groups($grupos_major_consulta, $grupos))
                {
                    $nav .= '<li><a href="major/deudas_masivas">Deudas Masivas</a></li>';
                }
                if (in_groups($grupos_admin, $grupos) || in_groups($grupos_major_solicitudes, $grupos) || in_groups($grupos_major_consulta, $grupos))
                {
                    $nav .= '<li><a href="major/solicitudes/listar">Solicitudes</a></li>';
                }
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        //<editor-fold defaultstate="collapsed" desc="Permisos Menú Más Beneficios">
        $c_mb = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_mas_beneficios_consulta, $grupos))
        {
            switch ($controlador)
            {
                case 'mas_beneficios/escritorio':
                case 'mas_beneficios/escritorio/index':
                    $c_mb['escritorio'] = ' class="current-page"';
                    break;
                case 'mas_beneficios/campanias':
                    $c_mb['campanias'] = ' class="current-page"';
                    break;
                case 'mas_beneficios/categorias':
                    $c_mb['categorias'] = ' class="current-page"';
                    break;
                case 'mas_beneficios/comercios':
                    $c_mb['comercios'] = ' class="current-page"';
                    break;
                case 'mas_beneficios/promociones':
                    $c_mb['promociones'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_mb))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-plus"></i> Más Beneficios <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_mb['escritorio']) ? '' : $c_mb['escritorio']) . '><a href="mas_beneficios/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="mas_beneficios/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li' . (empty($c_mb['campanias']) ? '' : $c_mb['campanias']) . '><a href="mas_beneficios/campanias/listar">Campañas</a></li>';
                $nav .= '<li' . (empty($c_mb['categorias']) ? '' : $c_mb['categorias']) . '><a href="mas_beneficios/categorias/listar">Categorias</a></li>';
                $nav .= '<li' . (empty($c_mb['comercios']) ? '' : $c_mb['comercios']) . '><a href="mas_beneficios/comercios/listar">Comercios</a></li>';
                $nav .= '<li' . (empty($c_mb['promociones']) ? '' : $c_mb['promociones']) . '><a href="mas_beneficios/promociones/listar">Promociones</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-plus"></i> Más Beneficios <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="mas_beneficios/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="mas_beneficios/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li><a href="mas_beneficios/campanias/listar">Campañas</a></li>';
                $nav .= '<li><a href="mas_beneficios/categorias/listar">Categorías</a></li>';
                $nav .= '<li><a href="mas_beneficios/comercios/listar">Comercios</a></li>';
                $nav .= '<li><a href="mas_beneficios/promociones/listar">Promociones</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_mas_beneficios_control, $grupos))
        {
            switch ($controlador)
            {
                case 'mas_beneficios/escritorio':
                case 'mas_beneficios/escritorio/index':
                    $c_mb['escritorio'] = ' class="current-page"';
                    break;
                case 'mas_beneficios/comercios':
                    $c_mb['comercios'] = ' class="current-page"';
                    break;
                case 'mas_beneficios/promociones':
                    $c_mb['promociones'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_mb))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-plus"></i> Más Beneficios <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_mb['escritorio']) ? '' : $c_mb['escritorio']) . '><a href="mas_beneficios/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="mas_beneficios/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li' . (empty($c_mb['comercios']) ? '' : $c_mb['comercios']) . '><a href="mas_beneficios/comercios/listar">Comercios</a></li>';
                $nav .= '<li' . (empty($c_mb['promociones']) ? '' : $c_mb['promociones']) . '><a href="mas_beneficios/promociones/listar">Promociones</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-plus"></i> Más Beneficios <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="mas_beneficios/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="mas_beneficios/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li><a href="mas_beneficios/comercios/listar">Comercios</a></li>';
                $nav .= '<li><a href="mas_beneficios/promociones/listar">Promociones</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_mas_beneficios_publico, $grupos))
        {
            switch ($controlador)
            {
                case 'mas_beneficios/escritorio':
                case 'mas_beneficios/escritorio/index':
                    $c_mb['escritorio'] = ' class="current-page"';
                    break;
                case 'mas_beneficios/comercios':
                    $c_mb['comercios'] = ' class="current-page"';
                    break;
                case 'mas_beneficios/promociones':
                    $c_mb['promociones'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_mb))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-plus"></i> Más Beneficios <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_mb['escritorio']) ? '' : $c_mb['escritorio']) . '><a href="mas_beneficios/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="mas_beneficios/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li' . (empty($c_mb['comercios']) ? '' : $c_mb['comercios']) . '><a href="mas_beneficios/comercios/listar">Comercios</a></li>';
                $nav .= '<li' . (empty($c_mb['promociones']) ? '' : $c_mb['promociones']) . '><a href="mas_beneficios/promociones/listar">Promociones</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-plus"></i> Más Beneficios <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="mas_beneficios/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="mas_beneficios/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '<li><a href="mas_beneficios/comercios/listar">Comercios</a></li>';
                $nav .= '<li><a href="mas_beneficios/promociones/listar">Promociones</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_mas_beneficios_beneficiario, $grupos))
        {
            switch ($controlador)
            {
                case 'mas_beneficios/escritorio':
                case 'mas_beneficios/escritorio/index':
                    $c_mb['escritorio'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_mb))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-plus"></i> Más Beneficios <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_mb['escritorio']) ? '' : $c_mb['escritorio']) . '><a href="mas_beneficios/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="mas_beneficios/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-plus"></i> Más Beneficios <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="mas_beneficios/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="mas_beneficios/front/inicio" target="_blank">Ver Sitio</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Niñez y Adolescencia">
        $c_na = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_ninez_adolescencia_consulta, $grupos) || in_groups($grupos_ninez_adolescencia_admin, $grupos))
        {
            switch ($url_actual)
            {
                case 'ninez_adolescencia/escritorio':
                case 'ninez_adolescencia/escritorio/index':
                    $c_na['escritorio'] = ' class="current-page"';
                    break;
                case 'ninez_adolescencia/expedientes/buscador':
                    $c_na['buscador'] = ' class="current-page"';
                    break;
                case 'ninez_adolescencia/efectores/listar':
                case 'ninez_adolescencia/efectores/agregar':
                case 'ninez_adolescencia/efectores/editar':
                case 'ninez_adolescencia/efectores/ver':
                case 'ninez_adolescencia/efectores/eliminar':
                    $c_na['efectores'] = ' class="current-page"';
                    break;
                case 'ninez_adolescencia/expedientes/listar':
                case 'ninez_adolescencia/expedientes/agregar':
                case 'ninez_adolescencia/expedientes/editar':
                case 'ninez_adolescencia/expedientes/ver':
                case 'ninez_adolescencia/expedientes/eliminar':
                case 'ninez_adolescencia/adultos_responsables/agregar':
                case 'ninez_adolescencia/adultos_responsables/editar':
                case 'ninez_adolescencia/adultos_responsables/ver':
                case 'ninez_adolescencia/adultos_responsables/eliminar':
                case 'ninez_adolescencia/menores/agregar':
                case 'ninez_adolescencia/menores/editar':
                case 'ninez_adolescencia/menores/ver':
                case 'ninez_adolescencia/menores/eliminar':
                case 'ninez_adolescencia/intervenciones/agregar':
                case 'ninez_adolescencia/intervenciones/editar':
                case 'ninez_adolescencia/intervenciones/ver':
                case 'ninez_adolescencia/intervenciones/eliminar':
                case 'ninez_adolescencia/adjuntos/agregar':
                case 'ninez_adolescencia/adjuntos/adjunto':
                case 'ninez_adolescencia/adjuntos/eliminar':
                    $c_na['expedientes'] = ' class="current-page"';
                    break;
                case 'ninez_adolescencia/motivos/listar':
                case 'ninez_adolescencia/motivos/agregar':
                case 'ninez_adolescencia/motivos/editar':
                case 'ninez_adolescencia/motivos/ver':
                case 'ninez_adolescencia/motivos/eliminar':
                    $c_na['motivos'] = ' class="current-page"';
                    break;
                case 'ninez_adolescencia/parentezcos_personas/listar':
                case 'ninez_adolescencia/parentezcos_personas/agregar':
                case 'ninez_adolescencia/parentezcos_personas/editar':
                case 'ninez_adolescencia/parentezcos_personas/ver':
                case 'ninez_adolescencia/parentezcos_personas/eliminar':
                    $c_na['parentezcos_personas'] = ' class="current-page"';
                    break;
                case 'ninez_adolescencia/tipos_adjuntos/listar':
                case 'ninez_adolescencia/tipos_adjuntos/agregar':
                case 'ninez_adolescencia/tipos_adjuntos/editar':
                case 'ninez_adolescencia/tipos_adjuntos/ver':
                case 'ninez_adolescencia/tipos_adjuntos/eliminar':
                    $c_na['tipos_adjuntos'] = ' class="current-page"';
                    break;
                case 'ninez_adolescencia/tipos_intervenciones/listar':
                case 'ninez_adolescencia/tipos_intervenciones/agregar':
                case 'ninez_adolescencia/tipos_intervenciones/editar':
                case 'ninez_adolescencia/tipos_intervenciones/ver':
                case 'ninez_adolescencia/tipos_intervenciones/eliminar':
                    $c_na['tipos_intervenciones'] = ' class="current-page"';
                    break;
                case 'ninez_adolescencia/tipos_parentezcos/listar':
                case 'ninez_adolescencia/tipos_parentezcos/agregar':
                case 'ninez_adolescencia/tipos_parentezcos/editar':
                case 'ninez_adolescencia/tipos_parentezcos/ver':
                case 'ninez_adolescencia/tipos_parentezcos/eliminar':
                    $c_na['tipos_parentezcos'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_na))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-child"></i> Niñez y Adolescencia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_na['escritorio']) ? '' : $c_na['escritorio']) . '><a href="ninez_adolescencia/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_na['buscador']) ? '' : $c_na['buscador']) . '><a href="ninez_adolescencia/expedientes/buscador">Buscador</a></li>';
                $nav .= '<li' . (empty($c_na['efectores']) ? '' : $c_na['efectores']) . '><a href="ninez_adolescencia/efectores/listar">Efectores</a></li>';
                $nav .= '<li' . (empty($c_na['expedientes']) ? '' : $c_na['expedientes']) . '><a href="ninez_adolescencia/expedientes/listar">Expedientes</a></li>';
                $nav .= '<li' . (empty($c_na['motivos']) ? '' : $c_na['motivos']) . '><a href="ninez_adolescencia/motivos/listar">Motivos</a></li>';
                $nav .= '<li' . (empty($c_na['parentezcos_personas']) ? '' : $c_na['parentezcos_personas']) . '><a href="ninez_adolescencia/parentezcos_personas/listar">Parentezcos</a></li>';
                $nav .= '<li' . (empty($c_na['tipos_adjuntos']) ? '' : $c_na['tipos_adjuntos']) . '><a href="ninez_adolescencia/tipos_adjuntos/listar">Tipos de Adjuntos</a></li>';
                $nav .= '<li' . (empty($c_na['tipos_intervenciones']) ? '' : $c_na['tipos_intervenciones']) . '><a href="ninez_adolescencia/tipos_intervenciones/listar">Tipos de Intervenciones</a></li>';
                $nav .= '<li' . (empty($c_na['tipos_parentezcos']) ? '' : $c_na['tipos_parentezcos']) . '><a href="ninez_adolescencia/tipos_parentezcos/listar">Tipos de Parentezcos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-child"></i> Niñez y Adolescencia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="ninez_adolescencia/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="ninez_adolescencia/expedientes/buscador">Buscador</a></li>';
                $nav .= '<li><a href="ninez_adolescencia/efectores/listar">Efectores</a></li>';
                $nav .= '<li><a href="ninez_adolescencia/expedientes/listar">Expedientes</a></li>';
                $nav .= '<li><a href="ninez_adolescencia/motivos/listar">Motivos</a></li>';
                $nav .= '<li><a href="ninez_adolescencia/parentezcos_personas/listar">Parentezcos</a></li>';
                $nav .= '<li><a href="ninez_adolescencia/tipos_adjuntos/listar">Tipos de Adjuntos</a></li>';
                $nav .= '<li><a href="ninez_adolescencia/tipos_intervenciones/listar">Tipos de Intervenciones</a></li>';
                $nav .= '<li><a href="ninez_adolescencia/tipos_parentezcos/listar">Tipos de Parentezcos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Notificaciones">
        $c_not = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_notificaciones_user, $grupos))
        {
            switch ($controlador)
            {
                case 'notificaciones/escritorio':
                case 'notificaciones/escritorio/index':
                    $c_not['escritorio'] = ' class="current-page"';
                    break;
                case 'notificaciones/cedulas':
                    $c_not['cedulas'] = ' class="current-page"';
                    break;
                case 'notificaciones/hojas_rutas':
                    $c_not['hojas_rutas'] = ' class="current-page"';
                    break;
                case 'notificaciones/tipos_documentos':
                    $c_not['tipos_documentos'] = ' class="current-page"';
                    break;
                case 'notificaciones/usuarios_areas':
                    $c_not['usuarios_areas'] = ' class="current-page"';
                    break;
                case 'notificaciones/zonas':
                    $c_not['zonas'] = ' class="current-page"';
                    break;
                case 'notificaciones/manuales':
                    $c_not['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_not))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-file-o"></i> Notificaciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_not['escritorio']) ? '' : $c_not['escritorio']) . '><a href="notificaciones/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_not['cedulas']) ? '' : $c_not['cedulas']) . '><a href="notificaciones/cedulas/listar">Cedulas</a></li>';
                $nav .= '<li' . (empty($c_not['hojas_rutas']) ? '' : $c_not['hojas_rutas']) . '><a href="notificaciones/hojas_rutas/listar">Hojas de Ruta</a></li>';
                $nav .= '<li' . (empty($c_not['tipos_documentos']) ? '' : $c_not['tipos_documentos']) . '><a href="notificaciones/tipos_documentos/listar">Tipos de Documento</a></li>';
                $nav .= '<li' . (empty($c_not['usuarios_areas']) ? '' : $c_not['usuarios_areas']) . '><a href="notificaciones/usuarios_areas/listar">Usuarios por Area</a></li>';
                $nav .= '<li' . (empty($c_not['zonas']) ? '' : $c_not['zonas']) . '><a href="notificaciones/zonas/listar">Zonas</a></li>';
                $nav .= '<li' . (empty($c_not['manual']) ? '' : $c_not['manual']) . '><a href="notificaciones/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-file-o"></i> Notificaciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="notificaciones/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="notificaciones/cedulas/listar">Cedulas</a></li>';
                $nav .= '<li><a href="notificaciones/hojas_rutas/listar">Hojas de Ruta</a></li>';
                $nav .= '<li><a href="notificaciones/tipos_documentos/listar">Tipos de Documento</a></li>';
                $nav .= '<li><a href="notificaciones/usuarios_areas/listar">Usuarios por Area</a></li>';
                $nav .= '<li><a href="notificaciones/zonas/listar">Zonas</a></li>';
                $nav .= '<li><a href="notificaciones/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_notificaciones_oficinas, $grupos))
        {
            switch ($controlador)
            {
                case 'notificaciones/escritorio':
                case 'notificaciones/escritorio/index':
                    $c_not['escritorio'] = ' class="current-page"';
                    break;
                case 'notificaciones/cedulas':
                    $c_not['cedulas'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_not))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-file-o"></i> Notificaciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_not['escritorio']) ? '' : $c_not['escritorio']) . '><a href="notificaciones/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_not['cedulas']) ? '' : $c_not['cedulas']) . '><a href="notificaciones/cedulas/listar">Cedulas</a></li>';
                $nav .= '<li' . (empty($c_not['manual']) ? '' : $c_not['manual']) . '><a href="notificaciones/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-file-o"></i> Notificaciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="notificaciones/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="notificaciones/cedulas/listar">Cedulas</a></li>';
                $nav .= '<li><a href="notificaciones/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_notificaciones_notificadores, $grupos))
        {
            switch ($controlador)
            {
                case 'notificaciones/escritorio':
                case 'notificaciones/escritorio/index':
                    $c_not['escritorio'] = ' class="current-page"';
                    break;
                case 'notificaciones/cedulas':
                    $c_not['cedulas'] = ' class="current-page"';
                    break;
                case 'notificaciones/hojas_rutas':
                    $c_not['hojas_rutas'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_not))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-file-o"></i> Notificaciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_not['escritorio']) ? '' : $c_not['escritorio']) . '><a href="notificaciones/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_not['cedulas']) ? '' : $c_not['cedulas']) . '><a href="notificaciones/cedulas/listar">Cedulas</a></li>';
                $nav .= '<li' . (empty($c_not['hojas_rutas']) ? '' : $c_not['hojas_rutas']) . '><a href="notificaciones/hojas_rutas/listar">Hojas de Ruta</a></li>';
                $nav .= '<li' . (empty($c_not['manual']) ? '' : $c_not['manual']) . '><a href="notificaciones/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-file-o"></i> Notificaciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="notificaciones/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="notificaciones/cedulas/listar">Cedulas</a></li>';
                $nav .= '<li><a href="notificaciones/hojas_rutas/listar">Hojas de Ruta</a></li>';
                $nav .= '<li><a href="notificaciones/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_notificaciones_control, $grupos))
        {
            // Estadisticas
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Obrador">
        $c_ob = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_obrador_consulta, $grupos) || in_groups($grupos_obrador_user, $grupos))
        {
            switch ($url_actual)
            {
                case 'obrador/escritorio':
                case 'obrador/escritorio/index':
                    $c_ob['escritorio'] = ' class="current-page"';
                    break;
                case 'obrador/articulos/listar':
                case 'obrador/articulos/agregar':
                case 'obrador/articulos/editar':
                case 'obrador/articulos/eliminar':
                case 'obrador/articulos/ver':
                    $c_ob['articulos'] = ' class="current-page"';
                    break;
                case 'obrador/compras/listar':
                case 'obrador/compras/agregar':
                case 'obrador/compras/editar':
                case 'obrador/compras/anular':
                case 'obrador/compras/ver':
                case 'obrador/compras/imprimir':
                    $c_ob['compras'] = ' class="current-page"';
                    break;
                case 'obrador/entregas/listar':
                case 'obrador/entregas/agregar':
                case 'obrador/entregas/editar':
                case 'obrador/entregas/anular':
                case 'obrador/entregas/ver':
                case 'obrador/entregas/imprimir':
                    $c_ob['entregas'] = ' class="current-page"';
                    break;
                case 'obrador/ganancias/listar':
                case 'obrador/ganancias/agregar':
                case 'obrador/ganancias/editar':
                case 'obrador/ganancias/eliminar':
                case 'obrador/ganancias/ver':
                    $c_ob['ganancias'] = ' class="current-page"';
                    break;
                case 'obrador/proveedores/listar':
                case 'obrador/proveedores/agregar':
                case 'obrador/proveedores/editar':
                case 'obrador/proveedores/eliminar':
                case 'obrador/proveedores/ver':
                    $c_ob['proveedores'] = ' class="current-page"';
                    break;
                case 'obrador/reportes/listar':
                case 'obrador/reportes/stock':
                case 'obrador/reportes/stock_critico':
                case 'obrador/reportes/entregas':
                case 'obrador/reportes/compras':
                    $c_ob['reportes'] = ' class="current-page"';
                    break;
                case 'obrador/situaciones_iva/listar':
                case 'obrador/situaciones_iva/agregar':
                case 'obrador/situaciones_iva/editar':
                case 'obrador/situaciones_iva/eliminar':
                case 'obrador/situaciones_iva/ver':
                    $c_ob['situaciones_iva'] = ' class="current-page"';
                    break;
                case 'obrador/tipos_articulos/listar':
                case 'obrador/tipos_articulos/agregar':
                case 'obrador/tipos_articulos/editar':
                case 'obrador/tipos_articulos/eliminar':
                case 'obrador/tipos_articulos/ver':
                    $c_ob['tipos_articulos'] = ' class="current-page"';
                    break;
                case 'obrador/tipos_proveedores/listar':
                case 'obrador/tipos_proveedores/agregar':
                case 'obrador/tipos_proveedores/editar':
                case 'obrador/tipos_proveedores/eliminar':
                case 'obrador/tipos_proveedores/ver':
                    $c_ob['tipos_proveedores'] = ' class="current-page"';
                    break;
                case 'obrador/tipos_unidades/listar':
                case 'obrador/tipos_unidades/agregar':
                case 'obrador/tipos_unidades/editar':
                case 'obrador/tipos_unidades/eliminar':
                case 'obrador/tipos_unidades/ver':
                    $c_ob['tipos_unidades'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_ob))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-cubes"></i> Obrador <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_ob['escritorio']) ? '' : $c_ob['escritorio']) . '><a href="obrador/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_ob['articulos']) ? '' : $c_ob['articulos']) . '><a href="obrador/articulos/listar">Artículos</a></li>';
                $nav .= '<li' . (empty($c_ob['compras']) ? '' : $c_ob['compras']) . '><a href="obrador/compras/listar">Compras</a></li>';
                $nav .= '<li' . (empty($c_ob['entregas']) ? '' : $c_ob['entregas']) . '><a href="obrador/entregas/listar">Entregas</a></li>';
                $nav .= '<li' . (empty($c_ob['ganancias']) ? '' : $c_ob['ganancias']) . '><a href="obrador/ganancias/listar">Ganancias</a></li>';
                $nav .= '<li' . (empty($c_ob['proveedores']) ? '' : $c_ob['proveedores']) . '><a href="obrador/proveedores/listar">Proveedores</a></li>';
                $nav .= '<li' . (empty($c_ob['reportes']) ? '' : $c_ob['reportes']) . '><a href="obrador/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_ob['situaciones_iva']) ? '' : $c_ob['situaciones_iva']) . '><a href="obrador/situaciones_iva/listar">Situaciones IVA</a></li>';
                $nav .= '<li' . (empty($c_ob['tipos_articulos']) ? '' : $c_ob['tipos_articulos']) . '><a href="obrador/tipos_articulos/listar">Tipos de Artículos</a></li>';
                $nav .= '<li' . (empty($c_ob['tipos_proveedores']) ? '' : $c_ob['tipos_proveedores']) . '><a href="obrador/tipos_proveedores/listar">Tipos de Proveedores</a></li>';
                $nav .= '<li' . (empty($c_ob['tipos_unidades']) ? '' : $c_ob['tipos_unidades']) . '><a href="obrador/tipos_unidades/listar">Tipos de Unidades</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-cubes"></i> Obrador <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="obrador/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="obrador/articulos/listar">Artículos</a></li>';
                $nav .= '<li><a href="obrador/compras/listar">Compras</a></li>';
                $nav .= '<li><a href="obrador/entregas/listar">Entregas</a></li>';
                $nav .= '<li><a href="obrador/ganancias/listar">Ganancias</a></li>';
                $nav .= '<li><a href="obrador/lugares/listar">Lugares</a></li>';
                $nav .= '<li><a href="obrador/proveedores/listar">Proveedores</a></li>';
                $nav .= '<li><a href="obrador/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="obrador/situaciones_iva/listar">Situaciones IVA</a></li>';
                $nav .= '<li><a href="obrador/tipos_articulos/listar">Tipos de Artículos</a></li>';
                $nav .= '<li><a href="obrador/tipos_proveedores/listar">Tipos de Proveedores</a></li>';
                $nav .= '<li><a href="obrador/tipos_unidades/listar">Tipos de Unidades</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Reclamos Major">
        $c_rm = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_reclamos_major_consulta, $grupos) || in_groups($grupos_reclamos_major_admin, $grupos))
        {
            switch ($url_actual)
            {
                case 'reclamos_major/escritorio':
                case 'reclamos_major/escritorio/index':
                    $c_rm['escritorio'] = ' class="current-page"';
                    break;
                case 'reclamos_major/categorias/listar':
                case 'reclamos_major/categorias/agregar':
                case 'reclamos_major/categorias/editar':
                case 'reclamos_major/categorias/ver':
                case 'reclamos_major/categorias/anular':
                    $c_rm['categorias'] = ' class="current-page"';
                    break;
                case 'reclamos_major/incidencias/listar':
                case 'reclamos_major/incidencias/agregar':
                case 'reclamos_major/incidencias/editar':
                case 'reclamos_major/incidencias/ver':
                case 'reclamos_major/incidencias/anular':
                    $c_rm['incidencias'] = ' class="current-page"';
                    break;
                case 'reclamos_major/reportes/listar':
                case 'reclamos_major/reportes/incidencias':
                    $c_rm['reportes'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_rm))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-warning"></i> Reclamos Major <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_rm['escritorio']) ? '' : $c_rm['escritorio']) . '><a href="reclamos_major/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_rm['categorias']) ? '' : $c_rm['categorias']) . '><a href="reclamos_major/categorias/listar">Categorías</a></li>';
                $nav .= '<li' . (empty($c_rm['incidencias']) ? '' : $c_rm['incidencias']) . '><a href="reclamos_major/incidencias/listar">Incidencias</a></li>';
                $nav .= '<li' . (empty($c_rm['reportes']) ? '' : $c_rm['reportes']) . '><a href="reclamos_major/reportes/listar">Reportes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-warning"></i> Reclamos Major <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="reclamos_major/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="reclamos_major/categorias/listar">Categorías</a></li>';
                $nav .= '<li><a href="reclamos_major/incidencias/listar">Incidencias</a></li>';
                $nav .= '<li><a href="reclamos_major/reportes/listar">Reportes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Reclamos GIS">
        $c_rgis = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_reclamos_gis_user, $grupos) || in_groups($grupos_reclamos_gis_consulta, $grupos))
        {
            switch ($controlador)
            {
                case 'reclamos_gis/escritorio':
                case 'reclamos_gis/escritorio/index':
                    $c_rgis['escritorio'] = ' class="current-page"';
                    break;
                case 'reclamos_gis/reclamos':
                    $c_rgis['reclamos'] = ' class="current-page"';
                    break;
                case 'reclamos_gis/reclamos_potrerillos':
                    $c_rgis['reclamos_potrerillos'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_rgis))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-map"></i> Reclamos GIS <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_rgis['escritorio']) ? '' : $c_rgis['escritorio']) . '><a href="reclamos_gis/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_rgis['reclamos']) ? '' : $c_rgis['reclamos']) . '><a href="reclamos_gis/reclamos/listar">Reclamos</a></li>';
                $nav .= '<li' . (empty($c_rgis['reclamos_potrerillos']) ? '' : $c_rgis['reclamos_potrerillos']) . '><a href="reclamos_gis/reclamos_potrerillos/listar">Reclamos Potrerillos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-map"></i> Reclamos GIS <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="reclamos_gis/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="reclamos_gis/reclamos/listar">Reclamos</a></li>';
                $nav .= '<li><a href="reclamos_gis/reclamos_potrerillos/listar">Reclamos Potrerillos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>  //editado por yoel grosso
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Reclamos GIS">
        $c_empleo = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_oficina_de_empleo, $grupos) )
        {
            switch ($controlador)
            {
                case 'oficina_de_empleo/escritorio':
                case 'oficina_de_empleo/escritorio/index':
                    $c_empleo['escritorio'] = ' class="current-page"';
                    break;
                case 'oficina_de_empleo/pedir_empleo':
                    $c_empleo['pedir_empleo'] = ' class="current-page"';
                    break;
                case 'oficina_de_empleo/Intermediacion':
                    $c_empleo['Intermediacion'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_empleo))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-user"></i> Oficina de Empleo <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_empleo['escritorio']) ? '' : $c_empleo['escritorio']) . '><a href="oficina_de_empleo/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_empleo['pedir_empleo']) ? '' : $c_empleo['pedir_empleo']) . '><a href="oficina_de_empleo/pedir_empleo/listar">Carga de CV</a></li>';
                $nav .= '<li' . (empty($c_empleo['Intermediacion']) ? '' : $c_empleo['Intermediacion']) . '><a href="oficina_de_empleo/Intermediacion/listar">Intermediacion laboral</a></li>';
                $nav .= '<li' . (empty($c_empleo['Busquedas']) ? '' : $c_empleo['Busquedas']) . '><a href="oficina_de_empleo/Busquedas/listar">Busquedas</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-user"></i> Oficina de Empleo <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="oficina_de_empleo/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="oficina_de_empleo/pedir_empleo/listar">carga de CV</a></li>';
                $nav .= '<li><a href="oficina_de_empleo/Intermediacion/listar">Intermediacion Laboral</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Recursos Humanos">
        $c_rh = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_recursos_humanos_admin, $grupos) || in_groups($grupos_recursos_humanos_user, $grupos) || in_groups($grupos_recursos_humanos_consulta, $grupos))
        {
            switch ($controlador)
            {
                case 'recursos_humanos/escritorio':
                case 'recursos_humanos/escritorio/index':
                    $c_rh['escritorio'] = ' class="current-page"';
                    break;
                case 'recursos_humanos/bonos':
                    $c_rh['bonos'] = ' class="current-page"';
                    break;
                case 'recursos_humanos/categorias':
                    $c_rh['categorias'] = ' class="current-page"';
                    break;
                case 'recursos_humanos/documentos_legajo':
                    $c_rh['documentos'] = ' class="current-page"';
                    break;
                case 'recursos_humanos/hobbies':
                    $c_rh['hobbies'] = ' class="current-page"';
                    break;
                case 'recursos_humanos/legajos':
                case 'recursos_humanos/datos_extra':
                    $c_rh['legajos'] = ' class="current-page"';
                    break;
                case 'recursos_humanos/manuales':
                    $c_rh['manuales'] = ' class="current-page"';
                    break;
                case 'recursos_humanos/usuarios_legajos':
                case 'recursos_humanos/usuarios_legajos/listar':
                case 'recursos_humanos/usuarios_legajos/agregar':
                case 'recursos_humanos/usuarios_legajos/editar':
                case 'recursos_humanos/usuarios_legajos/eliminar':
                case 'recursos_humanos/usuarios_legajos/ver':
                    $c_rh['usuarios_legajos'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_rh))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-address-card-o"></i> Recursos Humanos <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_rh['escritorio']) ? '' : $c_rh['escritorio']) . '><a href="recursos_humanos/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_rh['usuarios_legajos']) ? '' : $c_rh['usuarios_legajos']) . '><a href="recursos_humanos/usuarios_legajos/listar">Asignación de Legajos</a></li>';
                if (in_groups($grupos_admin, $grupos) || in_groups($grupos_recursos_humanos_bonos, $grupos))
                {
                    $nav .= '<li' . (empty($c_rh['bonos']) ? '' : $c_rh['bonos']) . '><a href="recursos_humanos/bonos/listar">Bonos</a></li>';
                }
                $nav .= '<li' . (empty($c_rh['categorias']) ? '' : $c_rh['categorias']) . '><a href="recursos_humanos/categorias/listar">Categorias</a></li>';
                $nav .= '<li' . (empty($c_rh['documentos']) ? '' : $c_rh['documentos']) . '><a href="recursos_humanos/documentos_legajo/listar">Documentos</a></li>';
                $nav .= '<li' . (empty($c_rh['hobbies']) ? '' : $c_rh['hobbies']) . '><a href="recursos_humanos/hobbies/listar">Hobbies</a></li>';
                $nav .= '<li' . (empty($c_rh['legajos']) ? '' : $c_rh['legajos']) . '><a href="recursos_humanos/legajos/listar">Legajos</a></li>';
                $nav .= '<li' . (empty($c_rh['manuales']) ? '' : $c_rh['manuales']) . '><a href="recursos_humanos/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-address-card-o"></i> Recursos Humanos <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="recursos_humanos/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="recursos_humanos/usuarios_legajos">Asignación de Legajos</a></li>';
                if (in_groups($grupos_admin, $grupos) || in_groups($grupos_recursos_humanos_bonos, $grupos))
                {
                    $nav .= '<li><a href="recursos_humanos/bonos/listar">Bonos</a></li>';
                }
                $nav .= '<li><a href="recursos_humanos/categorias/listar">Categorias</a></li>';
                $nav .= '<li><a href="recursos_humanos/documentos_legajo/listar">Documentos</a></li>';
                $nav .= '<li><a href="recursos_humanos/hobbies/listar">Hobbies</a></li>';
                $nav .= '<li><a href="recursos_humanos/legajos/listar">Legajos</a></li>';
                $nav .= '<li><a href="recursos_humanos/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_recursos_humanos_director, $grupos))
        {
            switch ($controlador)
            {
                case 'recursos_humanos/escritorio':
                case 'recursos_humanos/escritorio/index':
                    $c_rh['escritorio'] = ' class="current-page"';
                    break;
                case 'recursos_humanos/legajos':
                case 'recursos_humanos/documentos_legajo':
                    $c_rh['legajos'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_rh))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-address-card-o"></i> Recursos Humanos <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_rh['escritorio']) ? '' : $c_rh['escritorio']) . '><a href="recursos_humanos/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_rh['legajos']) ? '' : $c_rh['legajos']) . '><a href="recursos_humanos/legajos/listar_director">Legajos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-address-card-o"></i> Recursos Humanos <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="recursos_humanos/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="recursos_humanos/legajos/listar_director">Legajos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_recursos_humanos_publico, $grupos))
        {
            switch ($controlador)
            {
                case 'recursos_humanos/escritorio':
                case 'recursos_humanos/escritorio/index':
                    $c_rh['escritorio'] = ' class="current-page"';
                    break;
                case 'recursos_humanos/legajos':
                case 'recursos_humanos/documentos_legajo':
                    $c_rh['legajos'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_rh))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-address-card-o"></i> Recursos Humanos <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_rh['escritorio']) ? '' : $c_rh['escritorio']) . '><a href="recursos_humanos/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_rh['legajos']) ? '' : $c_rh['legajos']) . '><a href="recursos_humanos/legajos/listar_publicos">Legajos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-address-card-o"></i> Recursos Humanos <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="recursos_humanos/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="recursos_humanos/legajos/listar_publicos">Legajos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // 		// // <editor-fold defaultstate="collapsed" desc="Permisos Menú Resoluciones">
        $c_a = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_resoluciones_consulta, $grupos))
        {
            switch ($url_actual)
            {
                case 'resoluciones/escritorio':
                case 'resoluciones/escritorio/index':
                    $c_a['escritorio'] = ' class="current-page"';
                    break;
                case 'resoluciones/numeraciones/listar':
                case 'resoluciones/numeraciones/agregar':
                case 'resoluciones/numeraciones/editar':
                case 'resoluciones/numeraciones/eliminar':
                case 'resoluciones/numeraciones/ver':
                    $c_a['numeraciones'] = ' class="current-page"';
                    break;
                case 'resoluciones/resoluciones/listar':
                case 'resoluciones/resoluciones/agregar':
                case 'resoluciones/resoluciones/editar':
                case 'resoluciones/resoluciones/anular':
                case 'resoluciones/resoluciones/ver':
                    $c_a['resoluciones'] = ' class="current-page"';
                    break;
                case 'resoluciones/tipos_resoluciones/listar':
                case 'resoluciones/tipos_resoluciones/agregar':
                case 'resoluciones/tipos_resoluciones/editar':
                case 'resoluciones/tipos_resoluciones/eliminar':
                case 'resoluciones/tipos_resoluciones/ver':
                    $c_a['tipos_resoluciones'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_a))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-file-text"></i> Resoluciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_a['escritorio']) ? '' : $c_a['escritorio']) . '><a href="resoluciones/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_a['numeraciones']) ? '' : $c_a['numeraciones']) . '><a href="resoluciones/numeraciones/listar">Numeraciones</a></li>';
                $nav .= '<li' . (empty($c_a['resoluciones']) ? '' : $c_a['resoluciones']) . '><a href="resoluciones/resoluciones/listar">Resoluciones</a></li>';
                $nav .= '<li' . (empty($c_a['tipos_resoluciones']) ? '' : $c_a['tipos_resoluciones']) . '><a href="resoluciones/tipos_resoluciones/listar">Tipos de Resolución</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-file-text"></i> Resoluciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="resoluciones/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="resoluciones/numeraciones/listar">Numeraciones</a></li>';
                $nav .= '<li><a href="resoluciones/resoluciones/listar">Resoluciones</a></li>';
                $nav .= '<li><a href="resoluciones/tipos_resoluciones/listar">Tipos de Resolución</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_resoluciones_user, $grupos))
        {
            switch ($url_actual)
            {
                case 'resoluciones/escritorio':
                case 'resoluciones/escritorio/index':
                    $c_a['escritorio'] = ' class="current-page"';
                    break;
                case 'resoluciones/resoluciones/listar':
                case 'resoluciones/resoluciones/agregar':
                case 'resoluciones/resoluciones/editar':
                case 'resoluciones/resoluciones/eliminar':
                case 'resoluciones/resoluciones/ver':
                    $c_a['resoluciones'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_a))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-file-text"></i> Resoluciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_a['escritorio']) ? '' : $c_a['escritorio']) . '><a href="resoluciones/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_a['resoluciones']) ? '' : $c_a['resoluciones']) . '><a href="resoluciones/resoluciones/listar">Resoluciones</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-file-text"></i> Resoluciones <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="resoluciones/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="resoluciones/resoluciones/listar">Resoluciones</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        //<editor-fold defaultstate="collapsed" desc="Permisos Menú Stock Informática">
        $c_si = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_stock_informatica_user, $grupos) || in_groups($grupos_stock_informatica_consulta, $grupos))
        {
            switch ($url_actual)
            {
                case 'stock_informatica/escritorio':
                case 'stock_informatica/escritorio/index':
                    $c_si['escritorio'] = ' class="current-page"';
                    break;
                case 'stock_informatica/articulos/listar':
                case 'stock_informatica/articulos/agregar':
                case 'stock_informatica/articulos/editar':
                case 'stock_informatica/articulos/eliminar':
                case 'stock_informatica/articulos/ver':
                    $c_si['articulos'] = ' class="current-page"';
                    break;
                case 'stock_informatica/atributos/listar':
                case 'stock_informatica/atributos/agregar':
                case 'stock_informatica/atributos/editar':
                case 'stock_informatica/atributos/anular':
                case 'stock_informatica/atributos/ver':
                    $c_si['atributos'] = ' class="current-page"';
                    break;
                case 'stock_informatica/categorias/listar':
                case 'stock_informatica/categorias/agregar':
                case 'stock_informatica/categorias/editar':
                case 'stock_informatica/categorias/eliminar':
                case 'stock_informatica/categorias/ver':
                    $c_si['categorias'] = ' class="current-page"';
                    break;
                case 'stock_informatica/marcas/listar':
                case 'stock_informatica/marcas/agregar':
                case 'stock_informatica/marcas/editar':
                case 'stock_informatica/marcas/eliminar':
                case 'stock_informatica/marcas/ver':
                    $c_si['marcas'] = ' class="current-page"';
                    break;
                case 'stock_informatica/movimientos/listar':
                case 'stock_informatica/movimientos/agregar':
                case 'stock_informatica/movimientos/editar':
                case 'stock_informatica/movimientos/eliminar':
                case 'stock_informatica/movimientos/ver':
                    $c_si['movimientos'] = ' class="current-page"';
                    break;
                case 'stock_informatica/reportes/listar':
                case 'stock_informatica/reportes/stock_area':
                    $c_si['reportes'] = ' class="current-page"';
                    break;
                case 'stock_informatica/stock/listar':
                case 'stock_informatica/stock/ingreso':
                case 'stock_informatica/stock/transferencia_area':
                case 'stock_informatica/stock/transferencia_articulo':
                case 'stock_informatica/stock/ver':
                    $c_si['stock'] = ' class="current-page"';
                    break;
                case 'stock_informatica/subcategorias/listar':
                case 'stock_informatica/subcategorias/agregar':
                case 'stock_informatica/subcategorias/editar':
                case 'stock_informatica/subcategorias/eliminar':
                case 'stock_informatica/subcategorias/ver':
                    $c_si['stock'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_si))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-desktop"></i> Stock Informática <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_si['escritorio']) ? '' : $c_si['escritorio']) . '><a href="stock_informatica/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_si['articulos']) ? '' : $c_si['articulos']) . '><a href="stock_informatica/articulos/listar">Artículos</a></li>';
                $nav .= '<li' . (empty($c_si['atributos']) ? '' : $c_si['atributos']) . '><a href="stock_informatica/atributos/listar">Atributos</a></li>';
                $nav .= '<li' . (empty($c_si['categorias']) ? '' : $c_si['categorias']) . '><a href="stock_informatica/categorias/listar">Categorías</a></li>';
                $nav .= '<li' . (empty($c_si['marcas']) ? '' : $c_si['marcas']) . '><a href="stock_informatica/marcas/listar">Marcas</a></li>';
                $nav .= '<li' . (empty($c_si['movimientos']) ? '' : $c_si['movimientos']) . '><a href="stock_informatica/movimientos/listar">Movimientos</a></li>';
                $nav .= '<li' . (empty($c_si['reportes']) ? '' : $c_si['reportes']) . '><a href="stock_informatica/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_si['stock']) ? '' : $c_si['stock']) . '><a href="stock_informatica/stock/listar">Stock</a></li>';
                $nav .= '<li' . (empty($c_si['subcategorias']) ? '' : $c_si['subcategorias']) . '><a href="stock_informatica/subcategorias/listar">Subcategorías</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-desktop"></i> Stock Informática <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="stock_informatica/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="stock_informatica/articulos/listar">Artículos</a></li>';
                $nav .= '<li><a href="stock_informatica/atributos/listar">Atributos</a></li>';
                $nav .= '<li><a href="stock_informatica/categorias/listar">Categorías</a></li>';
                $nav .= '<li><a href="stock_informatica/marcas/listar">Marcas</a></li>';
                $nav .= '<li><a href="stock_informatica/movimientos/listar">Movimientos</a></li>';
                $nav .= '<li><a href="stock_informatica/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="stock_informatica/stock/listar">Stock</a></li>';
                $nav .= '<li><a href="stock_informatica/subcategorias/listar">Subcategorías</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        //<editor-fold defaultstate="collapsed" desc="Permisos Menú Tablero">
        $c_ta = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_tablero_consulta, $grupos))
        {
            switch ($url_actual)
            {
                case 'tablero/escritorio':
                case 'tablero/escritorio/index':
                    $c_ta['escritorio'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_ta))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-line-chart"></i> Tablero <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_ta['escritorio']) ? '' : $c_ta['escritorio']) . '><a href="tablero/escritorio">Inicio</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-line-chart"></i> Tablero <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="tablero/escritorio">Inicio</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Telefonía">
        $c_to = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_telefonia_consulta, $grupos) || in_groups($grupos_telefonia_admin, $grupos))
        {
            switch ($url_actual)
            {
                case 'telefonia/escritorio':
                case 'telefonia/escritorio/index':
                    $c_to['escritorio'] = ' class="current-page"';
                    break;
                case 'telefonia/categorias/listar':
                case 'telefonia/categorias/agregar':
                case 'telefonia/categorias/editar':
                case 'telefonia/categorias/ver':
                case 'telefonia/categorias/eliminar':
                    $c_to['categorias'] = ' class="current-page"';
                    break;
                case 'telefonia/equipos/listar':
                case 'telefonia/equipos/agregar':
                case 'telefonia/equipos/editar':
                case 'telefonia/equipos/ver':
                case 'telefonia/equipos/eliminar':
                    $c_to['equipos'] = ' class="current-page"';
                    break;
                case 'telefonia/lineas/listar':
                case 'telefonia/lineas/agregar':
                case 'telefonia/lineas/editar':
                case 'telefonia/lineas/ver':
                case 'telefonia/lineas/eliminar':
                    $c_to['lineas'] = ' class="current-page"';
                    break;
                case 'telefonia/lineas_fijas/listar':
                case 'telefonia/lineas_fijas/agregar':
                case 'telefonia/lineas_fijas/editar':
                case 'telefonia/lineas_fijas/ver':
                case 'telefonia/lineas_fijas/eliminar':
                    $c_to['lineas_fijas'] = ' class="current-page"';
                    break;
                case 'telefonia/lineas_fijas_consumos/listar':
                case 'telefonia/lineas_fijas_consumos/cargar':
                    $c_to['lineas_fijas_consumos'] = ' class="current-page"';
                    break;
                case 'telefonia/marcas/listar':
                case 'telefonia/marcas/agregar':
                case 'telefonia/marcas/editar':
                case 'telefonia/marcas/ver':
                case 'telefonia/marcas/eliminar':
                    $c_to['marcas'] = ' class="current-page"';
                    break;
                case 'telefonia/modelos/listar':
                case 'telefonia/modelos/agregar':
                case 'telefonia/modelos/editar':
                case 'telefonia/modelos/ver':
                case 'telefonia/modelos/eliminar':
                    $c_to['modelos'] = ' class="current-page"';
                    break;
                case 'telefonia/movimientos/listar':
                case 'telefonia/movimientos/agregar':
                case 'telefonia/movimientos/editar':
                case 'telefonia/movimientos/ver':
                case 'telefonia/movimientos/eliminar':
                    $c_to['movimientos'] = ' class="current-page"';
                    break;
                case 'telefonia/prestadores/listar':
                case 'telefonia/prestadores/agregar':
                case 'telefonia/prestadores/editar':
                case 'telefonia/prestadores/ver':
                case 'telefonia/prestadores/eliminar':
                    $c_to['prestadores'] = ' class="current-page"';
                    break;
                case 'telefonia/reportes/listar':
                case 'telefonia/reportes/consumo_lineas_fijas':
                case 'telefonia/reportes/equipos':
                case 'telefonia/reportes/lineas':
                case 'telefonia/reportes/lineas_listado':
                    $c_to['reportes'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_to))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-phone"></i> Telefonía <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_to['escritorio']) ? '' : $c_to['escritorio']) . '><a href="telefonia/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_to['categorias']) ? '' : $c_to['categorias']) . '><a href="telefonia/categorias/listar">Categorías</a></li>';
                $nav .= '<li' . (empty($c_to['equipos']) ? '' : $c_to['equipos']) . '><a href="telefonia/equipos/listar">Equipos</a></li>';
                $nav .= '<li' . (empty($c_to['lineas']) ? '' : $c_to['lineas']) . '><a href="telefonia/lineas/listar">Líneas</a></li>';
                $nav .= '<li' . (empty($c_to['lineas_fijas']) ? '' : $c_to['lineas_fijas']) . '><a href="telefonia/lineas_fijas/listar">Líneas Fijas</a></li>';
                $nav .= '<li' . (empty($c_to['lineas_fijas_consumos']) ? '' : $c_to['lineas_fijas_consumos']) . '><a href="telefonia/lineas_fijas_consumos/listar">Líneas Fijas Consumos</a></li>';
                $nav .= '<li' . (empty($c_to['marcas']) ? '' : $c_to['marcas']) . '><a href="telefonia/marcas/listar">Marcas</a></li>';
                $nav .= '<li' . (empty($c_to['modelos']) ? '' : $c_to['modelos']) . '><a href="telefonia/modelos/listar">Modelos</a></li>';
                $nav .= '<li' . (empty($c_to['movimientos']) ? '' : $c_to['movimientos']) . '><a href="telefonia/movimientos/listar">Movimientos</a></li>';
                $nav .= '<li' . (empty($c_to['prestadores']) ? '' : $c_to['prestadores']) . '><a href="telefonia/prestadores/listar">Prestadores</a></li>';
                $nav .= '<li' . (empty($c_to['reportes']) ? '' : $c_to['reportes']) . '><a href="telefonia/reportes/listar">Reportes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-phone"></i> Telefonía <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="telefonia/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="telefonia/categorias/listar">Categorías</a></li>';
                $nav .= '<li><a href="telefonia/equipos/listar">Equipos</a></li>';
                $nav .= '<li><a href="telefonia/lineas/listar">Líneas</a></li>';
                $nav .= '<li><a href="telefonia/lineas_fijas/listar">Líneas Fijas</a></li>';
                $nav .= '<li><a href="telefonia/lineas_fijas_consumos/listar">Líneas Fijas Consumos</a></li>';
                $nav .= '<li><a href="telefonia/marcas/listar">Marcas</a></li>';
                $nav .= '<li><a href="telefonia/modelos/listar">Modelos</a></li>';
                $nav .= '<li><a href="telefonia/movimientos/listar">Movimientos</a></li>';
                $nav .= '<li><a href="telefonia/prestadores/listar">Prestadores</a></li>';
                $nav .= '<li><a href="telefonia/reportes/listar">Reportes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Toner">
        $c_to = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_toner_consulta, $grupos) || in_groups($grupos_toner_admin, $grupos))
        {
            switch ($url_actual)
            {
                case 'toner/escritorio':
                case 'toner/escritorio/index':
                    $c_to['escritorio'] = ' class="current-page"';
                    break;
                case 'toner/consumibles/listar':
                case 'toner/consumibles/agregar':
                case 'toner/consumibles/editar':
                case 'toner/consumibles/ver':
                case 'toner/consumibles/eliminar':
                    $c_to['consumibles'] = ' class="current-page"';
                    break;
                case 'toner/consumibles_impresoras/listar':
                case 'toner/consumibles_impresoras/agregar':
                case 'toner/consumibles_impresoras/editar':
                case 'toner/consumibles_impresoras/ver':
                case 'toner/consumibles_impresoras/eliminar':
                    $c_to['consumibles_impresoras'] = ' class="current-page"';
                    break;
                case 'toner/impresoras/listar':
                case 'toner/impresoras/agregar':
                case 'toner/impresoras/editar':
                case 'toner/impresoras/ver':
                case 'toner/impresoras/eliminar':
                    $c_to['impresoras'] = ' class="current-page"';
                    break;
                case 'toner/impresoras_areas/listar':
                case 'toner/impresoras_areas/agregar':
                case 'toner/impresoras_areas/editar':
                case 'toner/impresoras_areas/ver':
                case 'toner/impresoras_areas/eliminar':
                    $c_to['impresoras_areas'] = ' class="current-page"';
                    break;
                case 'toner/marcas/listar':
                case 'toner/marcas/agregar':
                case 'toner/marcas/editar':
                case 'toner/marcas/ver':
                case 'toner/marcas/eliminar':
                    $c_to['marcas'] = ' class="current-page"';
                    break;
                case 'toner/movimientos/listar':
                case 'toner/movimientos/agregar':
                case 'toner/movimientos/ver':
                case 'toner/movimientos/anular':
                    $c_to['movimientos'] = ' class="current-page"';
                    break;
                case 'toner/pedidos_consumibles/listar':
                case 'toner/pedidos_consumibles/agregar':
                case 'toner/pedidos_consumibles/editar':
                case 'toner/pedidos_consumibles/ver':
                case 'toner/pedidos_consumibles/anular':
                case 'toner/pedidos_consumibles/eliminar':
                    $c_to['pedidos_consumibles'] = ' class="current-page"';
                    break;
                case 'toner/reportes/listar':
                case 'toner/reportes/consumo':
                case 'toner/reportes/consumo_area':
                case 'toner/reportes/historico_consumible':
                case 'toner/reportes/historico_consumo':
                case 'toner/reportes/impresoras':
                case 'toner/reportes/pedidos':
                    $c_to['reportes'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_to))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-print"></i> Toner <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_to['escritorio']) ? '' : $c_to['escritorio']) . '><a href="toner/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_to['consumibles']) ? '' : $c_to['consumibles']) . '><a href="toner/consumibles/listar">Consumibles</a></li>';
                $nav .= '<li' . (empty($c_to['consumibles_impresoras']) ? '' : $c_to['consumibles_impresoras']) . '><a href="toner/consumibles_impresoras/listar">Consumibles Impresora</a></li>';
                $nav .= '<li' . (empty($c_to['impresoras']) ? '' : $c_to['impresoras']) . '><a href="toner/impresoras/listar">Impresoras</a></li>';
                $nav .= '<li' . (empty($c_to['impresoras_areas']) ? '' : $c_to['impresoras_areas']) . '><a href="toner/impresoras_areas/listar">Impresoras Áreas</a></li>';
                $nav .= '<li' . (empty($c_to['marcas']) ? '' : $c_to['marcas']) . '><a href="toner/marcas/listar">Marcas</a></li>';
                $nav .= '<li' . (empty($c_to['movimientos']) ? '' : $c_to['movimientos']) . '><a href="toner/movimientos/listar">Movimientos</a></li>';
                $nav .= '<li' . (empty($c_to['pedidos_consumibles']) ? '' : $c_to['pedidos_consumibles']) . '><a href="toner/pedidos_consumibles/listar">Pedidos</a></li>';
                $nav .= '<li' . (empty($c_to['reportes']) ? '' : $c_to['reportes']) . '><a href="toner/reportes/listar">Reportes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-print"></i> Toner <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="toner/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="toner/consumibles/listar">Consumibles</a></li>';
                $nav .= '<li><a href="toner/consumibles_impresoras/listar">Consumibles Impresora</a></li>';
                $nav .= '<li><a href="toner/impresoras/listar">Impresoras</a></li>';
                $nav .= '<li><a href="toner/impresoras_areas/listar">Impresoras Áreas</a></li>';
                $nav .= '<li><a href="toner/marcas/listar">Marcas</a></li>';
                $nav .= '<li><a href="toner/movimientos/listar">Movimientos</a></li>';
                $nav .= '<li><a href="toner/pedidos_consumibles/listar">Pedidos</a></li>';
                $nav .= '<li><a href="toner/reportes/listar">Reportes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        //<editor-fold defaultstate="collapsed" desc="Permisos Menú Trámites a Distancia">
        $c_to = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_tramites_online_consulta, $grupos))
        {
            switch ($controlador)
            {
                case 'tramites_online/escritorio':
                case 'tramites_online/escritorio/index':
                    $c_to['escritorio'] = ' class="current-page"';
                    break;
                case 'tramites_online/adjuntos_tipos':
                    $c_to['adjuntos_tipos'] = ' class="current-page"';
                    break;
                case 'tramites_online/estados':
                    $c_to['estados'] = ' class="current-page"';
                    break;
                case 'tramites_online/formularios':
                    $c_to['formularios'] = ' class="current-page"';
                    break;
                case 'tramites_online/iniciadores':
                    $c_to['iniciadores'] = ' class="current-page"';
                    break;
                case 'tramites_online/iniciadores_tipos':
                    $c_to['iniciadores_tipos'] = ' class="current-page"';
                    break;
                case 'tramites_online/oficinas':
                    $c_to['oficinas'] = ' class="current-page"';
                    break;
                case 'tramites_online/procesos':
                    $c_to['procesos'] = ' class="current-page"';
                    break;
                case 'tramites_online/reportes':
                    $c_to['reportes'] = ' class="current-page"';
                    break;
                case 'tramites_online/tablero':
                case 'tramites_online/tablero/index':
                    $c_to['tablero'] = ' class="current-page"';
                    break;
                case 'tramites_online/tramites':
                    $c_to['bandeja'] = ' class="current-page"';
                    break;
                case 'tramites_online/usuarios_oficinas':
                    $c_to['usuarios_oficinas'] = ' class="current-page"';
                    break;

                case 'tramites_online/manuales':
                    $c_to['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_to))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-globe"></i> Trámites a Distancia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_to['escritorio']) ? '' : $c_to['escritorio']) . '><a href="tramites_online/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_to['bandeja']) ? '' : $c_to['bandeja']) . '><a href="tramites_online/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li' . (empty($c_to['estados']) ? '' : $c_to['estados']) . '><a href="tramites_online/estados/listar">Estados</a></li>';
                $nav .= '<li' . (empty($c_to['formularios']) ? '' : $c_to['formularios']) . '><a href="tramites_online/formularios/listar">Formularios</a></li>';
                $nav .= '<li' . (empty($c_to['iniciadores']) ? '' : $c_to['iniciadores']) . '><a href="tramites_online/iniciadores/listar">Iniciadores</a></li>';
                $nav .= '<li' . (empty($c_to['oficinas']) ? '' : $c_to['oficinas']) . '><a href="tramites_online/oficinas/listar">Oficinas</a></li>';
                $nav .= '<li' . (empty($c_to['procesos']) ? '' : $c_to['procesos']) . '><a href="tramites_online/procesos/listar">Procesos</a></li>';
                $nav .= '<li' . (empty($c_to['reportes']) ? '' : $c_to['reportes']) . '><a href="tramites_online/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_to['tablero']) ? '' : $c_to['tablero']) . '><a href="tramites_online/tablero/index">Tablero</a></li>';
                $nav .= '<li' . (empty($c_to['adjuntos_tipos']) ? '' : $c_to['adjuntos_tipos']) . '><a href="tramites_online/adjuntos_tipos/listar">Tipos de Adjuntos</a></li>';
                $nav .= '<li' . (empty($c_to['iniciadores_tipos']) ? '' : $c_to['iniciadores_tipos']) . '><a href="tramites_online/iniciadores_tipos/listar">Tipos de Iniciadores</a></li>';
                $nav .= '<li' . (empty($c_to['usuarios_oficinas']) ? '' : $c_to['usuarios_oficinas']) . '><a href="tramites_online/usuarios_oficinas/listar">Usuarios por Oficina</a></li>';
                $nav .= '<li' . (empty($c_to['manuales']) ? '' : $c_to['manuales']) . '><a href="tramites_online/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-globe"></i> Trámites a Distancia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="tramites_online/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li><a href="tramites_online/estados/listar">Estados</a></li>';
                $nav .= '<li><a href="tramites_online/formularios/listar">Formularios</a></li>';
                $nav .= '<li><a href="tramites_online/iniciadores/listar">Iniciadores</a></li>';
                $nav .= '<li><a href="tramites_online/oficinas/listar">Oficinas</a></li>';
                $nav .= '<li><a href="tramites_online/procesos/listar">Procesos</a></li>';
                $nav .= '<li><a href="tramites_online/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="tramites_online/tablero/index">Tablero</a></li>';
                $nav .= '<li><a href="tramites_online/adjuntos_tipos/listar">Tipos de Adjuntos</a></li>';
                $nav .= '<li><a href="tramites_online/iniciadores_tipos/listar">Tipos de Iniciadores</a></li>';
                $nav .= '<li><a href="tramites_online/usuarios_oficinas/listar">Usuarios por Oficina</a></li>';
                $nav .= '<li><a href="tramites_online/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_tramites_online_admin, $grupos))
        {
            switch ($controlador)
            {
                case 'tramites_online/escritorio':
                case 'tramites_online/escritorio/index':
                    $c_to['escritorio'] = ' class="current-page"';
                    break;
                case 'tramites_online/reportes':
                    $c_to['reportes'] = ' class="current-page"';
                    break;
                case 'tramites_online/tablero':
                case 'tramites_online/tablero/index':
                    $c_to['tablero'] = ' class="current-page"';
                    break;
                case 'tramites_online/tramites':
                    $c_to['bandeja'] = ' class="current-page"';
                    break;
                case 'tramites_online/manuales':
                    $c_to['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_to))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-globe"></i> Trámites a Distancia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_to['escritorio']) ? '' : $c_to['escritorio']) . '><a href="tramites_online/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_to['bandeja']) ? '' : $c_to['bandeja']) . '><a href="tramites_online/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li' . (empty($c_to['reportes']) ? '' : $c_to['reportes']) . '><a href="tramites_online/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_to['tablero']) ? '' : $c_to['tablero']) . '><a href="tramites_online/tablero/index">Tablero</a></li>';
                $nav .= '<li' . (empty($c_to['manuales']) ? '' : $c_to['manuales']) . '><a href="tramites_online/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-globe"></i> Trámites a Distancia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="tramites_online/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li><a href="tramites_online/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="tramites_online/tablero/index">Tablero</a></li>';
                $nav .= '<li><a href="tramites_online/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_tramites_online_area, $grupos))
        {
            switch ($controlador)
            {
                case 'tramites_online/escritorio':
                case 'tramites_online/escritorio/index':
                    $c_to['escritorio'] = ' class="current-page"';
                    break;
                case 'tramites_online/reportes':
                    $c_to['reportes'] = ' class="current-page"';
                    break;
                case 'tramites_online/tablero':
                case 'tramites_online/tablero/index':
                    $c_to['tablero'] = ' class="current-page"';
                    break;
                case 'tramites_online/tramites':
                    $c_to['bandeja'] = ' class="current-page"';
                    break;
                case 'tramites_online/manuales':
                    $c_to['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_to))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-globe"></i> Trámites a Distancia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_to['escritorio']) ? '' : $c_to['escritorio']) . '><a href="tramites_online/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_to['bandeja']) ? '' : $c_to['bandeja']) . '><a href="tramites_online/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li' . (empty($c_to['reportes']) ? '' : $c_to['reportes']) . '><a href="tramites_online/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_to['tablero']) ? '' : $c_to['tablero']) . '><a href="tramites_online/tablero/index">Tablero</a></li>';
                $nav .= '<li' . (empty($c_to['manuales']) ? '' : $c_to['manuales']) . '><a href="tramites_online/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-globe"></i> Trámites a Distancia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="tramites_online/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li><a href="tramites_online/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="tramites_online/tablero/index">Tablero</a></li>';
                $nav .= '<li><a href="tramites_online/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_tramites_online_publico, $grupos))
        {
            switch ($controlador)
            {
                case 'tramites_online/escritorio':
                case 'tramites_online/escritorio/index':
                    $c_to['escritorio'] = ' class="current-page"';
                    break;
                case 'tramites_online/tramites':
                    $c_to['bandeja'] = ' class="current-page"';
                    break;
                case 'tramites_online/manuales':
                    $c_to['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_to))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-globe"></i> Trámites a Distancia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_to['escritorio']) ? '' : $c_to['escritorio']) . '><a href="tramites_online/escritorio">Inicio</a></li>';
                $nav .= '<li><a target="_blank" href="https://lujandecuyo.gob.ar/guiadetramites/">Guía de Trámites</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/modal_iniciar" data-remote="false" data-toggle="modal" data-target="#remote_modal">Iniciar Trámite</a></li>';
                $nav .= '<li' . (empty($c_to['bandeja']) ? '' : $c_to['bandeja']) . '><a href="tramites_online/tramites/bandeja_entrada_publico">Bandeja de Entrada</a></li>';
                $nav .= '<li' . (empty($c_to['manuales']) ? '' : $c_to['manuales']) . '><a href="tramites_online/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';                
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-globe"></i> Trámites a Distancia <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li><a href="tramites_online/escritorio">Inicio</a></li>';
                $nav .= '<li><a target="_blank" href="https://lujandecuyo.gob.ar/guiadetramites/">Guía de Trámites</a></li>'; 
                $nav .= '<li><a href="tramites_online/tramites/modal_iniciar" data-remote="false" data-toggle="modal" data-target="#remote_modal">Iniciar Trámite</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/bandeja_entrada_publico">Bandeja de Entrada</a></li>';
                $nav .= '<li><a href="tramites_online/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            // HERE
			
				$nav .= '<li>';
                $nav .= '<a><i class="fa fa-user"></i>Actuantes<span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
				$nav .= '<li><a href="tramites_online/tramites/agregar/41">Registro Profesional/Apoderado</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/19">Registro Agrimensor</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-map"></i>Catastro<span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
				$nav .= '<li><a href="tramites_online/tramites/agregar/16">Fraccionamiento</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/42">Conjunto Inmob./Loteos</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/22">Cambio de Domicilio</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/39">Cambio de Adjudicatario</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/38">Alta Padrones de Inmueble</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/36">Solicitud de Numeración Domiciliaria</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/35">Inscripción de Dominio</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/37">Verificación y/o Corrección de datos</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/50">Transferencias</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';     

                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-building"></i>Obras Privadas<span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="tramites_online/tramites/agregar/21">Conexión Gas Domiciliario</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/31">Permisos de Conexión: Red Cloacas y/o Agua</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/23">Permiso Eléctrico: Reconexión</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/24">Permiso Eléctrico: Cambio de Sitio</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/25">Permiso Eléctrico: Aumento de Potencia</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/27">Permiso Eléctrico: Separación de Consumo</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/28">Permiso Eléctrico: Conexión Luz de Riego</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/26">Permiso Eléctrico: Conexión Luz para Barrio</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/29">Inspecciones Eléctricas</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/30">Permiso Eléctrico: Programa Luz en Casa</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/32">Permiso Eléctrico: Medidor Múltiple</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/33">Autorización Trámite Bancario</a></li>';
				$nav .= '</ul>';
                $nav .= '</li>';  
                
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-check"></i>Ordenamiento Territorial<span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="tramites_online/tramites/agregar/20">Informe de Uso de Suelo(Aptitud Urbanística)</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/44">Ofrecimiento de donación</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/43">Denominación de Calles</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/34">Solicitud de Cartografía Digital</a></li>';
                $nav .= '<li><a href="tramites_online/tramites/agregar/48">Canje de Espacio de Equipamiento</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';     

                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-tint"></i>Aguas Luján<span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="tramites_online/tramites/agregar/54">Demarcación de Red de Agua y Cloacas</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/53">Autorización Vuelco de Efluentes Cloacales</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/51">Venta de Agua</a></li>';
				$nav .= '<li><a href="tramites_online/tramites/agregar/52">Certificado de Provisión de Agua Potable</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>'; 
                //editado por yoel grosso  de aca se da el boton del sidebar para los usuarios generales
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-user"></i>oficina de empleo<span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="oficina_de_empleo/escritorio">Inicio</a></li>';
				$nav .= '<li><a href="oficina_de_empleo/pedir_empleo/listar">Cargar cv</a></li>';
				$nav .= '<li><a href="oficina_de_empleo/Intermediacion/listar">Intermediacion laboral</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>'; 



        /*    
            $nav .= '<li><a href="tramites_online/escritorio"><i class="fa fa-exchange"></i> OBRAS PRIVADAS</a></li>';
            $nav .= '<li><a href="tramites_online/tramites/agregar/21"><i class="fa fa-angle-right"></i> Conexión Gas Domiciliario</a></li>';
            $nav .= '<li><a href="tramites_online/tramites/agregar/23"><i class="fa fa-angle-right"></i> Permiso Eléctrico: Reconexión</a></li>';
            $nav .= '<li><a href="tramites_online/tramites/agregar/24"><i class="fa fa-angle-right"></i> Permiso Eléctrico: Cambio de Sitio</a></li>';
            $nav .= '<li><a href="tramites_online/escritorio"><i class="fa fa-exchange"></i> ORD. TERRIRORIAL</a></li>';
            $nav .= '<li><a href="tramites_online/tramites/agregar/20"><i class="fa fa-angle-right"></i> Informe Urbanístico</a></li>';
            $nav .= '<li><a href="tramites_online/escritorio"><i class="fa fa-map"></i> CATASTRO</a></li>';
            $nav .= '<li><a href="tramites_online/tramites/agregar/19"><i class="fa fa-angle-right"></i> Registro Agrimensor</a></li>';
            $nav .= '<li><a href="tramites_online/tramites/agregar/16"><i class="fa fa-angle-right"></i> Fraccionamiento</a></li>';
            $nav .= '<li><a href="tramites_online/tramites/agregar/17"><i class="fa fa-angle-right"></i> Conjunto Inmob./Loteos</a></li>';
            $nav .= '<li><a href="tramites_online/tramites/agregar/22"><i class="fa fa-angle-right"></i> Cambio de Domicilio</a></li>';
        */    
        }
        // </editor-fold>
        // // // <editor-fold defaultstate="collapsed" desc="Permisos Menú Transferencias">
        $c_ad = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_transferencias_consulta, $grupos))
        {
            switch ($controlador)
            {
                case 'transferencias/escritorio':
                case 'transferencias/escritorio/index':
                    $c_ad['escritorio'] = ' class="current-page"';
                    break;
                case 'transferencias/deudas':
                    $c_ad['deudas'] = ' class="current-page"';
                    break;
                case 'transferencias/escribanos':
                    $c_ad['escribanos'] = ' class="current-page"';
                    break;
                case 'transferencias/numeraciones':
                    $c_ad['numeraciones'] = ' class="current-page"';
                    break;
                case 'transferencias/reportes':
                    $c_ad['reportes'] = ' class="current-page"';
                    break;
                case 'transferencias/adjuntos_tipos':
                    $c_ad['adjuntos_tipos'] = ' class="current-page"';
                    break;
                case 'transferencias/tramites_tipos':
                    $c_ad['tramites_tipos'] = ' class="current-page"';
                    break;
                case 'transferencias/tramites':
                    if ($url_actual === 'transferencias/tramites/bandeja_entrada' || $url_actual === 'transferencias/tramites/revisar')
                    {
                        $c_ad['bandeja'] = ' class="current-page"';
                    }
                    else
                    {
                        $c_ad['tramites'] = ' class="current-page"';
                    }
                    break;
                case 'transferencias/usuarios_oficinas':
                    $c_ad['usuarios_oficinas'] = ' class="current-page"';
                    break;
                case 'transferencias/manuales':
                    $c_ad['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_ad))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-exchange"></i> Transferencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_ad['escritorio']) ? '' : $c_ad['escritorio']) . '><a href="transferencias/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_ad['bandeja']) ? '' : $c_ad['bandeja']) . '><a href="transferencias/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li' . (empty($c_ad['deudas']) ? '' : $c_ad['deudas']) . '><a href="transferencias/deudas/consultar">Consulta de Deuda</a></li>';
                $nav .= '<li' . (empty($c_ad['escribanos']) ? '' : $c_ad['escribanos']) . '><a href="transferencias/escribanos/listar">Escribanos</a></li>';
                $nav .= '<li' . (empty($c_ad['numeraciones']) ? '' : $c_ad['numeraciones']) . '><a href="transferencias/numeraciones/listar">Numeraciones</a></li>';
                $nav .= '<li' . (empty($c_ad['reportes']) ? '' : $c_ad['reportes']) . '><a href="transferencias/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_ad['adjuntos_tipos']) ? '' : $c_ad['adjuntos_tipos']) . '><a href="transferencias/adjuntos_tipos/listar">Tipos de Adjuntos</a></li>';
                $nav .= '<li' . (empty($c_ad['tramites_tipos']) ? '' : $c_ad['tramites_tipos']) . '><a href="transferencias/tramites_tipos/listar">Tipos de Trámites</a></li>';
                $nav .= '<li' . (empty($c_ad['tramites']) ? '' : $c_ad['tramites']) . '><a href="transferencias/tramites/listar">Trámites</a></li>';
                $nav .= '<li' . (empty($c_ad['usuarios_oficinas']) ? '' : $c_ad['usuarios_oficinas']) . '><a href="transferencias/usuarios_oficinas/listar">Usuarios por Oficina</a></li>';
                $nav .= '<li' . (empty($c_ad['manuales']) ? '' : $c_ad['manuales']) . '><a href="transferencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-exchange"></i> Transferencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="transferencias/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="transferencias/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li><a href="transferencias/deudas/consultar">Consulta de Deuda</a></li>';
                $nav .= '<li><a href="transferencias/escribanos/listar">Escribanos</a></li>';
                $nav .= '<li><a href="transferencias/numeraciones/listar">Numeraciones</a></li>';
                $nav .= '<li><a href="transferencias/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="transferencias/adjuntos_tipos/listar">Tipos de Adjuntos</a></li>';
                $nav .= '<li><a href="transferencias/tramites_tipos/listar">Tipos de Trámites</a></li>';
                $nav .= '<li><a href="transferencias/tramites/listar">Trámites</a></li>';
                $nav .= '<li><a href="transferencias/usuarios_oficinas/listar">Usuarios por Oficina</a></li>';
                $nav .= '<li><a href="transferencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_transferencias_municipal, $grupos))
        {
            switch ($controlador)
            {
                case 'transferencias/escritorio':
                case 'transferencias/escritorio/index':
                    $c_ad['escritorio'] = ' class="current-page"';
                    break;
                case 'transferencias/deudas':
                    $c_ad['deudas'] = ' class="current-page"';
                    break;
                case 'transferencias/escribanos':
                    $c_ad['escribanos'] = ' class="current-page"';
                    break;
                case 'transferencias/reportes':
                    $c_ad['reportes'] = ' class="current-page"';
                    break;
                case 'transferencias/tramites':
                    if ($url_actual === 'transferencias/tramites/bandeja_entrada' || $url_actual === 'transferencias/tramites/revisar')
                    {
                        $c_ad['bandeja'] = ' class="current-page"';
                    }
                    else
                    {
                        $c_ad['tramites'] = ' class="current-page"';
                    }
                    break;
                case 'transferencias/manuales':
                    $c_ad['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_ad))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-exchange"></i> Transferencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_ad['escritorio']) ? '' : $c_ad['escritorio']) . '><a href="transferencias/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_ad['bandeja']) ? '' : $c_ad['bandeja']) . '><a href="transferencias/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li' . (empty($c_ad['deudas']) ? '' : $c_ad['deudas']) . '><a href="transferencias/deudas/consultar">Consulta de Deuda</a></li>';
                $nav .= '<li' . (empty($c_ad['escribanos']) ? '' : $c_ad['escribanos']) . '><a href="transferencias/escribanos/listar">Escribanos</a></li>';
                $nav .= '<li' . (empty($c_ad['reportes']) ? '' : $c_ad['reportes']) . '><a href="transferencias/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_ad['tramites']) ? '' : $c_ad['tramites']) . '><a href="transferencias/tramites/listar">Trámites</a></li>';
                $nav .= '<li' . (empty($c_ad['manuales']) ? '' : $c_ad['manuales']) . '><a href="transferencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-exchange"></i> Transferencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="transferencias/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="transferencias/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li><a href="transferencias/deudas/consultar">Consulta de Deuda</a></li>';
                $nav .= '<li><a href="transferencias/escribanos/listar">Escribanos</a></li>';
                $nav .= '<li><a href="transferencias/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="transferencias/tramites/listar">Trámites</a></li>';
                $nav .= '<li><a href="transferencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_transferencias_area, $grupos))
        {
            switch ($controlador)
            {
                case 'transferencias/escritorio':
                case 'transferencias/escritorio/index':
                    $c_ad['escritorio'] = ' class="current-page"';
                    break;
                case 'transferencias/tramites':
                    if ($url_actual === 'transferencias/tramites/bandeja_entrada' || $url_actual === 'transferencias/tramites/revisar')
                    {
                        $c_ad['bandeja'] = ' class="current-page"';
                    }
                    else
                    {
                        $c_ad['tramites'] = ' class="current-page"';
                    }
                    break;
                case 'transferencias/manuales':
                    $c_ad['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_ad))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-exchange"></i> Transferencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_ad['escritorio']) ? '' : $c_ad['escritorio']) . '><a href="transferencias/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_ad['bandeja']) ? '' : $c_ad['bandeja']) . '><a href="transferencias/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li' . (empty($c_ad['tramites']) ? '' : $c_ad['tramites']) . '><a href="transferencias/tramites/listar">Trámites</a></li>';
                $nav .= '<li' . (empty($c_ad['manuales']) ? '' : $c_ad['manuales']) . '><a href="transferencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-exchange"></i> Transferencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="transferencias/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="transferencias/tramites/bandeja_entrada">Bandeja de Entrada</a></li>';
                $nav .= '<li><a href="transferencias/tramites/listar">Trámites</a></li>';
                $nav .= '<li><a href="transferencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_transferencias_publico, $grupos))
        {
            switch ($controlador)
            {
                case 'transferencias/escritorio':
                case 'transferencias/escritorio/index':
                    $c_ad['escritorio'] = ' class="current-page"';
                    break;
                case 'transferencias/deudas':
                    $c_ad['deudas'] = ' class="current-page"';
                    break;
                case 'transferencias/tramites':
                    if ($url_actual === 'transferencias/tramites/bandeja_entrada_publico' || $url_actual === 'transferencias/tramites/revisar' || $url_actual === 'transferencias/tramites/editar')
                    {
                        $c_ad['bandeja'] = ' class="current-page"';
                    }
                    else
                    {
                        $c_ad['tramites'] = ' class="current-page"';
                    }
                    break;
                case 'transferencias/manuales':
                    $c_ad['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_ad))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-exchange"></i> Transferencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_ad['escritorio']) ? '' : $c_ad['escritorio']) . '><a href="transferencias/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_ad['bandeja']) ? '' : $c_ad['bandeja']) . '><a href="transferencias/tramites/bandeja_entrada_publico">Bandeja de Entrada</a></li>';
                $nav .= '<li' . (empty($c_ad['deudas']) ? '' : $c_ad['deudas']) . '><a href="transferencias/deudas/consultar">Consulta de Deuda</a></li>';
                $nav .= '<li' . (empty($c_ad['tramites']) ? '' : $c_ad['tramites']) . '><a href="transferencias/tramites/listar_publico">Trámites</a></li>';
                $nav .= '<li' . (empty($c_ad['manuales']) ? '' : $c_ad['manuales']) . '><a href="transferencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-exchange"></i> Transferencias <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="transferencias/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="transferencias/tramites/bandeja_entrada_publico">Bandeja de Entrada</a></li>';
                $nav .= '<li><a href="transferencias/deudas/consultar">Consulta de Deuda</a></li>';
                $nav .= '<li><a href="transferencias/tramites/listar_publico">Trámites</a></li>';
                $nav .= '<li><a href="transferencias/manuales">Manual de Usuario</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // // <editor-fold defaultstate="collapsed" desc="Permisos Menú Vales Combustible">
        $c_a = array();
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_vales_combustible_consulta, $grupos) || in_groups($grupos_vales_combustible_contaduria, $grupos))
        {
            switch ($url_actual)
            {
                case 'vales_combustible/escritorio':
                case 'vales_combustible/escritorio/index':
                    $c_a['escritorio'] = ' class="current-page"';
                    break;
                case 'vales_combustible/autorizaciones/listar':
                case 'vales_combustible/autorizaciones/listar_pendientes':
                case 'vales_combustible/autorizaciones/agregar':
                case 'vales_combustible/autorizaciones/editar':
                case 'vales_combustible/autorizaciones/anular':
                case 'vales_combustible/autorizaciones/ver':
                    $c_a['autorizaciones'] = ' class="current-page"';
                    break;
                case 'vales_combustible/cupos_combustible/listar':
                case 'vales_combustible/cupos_combustible/agregar':
                case 'vales_combustible/cupos_combustible/editar':
                case 'vales_combustible/cupos_combustible/eliminar':
                case 'vales_combustible/cupos_combustible/ver':
                    $c_a['cupos_combustible'] = ' class="current-page"';
                    break;
                case 'vales_combustible/estaciones/listar':
                case 'vales_combustible/estaciones/agregar':
                case 'vales_combustible/estaciones/editar':
                case 'vales_combustible/estaciones/eliminar':
                case 'vales_combustible/estaciones/ver':
                    $c_a['estaciones'] = ' class="current-page"';
                    break;
                case 'vales_combustible/facturas/listar':
                case 'vales_combustible/facturas/agregar':
                case 'vales_combustible/facturas/editar':
                case 'vales_combustible/facturas/eliminar':
                case 'vales_combustible/facturas/ver':
                    $c_a['facturas'] = ' class="current-page"';
                    break;
                case 'vales_combustible/ordenes_compra/listar':
                case 'vales_combustible/ordenes_compra/agregar':
                case 'vales_combustible/ordenes_compra/editar':
                case 'vales_combustible/ordenes_compra/eliminar':
                case 'vales_combustible/ordenes_compra/ver':
                    $c_a['ordenes_compra'] = ' class="current-page"';
                    break;
                case 'vales_combustible/remitos/listar':
                case 'vales_combustible/remitos/agregar':
                case 'vales_combustible/remitos/editar':
                case 'vales_combustible/remitos/eliminar':
                case 'vales_combustible/remitos/ver':
                    $c_a['remitos'] = ' class="current-page"';
                    break;
                case 'vales_combustible/reportes/listar':
                case 'vales_combustible/reportes/facturas':
                case 'vales_combustible/reportes/areas':
                case 'vales_combustible/reportes/ordenes_compra':
                case 'vales_combustible/reportes/ordenes_compra_detalle':
                case 'vales_combustible/reportes/tipos_combustible':
                case 'vales_combustible/reportes/emitidos':
                case 'vales_combustible/reportes/fuera_termino':
                case 'vales_combustible/reportes/resumen_vales':
                case 'vales_combustible/reportes/sin_uso':
                case 'vales_combustible/reportes/vencidos':
                    $c_a['reportes'] = ' class="current-page"';
                    break;
                case 'vales_combustible/tipos_adjuntos/listar':
                case 'vales_combustible/tipos_adjuntos/agregar':
                case 'vales_combustible/tipos_adjuntos/editar':
                case 'vales_combustible/tipos_adjuntos/eliminar':
                case 'vales_combustible/tipos_adjuntos/ver':
                    $c_a['tipos_adjuntos'] = ' class="current-page"';
                    break;
                case 'vales_combustible/tipos_combustible/listar':
                case 'vales_combustible/tipos_combustible/agregar':
                case 'vales_combustible/tipos_combustible/editar':
                case 'vales_combustible/tipos_combustible/eliminar':
                case 'vales_combustible/tipos_combustible/ver':
                    $c_a['tipos_combustible'] = ' class="current-page"';
                    break;
                case 'vales_combustible/tipos_vehiculo/listar':
                case 'vales_combustible/tipos_vehiculo/agregar':
                case 'vales_combustible/tipos_vehiculo/editar':
                case 'vales_combustible/tipos_vehiculo/eliminar':
                case 'vales_combustible/tipos_vehiculo/ver':
                    $c_a['tipos_vehiculo'] = ' class="current-page"';
                    break;
                case 'vales_combustible/usuarios_areas/listar':
                case 'vales_combustible/usuarios_areas/agregar':
                case 'vales_combustible/usuarios_areas/editar':
                case 'vales_combustible/usuarios_areas/eliminar':
                case 'vales_combustible/usuarios_areas/ver':
                    $c_a['usuarios_areas'] = ' class="current-page"';
                    break;
                case 'vales_combustible/vales/listar':
                case 'vales_combustible/vales/agregar':
                case 'vales_combustible/vales/agregar_masivo':
                case 'vales_combustible/vales/editar':
                case 'vales_combustible/vales/editar_hac':
                case 'vales_combustible/vales/editar_con':
                case 'vales_combustible/vales/imprimir':
                case 'vales_combustible/vales/imprimir_planilla':
                case 'vales_combustible/vales/anular':
                case 'vales_combustible/vales/anular_masivo':
                case 'vales_combustible/vales/desanular':
                case 'vales_combustible/vales/ver':
                    $c_a['vales'] = ' class="current-page"';
                    break;
                case 'vales_combustible/vales/listar_pendientes':
                    $c_a['vales_pendientes'] = ' class="current-page"';
                    break;
                case 'vales_combustible/valores_combustible/listar':
                case 'vales_combustible/valores_combustible/agregar':
                case 'vales_combustible/valores_combustible/editar':
                case 'vales_combustible/valores_combustible/eliminar':
                case 'vales_combustible/valores_combustible/ver':
                    $c_a['valores_combustible'] = ' class="current-page"';
                    break;
                case 'vales_combustible/vehiculos/listar':
                case 'vales_combustible/vehiculos/agregar':
                case 'vales_combustible/vehiculos/editar':
                case 'vales_combustible/vehiculos/eliminar':
                case 'vales_combustible/vehiculos/ver':
                    $c_a['vehiculos'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_a))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_a['escritorio']) ? '' : $c_a['escritorio']) . '><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_a['autorizaciones']) ? '' : $c_a['autorizaciones']) . '><a href="vales_combustible/autorizaciones/listar">Autorizaciones</a></li>';
                $nav .= '<li' . (empty($c_a['cupos_combustible']) ? '' : $c_a['cupos_combustible']) . '><a href="vales_combustible/cupos_combustible/listar">Cupos Combustible</a></li>';
                $nav .= '<li' . (empty($c_a['estaciones']) ? '' : $c_a['estaciones']) . '><a href="vales_combustible/estaciones/listar">Estaciones</a></li>';
                $nav .= '<li' . (empty($c_a['facturas']) ? '' : $c_a['facturas']) . '><a href="vales_combustible/facturas/listar">Facturas</a></li>';
                $nav .= '<li' . (empty($c_a['ordenes_compra']) ? '' : $c_a['ordenes_compra']) . '><a href="vales_combustible/ordenes_compra/listar">Órdenes de Compra</a></li>';
                $nav .= '<li' . (empty($c_a['remitos']) ? '' : $c_a['remitos']) . '><a href="vales_combustible/remitos/listar">Remitos</a></li>';
                $nav .= '<li' . (empty($c_a['reportes']) ? '' : $c_a['reportes']) . '><a href="vales_combustible/reportes/listar">Reportes</a></li>';
                if (in_groups($grupos_admin, $grupos))
                {
                    $nav .= '<li' . (empty($c_a['tipos_adjuntos']) ? '' : $c_a['tipos_adjuntos']) . '><a href="vales_combustible/tipos_adjuntos/listar">Tipos Adjuntos</a></li>';
                    $nav .= '<li' . (empty($c_a['tipos_combustible']) ? '' : $c_a['tipos_combustible']) . '><a href="vales_combustible/tipos_combustible/listar">Tipos Combustible</a></li>';
                    $nav .= '<li' . (empty($c_a['tipos_vehiculo']) ? '' : $c_a['tipos_vehiculo']) . '><a href="vales_combustible/tipos_vehiculo/listar">Tipos Vehículo</a></li>';
                    $nav .= '<li' . (empty($c_a['usuarios_areas']) ? '' : $c_a['usuarios_areas']) . '><a href="vales_combustible/usuarios_areas/listar">Usuarios Áreas</a></li>';
                }
                $nav .= '<li' . (empty($c_a['vales']) ? '' : $c_a['vales']) . '><a href="vales_combustible/vales/listar">Vales</a></li>';
                if (in_groups($grupos_admin, $grupos))
                {
                    $nav .= '<li' . (empty($c_a['vales_pendientes']) ? '' : $c_a['vales_pendientes']) . '><a href="vales_combustible/vales/listar_pendientes">Vales Pendientes</a></li>';
                }
                $nav .= '<li' . (empty($c_a['valores_combustible']) ? '' : $c_a['valores_combustible']) . '><a href="vales_combustible/valores_combustible/listar">Valores Combustible</a></li>';
                $nav .= '<li' . (empty($c_a['vehiculos']) ? '' : $c_a['vehiculos']) . '><a href="vales_combustible/vehiculos/listar">Vehículos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="vales_combustible/autorizaciones/listar">Autorizaciones</a></li>';
                $nav .= '<li><a href="vales_combustible/cupos_combustible/listar">Cupos Combustible</a></li>';
                $nav .= '<li><a href="vales_combustible/estaciones/listar">Estaciones</a></li>';
                $nav .= '<li><a href="vales_combustible/facturas/listar">Facturas</a></li>';
                $nav .= '<li><a href="vales_combustible/ordenes_compra/listar">Órdenes de Compra</a></li>';
                $nav .= '<li><a href="vales_combustible/remitos/listar">Remitos</a></li>';
                $nav .= '<li><a href="vales_combustible/reportes/listar">Reportes</a></li>';
                if (in_groups($grupos_admin, $grupos))
                {
                    $nav .= '<li><a href="vales_combustible/tipos_combustible/listar">Tipos Combustible</a></li>';
                    $nav .= '<li><a href="vales_combustible/tipos_vehiculo/listar">Tipos Vehículo</a></li>';
                    $nav .= '<li><a href="vales_combustible/usuarios_areas/listar">Usuarios Áreas</a></li>';
                }
                $nav .= '<li><a href="vales_combustible/vales/listar">Vales</a></li>';
                if (in_groups($grupos_admin, $grupos))
                {
                    $nav .= '<li><a href="vales_combustible/vales/listar_pendientes">Vales Pendientes</a></li>';
                }
                $nav .= '<li><a href="vales_combustible/valores_combustible/listar">Valores Combustible</a></li>';
                $nav .= '<li><a href="vales_combustible/vehiculos/listar">Vehículos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_vales_combustible_hacienda, $grupos))
        {
            switch ($url_actual)
            {
                case 'vales_combustible/escritorio':
                case 'vales_combustible/escritorio/index':
                    $c_a['escritorio'] = ' class="current-page"';
                    break;
                case 'vales_combustible/cupos_combustible/listar':
                case 'vales_combustible/cupos_combustible/agregar':
                case 'vales_combustible/cupos_combustible/editar':
                case 'vales_combustible/cupos_combustible/eliminar':
                case 'vales_combustible/cupos_combustible/ver':
                    $c_a['cupos_combustible'] = ' class="current-page"';
                    break;
                case 'vales_combustible/reportes/listar':
                case 'vales_combustible/reportes/resumen_vales':
                    $c_a['reportes'] = ' class="current-page"';
                    break;
                case 'vales_combustible/vales/listar':
                case 'vales_combustible/vales/agregar':
                case 'vales_combustible/vales/agregar_masivo':
                case 'vales_combustible/vales/editar':
                case 'vales_combustible/vales/editar_hac':
                case 'vales_combustible/vales/editar_con':
                case 'vales_combustible/vales/imprimir':
                case 'vales_combustible/vales/anular':
                case 'vales_combustible/vales/anular_masivo':
                case 'vales_combustible/vales/desanular':
                case 'vales_combustible/vales/ver':
                    $c_a['vales'] = ' class="current-page"';
                    break;
                case 'vales_combustible/vales/listar_pendientes':
                    $c_a['vales_pendientes'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_a))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_a['escritorio']) ? '' : $c_a['escritorio']) . '><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_a['cupos_combustible']) ? '' : $c_a['cupos_combustible']) . '><a href="vales_combustible/cupos_combustible/listar">Cupos Combustible</a></li>';
                $nav .= '<li' . (empty($c_a['reportes']) ? '' : $c_a['reportes']) . '><a href="vales_combustible/reportes/listar">Reportes</a></li>';
                $nav .= '<li' . (empty($c_a['vales']) ? '' : $c_a['vales']) . '><a href="vales_combustible/vales/listar">Vales</a></li>';
                $nav .= '<li' . (empty($c_a['vales_pendientes']) ? '' : $c_a['vales_pendientes']) . '><a href="vales_combustible/vales/listar_pendientes">Vales Pendientes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="vales_combustible/cupos_combustible/listar">Cupos Combustible</a></li>';
                $nav .= '<li><a href="vales_combustible/reportes/listar">Reportes</a></li>';
                $nav .= '<li><a href="vales_combustible/vales/listar">Vales</a></li>';
                $nav .= '<li><a href="vales_combustible/vales/listar_pendientes">Vales Pendientes</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_vales_combustible_autorizaciones, $grupos))
        {
            switch ($url_actual)
            {
                case 'vales_combustible/escritorio':
                case 'vales_combustible/escritorio/index':
                    $c_a['escritorio'] = ' class="current-page"';
                    break;
                case 'vales_combustible/autorizaciones/listar':
                case 'vales_combustible/autorizaciones/agregar':
                case 'vales_combustible/autorizaciones/editar':
                case 'vales_combustible/autorizaciones/anular':
                case 'vales_combustible/autorizaciones/ver':
                    $c_a['autorizaciones'] = ' class="current-page"';
                    break;
                case 'vales_combustible/autorizaciones/listar_pendientes':
                case 'vales_combustible/autorizaciones/cargar':
                    $c_a['autorizaciones_p'] = ' class="current-page"';
                    break;
                case 'vales_combustible/vehiculos/listar':
                case 'vales_combustible/vehiculos/agregar':
                case 'vales_combustible/vehiculos/editar':
                case 'vales_combustible/vehiculos/eliminar':
                case 'vales_combustible/vehiculos/ver':
                    $c_a['vehiculos'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_a))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_a['escritorio']) ? '' : $c_a['escritorio']) . '><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_a['autorizaciones']) ? '' : $c_a['autorizaciones']) . '><a href="vales_combustible/autorizaciones/listar">Autorizaciones</a></li>';
                $nav .= '<li' . (empty($c_a['autorizaciones_p']) ? '' : $c_a['autorizaciones_p']) . '><a href="vales_combustible/autorizaciones/listar_pendientes">Autorizaciones Pendientes</a></li>';
                $nav .= '<li' . (empty($c_a['vehiculos']) ? '' : $c_a['vehiculos']) . '><a href="vales_combustible/vehiculos/listar">Vehículos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="vales_combustible/autorizaciones/listar">Autorizaciones</a></li>';
                $nav .= '<li><a href="vales_combustible/autorizaciones/listar_pendientes">Autorizaciones Pendientes</a></li>';
                $nav .= '<li><a href="vales_combustible/vehiculos/listar">Vehículos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_vales_combustible_obrador, $grupos))
        {
            switch ($url_actual)
            {
                case 'vales_combustible/escritorio':
                case 'vales_combustible/escritorio/index':
                    $c_a['escritorio'] = ' class="current-page"';
                    break;
                case 'vales_combustible/autorizaciones/listar':
                case 'vales_combustible/autorizaciones/agregar':
                case 'vales_combustible/autorizaciones/editar':
                case 'vales_combustible/autorizaciones/anular':
                case 'vales_combustible/autorizaciones/ver':
                    $c_a['autorizaciones'] = ' class="current-page"';
                    break;
                case 'vales_combustible/vehiculos/listar':
                case 'vales_combustible/vehiculos/agregar':
                case 'vales_combustible/vehiculos/editar':
                case 'vales_combustible/vehiculos/eliminar':
                case 'vales_combustible/vehiculos/ver':
                    $c_a['vehiculos'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_a))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_a['escritorio']) ? '' : $c_a['escritorio']) . '><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_a['autorizaciones']) ? '' : $c_a['autorizaciones']) . '><a href="vales_combustible/autorizaciones/listar">Autorizaciones</a></li>';
                $nav .= '<li' . (empty($c_a['vehiculos']) ? '' : $c_a['vehiculos']) . '><a href="vales_combustible/vehiculos/listar">Vehículos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="vales_combustible/autorizaciones/listar">Autorizaciones</a></li>';
                $nav .= '<li><a href="vales_combustible/vehiculos/listar">Vehículos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_vales_combustible_estacion, $grupos))
        {
            switch ($url_actual)
            {
                case 'vales_combustible/escritorio':
                case 'vales_combustible/escritorio/index':
                    $c_a['escritorio'] = ' class="current-page"';
                    break;
                case 'vales_combustible/autorizaciones/listar_pendientes':
                case 'vales_combustible/autorizaciones/cargar':
                case 'vales_combustible/autorizaciones/ver':
                    $c_a['autorizaciones'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_a))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_a['escritorio']) ? '' : $c_a['escritorio']) . '><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_a['autorizaciones']) ? '' : $c_a['autorizaciones']) . '><a href="vales_combustible/autorizaciones/listar_pendientes">Autorizaciones</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="vales_combustible/autorizaciones/listar_pendientes">Autorizaciones</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        elseif (in_groups($grupos_vales_combustible_areas, $grupos))
        {
            switch ($url_actual)
            {
                case 'vales_combustible/escritorio':
                case 'vales_combustible/escritorio/index':
                    $c_a['escritorio'] = ' class="current-page"';
                    break;
                case 'vales_combustible/vales/listar_areas':
                case 'vales_combustible/vales/solicitar':
                case 'vales_combustible/vales/editar_area':
                case 'vales_combustible/vales/ver':
                    $c_a['vales'] = ' class="current-page"';
                    break;
                case 'vales_combustible/vehiculos/listar':
                case 'vales_combustible/vehiculos/agregar_area':
                case 'vales_combustible/vehiculos/editar_area':
                case 'vales_combustible/vehiculos/eliminar':
                case 'vales_combustible/vehiculos/ver':
                    $c_a['vehiculos'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_a))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_a['escritorio']) ? '' : $c_a['escritorio']) . '><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li' . (empty($c_a['vales']) ? '' : $c_a['vales']) . '><a href="vales_combustible/vales/listar_areas">Vales</a></li>';
                $nav .= '<li' . (empty($c_a['vehiculos']) ? '' : $c_a['vehiculos']) . '><a href="vales_combustible/vehiculos/listar">Vehículos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-truck"></i> Vales Combustible <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="vales_combustible/escritorio">Inicio</a></li>';
                $nav .= '<li><a href="vales_combustible/vales/listar_areas">Vales</a></li>';
                $nav .= '<li><a href="vales_combustible/vehiculos/listar">Vehículos</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // // <editor-fold defaultstate="collapsed" desc="Permisos Parámetros">
        $c_ad = array();
        if (in_groups($grupos_admin, $grupos))
        {
            switch ($controlador)
            {
                case 'areas':
                    $c_ad['areas'] = ' class="current-page"';
                    break;
                case 'departamentos':
                    $c_ad['departamentos'] = ' class="current-page"';
                    break;
                case 'domicilios':
                    $c_ad['domicilios'] = ' class="current-page"';
                    break;
                case 'localidades':
                    $c_ad['localidades'] = ' class="current-page"';
                    break;
                case 'manuales_categorias':
                    $c_ad['manuales_categorias'] = ' class="current-page"';
                    break;
                case 'manuales':
                    $c_ad['manuales'] = ' class="current-page"';
                    break;
                case 'nacionalidades':
                    $c_ad['nacionalidades'] = ' class="current-page"';
                    break;
                case 'personas':
                    $c_ad['personas'] = ' class="current-page"';
                    break;
                case 'provincias':
                    $c_ad['provincias'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_ad))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-wrench"></i> Parámetros <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_ad['areas']) ? '' : $c_ad['areas']) . '><a href="areas/listar">Áreas</a></li>';
                $nav .= '<li' . (empty($c_ad['departamentos']) ? '' : $c_ad['departamentos']) . '><a href="departamentos/listar">Departamentos</a></li>';
                $nav .= '<li' . (empty($c_ad['domicilios']) ? '' : $c_ad['domicilios']) . '><a href="domicilios/listar">Domicilios</a></li>';
                $nav .= '<li' . (empty($c_ad['localidades']) ? '' : $c_ad['localidades']) . '><a href="localidades/listar">Localidades</a></li>';
                $nav .= '<li' . (empty($c_ad['manuales_categorias']) ? '' : $c_ad['manuales_categorias']) . '><a href="manuales_categorias/listar">Manuales Categorías</a></li>';
                $nav .= '<li' . (empty($c_ad['manuales']) ? '' : $c_ad['manuales']) . '><a href="manuales/listar">Manuales</a></li>';
                $nav .= '<li' . (empty($c_ad['nacionalidades']) ? '' : $c_ad['nacionalidades']) . '><a href="nacionalidades/listar">Nacionalidades</a></li>';
                $nav .= '<li' . (empty($c_ad['personas']) ? '' : $c_ad['personas']) . '><a href="personas/listar">Personas</a></li>';
                $nav .= '<li' . (empty($c_ad['provincias']) ? '' : $c_ad['provincias']) . '><a href="provincias/listar">Provincias</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-wrench"></i> Parámetros <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="areas/listar">Áreas</a></li>';
                $nav .= '<li><a href="departamentos/listar">Departamentos</a></li>';
                $nav .= '<li><a href="domicilios/listar">Domicilios</a></li>';
                $nav .= '<li><a href="localidades/listar">Localidades</a></li>';
                $nav .= '<li><a href="manuales_categorias/listar">Manuales Categorías</a></li>';
                $nav .= '<li><a href="manuales/listar">Manuales</a></li>';
                $nav .= '<li><a href="nacionalidades/listar">Nacionalidades</a></li>';
                $nav .= '<li><a href="personas/listar">Personas</a></li>';
                $nav .= '<li><a href="provincias/listar">Provincias</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        else if (in_groups($grupos_admin_manuales, $grupos))
        {
            switch ($controlador)
            {
                case 'manuales_categorias':
                    $c_ad['manuales_categorias'] = ' class="current-page"';
                    break;
                case 'manuales':
                    $c_ad['manuales'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_ad))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-wrench"></i> Parámetros <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_ad['manuales_categorias']) ? '' : $c_ad['manuales_categorias']) . '><a href="manuales_categorias/listar">Manuales Categorías</a></li>';
                $nav .= '<li' . (empty($c_ad['manuales']) ? '' : $c_ad['manuales']) . '><a href="manuales/listar">Manuales</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-wrench"></i> Parámetros <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="manuales_categorias/listar">Manuales Categorías</a></li>';
                $nav .= '<li><a href="manuales/listar">Manuales</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Permisos Menú Admin">
        $c_ad = array();
        if (in_groups($grupos_admin, $grupos))
        {
            switch ($controlador)
            {
                case 'grupos':
                    $c_ad['grupos'] = ' class="current-page"';
                    break;
                case 'modulos':
                    $c_ad['modulos'] = ' class="current-page"';
                    break;
                case 'usuarios':
                    $c_ad['usuarios'] = ' class="current-page"';
                    break;
            }
            if (!empty($c_ad))
            {
                $nav .= '<li class="current-page active active-init">';
                $nav .= '<a class="active"><i class="fa fa-gears"></i> Administrar <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu child_menu_open">';
                $nav .= '<li' . (empty($c_ad['grupos']) ? '' : $c_ad['grupos']) . '><a href="grupos/listar">Grupos</a></li>';
                $nav .= '<li' . (empty($c_ad['modulos']) ? '' : $c_ad['modulos']) . '><a href="modulos/listar">Módulos</a></li>';
                $nav .= '<li' . (empty($c_ad['usuarios']) ? '' : $c_ad['usuarios']) . '><a href="usuarios/listar">Usuarios</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
            else
            {
                $nav .= '<li>';
                $nav .= '<a><i class="fa fa-gears"></i> Administrar <span class="fa fa-chevron-down"></span></a>';
                $nav .= '<ul class="nav child_menu">';
                $nav .= '<li><a href="grupos/listar">Grupos</a></li>';
                $nav .= '<li><a href="modulos/listar">Módulos</a></li>';
                $nav .= '<li><a href="usuarios/listar">Usuarios</a></li>';
                $nav .= '</ul>';
                $nav .= '</li>';
            }
        }
        // </editor-fold>
        if (in_groups($grupos_manuales, $grupos))
        {
            $nav .= ($controlador === 'manuales') ? '<li class="current-page active active-init"><a class="active" href="manuales/visualizar"><i class="fa fa-question"></i> Manuales</a></li>' : '<li><a href="manuales/visualizar"><i class="fa fa-question"></i> Manuales</a></li>';
        }
        return array('nav' => $nav, 'user_menu' => $user_menu);
    }
}

// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio General">
if (!function_exists('load_permisos_escritorio'))
{

    function load_permisos_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_actasisp = array('admin', 'actasisp_user', 'actasisp_inspector', 'actasisp_consulta_general');
        $grupos_antenas = array('admin', 'antenas_admin', 'antenas_consulta_general');
        $grupos_asesoria_letrada = array('admin', 'asesoria_letrada_admin', 'asesoria_letrada_consulta_general', 'asesoria_letrada_user', 'asesoria_letrada_area');
        $grupos_asistencia = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user', 'asistencia_consulta_general');
        $grupos_defunciones = array('admin', 'defunciones_user', 'defunciones_consulta_general');
        $grupos_desarrollo_social = array('admin', 'desarrollo_social_user', 'desarrollo_social_consulta_general');
        $grupos_gobierno = array('admin', 'gobierno_user', 'gobierno_consulta_general');
        $grupos_incidencias = array('admin', 'incidencias_admin', 'incidencias_consulta_general', 'incidencias_user', 'incidencias_area');
        $grupos_lujan_pass = array('admin', 'lujan_pass_control', 'lujan_pass_publico', 'lujan_pass_beneficiario', 'lujan_pass_consulta_general');
        $grupos_mas_beneficios = array('admin', 'mas_beneficios_control', 'mas_beneficios_publico', 'mas_beneficios_beneficiario', 'mas_beneficios_consulta_general');
        $grupos_major = array('admin', 'major_boletos', 'major_deudas', 'major_deudas_masivas', 'major_solicitudes', 'major_consulta_general');
        $grupos_ninez_adolescencia = array('admin', 'ninez_adolescencia_admin', 'ninez_adolescencia_consulta_general', 'ninez_adolescencia_user');
        $grupos_notificaciones = array('admin', 'notificaciones_user', 'notificaciones_areas', 'notificaciones_notificadores', 'notificaciones_control');
        $grupos_obrador = array('admin', 'obrador_user', 'obrador_consulta_general');
        $grupos_reclamos_major = array('admin', 'reclamos_major_admin', 'reclamos_major_consulta_general');
        $grupos_reclamos_gis = array('admin', 'reclamos_gis_user', 'reclamos_gis_consulta_general');
        $grupos_oficina_de_empleo = array('admin', 'oficina_empleo', 'oficina_empleo_general','tramites_online_publico');//editado por yoel grosso
        $grupos_recursos_humanos = array('admin', 'recursos_humanos_admin', 'recursos_humanos_user', 'recursos_humanos_director', 'recursos_humanos_publico', 'recursos_humanos_consulta_general');
        $grupos_resoluciones = array('admin', 'resoluciones_user', 'resoluciones_consulta_general');
        $grupos_stock_informatica = array('admin', 'stock_informatica_user', 'stock_informatica_consulta_general');
        $grupos_tablero = array('admin', 'tablero_consulta_general');
        $grupos_telefonia = array('admin', 'telefonia_admin', 'telefonia_consulta_general');
        $grupos_toner = array('admin', 'toner_admin', 'toner_consulta_general');
        $grupos_tramites_online = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_publico', 'tramites_online_consulta_general');
        $grupos_transferencias = array('admin', 'transferencias_municipal', 'transferencias_area', 'transferencias_publico', 'transferencias_consulta_general');
        $grupos_vales_combustible = array('admin', 'vales_combustible_contaduria', 'vales_combustible_hacienda', 'vales_combustible_areas', 'vales_combustible_autorizaciones', 'vales_combustible_obrador', 'vales_combustible_estacion', 'vales_combustible_consulta_general');
        //PARA MOSTRAR MANUALES
        $grupos_manuales = array(
            'admin',
            'actasisp_user', 'actasisp_inspector', 'actasisp_consulta_general',
            'antenas_admin', 'antenas_consulta_general',
            'asesoria_letrada_admin', 'asesoria_letrada_consulta_general', 'asesoria_letrada_user', 'asesoria_letrada_area',
            'asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user', 'asistencia_consulta_general',
            'defunciones_user', 'defunciones_consulta_general',
            'desarrollo_social_user', 'desarrollo_social_consulta_general',
            'gobierno_user', 'gobierno_consulta_general',
            'incidencias_admin', 'incidencias_consulta_general', 'incidencias_user', 'incidencias_area',
            'lujan_pass_control', 'lujan_pass_consulta_general',
            'mas_beneficios_control', 'mas_beneficios_consulta_general',
            'major_boletos', 'major_deudas', 'major_deudas_masivas', 'major_solicitudes', 'major_consulta_general',
            'ninez_adolescencia_admin', 'ninez_adolescencia_consulta_general', 'ninez_adolescencia_user',
            'notificaciones_user', 'notificaciones_areas', 'notificaciones_notificadores', 'notificaciones_control',
            'obrador_user', 'obrador_consulta_general',
            'reclamos_major_admin', 'reclamos_major_consulta_general',
            'reclamos_gis_user', 'reclamos_gis_consulta_general',
            'oficina_empleo', 'oficina_empleo_general',      //editado por yoel grosso
            'recursos_humanos_admin', 'recursos_humanos_user', 'recursos_humanos_director', 'recursos_humanos_publico', 'recursos_humanos_consulta_general',
            'resoluciones_user', 'resoluciones_consulta_general',
            'stock_informatica_user', 'stock_informatica_consulta_general',
            'tablero_consulta_general',
            'telefonia_admin', 'telefonia_consulta_general',
            'toner_admin', 'toner_consulta_general',
            'tramites_online_admin', 'tramites_online_area', 'tramites_online_consulta_general',
            'transferencias_municipal', 'transferencias_area', 'transferencias_consulta_general',
            'vales_combustible_contaduria', 'vales_combustible_hacienda', 'vales_combustible_areas', 'vales_combustible_autorizaciones', 'vales_combustible_obrador', 'vales_combustible_estacion', 'vales_combustible_consulta_general'
        );

        if (in_groups($grupos_actasisp, $grupos))
        {
            $accesos[$indice]['href'] = 'actasisp/escritorio';
            $accesos[$indice]['title'] = 'Actas ISP';
            $accesos[$indice]['icon'] = 'fa-file';
            $indice++;
        }
        if (in_groups($grupos_antenas, $grupos))
        {
            $accesos[$indice]['href'] = 'antenas/escritorio';
            $accesos[$indice]['title'] = 'Antenas';
            $accesos[$indice]['icon'] = 'fa-wifi';
            $indice++;
        }
        if (in_groups($grupos_asesoria_letrada, $grupos))
        {
            $accesos[$indice]['href'] = 'asesoria_letrada/escritorio';
            $accesos[$indice]['title'] = 'Asesoría Letrada';
            $accesos[$indice]['icon'] = 'fa-book';
            $indice++;
        }
        if (in_groups($grupos_asistencia, $grupos))
        {
            $accesos[$indice]['href'] = 'asistencia/escritorio';
            $accesos[$indice]['title'] = 'Asistencia';
            $accesos[$indice]['icon'] = 'fa-clock-o';
            $indice++;
        }
        if (in_groups($grupos_defunciones, $grupos))
        {
            $accesos[$indice]['href'] = 'defunciones/escritorio';
            $accesos[$indice]['title'] = 'Defunciones';
            $accesos[$indice]['icon'] = 'fa-university';
            $indice++;
        }
        if (in_groups($grupos_desarrollo_social, $grupos))
        {
            $accesos[$indice]['href'] = 'desarrollo_social/escritorio';
            $accesos[$indice]['title'] = 'Desarrollo Social';
            $accesos[$indice]['icon'] = 'fa-universal-access';
            $indice++;
        }
        if (in_groups($grupos_gobierno, $grupos))
        {
            $accesos[$indice]['href'] = 'gobierno/escritorio';
            $accesos[$indice]['title'] = 'Gobierno';
            $accesos[$indice]['icon'] = 'fa-file-text-o';
            $indice++;
        }
        if (in_groups($grupos_incidencias, $grupos))
        {
            $accesos[$indice]['href'] = 'incidencias/escritorio';
            $accesos[$indice]['title'] = 'Incidencias';
            $accesos[$indice]['icon'] = 'fa-warning';
            $indice++;
        }
        if (in_groups($grupos_lujan_pass, $grupos))
        {
            $accesos[$indice]['href'] = 'lujan_pass/escritorio';
            $accesos[$indice]['title'] = 'Luján Pass';
            $accesos[$indice]['icon'] = 'fa-map-marker';
            $indice++;
        }
        if (in_groups($grupos_major, $grupos))
        {
            $accesos[$indice]['href'] = 'major/escritorio';
            $accesos[$indice]['title'] = 'M@jor';
            $accesos[$indice]['icon'] = 'fa-at';
            $indice++;
        }
        if (in_groups($grupos_mas_beneficios, $grupos))
        {
            $accesos[$indice]['href'] = 'mas_beneficios/escritorio';
            $accesos[$indice]['title'] = 'Más Beneficios';
            $accesos[$indice]['icon'] = 'fa-plus';
            $indice++;
        }
        if (in_groups($grupos_ninez_adolescencia, $grupos))
        {
            $accesos[$indice]['href'] = 'ninez_adolescencia/escritorio';
            $accesos[$indice]['title'] = 'Niñez y Adolescencia';
            $accesos[$indice]['icon'] = 'fa-child';
            $indice++;
        }
        if (in_groups($grupos_notificaciones, $grupos))
        {
            $accesos[$indice]['href'] = 'notificaciones/escritorio';
            $accesos[$indice]['title'] = 'Notificaciones';
            $accesos[$indice]['icon'] = 'fa-file-o';
            $indice++;
        }
        if (in_groups($grupos_obrador, $grupos))
        {
            $accesos[$indice]['href'] = 'obrador/escritorio';
            $accesos[$indice]['title'] = 'Obrador';
            $accesos[$indice]['icon'] = 'fa-cubes';
            $indice++;
        }
        if (in_groups($grupos_reclamos_major, $grupos))
        {
            $accesos[$indice]['href'] = 'reclamos_major/escritorio';
            $accesos[$indice]['title'] = 'Reclamos Major';
            $accesos[$indice]['icon'] = 'fa-warning';
            $indice++;
        }
        if (in_groups($grupos_reclamos_gis, $grupos))
        {
            $accesos[$indice]['href'] = 'reclamos_gis/escritorio';
            $accesos[$indice]['title'] = 'Reclamos GIS';
            $accesos[$indice]['icon'] = 'fa-map';
            $indice++;
        }
        if (in_groups($grupos_oficina_de_empleo, $grupos)) //editado por yoel grosso
        {
            $accesos[$indice]['href'] = 'oficina_de_empleo/escritorio';
            $accesos[$indice]['title'] = 'Oficina de empleo';
            $accesos[$indice]['icon'] = 'fa-user';
            $indice++;
        }
        if (in_groups($grupos_recursos_humanos, $grupos))
        {
            $accesos[$indice]['href'] = 'recursos_humanos/escritorio';
            $accesos[$indice]['title'] = 'Recursos Humanos';
            $accesos[$indice]['icon'] = 'fa-address-card-o';
            $indice++;
        }
        if (in_groups($grupos_resoluciones, $grupos))
        {
            $accesos[$indice]['href'] = 'resoluciones/escritorio';
            $accesos[$indice]['title'] = 'Resoluciones';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
        }
        if (in_groups($grupos_stock_informatica, $grupos))
        {
            $accesos[$indice]['href'] = 'stock_informatica/escritorio';
            $accesos[$indice]['title'] = 'Stock Informática';
            $accesos[$indice]['icon'] = 'fa-desktop';
            $indice++;
        }
        if (in_groups($grupos_tablero, $grupos))
        {
            $accesos[$indice]['href'] = 'tablero/escritorio';
            $accesos[$indice]['title'] = 'Tablero';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
        }
        if (in_groups($grupos_telefonia, $grupos))
        {
            $accesos[$indice]['href'] = 'telefonia/escritorio';
            $accesos[$indice]['title'] = 'Telefonía';
            $accesos[$indice]['icon'] = 'fa-phone';
            $indice++;
        }
        if (in_groups($grupos_toner, $grupos))
        {
            $accesos[$indice]['href'] = 'toner/escritorio';
            $accesos[$indice]['title'] = 'Toner';
            $accesos[$indice]['icon'] = 'fa-print';
            $indice++;
        }
        if (in_groups($grupos_transferencias, $grupos))
        {
            $accesos[$indice]['href'] = 'transferencias/escritorio';
            $accesos[$indice]['title'] = 'Transferencias';
            $accesos[$indice]['icon'] = 'fa-exchange';
            $indice++;
        }
        if (in_groups($grupos_tramites_online, $grupos))
        {
            $accesos[$indice]['href'] = 'tramites_online/escritorio';
            $accesos[$indice]['title'] = 'Trámites a Distancia';
            $accesos[$indice]['icon'] = 'fa-globe';
            $indice++;
        }
        if (in_groups($grupos_vales_combustible, $grupos))
        {
            $accesos[$indice]['href'] = 'vales_combustible/escritorio';
            $accesos[$indice]['title'] = 'Vales Combustible';
            $accesos[$indice]['icon'] = 'fa-truck';
            $indice++;
        }
        if (in_groups($grupos_manuales, $grupos))
        {
            $accesos[$indice]['href'] = 'manuales/visualizar';
            $accesos[$indice]['title'] = 'Manuales';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
//<editor-fold defaultstate="collapsed" desc="Permisos Escritorio Actas ISP">
if (!function_exists('load_permisos_actasisp_escritorio'))
{

    function load_permisos_actasisp_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'actasisp_user', 'actasisp_consulta_general');
        $grupos_inspector = array('actasisp_inspector');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'actasisp/actas/listar';
            $accesos[$indice]['title'] = 'Actas';
            $accesos[$indice]['icon'] = 'fa-file';
            $indice++;
            $accesos[$indice]['href'] = 'actasisp/inspectores/listar';
            $accesos[$indice]['title'] = 'Inspectores';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
            $accesos[$indice]['href'] = 'actasisp/motivos/listar';
            $accesos[$indice]['title'] = 'Motivos';
            $accesos[$indice]['icon'] = 'fa-list-ul';
            $indice++;
            $accesos[$indice]['href'] = 'actasisp/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
        }
        else if (in_groups($grupos_inspector, $grupos))
        {
            $accesos[$indice]['href'] = 'actasisp/actas/listar';
            $accesos[$indice]['title'] = 'Actas';
            $accesos[$indice]['icon'] = 'fa-file';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// // <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Antenas">
if (!function_exists('load_permisos_antenas_escritorio'))
{

    function load_permisos_antenas_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'antenas_admin', 'antenas_consulta_general');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'antenas/antenas/listar';
            $accesos[$indice]['title'] = 'Antenas';
            $accesos[$indice]['icon'] = 'fa-wifi';
            $indice++;
            $accesos[$indice]['href'] = 'antenas/denuncias/listar';
            $accesos[$indice]['title'] = 'Denuncias';
            $accesos[$indice]['icon'] = 'fa-warning';
            $indice++;
            $accesos[$indice]['href'] = 'antenas/habilitaciones/listar';
            $accesos[$indice]['title'] = 'Habilitaciones';
            $accesos[$indice]['icon'] = 'fa-thumbs-up';
            $indice++;
            $accesos[$indice]['href'] = 'antenas/mapa/ver';
            $accesos[$indice]['title'] = 'Mapa';
            $accesos[$indice]['icon'] = 'fa-map';
            $indice++;
            $accesos[$indice]['href'] = 'antenas/proveedores/listar';
            $accesos[$indice]['title'] = 'Proveedores';
            $accesos[$indice]['icon'] = 'fa-tags';
            $indice++;
            $accesos[$indice]['href'] = 'antenas/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'antenas/torres/listar';
            $accesos[$indice]['title'] = 'Torres';
            $accesos[$indice]['icon'] = 'fa-podcast';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Asesoría Letrada">
if (!function_exists('load_permisos_asesoria_letrada_escritorio'))
{

    function load_permisos_asesoria_letrada_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'asesoria_letrada_admin', 'asesoria_letrada_consulta_general');
        $grupos_user = array('asesoria_letrada_user');
        $grupos_area = array('asesoria_letrada_area');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'asesoria_letrada/categorias/listar';
            $accesos[$indice]['title'] = 'Categorías';
            $accesos[$indice]['icon'] = 'fa-folder-open-o';
            $indice++;
            $accesos[$indice]['href'] = 'asesoria_letrada/incidencias/listar';
            $accesos[$indice]['title'] = 'Incidencias';
            $accesos[$indice]['icon'] = 'fa-warning';
            $indice++;
            $accesos[$indice]['href'] = 'asesoria_letrada/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'asesoria_letrada/sectores/listar';
            $accesos[$indice]['title'] = 'Sectores';
            $accesos[$indice]['icon'] = 'fa-sitemap';
            $indice++;
            $accesos[$indice]['href'] = 'asesoria_letrada/usuarios_areas/listar';
            $accesos[$indice]['title'] = 'Usuarios por area';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
            $accesos[$indice]['href'] = 'asesoria_letrada/usuarios_sectores/listar';
            $accesos[$indice]['title'] = 'Usuarios por sector';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
        }
        else if (in_groups($grupos_user, $grupos))  // TECNICOS
        {
            $accesos[$indice]['href'] = 'asesoria_letrada/incidencias/listar_area';
            $accesos[$indice]['title'] = 'Incidencias Solicitadas';
            $accesos[$indice]['icon'] = 'fa-warning';
            $indice++;
            $accesos[$indice]['href'] = 'asesoria_letrada/incidencias/listar_tecnico';
            $accesos[$indice]['title'] = 'Incidencias Recibidas';
            $accesos[$indice]['icon'] = 'fa-exclamation-circle';
            $indice++;
        }
        else if (in_groups($grupos_area, $grupos))
        {
            $accesos[$indice]['href'] = 'asesoria_letrada/incidencias/listar_area';
            $accesos[$indice]['title'] = 'Incidencias Solicitadas';
            $accesos[$indice]['icon'] = 'fa-warning';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Asistencia">
if (!function_exists('load_permisos_asistencia_escritorio'))
{

    function load_permisos_asistencia_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'asistencia_consulta_general');
        $grupos_asistencia_rrhh = array('admin', 'asistencia_rrhh');
        $grupos_asistencia_control = array('asistencia_control');
        $grupos_asistencia_director = array('asistencia_director');
        $grupos_asistencia_contralor = array('asistencia_contralor');
        $grupos_asistencia_user = array('asistencia_user');
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_asistencia_rrhh, $grupos))
        {
            $accesos[$indice]['href'] = 'asistencia/personal_major/buscador';
            $accesos[$indice]['title'] = 'Buscador';
            $accesos[$indice]['icon'] = 'fa-search';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/fichadas/ver';
            $accesos[$indice]['title'] = 'Fichadas';
            $accesos[$indice]['icon'] = 'fa-clock-o';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/formularios/listar';
            $accesos[$indice]['title'] = 'Formularios';
            $accesos[$indice]['icon'] = 'fa-file-pdf-o';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/horarios_major/listar';
            $accesos[$indice]['title'] = 'Horarios Major';
            $accesos[$indice]['icon'] = 'fa-calendar';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/personal_major/listar';
            $accesos[$indice]['title'] = 'Personal Major';
            $accesos[$indice]['icon'] = 'fa-sitemap';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_diario';
            $accesos[$indice]['title'] = 'Parte Diario';
            $accesos[$indice]['icon'] = 'fa-file-text-o';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_diario_horas';
            $accesos[$indice]['title'] = 'Parte Diario de Horas';
            $accesos[$indice]['icon'] = 'fa-files-o';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_diario_impresion';
            $accesos[$indice]['title'] = 'Parte Diario Impresión';
            $accesos[$indice]['icon'] = 'fa-files-o';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_estadistico';
            $accesos[$indice]['title'] = 'Parte Estadístico';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_novedades';
            $accesos[$indice]['title'] = 'Parte Novedades';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_relojes';
            $accesos[$indice]['title'] = 'Parte Relojes';
            $accesos[$indice]['icon'] = 'fa-file-text-o';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/reportes_major/reporte_diario';
            $accesos[$indice]['title'] = 'Reporte Diario';
            $accesos[$indice]['icon'] = 'fa-file-o';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/usuarios/listar';
            $accesos[$indice]['title'] = 'Usuarios';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
        }
        elseif (in_groups($grupos_asistencia_director, $grupos) || in_groups($grupos_asistencia_contralor, $grupos) || in_groups($grupos_asistencia_control, $grupos))
        {
            $accesos[$indice]['href'] = 'asistencia/personal_major/buscador';
            $accesos[$indice]['title'] = 'Buscador';
            $accesos[$indice]['icon'] = 'fa-search';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/fichadas/ver';
            $accesos[$indice]['title'] = 'Fichadas';
            $accesos[$indice]['icon'] = 'fa-clock-o';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/formularios/listar';
            $accesos[$indice]['title'] = 'Formularios';
            $accesos[$indice]['icon'] = 'fa-file-pdf-o';
            $indice++;
            if (in_groups($grupos_asistencia_control, $grupos))
            {
                $accesos[$indice]['href'] = 'asistencia/horarios_major/listar';
                $accesos[$indice]['title'] = 'Horarios Major';
                $accesos[$indice]['icon'] = 'fa-calendar';
                $indice++;
            }
            $accesos[$indice]['href'] = 'asistencia/personal_major/listar';
            $accesos[$indice]['title'] = 'Personal Major';
            $accesos[$indice]['icon'] = 'fa-sitemap';
            $indice++;
            if (in_groups($grupos_asistencia_control, $grupos) || in_groups($grupos_asistencia_contralor, $grupos))
            {
                $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_diario';
                $accesos[$indice]['title'] = 'Parte Diario';
                $accesos[$indice]['icon'] = 'fa-file-text-o';
                $indice++;
                $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_diario_horas';
                $accesos[$indice]['title'] = 'Parte Diario de Horas';
                $accesos[$indice]['icon'] = 'fa-file-text-o';
                $indice++;
                $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_diario_impresion';
                $accesos[$indice]['title'] = 'Parte Diario Impresión';
                $accesos[$indice]['icon'] = 'fa-file-text-o';
                $indice++;
            }
            if (in_groups($grupos_asistencia_control, $grupos))
            {
                $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_estadistico';
                $accesos[$indice]['title'] = 'Parte Estadístico';
                $accesos[$indice]['icon'] = 'fa-line-chart';
                $indice++;
            }
            $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_novedades';
            $accesos[$indice]['title'] = 'Parte Novedades';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            if (in_groups($grupos_asistencia_control, $grupos))
            {
                $accesos[$indice]['href'] = 'asistencia/reportes_major/parte_relojes';
                $accesos[$indice]['title'] = 'Parte Relojes';
                $accesos[$indice]['icon'] = 'fa-file-text-o';
                $indice++;
            }
            $accesos[$indice]['href'] = 'asistencia/reportes_major/reporte_diario';
            $accesos[$indice]['title'] = 'Reporte Diario';
            $accesos[$indice]['icon'] = 'fa-file-o';
            $indice++;
        }
        elseif (in_groups($grupos_asistencia_user, $grupos))
        {
            $accesos[$indice]['href'] = 'asistencia/fichadas/ver';
            $accesos[$indice]['title'] = 'Fichadas';
            $accesos[$indice]['icon'] = 'fa-clock-o';
            $indice++;
            $accesos[$indice]['href'] = 'asistencia/formularios/listar';
            $accesos[$indice]['title'] = 'Formularios';
            $accesos[$indice]['icon'] = 'fa-file-pdf-o';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
//<editor-fold defaultstate="collapsed" desc="Permisos Escritorio Defunciones">
if (!function_exists('load_permisos_defunciones_escritorio'))
{

    function load_permisos_defunciones_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'defunciones_consulta_general');
        $grupos_defunciones = array('defunciones_user');
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_defunciones, $grupos))
        {
            $accesos[$indice]['href'] = 'defunciones/cementerios/listar';
            $accesos[$indice]['title'] = 'Cementerios';
            $accesos[$indice]['icon'] = 'fa-university';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/cocherias/listar';
            $accesos[$indice]['title'] = 'Cocherías';
            $accesos[$indice]['icon'] = 'fa-truck';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/constructores/listar';
            $accesos[$indice]['title'] = 'Constructores';
            $accesos[$indice]['icon'] = 'fa-id-card-o';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/difuntos/listar';
            $accesos[$indice]['title'] = 'Difuntos';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/expedientes/listar';
            $accesos[$indice]['title'] = 'Expedientes';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/expedientes_pjm/listar';
            $accesos[$indice]['title'] = 'Expedientes PJM';
            $accesos[$indice]['icon'] = 'fa-file-text-o';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/operaciones/listar';
            $accesos[$indice]['title'] = 'Operaciones';
            $accesos[$indice]['icon'] = 'fa-archive';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/tramites/nuevo';
            $accesos[$indice]['title'] = 'Nuevo Trámite';
            $accesos[$indice]['icon'] = 'fa-folder-o';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/tramites/iniciar';
            $accesos[$indice]['title'] = 'Iniciar Trámite';
            $accesos[$indice]['icon'] = 'fa-folder-open-o';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/propietarios/listar';
            $accesos[$indice]['title'] = 'Propietarios';
            $accesos[$indice]['icon'] = 'fa-address-card';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/solicitantes/listar';
            $accesos[$indice]['title'] = 'Solicitantes';
            $accesos[$indice]['icon'] = 'fa-user-circle';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/ubicaciones/listar';
            $accesos[$indice]['title'] = 'Ubicaciones';
            $accesos[$indice]['icon'] = 'fa-map-o';
            $indice++;
            $accesos[$indice]['href'] = 'defunciones/tablero/listar';
            $accesos[$indice]['title'] = 'Vencimientos';
            $accesos[$indice]['icon'] = 'fa-calendar';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
//<editor-fold defaultstate="collapsed" desc="Permisos Escritorio Desarrollo Social">
if (!function_exists('load_permisos_desarrollo_social_escritorio'))
{

    function load_permisos_desarrollo_social_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'desarrollo_social_consulta_general');
        $grupos_desarrollo_social = array('desarrollo_social_user');
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_desarrollo_social, $grupos))
        {
            $accesos[$indice]['href'] = 'desarrollo_social/articulos/listar';
            $accesos[$indice]['title'] = 'Artículos';
            $accesos[$indice]['icon'] = 'fa-cube';
            $indice++;
            $accesos[$indice]['href'] = 'desarrollo_social/beneficiarios/listar';
            $accesos[$indice]['title'] = 'Beneficiarios';
            $accesos[$indice]['icon'] = 'fa-address-card';
            $indice++;
            $accesos[$indice]['href'] = 'desarrollo_social/compras/listar';
            $accesos[$indice]['title'] = 'Compras';
            $accesos[$indice]['icon'] = 'fa-cart-plus';
            $indice++;
            $accesos[$indice]['href'] = 'desarrollo_social/entregas/listar';
            $accesos[$indice]['title'] = 'Entregas';
            $accesos[$indice]['icon'] = 'fa-cart-arrow-down';
            $indice++;
            $accesos[$indice]['href'] = 'desarrollo_social/lugares/listar';
            $accesos[$indice]['title'] = 'Lugares';
            $accesos[$indice]['icon'] = 'fa-university';
            $indice++;
            $accesos[$indice]['href'] = 'desarrollo_social/proveedores/listar';
            $accesos[$indice]['title'] = 'Proveedores';
            $accesos[$indice]['icon'] = 'fa-briefcase';
            $indice++;
            $accesos[$indice]['href'] = 'desarrollo_social/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'desarrollo_social/tipos_articulos/listar';
            $accesos[$indice]['title'] = 'Tipos de Artículos';
            $accesos[$indice]['icon'] = 'fa-cubes';
            $indice++;
            $accesos[$indice]['href'] = 'desarrollo_social/tipos_lugares/listar';
            $accesos[$indice]['title'] = 'Tipos de Lugares';
            $accesos[$indice]['icon'] = 'fa-building';
            $indice++;
            $accesos[$indice]['href'] = 'desarrollo_social/tipos_proveedores/listar';
            $accesos[$indice]['title'] = 'Tipos de Proveedores';
            $accesos[$indice]['icon'] = 'fa-truck';
            $indice++;
            $accesos[$indice]['href'] = 'desarrollo_social/tipos_unidades/listar';
            $accesos[$indice]['title'] = 'Tipos de Unidades';
            $accesos[$indice]['icon'] = 'fa-percent';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
//<editor-fold defaultstate="collapsed" desc="Permisos Escritorio Gobierno">
if (!function_exists('load_permisos_gobierno_escritorio'))
{

    function load_permisos_gobierno_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'gobierno_consulta_general');
        $grupos_gobierno = array('gobierno_user');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'gobierno/documentos/listar_decretos';
            $accesos[$indice]['title'] = 'Decretos';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'gobierno/documentos/listar';
            $accesos[$indice]['title'] = 'Documentos';
            $accesos[$indice]['icon'] = 'fa-file-text-o';
            $indice++;
            $accesos[$indice]['href'] = 'gobierno/numeraciones/listar';
            $accesos[$indice]['title'] = 'Numeraciones';
            $accesos[$indice]['icon'] = 'fa-list-ol';
            $indice++;
            $accesos[$indice]['href'] = 'gobierno/partes/listar';
            $accesos[$indice]['title'] = 'Partes';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
            $accesos[$indice]['href'] = 'gobierno/tipos_documentos/listar';
            $accesos[$indice]['title'] = 'Tipos de Documentos';
            $accesos[$indice]['icon'] = 'fa-folder-open';
            $indice++;
        }
        else if (in_groups($grupos_gobierno, $grupos))
        {
            $accesos[$indice]['href'] = 'gobierno/documentos/listar_decretos';
            $accesos[$indice]['title'] = 'Decretos';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'gobierno/documentos/listar';
            $accesos[$indice]['title'] = 'Documentos';
            $accesos[$indice]['icon'] = 'fa-file-text-o';
            $indice++;
            $accesos[$indice]['href'] = 'gobierno/numeraciones/listar';
            $accesos[$indice]['title'] = 'Numeraciones';
            $accesos[$indice]['icon'] = 'fa-list-ol';
            $indice++;
            $accesos[$indice]['href'] = 'gobierno/partes/listar';
            $accesos[$indice]['title'] = 'Partes';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Incidencias">
if (!function_exists('load_permisos_incidencias_escritorio'))
{

    function load_permisos_incidencias_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'incidencias_admin', 'incidencias_consulta_general');
        $grupos_user = array('incidencias_user');
        $grupos_area = array('incidencias_area');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'incidencias/categorias/listar';
            $accesos[$indice]['title'] = 'Categorías';
            $accesos[$indice]['icon'] = 'fa-folder-open-o';
            $indice++;
            $accesos[$indice]['href'] = 'incidencias/incidencias/listar';
            $accesos[$indice]['title'] = 'Incidencias';
            $accesos[$indice]['icon'] = 'fa-warning';
            $indice++;
            $accesos[$indice]['href'] = 'incidencias/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'incidencias/sectores/listar';
            $accesos[$indice]['title'] = 'Sectores';
            $accesos[$indice]['icon'] = 'fa-sitemap';
            $indice++;
            $accesos[$indice]['href'] = 'incidencias/usuarios_areas/listar';
            $accesos[$indice]['title'] = 'Usuarios por area';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
            $accesos[$indice]['href'] = 'incidencias/usuarios_sectores/listar';
            $accesos[$indice]['title'] = 'Usuarios por sector';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
            $accesos[$indice]['href'] = 'incidencias/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        else if (in_groups($grupos_user, $grupos))  // TECNICOS
        {
            $accesos[$indice]['href'] = 'incidencias/incidencias/listar_area';
            $accesos[$indice]['title'] = 'Incidencias Solicitadas';
            $accesos[$indice]['icon'] = 'fa-warning';
            $indice++;
            $accesos[$indice]['href'] = 'incidencias/incidencias/listar_tecnico';
            $accesos[$indice]['title'] = 'Incidencias Recibidas';
            $accesos[$indice]['icon'] = 'fa-exclamation-circle';
            $indice++;
            $accesos[$indice]['href'] = 'incidencias/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        else if (in_groups($grupos_area, $grupos))
        {
            $accesos[$indice]['href'] = 'incidencias/incidencias/listar_area';
            $accesos[$indice]['title'] = 'Incidencias Solicitadas';
            $accesos[$indice]['icon'] = 'fa-warning';
            $indice++;
            $accesos[$indice]['href'] = 'incidencias/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
//<editor-fold defaultstate="collapsed" desc="Permisos Escritorio Luján Pass">
if (!function_exists('load_permisos_lujan_pass_escritorio'))
{

    function load_permisos_lujan_pass_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'major_consulta_general');
        $grupos_lujan_pass_control = array('lujan_pass_control');
        $grupos_lujan_pass_publico = array('lujan_pass_publico');
        $grupos_lujan_pass_beneficiario = array('lujan_pass_beneficiario');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'lujan_pass/campanias';
            $accesos[$indice]['title'] = 'Campañas';
            $accesos[$indice]['icon'] = 'fa-calendar';
            $indice++;
            $accesos[$indice]['href'] = 'lujan_pass/categorias';
            $accesos[$indice]['title'] = 'Categorias';
            $accesos[$indice]['icon'] = 'fa-folder';
            $indice++;
            $accesos[$indice]['href'] = 'lujan_pass/comercios';
            $accesos[$indice]['title'] = 'Comercios';
            $accesos[$indice]['icon'] = 'fa-building';
            $indice++;
            $accesos[$indice]['href'] = 'lujan_pass/promociones';
            $accesos[$indice]['title'] = 'Descuentos';
            $accesos[$indice]['icon'] = 'fa-gift';
            $indice++;
        }
        if (in_groups($grupos_lujan_pass_control, $grupos))
        {
            $accesos[$indice]['href'] = 'lujan_pass/comercios';
            $accesos[$indice]['title'] = 'Comercios';
            $accesos[$indice]['icon'] = 'fa-building';
            $indice++;
            $accesos[$indice]['href'] = 'lujan_pass/promociones';
            $accesos[$indice]['title'] = 'Descuentos';
            $accesos[$indice]['icon'] = 'fa-gift';
            $indice++;
        }
        if (in_groups($grupos_lujan_pass_publico, $grupos))
        {
            $accesos[$indice]['href'] = 'lujan_pass/comercios';
            $accesos[$indice]['title'] = 'Comercios';
            $accesos[$indice]['icon'] = 'fa-building';
            $indice++;
            $accesos[$indice]['href'] = 'lujan_pass/promociones';
            $accesos[$indice]['title'] = 'Descuentos';
            $accesos[$indice]['icon'] = 'fa-gift';
            $indice++;
        }
        if (in_groups($grupos_lujan_pass_beneficiario, $grupos))
        {
            
        }
        return $accesos;
    }
}
// </editor-fold>
// // <editor-fold defaultstate="collapsed" desc="Permisos Escritorio M@jor">
if (!function_exists('load_permisos_major_escritorio'))
{

    function load_permisos_major_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'major_consulta_general');
        $grupos_major_boletos = array('major_boletos');
        $grupos_major_deudas = array('major_deudas');
        $grupos_major_deudas_masivas = array('major_deudas_masivas');
        $grupos_major_solicitudes = array('major_solicitudes');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'major/boletos';
            $accesos[$indice]['title'] = 'Boletos';
            $accesos[$indice]['icon'] = 'fa-file-o';
            $indice++;
            $accesos[$indice]['href'] = 'major/deudas';
            $accesos[$indice]['title'] = 'Deudas';
            $accesos[$indice]['icon'] = 'fa-money';
            $indice++;
            $accesos[$indice]['href'] = 'major/deudas_masivas';
            $accesos[$indice]['title'] = 'Deudas Masivas';
            $accesos[$indice]['icon'] = 'fa-usd';
            $indice++;
            $accesos[$indice]['href'] = 'major/solicitudes/listar';
            $accesos[$indice]['title'] = 'Solicitudes';
            $accesos[$indice]['icon'] = 'fa-file-text-o';
            $indice++;
        }
        if (in_groups($grupos_major_boletos, $grupos))
        {
            $accesos[$indice]['href'] = 'major/boletos';
            $accesos[$indice]['title'] = 'Boletos';
            $accesos[$indice]['icon'] = 'fa-file-o';
            $indice++;
        }
        if (in_groups($grupos_major_deudas, $grupos))
        {
            $accesos[$indice]['href'] = 'major/deudas';
            $accesos[$indice]['title'] = 'Deudas';
            $accesos[$indice]['icon'] = 'fa-money';
            $indice++;
        }
        if (in_groups($grupos_major_deudas_masivas, $grupos))
        {
            $accesos[$indice]['href'] = 'major/deudas_masivas';
            $accesos[$indice]['title'] = 'Deudas Masivas';
            $accesos[$indice]['icon'] = 'fa-usd';
            $indice++;
        }
        if (in_groups($grupos_major_solicitudes, $grupos))
        {
            $accesos[$indice]['href'] = 'major/solicitudes/listar';
            $accesos[$indice]['title'] = 'Solicitudes';
            $accesos[$indice]['icon'] = 'fa-file-text-o';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Más Beneficios">
if (!function_exists('load_permisos_mas_beneficios_escritorio'))
{

    function load_permisos_mas_beneficios_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'major_consulta_general');
        $grupos_mas_beneficios_control = array('mas_beneficios_control');
        $grupos_mas_beneficios_publico = array('mas_beneficios_publico');
        $grupos_mas_beneficios_beneficiario = array('mas_beneficios_beneficiario');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'mas_beneficios/campanias';
            $accesos[$indice]['title'] = 'Campañas';
            $accesos[$indice]['icon'] = 'fa-calendar';
            $indice++;
            $accesos[$indice]['href'] = 'mas_beneficios/categorias';
            $accesos[$indice]['title'] = 'Categorias';
            $accesos[$indice]['icon'] = 'fa-folder';
            $indice++;
            $accesos[$indice]['href'] = 'mas_beneficios/comercios';
            $accesos[$indice]['title'] = 'Comercios';
            $accesos[$indice]['icon'] = 'fa-building';
            $indice++;
            $accesos[$indice]['href'] = 'mas_beneficios/promociones';
            $accesos[$indice]['title'] = 'Promociones';
            $accesos[$indice]['icon'] = 'fa-gift';
            $indice++;
        }
        if (in_groups($grupos_mas_beneficios_control, $grupos))
        {
            $accesos[$indice]['href'] = 'mas_beneficios/comercios';
            $accesos[$indice]['title'] = 'Comercios';
            $accesos[$indice]['icon'] = 'fa-building';
            $indice++;
            $accesos[$indice]['href'] = 'mas_beneficios/promociones';
            $accesos[$indice]['title'] = 'Promociones';
            $accesos[$indice]['icon'] = 'fa-gift';
            $indice++;
        }
        if (in_groups($grupos_mas_beneficios_publico, $grupos))
        {
            $accesos[$indice]['href'] = 'mas_beneficios/comercios';
            $accesos[$indice]['title'] = 'Comercios';
            $accesos[$indice]['icon'] = 'fa-building';
            $indice++;
            $accesos[$indice]['href'] = 'mas_beneficios/promociones';
            $accesos[$indice]['title'] = 'Promociones';
            $accesos[$indice]['icon'] = 'fa-gift';
            $indice++;
        }
        if (in_groups($grupos_mas_beneficios_beneficiario, $grupos))
        {
            
        }
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Niñez y Adolescencia">
if (!function_exists('load_permisos_ninez_adolescencia_escritorio'))
{

    function load_permisos_ninez_adolescencia_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'ninez_adolescencia_admin', 'ninez_adolescencia_consulta_general');
        $grupos_user = array('ninez_adolescencia_user');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'ninez_adolescencia/expedientes/buscador';
            $accesos[$indice]['title'] = 'Buscador';
            $accesos[$indice]['icon'] = 'fa-search';
            $indice++;
            $accesos[$indice]['href'] = 'ninez_adolescencia/efectores/listar';
            $accesos[$indice]['title'] = 'Efectores';
            $accesos[$indice]['icon'] = 'fa-briefcase';
            $indice++;
            $accesos[$indice]['href'] = 'ninez_adolescencia/expedientes/listar';
            $accesos[$indice]['title'] = 'Expedientes';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'ninez_adolescencia/motivos/listar';
            $accesos[$indice]['title'] = 'Motivos';
            $accesos[$indice]['icon'] = 'fa-tags';
            $indice++;
            $accesos[$indice]['href'] = 'ninez_adolescencia/parentezcos_personas/listar';
            $accesos[$indice]['title'] = 'Parentezcos';
            $accesos[$indice]['icon'] = 'fa-group';
            $indice++;
            $accesos[$indice]['href'] = 'ninez_adolescencia/tipos_adjuntos/listar';
            $accesos[$indice]['title'] = 'Tipos de Adjuntos';
            $accesos[$indice]['icon'] = 'fa-folder-open-o';
            $indice++;
            $accesos[$indice]['href'] = 'ninez_adolescencia/tipos_intervenciones/listar';
            $accesos[$indice]['title'] = 'Tipos de Intervenciones';
            $accesos[$indice]['icon'] = 'fa-list-ul';
            $indice++;
            $accesos[$indice]['href'] = 'ninez_adolescencia/tipos_parentezcos/listar';
            $accesos[$indice]['title'] = 'Tipos de Parentezcos';
            $accesos[$indice]['icon'] = 'fa-sitemap';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Notificaciones">
if (!function_exists('load_permisos_notificaciones_escritorio'))
{

    function load_permisos_notificaciones_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'notificaciones_user');
        $grupos_notificaciones_oficinas_externas = array('notificaciones_areas');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'notificaciones/cedulas/listar';
            $accesos[$indice]['title'] = 'Cédulas';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'notificaciones/hojas_rutas';
            $accesos[$indice]['title'] = 'Hojas de Ruta';
            $accesos[$indice]['icon'] = 'fa-list-ol';
            $indice++;
            $accesos[$indice]['href'] = 'notificaciones/tipos_documentos';
            $accesos[$indice]['title'] = 'Tipos de Documentos';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'notificaciones/usuarios_areas/listar';
            $accesos[$indice]['title'] = 'Usuarios por Area';
            $accesos[$indice]['icon'] = 'fa-address-card-o';
            $indice++;
            $accesos[$indice]['href'] = 'notificaciones/zonas/listar';
            $accesos[$indice]['title'] = 'Zonas';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
        }
        else if (in_groups($grupos_notificaciones_oficinas_externas, $grupos))
        {
            $accesos[$indice]['href'] = 'notificaciones/cedulas/listar';
            $accesos[$indice]['title'] = 'Cédulas';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
//<editor-fold defaultstate="collapsed" desc="Permisos Escritorio Obrador">
if (!function_exists('load_permisos_obrador_escritorio'))
{

    function load_permisos_obrador_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'obrador_consulta_general');
        $grupos_obrador = array('obrador_user');
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_obrador, $grupos))
        {
            $accesos[$indice]['href'] = 'obrador/articulos/listar';
            $accesos[$indice]['title'] = 'Artículos';
            $accesos[$indice]['icon'] = 'fa-cube';
            $indice++;
            $accesos[$indice]['href'] = 'obrador/compras/listar';
            $accesos[$indice]['title'] = 'Compras';
            $accesos[$indice]['icon'] = 'fa-cart-plus';
            $indice++;
            $accesos[$indice]['href'] = 'obrador/entregas/listar';
            $accesos[$indice]['title'] = 'Entregas';
            $accesos[$indice]['icon'] = 'fa-cart-arrow-down';
            $indice++;
            $accesos[$indice]['href'] = 'obrador/beneficiarios/listar';
            $accesos[$indice]['title'] = 'Ganancias';
            $accesos[$indice]['icon'] = 'fa-money';
            $indice++;
            $accesos[$indice]['href'] = 'obrador/proveedores/listar';
            $accesos[$indice]['title'] = 'Proveedores';
            $accesos[$indice]['icon'] = 'fa-briefcase';
            $indice++;
            $accesos[$indice]['href'] = 'obrador/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'obrador/situaciones_iva/listar';
            $accesos[$indice]['title'] = 'Situaciones IVA';
            $accesos[$indice]['icon'] = 'fa-address-card';
            $indice++;
            $accesos[$indice]['href'] = 'obrador/tipos_articulos/listar';
            $accesos[$indice]['title'] = 'Tipos de Artículos';
            $accesos[$indice]['icon'] = 'fa-cubes';
            $indice++;
            $accesos[$indice]['href'] = 'obrador/tipos_proveedores/listar';
            $accesos[$indice]['title'] = 'Tipos de Proveedores';
            $accesos[$indice]['icon'] = 'fa-truck';
            $indice++;
            $accesos[$indice]['href'] = 'obrador/tipos_unidades/listar';
            $accesos[$indice]['title'] = 'Tipos de Unidades';
            $accesos[$indice]['icon'] = 'fa-percent';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Reclamos Major">
if (!function_exists('load_permisos_reclamos_major_escritorio'))
{

    function load_permisos_reclamos_major_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'reclamos_major_admin', 'reclamos_major_consulta_general');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'reclamos_major/categorias/listar';
            $accesos[$indice]['title'] = 'Categorías';
            $accesos[$indice]['icon'] = 'fa-folder-open-o';
            $indice++;
            $accesos[$indice]['href'] = 'reclamos_major/incidencias/listar';
            $accesos[$indice]['title'] = 'Incidencias';
            $accesos[$indice]['icon'] = 'fa-warning';
            $indice++;
            $accesos[$indice]['href'] = 'reclamos_major/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Reclamos GIS">
if (!function_exists('load_permisos_reclamos_gis_escritorio'))
{

    function load_permisos_reclamos_gis_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'reclamos_gis_consulta_general');
        $grupos_reclamos_gis = array('reclamos_gis_user');
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_reclamos_gis, $grupos))
        {
            $accesos[$indice]['href'] = 'reclamos_gis/reclamos/listar';
            $accesos[$indice]['title'] = 'Reclamos';
            $accesos[$indice]['icon'] = 'fa-file-o';
            $indice++;
            $accesos[$indice]['href'] = 'reclamos_gis/reclamos_potrerillos/listar';
            $accesos[$indice]['title'] = 'Reclamos Potrerillos';
            $accesos[$indice]['icon'] = 'fa-file';
            $indice++;
        }
        return $accesos;
    }
}
//editado por yoel grosso 
if (!function_exists('load_permisos_oficina_de_empleo_escritorio'))
{
    function load_permisos_oficina_de_empleo_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'oficina_empleo_general','oficina_empleo');
        $grupos_oficina_de_empleo = array('user','tramites_online_publico');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'oficina_de_empleo/Busquedas/listar';
            $accesos[$indice]['title'] = 'Busquedas';
            $accesos[$indice]['icon'] = 'fa fa-search';
            $indice++;
        }            
        $accesos[$indice]['href'] = 'oficina_de_empleo/pedir_empleo/listar';
        $accesos[$indice]['title'] = 'carga de cv';
        $accesos[$indice]['icon'] = 'fa fa-user';
        $indice++;
        $accesos[$indice]['href'] = 'oficina_de_empleo/Intermediacion/listar';
        $accesos[$indice]['title'] = 'Intermediacion laboral';
        $accesos[$indice]['icon'] = 'fa-file';
        $indice++;   
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Recursos Humanos">
if (!function_exists('load_permisos_recursos_humanos_escritorio'))
{

    function load_permisos_recursos_humanos_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'recursos_humanos_admin', 'recursos_humanos_consulta_general');
        $grupos_recursos_humanos = array('recursos_humanos_user');
        $grupos_recursos_humanos_director = array('recursos_humanos_director');
        $grupos_recursos_humanos_publico = array('recursos_humanos_publico');
        $grupos_recursos_humanos_bonos = array('recursos_humanos_bonos');
        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_recursos_humanos, $grupos))
        {
            $accesos[$indice]['href'] = 'recursos_humanos/usuarios_legajos/listar';
            $accesos[$indice]['title'] = 'Asignación de Legajos';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
            if (in_groups($grupos_admin, $grupos) || in_groups($grupos_recursos_humanos_bonos, $grupos))
            {
                $accesos[$indice]['href'] = 'recursos_humanos/bonos/listar';
                $accesos[$indice]['title'] = 'Bonos';
                $accesos[$indice]['icon'] = 'fa-file-text-o';
                $indice++;
            }
            $accesos[$indice]['href'] = 'recursos_humanos/categorias/listar';
            $accesos[$indice]['title'] = 'Categorías';
            $accesos[$indice]['icon'] = 'fa-folder-open-o';
            $indice++;
            $accesos[$indice]['href'] = 'recursos_humanos/documentos_legajo/listar';
            $accesos[$indice]['title'] = 'Documentos';
            $accesos[$indice]['icon'] = 'fa-file-o';
            $indice++;
            $accesos[$indice]['href'] = 'recursos_humanos/hobbies/listar';
            $accesos[$indice]['title'] = 'Hobbies';
            $accesos[$indice]['icon'] = 'fa-futbol-o';
            $indice++;
            $accesos[$indice]['href'] = 'recursos_humanos/legajos/listar';
            $accesos[$indice]['title'] = 'Legajos';
            $accesos[$indice]['icon'] = 'fa-address-card-o';
            $indice++;
            $accesos[$indice]['href'] = 'recursos_humanos/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        elseif (in_groups($grupos_recursos_humanos_director, $grupos))
        {
            $accesos[$indice]['href'] = 'recursos_humanos/legajos/listar_director';
            $accesos[$indice]['title'] = 'Legajos';
            $accesos[$indice]['icon'] = 'fa-address-card-o';
            $indice++;
        }
        elseif (in_groups($grupos_recursos_humanos_publico, $grupos))
        {
            $accesos[$indice]['href'] = 'recursos_humanos/legajos/listar_publicos';
            $accesos[$indice]['title'] = 'Legajos';
            $accesos[$indice]['icon'] = 'fa-address-card-o';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// // <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Resoluciones">
if (!function_exists('load_permisos_resoluciones_escritorio'))
{

    function load_permisos_resoluciones_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'resoluciones_consulta_general');
        $grupos_resoluciones = array('resoluciones_user');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'resoluciones/numeraciones/listar';
            $accesos[$indice]['title'] = 'Numeraciones';
            $accesos[$indice]['icon'] = 'fa-list-ol';
            $indice++;
            $accesos[$indice]['href'] = 'resoluciones/resoluciones/listar';
            $accesos[$indice]['title'] = 'Resoluciones';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'resoluciones/tipos_resoluciones/listar';
            $accesos[$indice]['title'] = 'Tipos de Resolución';
            $accesos[$indice]['icon'] = 'fa-folder-open';
            $indice++;
        }
        else if (in_groups($grupos_resoluciones, $grupos))
        {
            $accesos[$indice]['href'] = 'resoluciones/resoluciones/listar';
            $accesos[$indice]['title'] = 'Resoluciones';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
//<editor-fold defaultstate="collapsed" desc="Permisos Escritorio Stock Informática">
if (!function_exists('load_permisos_stock_informatica_escritorio'))
{

    function load_permisos_stock_informatica_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'stock_informatica_user', 'stock_informatica_consulta_general');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'stock_informatica/articulos/listar';
            $accesos[$indice]['title'] = 'Artículos';
            $accesos[$indice]['icon'] = 'fa-laptop';
            $indice++;
            $accesos[$indice]['href'] = 'stock_informatica/atributos/listar';
            $accesos[$indice]['title'] = 'Atributos';
            $accesos[$indice]['icon'] = 'fa-list';
            $indice++;
            $accesos[$indice]['href'] = 'stock_informatica/categorias/listar';
            $accesos[$indice]['title'] = 'Categorías';
            $accesos[$indice]['icon'] = 'fa-th-large';
            $indice++;
            $accesos[$indice]['href'] = 'stock_informatica/marcas/listar';
            $accesos[$indice]['title'] = 'Marcas';
            $accesos[$indice]['icon'] = 'fa-android';
            $indice++;
            $accesos[$indice]['href'] = 'stock_informatica/movimientos/listar';
            $accesos[$indice]['title'] = 'Movimientos';
            $accesos[$indice]['icon'] = 'fa-exchange';
            $indice++;
            $accesos[$indice]['href'] = 'stock_informatica/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'stock_informatica/stock/listar';
            $accesos[$indice]['title'] = 'Stock';
            $accesos[$indice]['icon'] = 'fa-desktop';
            $indice++;
            $accesos[$indice]['href'] = 'stock_informatica/resoluciones/listar';
            $accesos[$indice]['title'] = 'Subcategorías';
            $accesos[$indice]['icon'] = 'fa-th';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Telefonía">
if (!function_exists('load_permisos_telefonia_escritorio'))
{

    function load_permisos_telefonia_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'telefonia_admin', 'telefonia_consulta_general');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'telefonia/categorias/listar';
            $accesos[$indice]['title'] = 'Categorías';
            $accesos[$indice]['icon'] = 'fa-tags';
            $indice++;
            $accesos[$indice]['href'] = 'telefonia/equipos/listar';
            $accesos[$indice]['title'] = 'Equipos';
            $accesos[$indice]['icon'] = 'fa-mobile';
            $indice++;
            $accesos[$indice]['href'] = 'telefonia/lineas/listar';
            $accesos[$indice]['title'] = 'Líneas';
            $accesos[$indice]['icon'] = 'fa-phone';
            $indice++;
            $accesos[$indice]['href'] = 'telefonia/lineas_fijas/listar';
            $accesos[$indice]['title'] = 'Líneas Fijas';
            $accesos[$indice]['icon'] = 'fa-phone-square';
            $indice++;
            $accesos[$indice]['href'] = 'telefonia/lineas_fijas_consumos/listar';
            $accesos[$indice]['title'] = 'Líneas Fijas Consumos';
            $accesos[$indice]['icon'] = 'fa-money';
            $indice++;
            $accesos[$indice]['href'] = 'telefonia/marcas/listar';
            $accesos[$indice]['title'] = 'Marcas';
            $accesos[$indice]['icon'] = 'fa-registered';
            $indice++;
            $accesos[$indice]['href'] = 'telefonia/modelos/listar';
            $accesos[$indice]['title'] = 'Modelos';
            $accesos[$indice]['icon'] = 'fa-th';
            $indice++;
            $accesos[$indice]['href'] = 'telefonia/movimientos/listar';
            $accesos[$indice]['title'] = 'Movimientos';
            $accesos[$indice]['icon'] = 'fa-exchange';
            $indice++;
            $accesos[$indice]['href'] = 'telefonia/prestadores/listar';
            $accesos[$indice]['title'] = 'Prestadores';
            $accesos[$indice]['icon'] = 'fa-signal';
            $indice++;
            $accesos[$indice]['href'] = 'telefonia/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Toner">
if (!function_exists('load_permisos_toner_escritorio'))
{

    function load_permisos_toner_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'toner_admin', 'toner_consulta_general');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'toner/consumibles/listar';
            $accesos[$indice]['title'] = 'Consumibles';
            $accesos[$indice]['icon'] = 'fa-tint';
            $indice++;
            $accesos[$indice]['href'] = 'toner/consumibles_impresoras/listar';
            $accesos[$indice]['title'] = 'Consumibles Impresora';
            $accesos[$indice]['icon'] = 'fa-tags';
            $indice++;
            $accesos[$indice]['href'] = 'toner/impresoras/listar';
            $accesos[$indice]['title'] = 'Impresoras';
            $accesos[$indice]['icon'] = 'fa-print';
            $indice++;
            $accesos[$indice]['href'] = 'toner/impresoras_areas/listar';
            $accesos[$indice]['title'] = 'Impresoras Áreas';
            $accesos[$indice]['icon'] = 'fa-sitemap';
            $indice++;
            $accesos[$indice]['href'] = 'toner/marcas/listar';
            $accesos[$indice]['title'] = 'Marcas';
            $accesos[$indice]['icon'] = 'fa-registered';
            $indice++;
            $accesos[$indice]['href'] = 'toner/movimientos/listar';
            $accesos[$indice]['title'] = 'Movimientos';
            $accesos[$indice]['icon'] = 'fa-exchange';
            $indice++;
            $accesos[$indice]['href'] = 'toner/pedidos_consumibles/listar';
            $accesos[$indice]['title'] = 'Pedidos';
            $accesos[$indice]['icon'] = 'fa-file-text-o';
            $indice++;
            $accesos[$indice]['href'] = 'toner/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
//<editor-fold defaultstate="collapsed" desc="Permisos Escritorio Trámites a Distancia">
if (!function_exists('load_permisos_tramites_online_escritorio'))
{

    function load_permisos_tramites_online_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'tramites_online_consulta_general');
        $grupos_tramites_online_admin = array('tramites_online_admin');
        $grupos_tramites_online_area = array('tramites_online_area');
        $grupos_tramites_online_publico = array('tramites_online_publico');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'tramites_online/tramites/bandeja_entrada';
            $accesos[$indice]['title'] = 'Bandeja de Entrada';
            $accesos[$indice]['icon'] = 'fa-inbox';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/estados/listar';
            $accesos[$indice]['title'] = 'Estados';
            $accesos[$indice]['icon'] = 'fa-exchange';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/formularios/listar';
            $accesos[$indice]['title'] = 'Formularios';
            $accesos[$indice]['icon'] = 'fa-file-text-o';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/iniciadores/listar';
            $accesos[$indice]['title'] = 'Iniciadores';
            $accesos[$indice]['icon'] = 'fa-user';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/oficinas/listar';
            $accesos[$indice]['title'] = 'Oficinas';
            $accesos[$indice]['icon'] = 'fa-building';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/procesos/listar';
            $accesos[$indice]['title'] = 'Procesos';
            $accesos[$indice]['icon'] = 'fa-folder-open';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/adjuntos_tipos/listar';
            $accesos[$indice]['title'] = 'Tipos de Adjuntos';
            $accesos[$indice]['icon'] = 'fa-folder-open-o';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/iniciadores_tipos/listar';
            $accesos[$indice]['title'] = 'Tipos de Iniciadores';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/usuarios_oficinas/listar';
            $accesos[$indice]['title'] = 'Usuarios por Oficina';
            $accesos[$indice]['icon'] = 'fa-user';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        else if (in_groups($grupos_tramites_online_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'tramites_online/tramites/bandeja_entrada';
            $accesos[$indice]['title'] = 'Bandeja de Entrada';
            $accesos[$indice]['icon'] = 'fa-inbox';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        else if (in_groups($grupos_tramites_online_area, $grupos))
        {
            $accesos[$indice]['href'] = 'tramites_online/tramites/bandeja_entrada';
            $accesos[$indice]['title'] = 'Bandeja de Entrada';
            $accesos[$indice]['icon'] = 'fa-inbox';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        else if (in_groups($grupos_tramites_online_publico, $grupos))
        {
            $accesos[$indice]['href'] = 'tramites_online/tramites/bandeja_entrada_publico';
            $accesos[$indice]['title'] = 'Bandeja de Entrada';
            $accesos[$indice]['icon'] = 'fa-inbox';
            $indice++;
            $accesos[$indice]['href'] = 'tramites_online/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// // // <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Transferencias">
if (!function_exists('load_permisos_transferencias_escritorio'))
{

    function load_permisos_transferencias_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'transferencias_consulta_general');
        $grupos_transferencias_municipal = array('transferencias_municipal');
        $grupos_transferencias_area = array('transferencias_area');
        $grupos_transferencias_publico = array('transferencias_publico');
        if (in_groups($grupos_admin, $grupos))
        {
            $accesos[$indice]['href'] = 'transferencias/tramites/bandeja_entrada';
            $accesos[$indice]['title'] = 'Bandeja de Entrada';
            $accesos[$indice]['icon'] = 'fa-inbox';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/deudas/consultar';
            $accesos[$indice]['title'] = 'Consulta de Deuda';
            $accesos[$indice]['icon'] = 'fa-money';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/escribanos/listar';
            $accesos[$indice]['title'] = 'Escribanos';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/numeraciones/listar';
            $accesos[$indice]['title'] = 'Numeraciones';
            $accesos[$indice]['icon'] = 'fa-list-ol';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/adjuntos_tipos/listar';
            $accesos[$indice]['title'] = 'Tipos de Adjuntos';
            $accesos[$indice]['icon'] = 'fa-folder-open-o';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/tramites_tipos/listar';
            $accesos[$indice]['title'] = 'Tipos de Trámites';
            $accesos[$indice]['icon'] = 'fa-folder-open';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/tramites/listar';
            $accesos[$indice]['title'] = 'Trámites';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/usuarios_oficinas/listar';
            $accesos[$indice]['title'] = 'Usuarios por Oficina';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        else if (in_groups($grupos_transferencias_municipal, $grupos))
        {
            $accesos[$indice]['href'] = 'transferencias/tramites/bandeja_entrada';
            $accesos[$indice]['title'] = 'Bandeja de Entrada';
            $accesos[$indice]['icon'] = 'fa-inbox';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/deudas/consultar';
            $accesos[$indice]['title'] = 'Consulta de Deuda';
            $accesos[$indice]['icon'] = 'fa-money';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/escribanos/listar';
            $accesos[$indice]['title'] = 'Escribanos';
            $accesos[$indice]['icon'] = 'fa-users';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/tramites/listar';
            $accesos[$indice]['title'] = 'Trámites';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        else if (in_groups($grupos_transferencias_area, $grupos))
        {
            $accesos[$indice]['href'] = 'transferencias/tramites/bandeja_entrada';
            $accesos[$indice]['title'] = 'Bandeja de Entrada';
            $accesos[$indice]['icon'] = 'fa-inbox';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/tramites/listar';
            $accesos[$indice]['title'] = 'Trámites';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        else if (in_groups($grupos_transferencias_publico, $grupos))
        {
            $accesos[$indice]['href'] = 'transferencias/tramites/bandeja_entrada_publico';
            $accesos[$indice]['title'] = 'Bandeja de Entrada';
            $accesos[$indice]['icon'] = 'fa-inbox';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/deudas/consultar';
            $accesos[$indice]['title'] = 'Consulta de Deuda';
            $accesos[$indice]['icon'] = 'fa-money';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/tramites/listar_publico';
            $accesos[$indice]['title'] = 'Trámites';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'transferencias/manuales';
            $accesos[$indice]['title'] = 'Manual de Usuario';
            $accesos[$indice]['icon'] = 'fa-question';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Permisos Escritorio Vales Combustible">
if (!function_exists('load_permisos_vales_combustible_escritorio'))
{

    function load_permisos_vales_combustible_escritorio($grupos)
    {
        $accesos = array();
        $indice = 0;
        $grupos_admin = array('admin', 'vales_combustible_consulta_general');
        $grupos_vales_combustible_contaduria = array('vales_combustible_contaduria');
        $grupos_vales_combustible_hacienda = array('vales_combustible_hacienda');
        $grupos_vales_combustible_areas = array('vales_combustible_areas');
        $grupos_vales_combustible_autorizaciones = array('vales_combustible_autorizaciones');
        $grupos_vales_combustible_obrador = array('vales_combustible_obrador');
        $grupos_vales_combustible_estacion = array('vales_combustible_estacion');

        if (in_groups($grupos_admin, $grupos) || in_groups($grupos_vales_combustible_contaduria, $grupos))
        {
            $accesos[$indice]['href'] = 'vales_combustible/autorizaciones/listar';
            $accesos[$indice]['title'] = 'Autorizaciones';
            $accesos[$indice]['icon'] = 'fa-thumbs-o-up';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/cupos_combustible/listar';
            $accesos[$indice]['title'] = 'Cupos Combustible';
            $accesos[$indice]['icon'] = 'fa-thermometer-half';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/estaciones/listar';
            $accesos[$indice]['title'] = 'Estaciones';
            $accesos[$indice]['icon'] = 'fa-industry';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/facturas/listar';
            $accesos[$indice]['title'] = 'Facturas';
            $accesos[$indice]['icon'] = 'fa-file-text';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/ordenes_compra/listar';
            $accesos[$indice]['title'] = 'Órdenes de Compra';
            $accesos[$indice]['icon'] = 'fa-file-text-o';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/remitos/listar';
            $accesos[$indice]['title'] = 'Remitos';
            $accesos[$indice]['icon'] = 'fa-file';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/reportes/listar';
            $accesos[$indice]['title'] = 'Reportes';
            $accesos[$indice]['icon'] = 'fa-line-chart';
            $indice++;
            if (in_groups($grupos_admin, $grupos))
            {
                $accesos[$indice]['href'] = 'vales_combustible/tipos_adjuntos/listar';
                $accesos[$indice]['title'] = 'Tipos Adjuntos';
                $accesos[$indice]['icon'] = 'fa-folder-open-o';
                $indice++;
                $accesos[$indice]['href'] = 'vales_combustible/tipos_combustible/listar';
                $accesos[$indice]['title'] = 'Tipos Combustible';
                $accesos[$indice]['icon'] = 'fa-tint';
                $indice++;
                $accesos[$indice]['href'] = 'vales_combustible/tipos_vehiculo/listar';
                $accesos[$indice]['title'] = 'Tipos Vehículo';
                $accesos[$indice]['icon'] = 'fa-car';
                $indice++;
                $accesos[$indice]['href'] = 'vales_combustible/usuarios_areas/listar';
                $accesos[$indice]['title'] = 'Usuarios Áreas';
                $accesos[$indice]['icon'] = 'fa-users';
                $indice++;
            }
            $accesos[$indice]['href'] = 'vales_combustible/vales/listar';
            $accesos[$indice]['title'] = 'Vales';
            $accesos[$indice]['icon'] = 'fa-sticky-note';
            $indice++;
            if (in_groups($grupos_admin, $grupos))
            {
                $accesos[$indice]['href'] = 'vales_combustible/vales/listar_pendientes';
                $accesos[$indice]['title'] = 'Vales Pendientes';
                $accesos[$indice]['icon'] = 'fa-sticky-note-o';
                $indice++;
            }
            $accesos[$indice]['href'] = 'vales_combustible/valores_combustible/listar';
            $accesos[$indice]['title'] = 'Valores Combustible';
            $accesos[$indice]['icon'] = 'fa-money';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/vehiculos/listar';
            $accesos[$indice]['title'] = 'Vehículos';
            $accesos[$indice]['icon'] = 'fa-truck';
            $indice++;
        }
        else if (in_groups($grupos_vales_combustible_hacienda, $grupos))
        {
            $accesos[$indice]['href'] = 'vales_combustible/cupos_combustible/listar';
            $accesos[$indice]['title'] = 'Cupos Combustible';
            $accesos[$indice]['icon'] = 'fa-thermometer-half';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/vales/listar';
            $accesos[$indice]['title'] = 'Vales';
            $accesos[$indice]['icon'] = 'fa-sticky-note';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/vales/listar_pendientes';
            $accesos[$indice]['title'] = 'Vales Pendientes';
            $accesos[$indice]['icon'] = 'fa-sticky-note-o';
            $indice++;
        }
        else if (in_groups($grupos_vales_combustible_autorizaciones, $grupos))
        {
            $accesos[$indice]['href'] = 'vales_combustible/autorizaciones/listar';
            $accesos[$indice]['title'] = 'Autorizaciones';
            $accesos[$indice]['icon'] = 'fa-thumbs-o-up';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/autorizaciones/listar_pendientes';
            $accesos[$indice]['title'] = 'Autorizaciones Pendientes';
            $accesos[$indice]['icon'] = 'fa-thumbs-up';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/vehiculos/listar';
            $accesos[$indice]['title'] = 'Vehículos';
            $accesos[$indice]['icon'] = 'fa-truck';
            $indice++;
        }
        else if (in_groups($grupos_vales_combustible_obrador, $grupos))
        {
            $accesos[$indice]['href'] = 'vales_combustible/autorizaciones/listar';
            $accesos[$indice]['title'] = 'Autorizaciones';
            $accesos[$indice]['icon'] = 'fa-thumbs-o-up';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/vehiculos/listar';
            $accesos[$indice]['title'] = 'Vehículos';
            $accesos[$indice]['icon'] = 'fa-truck';
            $indice++;
        }
        else if (in_groups($grupos_vales_combustible_estacion, $grupos))
        {
            $accesos[$indice]['href'] = 'vales_combustible/autorizaciones/listar_pendientes';
            $accesos[$indice]['title'] = 'Autorizaciones';
            $accesos[$indice]['icon'] = 'fa-thumbs-up';
            $indice++;
        }
        else if (in_groups($grupos_vales_combustible_areas, $grupos))
        {
            $accesos[$indice]['href'] = 'vales_combustible/vales/listar_areas';
            $accesos[$indice]['title'] = 'Vales';
            $accesos[$indice]['icon'] = 'fa-sticky-note-o';
            $indice++;
            $accesos[$indice]['href'] = 'vales_combustible/vehiculos/listar';
            $accesos[$indice]['title'] = 'Vehículos';
            $accesos[$indice]['icon'] = 'fa-truck';
            $indice++;
        }
        return $accesos;
    }
}
// </editor-fold>
