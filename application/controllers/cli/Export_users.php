<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Export_users extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->input->is_cli_request())
        {
            show_404();
        }
        $this->load->model('Usuarios_model');
        $this->load->library('auth0');
    }

    public function index()
    {
        show_404();
    }

    public function export($test = 1, $from = 0, $to = 99999)
    {
        $usuarios = $this->Usuarios_model->get(array(
            'active' => 1,
            'id >' => $from,
            'id <=' => $to,
            'join' => array(
                array('personas', 'personas.id = users.persona_id', 'left', array(
                        'personas.nombre',
                        'personas.apellido',
                        'personas.email',
                    )
                )
            )
        ));
        echo "Exportando usuarios";
        if (!empty($usuarios))
        {
            $json_usuarios = [];

            foreach ($usuarios as $Usuario)
            {
                $user2 = array(
                    "email" => $Usuario->email,
                    "email_verified" => false,
                    "user_id" => $Usuario->id,
                    "username" => $Usuario->username,
                    "given_name" => $Usuario->nombre,
                    "family_name" => $Usuario->apellido,
                    "name" => "$Usuario->nombre $Usuario->apellido",
                    "custom_password_hash" => array(
                        "algorithm" => "bcrypt",
                        "hash" => array(
                            "value" => str_replace("$2y$", "$2a$", $Usuario->password),
                        )
                    )
                );
                $json_usuarios[] = $user2;
                echo ".";
            }
            $fp = fopen("usuarios-$from-$to.json", 'w');
            fwrite($fp, json_encode($json_usuarios));
            fclose($fp);

            if ($test !== 1)
            {
                echo "Enviando usuarios";
                $mgmt_api = $this->auth0->get_management();
                $results = $mgmt_api->jobs()->importUsers("usuarios-$from-$to.json", SIS_AUTH0_CONNECTION_ID, ['send_completion_email' => TRUE]);
                $fp2 = fopen("resultado-$from-$to.json", 'w');
                fwrite($fp2, json_encode($results));
                fclose($fp2);
                sleep(300);
                $errors = $mgmt_api->jobs()->getErrors($results['id']);
                $fp3 = fopen("errores-$from-$to.json", 'w');
                fwrite($fp3, json_encode($errors));
                fclose($fp3);
            }
        }
    }
}
