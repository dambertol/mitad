<?php

use Auth0\SDK\API\Authentication;
use Auth0\SDK\API\Management;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Auth0 Class
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Leandro
 * @created		05/08/2020
 */
class Auth0
{

    protected $_auth;
    protected $_config = [
        'client_secret' => SIS_AUTH0_CLIENT_SECRET,
        'client_id' => SIS_AUTH0_CLIENT_ID,
        'audience' => SIS_AUTH0_MANAGEMENT_AUDIENCE,
    ];
    protected $_result;
    protected $_management;

    function __construct()
    {
        log_message('debug', 'Auth0 Class Initialized');
    }

    public function get_auth()
    {
        if (empty($this->_auth))
        {
            $this->_auth = new Authentication(SIS_AUTH0_DOMAIN, SIS_AUTH0_CLIENT_ID);
        }

        return $this->_auth;
    }

    public function get_access_token()
    {
        if (empty($this->_auth))
        {
            $this->get_auth();
        }

        try
        {
            $this->_result = $this->_auth->client_credentials($this->_config);
        } catch (Exception $e)
        {
            log_message('error', $e->getMessage());
        }

        return $this->_result['access_token'];
    }

    public function get_management()
    {
        if (empty($this->_result))
        {
            $this->get_access_token();
        }

        if (empty($this->_management))
        {
            $this->_management = new Management($this->_result['access_token'], SIS_AUTH0_DOMAIN);
        }

        return $this->_management;
    }
}
