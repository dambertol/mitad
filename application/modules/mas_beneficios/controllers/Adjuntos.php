<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adjuntos extends MY_Controller {

    /**
     * Controlador de Adjuntos
     * Autor: Leandro
     * Creado: 23/04/2020
     * Modificado: 23/04/2020 (Leandro)
     */
    function __construct() {
        $this->auth = FALSE;
        parent::__construct();
        $this->modulo = 'mas_beneficios';
        // Inicializaciones necesarias colocar acá.
    }

    public function descargar($entidad_nombre = NULL, $archivo_id = NULL) {
        return;
    }

    public function ver($entidad_nombre = NULL, $directorio_nombre = NULL, $sub_directorio_nombre = NULL, $archivo_id = NULL) {
        $path = "uploads/$this->modulo/$entidad_nombre/";
        $file = $path . $directorio_nombre;
        if (!file_exists($file)) {
            show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
        }

        $this->load->helper('file');
        header('Content-Type: ' . get_mime_by_extension($file));
        $last_modified = gmdate('D, d M Y H:i:s', filemtime($file));
        $etag = '"' . md5($last_modified) . '"';
        header("Last-Modified: $last_modified GMT");
        header('ETag: ' . $etag);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 100000000) . ' GMT');
        readfile($file);
        exit();
    }

    public function modal_agregar($entidad_nombre = NULL) {
        return;
    }

    public function agregar($entidad_nombre = NULL) {
        return;
    }

}
