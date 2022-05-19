<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Auth0 Model
 *
 * @package        	CodeIgniter
 * @subpackage    	Models
 * @category    	Models
 * @author        	Leandro
 * @created		12/08/2020
 */
class Auth0_model extends CI_Model
{

    protected $errors;
    protected $_mgmt_api;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('auth0');
        $this->_mgmt_api = $this->auth0->get_management();
        $this->errors = array();
        // Inicializaciones necesarias colocar acá.
    }

    /**
     * create_user: Crea un usuario en Auth0.
     *
     * @param int $id
     * @param array $data
     * @return bool $result
     */
    public function create_user($id, $data = [])
    {
        try
        {
            $mgmt_api = $this->auth0->get_management();
            $auth0_user_data = [
                "email" => $data['email'],
                "blocked" => false,
                "email_verified" => false,
                "given_name" => $data['nombre'],
                "family_name" => $data['apellido'],
                "name" => $data['nombre'] . " " . $data['apellido'],
                "user_id" => "$id",
                "connection" => SIS_AUTH0_CONNECTION_NAME,
                "password" => $data['password'],
                "verify_email" => true,
                "username" => $data['username']
            ];
            $results = $mgmt_api->users()->create($auth0_user_data);
            return TRUE;
        } catch (\GuzzleHttp\Exception\RequestException $e)
        {
            if ($e->hasResponse())
            {
                $response = $e->getResponse();
                $response_body = json_decode((string) $response->getBody());
                $this->set_error($response_body->message);
            }
            return FALSE;
        }
    }

    /**
     * update_user: Modifica un usuario en Auth0.
     *
     * @param int $id
     * @param array $data
     * @return bool $result
     */
    public function update_user($id, $data = [])
    {
        try
        {
            $mgmt_api = $this->auth0->get_management();
            $auth0_user_data = [
                "connection" => SIS_AUTH0_CONNECTION_NAME,
            ];
            if (isset($data['email']))
            {
                $auth0_user_data['email'] = $data['email'];
                $auth0_user_data['email_verified'] = false;
                $auth0_user_data['verify_email'] = true;
            }
            if (isset($data['active']))
            {
                $auth0_user_data['blocked'] = $data['active'] === 0 ? true : false;
            }
            if (isset($data['nombre']) && isset($data['apellido']))
            {
                $auth0_user_data['given_name'] = $data['nombre'];
                $auth0_user_data['family_name'] = $data['apellido'];
                $auth0_user_data['name'] = $data['nombre'] . " " . $data['apellido'];
            }
            if (isset($data['password']))
            {
                $auth0_user_data['password'] = $data['password'];
            }
            $results = $mgmt_api->users()->update("auth0|$id", $auth0_user_data);
            return TRUE;
        } catch (\GuzzleHttp\Exception\RequestException $e)
        {
            if ($e->hasResponse())
            {
                $response = $e->getResponse();
                $response_body = json_decode((string) $response->getBody());
                $this->set_error($response_body->message);
            }
            return FALSE;
        }
    }

    /**
     * set_error: Setea un mensaje de error
     *
     * @param string $error
     *
     * @return string
     */
    public function set_error($error)
    {
        $this->errors[] = $error;

        return $error;
    }

    /**
     * errors: Devuelve un mensaje de error
     *
     * @return string
     */
    public function errors()
    {
        $_output = '';
        foreach ($this->errors as $error)
        {
            $_output .= '<p>' . $error . '</p>';
        }

        return $_output;
    }
}
