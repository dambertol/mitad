<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Perfil extends MY_Controller
{

    /**
     * Controlador de Perfil
     * Autor: Leandro
     * Creado: 05/01/2021
     * Modificado: 05/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'lujan_pass_control', 'lujan_pass_publico', 'lujan_pass_beneficiario', 'lujan_pass_consulta_general');
        $this->grupos_publico = array('admin', 'lujan_pass_control', 'lujan_pass_publico', 'lujan_pass_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('Usuarios_model');
        $usuario = $this->Usuarios_model->get_one($this->session->userdata('user_id'));
        if (empty($usuario))
        {
            show_error('No se encontró el usuario', 500, 'Registro no encontrado');
        }

        $usuario->localidad = NULL;
        if (!empty($usuario->domicilio_id))
        {
            $this->load->model('Domicilios_model');
            $domicilio = $this->Domicilios_model->get_one($usuario->domicilio_id);
            if (!empty($domicilio))
            {
                $usuario->localidad = $domicilio->localidad;
            }
        }

        if (in_groups($this->grupos_publico, $this->grupos))
        {
            $data['administrar'] = TRUE;
        }

        $data['tarjeta'] = $this->generar_tarjeta("$usuario->apellido $usuario->nombre", $usuario->dni);
        $data['cuil'] = array(
            'name' => 'cuil',
            'id' => 'cuil',
            'type' => 'text',
            'value' => $usuario->cuil,
            'class' => 'form-control',
            'pattern' => '([0-9]{2})([-]?)(\d{8})([-]?)([0-9]{1})',
            'title' => 'Debe ingresar un CUIL',
            'data-minlength' => 11,
            'maxlength' => 13,
            'required' => 'required',
            'readonly' => 'readonly'
        );
        $data['nombre'] = array(
            'name' => 'nombre',
            'id' => 'nombre',
            'type' => 'text',
            'value' => $usuario->nombre,
            'class' => 'form-control',
            'maxlength' => 50,
            'required' => 'required',
            'readonly' => 'readonly'
        );
        $data['apellido'] = array(
            'name' => 'apellido',
            'id' => 'apellido',
            'type' => 'text',
            'value' => $usuario->apellido,
            'class' => 'form-control',
            'maxlength' => 50,
            'required' => 'required',
            'readonly' => 'readonly'
        );
        $data['sexo'] = array(
            'name' => 'sexo',
            'id' => 'sexo',
            'type' => 'text',
            'value' => $usuario->sexo,
            'class' => 'form-control',
            'maxlength' => 50,
            'required' => 'required',
            'readonly' => 'readonly'
        );
        $data['email'] = array(
            'name' => 'email',
            'id' => 'email',
            'type' => 'text',
            'value' => $usuario->email,
            'class' => 'form-control',
            'maxlength' => 50,
            'required' => 'required',
            'readonly' => 'readonly'
        );
        $data['celular'] = array(
            'name' => 'celular',
            'id' => 'celular',
            'type' => 'text',
            'value' => $usuario->celular,
            'class' => 'form-control',
            'pattern' => '(\d{3} \d{3} \d{4})|(\d{10})',
            'title' => 'Ingrese sólo números sin el 0 y sin el 15',
            'maxlength' => 12,
            'required' => 'required',
            'readonly' => 'readonly'
        );
        $data['localidad'] = array(
            'name' => 'localidad',
            'id' => 'localidad',
            'type' => 'text',
            'value' => $usuario->localidad,
            'class' => 'form-control',
            'maxlength' => 50,
            'required' => 'required',
            'readonly' => 'readonly'
        );

        $data['error'] = json_encode((!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error')));
        $data['message'] = json_encode($this->session->flashdata('message'));

        $data['image'] = 'img/lujan_pass/home.jpg';
        $data['title'] = 'Mis Datos';
        $data['usuario_logueado'] = $this->ion_auth->logged_in();
        $this->load_template('lujan_pass/front/perfil/perfil_content', $data);
    }

    private function generar_tarjeta($nombre, $numero)
    {
        $image = imagecreatefrompng("img/lujan_pass/tarjeta.png");
        $black = imagecolorallocate($image, 71, 71, 71);
        $white = imagecolorallocate($image, 255, 255, 255);
        $start_x = 30;
        $start_y = 260;
        $font_path = realpath('fonts/verdana.ttf');
//        $this->imagettfstroketext($image, 20, 0, $start_x, $start_y, $black, $white, $font_path, strtoupper($nombre), 2);
        imagettftext($image, 20, 0, $start_x, $start_y, $black, $font_path, mb_strtoupper($nombre));
        $start_y += 50;
//        $this->imagettfstroketext($image, 20, 0, $start_x, $start_y, $black, $white, $font_path, "$numero", 2);
        imagettftext($image, 20, 0, $start_x, $start_y, $black, $font_path, "$numero");

        imagealphablending($image, false);
        imagesavealpha($image, true);
        ob_start();
        imagepng($image, NULL, 9, PNG_NO_FILTER);
        $i = ob_get_contents();
        $attachment = chunk_split(base64_encode($i));
        ob_clean();
        imagedestroy($image);

        return $attachment;
    }

    private function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px)
    {
        for ($c1 = ($x - abs($px)); $c1 <= ($x + abs($px)); $c1++)
        {
            for ($c2 = ($y - abs($px)); $c2 <= ($y + abs($px)); $c2++)
            {
                $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
            }
        }
        return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
    }

    protected function load_template($contenido = 'general', $datos = NULL)
    {
        $data['menu'] = $this->load->view('lujan_pass/front/template/menu', $datos, TRUE);
        $data['content'] = $this->load->view($contenido, $datos, TRUE);
        $data['footer'] = $this->load->view('lujan_pass/front/template/footer', $datos, TRUE);
        $this->load->view('lujan_pass/front/template/template', $data);
    }
}
