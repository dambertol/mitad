<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter ORO CRM Model
 *
 * @package        	CodeIgniter
 * @subpackage    	Models
 * @category    	Models
 * @author        	Leandro
 * @created		24/09/2020
 */
class Oro_model extends CI_Model
{

    protected $errors;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Oro', SIS_ORO_URL);
        $this->oro->login(SIS_ORO_USER, SIS_ORO_KEY);
        $this->errors = array();
        // Inicializaciones necesarias colocar acá.
    }

    /**
     * send_data: Envia datos al CRM.
     *
     * @param array $data
     * @return bool $result
     */
    public function send_data($data = [])
    {
        $contact_id = $this->check_contact($data['dni']);
        $body = [
            "contact" => [
                "owner" => 1,
                "firstName" => $data['nombre'],
                "lastName" => $data['apellido'],
                "gender" => $data['sexo'] === 'Masculino' ? "male" : "female",
                "identificationNumber" => $data['dni'],
                "identificationType" => "dni",
                "taxIdentificationNumber" => $data['cuil'],
                "emails" => [
                    [
                        "email" => $data['email'],
                        "primary" => true,
                        "optIn" => true,
                        "unsuscribed" => false,
                        "status" => "valid"
                    ]
                ],
                "externalReferences" => [
                    [
                        "externalId" => $data['id'],
                        "source" => "Sistema MLC"
                    ]
                ],
                "sources" => [
                    "Sistema MLC"
                ]
            ]
        ];

        if (!empty($data['fecha_nacimiento']) && $data['fecha_nacimiento'] !== 'NULL')
        {
            $body["contact"]["birthday"] = $data['fecha_nacimiento'];
        }

        if (!empty($data['telefono']))
        {
            $cod = substr($data['telefono'], 0, 3);
            $tel = substr($data['telefono'], 3);
            $body["contact"]["phones"][] = [
                "phone" => $tel,
                "areaCode" => $cod,
                "primary" => false,
                "phoneType" => "landline"
            ];
        }

        if (!empty($data['celular']))
        {
            $cod = substr($data['celular'], 0, 3);
            $tel = substr($data['celular'], 3);
            $body["contact"]["phones"][] = [
                "phone" => $tel,
                "areaCode" => $cod,
                "primary" => true,
                "phoneType" => "mobile"
            ];
        }

        if (!empty($data['calle']))
        {
            $this->load->model('Localidades_model');
            $provincia = $this->Localidades_model->get_provincia($data['localidad_id']);
            if (!empty($provincia))
            {
                $regionCode = $provincia->codigo_p;
                $department = $provincia->codigo_d;
                $city = $provincia->codigo_l;
            }
            else
            {
                $regionCode = "";
                $department = "";
                $city = "";
            }

            $body["contact"]["addresses"] = [
                [
                    "street" => $data['calle'],
                    "street2" => $data['piso'] . " " . $data['dpto'] . " " . $data['manzana'] . " " . $data['casa'],
                    "regionCode" => $regionCode,
                    "department" => $department,
                    "city" => $city,
                    "doorNumber" => $data['altura'],
                    "countryCode" => "AR",
                    "primary" => true,
                ]
            ];
        }

        if (!empty($data['tags']))
        {
            $body["contact"]["tags"] = [
                0 => $data['tags']
            ];
        }

        try
        {
            if (!empty($contact_id))
            {
                list($code, $response) = $this->oro->execute("contacts/$contact_id.json", 'PUT', $body);
            }
            else
            {
                list($code, $response) = $this->oro->execute('contacts.json', 'POST', $body);
            }

            if ($code === 200 || $code === 201 || $code === 204)
            {
                return TRUE;
            }
            else
            {
                $this->set_error($code);
                return FALSE;
            }
        } catch (\Exception $e)
        {
            $this->set_error($e);
            return FALSE;
        }
    }

    /**
     * check_contact: Comprueba si existe un contacto, devuelve su id
     *
     * @param string $dni
     *
     * @return int|bool $id
     */
    public function check_contact($dni)
    {
        try
        {
            list($code, $response) = $this->oro->execute("contacts.json?identificationNumber=$dni&identificationType=dni", 'GET');
            if ($code === 200)
            {
                if (!empty($response[0]))
                {
                    return $response[0]['id'];
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                $this->set_error($code);
                return FALSE;
            }
        } catch (\Exception $e)
        {
            $this->set_error($e);
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
