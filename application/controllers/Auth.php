<?php

use Auth0\SDK\Auth0;
use Jumbojett\OpenIDConnectClient;

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation', 'auth0'));
        $this->load->helper(array('url', 'language'));

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->grupos = groups_names($this->ion_auth->get_users_groups()->result_array());
        $this->grupos_permitidos = array('admin');
        $this->grupos_admin = array('admin');
    }

    // redirect if needed, otherwise display the user list
    public function index() {

        if (!$this->ion_auth->logged_in()) {
            // redirect them to the login page
            redirect('auth/login', 'refresh');
        } elseif (!$this->ion_auth->is_admin()) { // remove this elseif if you want to enable this for non-admins
            // redirect them to the home page because they must be an administrator to view this
            return show_error('You must be an administrator to view this page.');
        } else {
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            //list the users
            $this->data['users'] = $this->ion_auth->users()->result();
            foreach ($this->data['users'] as $k => $user) {
                $this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
            }

            $this->_render_page('auth/index', $this->data);
        }
    }

    // log the user in
    public function login($last_url = NULL, $tipo = NULL) {
        if (SIS_AUTH_MODE === 'local') {
            $this->data['title'] = $this->lang->line('login_heading');

            //validate form input
            $this->form_validation->set_rules('legajo', str_replace(':', '', $this->lang->line('login_identity_label')), 'required|integer');
            $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');

            if ($this->form_validation->run() === TRUE) {
                // check to see if the user is logging in
                // check for "remember me"
                $remember = (bool) $this->input->post('remember');

                if ($this->ion_auth->login($this->input->post('legajo'), $this->input->post('password'), $remember)) {
                    //if the login is successful
                    //redirect them back to the home page
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    if (SIS_SUB_DOMAIN === 'lujan_pass') {
                        redirect('lujan_pass/front/perfil', 'refresh');
                    } else {
                        if (!empty($last_url) && $last_url !== 'forgot_password') {
                            redirect(str_replace('%20', '/', $last_url));
                        } else {
                            redirect('escritorio', 'refresh');
                        }
                    }
                } else {
                    // if the login was un-successful
                    // redirect them back to the login page
                    // use redirects instead of loading views for compatibility with MY_Controller libraries
                    $this->session->set_flashdata('error', $this->ion_auth->errors());
                    if (!empty($last_url) && $last_url !== 'forgot_password') {
                        redirect("auth/login/$last_url", 'refresh');
                    } else {
                        redirect('auth/login', 'refresh');
                    }
                }
            } else {
                // the user is not logging in so display the login page
                // set the flash data error message if there is one
                if (SIS_SUB_DOMAIN === 'mas_beneficios' || SIS_SUB_DOMAIN === 'lujan_pass') {
                    $this->data['error'] = json_encode((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
                    $this->data['message'] = json_encode($this->session->flashdata('message'));
                } else {
                    $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                    $this->data['message'] = $this->session->flashdata('message');
                }

                $this->data['legajo'] = array(
                    'id' => 'legajo',
                    'name' => 'legajo',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('legajo'),
                    'class' => 'form-control',
                    'placeholder' => 'Documento',
                    'required' => 'required',
                    'autofocus' => 'autofocus',
                    'pattern' => '^([0-9]*)$',
                    'title' => 'Debe ingresar sólo números'
                );
                $this->data['password'] = array(
                    'id' => 'password',
                    'name' => 'password',
                    'type' => 'password',
                    'class' => 'form-control submit-enter',
                    'placeholder' => 'Contraseña',
                    'required' => 'required'
                );

                if (SIS_SUB_DOMAIN === 'transferencias') {
                    $this->_render_page('transferencias/login', $this->data);
                } elseif (SIS_SUB_DOMAIN === 'consultas') {
                    $this->_render_page('tramites_online/login', $this->data);
                } elseif (SIS_SUB_DOMAIN === 'tramites') {
                    $this->_render_page('tramites_online/login', $this->data);
                } elseif (SIS_SUB_DOMAIN === 'mas_beneficios') {
                    $this->data['image'] = 'img/mas_beneficios/login.jpeg';
                    $this->data['menu'] = $this->load->view('mas_beneficios/front/template/menu', $this->data, TRUE);
                    if ($tipo === 'ben') {
                        $this->data['content'] = $this->load->view('mas_beneficios/front/login_ben', $this->data, TRUE);
                    } else {
                        $this->data['content'] = $this->load->view('mas_beneficios/front/login_com', $this->data, TRUE);
                    }
                    $this->data['footer'] = $this->load->view('mas_beneficios/front/template/footer', $this->data, TRUE);
                    $this->load->view('mas_beneficios/front/template/template', $this->data);
                } elseif (SIS_SUB_DOMAIN === 'lujan_pass') {
                    $this->data['image'] = 'img/lujan_pass/login.jpeg';
                    $this->data['menu'] = $this->load->view('lujan_pass/front/template/menu', $this->data, TRUE);
                    if ($tipo === 'ben') {
                        $this->data['content'] = $this->load->view('lujan_pass/front/login_ben', $this->data, TRUE);
                    } else {
                        $this->data['content'] = $this->load->view('lujan_pass/front/login_com', $this->data, TRUE);
                    }
                    $this->data['footer'] = $this->load->view('lujan_pass/front/template/footer', $this->data, TRUE);
                    $this->load->view('lujan_pass/front/template/template', $this->data);
                } else {
                    $this->_render_page('auth/login', $this->data);
                }
            }
        } else {
            $auth0 = new Auth0([
                'domain' => SIS_AUTH0_DOMAIN,
                'client_id' => SIS_AUTH0_CLIENT_ID,
                'redirect_uri' => SIS_BASE_URL . '/auth/check_auth0/',
                'scope' => 'openid profile email'
            ]);
            $auth0->login();
        }
    }

    public function check_auth0() {
        if (!$this->input->get('error')) {
            try {
                $auth0 = new Auth0([
                    'domain' => SIS_AUTH0_DOMAIN,
                    'client_id' => SIS_AUTH0_CLIENT_ID,
                    'client_secret' => SIS_AUTH0_CLIENT_SECRET,
                    'redirect_uri' => SIS_BASE_URL . '/auth/check_auth0/'
                ]);
                $this->userInfo = $auth0->getUser();

                if ($this->userInfo) {
                    $identity = $this->userInfo['https://sistemamlc.lujandecuyo.gob.ar/username'];
                    $email = $this->userInfo['email'];
                    if ($this->ion_auth->login_auth0($identity, $email)) {
                        if (SIS_SUB_DOMAIN === 'lujan_pass') {
                            redirect('lujan_pass/front/perfil', 'refresh');
                        } else {
                            redirect('escritorio', 'refresh');
                        }
                    }
                }
            } catch (Exception $e) {
                log_message('error', $e->getMessage());
            }
        }

        $this->logout();
    }

    // log the user out
    public function logout() {
        if (SIS_AUTH_MODE === 'local') {
            $this->data['title'] = "Logout";

            // log the user out
            $logout = $this->ion_auth->logout();

            // redirect them to the login page
            redirect('auth/logout_message', 'refresh');
        } else {
            $logout = $this->ion_auth->logout();    //LOCAL
            $auth0 = new Auth0([
                'domain' => SIS_AUTH0_DOMAIN,
                'client_id' => SIS_AUTH0_CLIENT_ID,
                'redirect_uri' => SIS_BASE_URL
            ]);
            $auth_api = $this->auth0->get_auth();
            $auth0->logout();
            header('Location: ' . $auth_api->get_logout_link(SIS_BASE_URL, SIS_AUTH0_CLIENT_ID));
            exit;
        }
    }

    public function logout_message() {
        $this->session->set_flashdata('message', '<br />Sesión finalizada con éxito');
        if (SIS_SUB_DOMAIN === 'lujan_pass') {
            redirect('lujan_pass/front/inicio', 'refresh');
        } else {
            redirect('auth/login', 'refresh');
        }
    }

    // change password
    public function change_password() {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|matches[new_confirm]|callback_control_password');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $user = $this->ion_auth->user()->row();

        if ($this->form_validation->run() == false) {
            // display the form
            // set the flash data error message if there is one
            if (SIS_SUB_DOMAIN === 'mas_beneficios' || SIS_SUB_DOMAIN === 'lujan_pass') {
                $this->data['error'] = json_encode((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
                $this->data['message'] = json_encode($this->session->flashdata('message'));
            } else {
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->data['message'] = $this->session->flashdata('message');
            }

            $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
            $this->data['old_password'] = array(
                'name' => 'old',
                'id' => 'old',
                'type' => 'password',
                'required' => 'required'
            );
            $this->data['new_password'] = array(
                'name' => 'new',
                'id' => 'new',
                'type' => 'password',
                'required' => 'required',
                'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                'title' => 'Contraseña debe tener al menos ' . $this->data['min_password_length'] . ' caracteres'
            );
            $this->data['new_password_confirm'] = array(
                'name' => 'new_confirm',
                'id' => 'new_confirm',
                'type' => 'password',
                'required' => 'required',
                'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
            );
            $this->data['user_id'] = array(
                'name' => 'user_id',
                'id' => 'user_id',
                'type' => 'hidden',
                'value' => $user->id,
            );

            // render
            $this->data['title'] = TITLE . ' - Cambiar contraseña';
            $this->load_template('auth/change_password', $this->data);
        } else {
            $identity = $this->session->userdata('identity');

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
            if (SIS_AUTH_MODE === 'auth0') {
                // AUTH0
                if ($this->db->trans_status() && $trans_ok) {
                    $this->load->model('Auth0_model');
                    $data['password'] = $this->input->post('new');
                    $trans_ok = $this->Auth0_model->update_user($user->id, $data);
                }
            }

            if ($this->db->trans_status() && $trans_ok) {
                //if the password was successfully changed
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->logout();
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                redirect('auth/change_password', 'refresh');
            }
        }
    }

    // forgot password
    function forgot_password() {
        $this->load->library('recaptcha');

        // setting validation rules by checking whether identity is username or email
        if ($this->config->item('identity', 'ion_auth') != 'email') {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
        } else {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
        }

        if ($this->form_validation->run() === TRUE) {
            $recaptcha = $this->input->post('g-recaptcha-response');
            if (!empty($recaptcha)) {
                $response = $this->recaptcha->verifyResponse($recaptcha);
                if (isset($response['success']) and $response['success'] === TRUE) {
                    $identity_column = $this->config->item('identity', 'ion_auth');
                    $identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();
                    if (empty($identity)) {

                        if ($this->config->item('identity', 'ion_auth') != 'email') {
                            $this->ion_auth->set_error('forgot_password_identity_not_found');
                        } else {
                            $this->ion_auth->set_error('forgot_password_email_not_found');
                        }

                        $this->session->set_flashdata('error', $this->ion_auth->errors());
                        redirect("auth/forgot_password", 'refresh');
                    }

                    // run the forgotten password method to email an activation code to the user
                    $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

                    if ($forgotten) {
                        // if there were no errors
                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
                    } else {
                        $this->session->set_flashdata('error', $this->ion_auth->errors());
                        redirect("auth/forgot_password", 'refresh');
                    }
                } else {
                    $this->session->set_flashdata('error', '<br />Captcha inválido');
                    redirect("auth/forgot_password", 'refresh');
                }
            } else {
                $this->session->set_flashdata('error', '<br />Debe completar el captcha');
                redirect("auth/forgot_password", 'refresh');
            }
        }
        $this->data['type'] = $this->config->item('identity', 'ion_auth');
        // setup the input
        $this->data['identity'] = array(
            'name' => 'identity',
            'id' => 'identity',
            'type' => 'text',
            'class' => 'form-control',
            'placeholder' => 'Documento',
            'required' => 'required',
            'autofocus' => 'autofocus',
            'maxlength' => 9,
            'pattern' => '^([0-9]*)$',
            'title' => 'Debe ingresar sólo números'
        );

        if ($this->config->item('identity', 'ion_auth') != 'email') {
            $this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
        } else {
            $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
        }

        // set any errors and display the form
        if (SIS_SUB_DOMAIN === 'mas_beneficios' || SIS_SUB_DOMAIN === 'lujan_pass') {
            $this->data['error'] = json_encode((validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('error'))));
            $this->data['message'] = json_encode($this->session->flashdata('message'));
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('error')));
            $this->data['message'] = $this->session->flashdata('message');
        }

        $this->data['recaptcha_widget'] = $this->recaptcha->getWidgetInvisible(array('data-callback' => 'submitForm'));
        $this->data['recaptcha_script'] = $this->recaptcha->getScriptTag();
        $this->data['title'] = TITLE . ' - Recuperá tu contraseña';
        if (SIS_SUB_DOMAIN === 'transferencias') {
            $this->_render_page('transferencias/forgot_password', $this->data);
        } else if (SIS_SUB_DOMAIN === 'consultas') {
            $this->_render_page('tramites_online/forgot_password', $this->data);
        } else if (SIS_SUB_DOMAIN === 'tramites') {
            $this->_render_page('tramites_online/forgot_password', $this->data);
        } elseif (SIS_SUB_DOMAIN === 'mas_beneficios') {
            $this->data['image'] = 'img/mas_beneficios/login.jpeg';
            $this->data['menu'] = $this->load->view('mas_beneficios/front/template/menu', $this->data, TRUE);
            $this->data['content'] = $this->load->view('mas_beneficios/front/forgot_password', $this->data, TRUE);
            $this->data['footer'] = $this->load->view('mas_beneficios/front/template/footer', $this->data, TRUE);
            $this->load->view('mas_beneficios/front/template/template', $this->data);
        } elseif (SIS_SUB_DOMAIN === 'lujan_pass') {
            $this->data['image'] = 'img/lujan_pass/login.jpeg';
            $this->data['menu'] = $this->load->view('lujan_pass/front/template/menu', $this->data, TRUE);
            $this->data['content'] = $this->load->view('lujan_pass/front/forgot_password', $this->data, TRUE);
            $this->data['footer'] = $this->load->view('lujan_pass/front/template/footer', $this->data, TRUE);
            $this->load->view('lujan_pass/front/template/template', $this->data);
        } else {
            $this->_render_page('auth/forgot_password', $this->data);
        }
    }

//	 reset password - final step for forgotten password
    public function reset_password($code = NULL) {
        if (!$code) {
            show_404();
        }

        $user = $this->ion_auth->forgotten_password_check($code);

        if ($user) {
            // if the code is valid then display the password reset form
            $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|matches[new_confirm]|callback_control_password');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

            if ($this->form_validation->run() === FALSE) {
                // display the form
                // set the flash data error message if there is one
                if (SIS_SUB_DOMAIN === 'mas_beneficios' || SIS_SUB_DOMAIN === 'lujan_pass') {
                    $this->data['error'] = json_encode((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
                    $this->data['message'] = json_encode($this->session->flashdata('message'));
                } else {
                    $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                    $this->data['message'] = $this->session->flashdata('message');
                }

                $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
                $this->data['new_password'] = array(
                    'name' => 'new',
                    'id' => 'new',
                    'type' => 'password',
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                    'title' => 'Mínimo 8 caracteres',
                    'class' => 'form-control',
                    'placeholder' => 'Nueva contraseña',
                    'required' => 'required',
                    'style' => 'margin-bottom:0;'
                );
                $this->data['new_password_confirm'] = array(
                    'name' => 'new_confirm',
                    'id' => 'new_confirm',
                    'type' => 'password',
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                    'title' => 'Mínimo 8 caracteres',
                    'class' => 'form-control',
                    'placeholder' => 'Confirmar contraseña',
                    'required' => 'required',
                    'style' => 'margin-bottom:0;'
                );
                $this->data['user_id'] = array(
                    'name' => 'user_id',
                    'id' => 'user_id',
                    'type' => 'hidden',
                    'value' => $user->id
                );
                $this->data['csrf'] = $this->_get_csrf_nonce();
                $this->data['code'] = $code;

                // render
                if (SIS_SUB_DOMAIN === 'transferencias') {
                    $this->_render_page('transferencias/reset_password', $this->data);
                } elseif (SIS_SUB_DOMAIN === 'consultas') {
                    $this->_render_page('tramites_online/reset_password', $this->data);
                } elseif (SIS_SUB_DOMAIN === 'tramites') {
                    $this->_render_page('tramites_online/reset_password', $this->data);
                } elseif (SIS_SUB_DOMAIN === 'mas_beneficios') {
                    $this->data['image'] = 'img/mas_beneficios/login.jpeg';
                    $this->data['menu'] = $this->load->view('mas_beneficios/front/template/menu', $this->data, TRUE);
                    $this->data['content'] = $this->load->view('mas_beneficios/front/reset_password', $this->data, TRUE);
                    $this->data['footer'] = $this->load->view('mas_beneficios/front/template/footer', $this->data, TRUE);
                    $this->load->view('mas_beneficios/front/template/template', $this->data);
                } elseif (SIS_SUB_DOMAIN === 'lujan_pass') {
                    $this->data['image'] = 'img/lujan_pass/login.jpeg';
                    $this->data['menu'] = $this->load->view('lujan_pass/front/template/menu', $this->data, TRUE);
                    $this->data['content'] = $this->load->view('lujan_pass/front/reset_password', $this->data, TRUE);
                    $this->data['footer'] = $this->load->view('lujan_pass/front/template/footer', $this->data, TRUE);
                    $this->load->view('lujan_pass/front/template/template', $this->data);
                } else {
                    $this->_render_page('auth/reset_password', $this->data);
                }
            } else {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id')) {

                    // something fishy might be up
                    $this->ion_auth->clear_forgotten_password_code($code);

                    show_error($this->lang->line('error_csrf'));
                } else {
                    // finally change the password
                    $identity = $user->{$this->config->item('identity', 'ion_auth')};

                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

                    if ($change) {
                        // if the password was successfully changed
                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        redirect("auth/login", 'refresh');
                    } else {
                        $this->session->set_flashdata('message', $this->ion_auth->errors());
                        redirect('auth/reset_password/' . $code, 'refresh');
                    }
                }
            }
        } else {
            // if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    // activate the user
//	public function activate($id, $code=false)
//	{
//		if ($code !== false)
//		{
//			$activation = $this->ion_auth->activate($id, $code);
//		}
//		else if ($this->ion_auth->is_admin())
//		{
//			$activation = $this->ion_auth->activate($id);
//		}
//
//		if ($activation)
//		{
//			// redirect them to the auth page
//			$this->session->set_flashdata('message', $this->ion_auth->messages());
//			redirect("auth", 'refresh');
//		}
//		else
//		{
//			// redirect them to the forgot password page
//			$this->session->set_flashdata('message', $this->ion_auth->errors());
//			redirect("auth/forgot_password", 'refresh');
//		}
//	}
    // deactivate the user
//	public function deactivate($id = NULL)
//	{
//		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
//		{
//			// redirect them to the home page because they must be an administrator to view this
//			return show_error('You must be an administrator to view this page.');
//		}
//
//		$id = (int) $id;
//
//		$this->load->library('form_validation');
//		$this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
//		$this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');
//
//		if ($this->form_validation->run() == FALSE)
//		{
//			// insert csrf check
//			$this->data['csrf'] = $this->_get_csrf_nonce();
//			$this->data['user'] = $this->ion_auth->user($id)->row();
//
//			$this->_render_page('auth/deactivate_user', $this->data);
//		}
//		else
//		{
//			// do we really want to deactivate?
//			if ($this->input->post('confirm') == 'yes')
//			{
//				// do we have a valid request?
//				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
//				{
//					show_error($this->lang->line('error_csrf'));
//				}
//
//				// do we have the right userlevel?
//				if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin())
//				{
//					$this->ion_auth->deactivate($id);
//				}
//			}
//
//			// redirect them back to the auth page
//			redirect('auth', 'refresh');
//		}
//	}
    //register user
    function register($tipo = NULL) {
        if ($this->ion_auth->logged_in()) {
            redirect('escritorio', 'refresh');
        }

        $this->load->model('Localidades_model');
        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        if ((SIS_SUB_DOMAIN === 'mas_beneficios' || SIS_SUB_DOMAIN === 'lujan_pass') && $tipo !== 'ben') {
            $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'departamento_id' => 345, 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));
        } else {
            $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));
        }
        $this->load->library('recaptcha');
        $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[50]');
        $this->form_validation->set_rules('apellido', 'Apellido', 'required|max_length[50]');
        if (SIS_SUB_DOMAIN === 'lujan_pass' && $tipo === 'ben') {
            $this->form_validation->set_rules('dni', 'DNI', 'required|integer|max_length[8]');
        } else {
            $this->form_validation->set_rules('cuil', 'CUIL', 'required|validate_cuil');
        }
        $this->form_validation->set_rules('sexo', 'Sexo', 'required|callback_control_combo[sexo]');
        $this->form_validation->set_rules('email', 'Correo Electrónico', 'required|valid_email');
        $this->form_validation->set_rules('email_confirmation', 'Correo Electrónico Confirmacion', 'required|valid_email|matches[email]');
        $this->form_validation->set_rules('celular', 'Celular', 'required|max_length[12]|min_length[10]');
        $this->form_validation->set_rules('localidad', 'Localidad', 'required|callback_control_combo[localidad]');

        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE) {
            $recaptcha = $this->input->post('g-recaptcha-response');
            if (!empty($recaptcha)) {
                $response = $this->recaptcha->verifyResponse($recaptcha);
                if (isset($response['success']) and $response['success'] === TRUE) {
                    $this->db->trans_begin();
                    $trans_ok = TRUE;

                    $this->load->model('Domicilios_model');
                    $this->load->model('Personas_model');
                    if (SIS_SUB_DOMAIN === 'lujan_pass' && $tipo === 'ben') {
                        $dni = $this->input->post('dni');
                        $cuil = $this->generate_cuit($dni, $this->input->post('sexo'));
                    } else {
                        $cuil = str_replace('-', '', $this->input->post('cuil'));
                        $dni = substr($cuil, 2, -1);
                    }
                    $nombre = $this->input->post('nombre');
                    $apellido = $this->input->post('apellido');
                    $celular = str_replace(' ', '', $this->input->post('celular'));
                    $persona = $this->Personas_model->get(array('dni' => $dni));
                    if (empty($persona)) {
                        $trans_ok &= $this->Domicilios_model->create(array(
                            'calle' => 'S/C',
                            'altura' => 'S/N',
                            'localidad_id' => $this->input->post('localidad')), FALSE);

                        $domicilio_id = $this->Domicilios_model->get_row_id();

                        $trans_ok &= $this->Personas_model->create(array(
                            'dni' => $dni,
                            'sexo' => $this->input->post('sexo'),
                            'cuil' => $cuil,
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'email' => $this->input->post('email'),
                            'celular' => $celular,
                            'nacionalidad_id' => 1,
                            'domicilio_id' => $domicilio_id), FALSE);

                        $persona_id = $this->Personas_model->get_row_id();
                        $nueva_persona = TRUE;
                    } else {
                        $this->load->model('Usuarios_model');
                        $usuario = $this->Usuarios_model->get(array('persona_id' => $persona[0]->id));
                        if (empty($usuario)) {
                            $persona_id = $persona[0]->id;
                            $nueva_persona = FALSE;
                        }
                    }

                    if ($this->db->trans_status() && $trans_ok) {
                        if (empty($persona_id)) { //YA EXISTE PERSONA Y TIENE USUARIO
                            $this->load->model('Grupos_model');
                            if (SIS_SUB_DOMAIN === 'tramites') {
                                $grupos_usuario = $this->Grupos_model->get(
                                        array(
                                            'join' => array(
                                                array('users_groups', 'users_groups.group_id = groups.id', 'left')
                                            ),
                                            'where' => array(
                                                array('column' => "groups.name IN ('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_publico', 'tramites_online_consulta_general')", 'value' => '', 'override' => TRUE),
                                                array('column' => 'users_groups.user_id', 'value' => $usuario[0]->id)
                                            )
                                        )
                                );
                                if (empty($grupos_usuario)) {
                                    $this->load->model('Grupos_model');
                                    $this->load->model('Usuarios_grupos_model');
                                    $grupo = $this->Grupos_model->get(array('name' => 'tramites_online_publico'));
                                    $trans_ok &= $this->Usuarios_grupos_model->create(array(
                                        'user_id' => $usuario[0]->id,
                                        'group_id' => $grupo[0]->id
                                            ), FALSE);
                                }
                            } elseif (SIS_SUB_DOMAIN === 'mas_beneficios') {
                                if ($tipo === 'ben') {
                                    $grupos_usuario = $this->Grupos_model->get(
                                            array(
                                                'join' => array(
                                                    array('users_groups', 'users_groups.group_id = groups.id', 'left')
                                                ),
                                                'where' => array(
                                                    array('column' => "groups.name IN ('admin', 'mas_beneficios_control', 'mas_beneficios_beneficiario', 'mas_beneficios_consulta_general')", 'value' => '', 'override' => TRUE),
                                                    array('column' => 'users_groups.user_id', 'value' => $usuario[0]->id)
                                                )
                                            )
                                    );
                                    if (empty($grupos_usuario)) {
                                        $this->load->model('Grupos_model');
                                        $this->load->model('Usuarios_grupos_model');
                                        $grupo = $this->Grupos_model->get(array('name' => 'mas_beneficios_beneficiario'));
                                        $trans_ok &= $this->Usuarios_grupos_model->create(array(
                                            'user_id' => $usuario[0]->id,
                                            'group_id' => $grupo[0]->id
                                                ), FALSE);
                                    }
                                } else {
                                    $grupos_usuario = $this->Grupos_model->get(
                                            array(
                                                'join' => array(
                                                    array('users_groups', 'users_groups.group_id = groups.id', 'left')
                                                ),
                                                'where' => array(
                                                    array('column' => "groups.name IN ('admin', 'mas_beneficios_control', 'mas_beneficios_publico', 'mas_beneficios_consulta_general')", 'value' => '', 'override' => TRUE),
                                                    array('column' => 'users_groups.user_id', 'value' => $usuario[0]->id)
                                                )
                                            )
                                    );
                                    if (empty($grupos_usuario)) {
                                        $this->load->model('Grupos_model');
                                        $this->load->model('Usuarios_grupos_model');
                                        $grupo = $this->Grupos_model->get(array('name' => 'mas_beneficios_publico'));
                                        $trans_ok &= $this->Usuarios_grupos_model->create(array(
                                            'user_id' => $usuario[0]->id,
                                            'group_id' => $grupo[0]->id
                                                ), FALSE);
                                    }
                                }
                            } elseif (SIS_SUB_DOMAIN === 'lujan_pass') {
                                if ($tipo === 'ben') {
                                    $grupos_usuario = $this->Grupos_model->get(
                                            array(
                                                'join' => array(
                                                    array('users_groups', 'users_groups.group_id = groups.id', 'left')
                                                ),
                                                'where' => array(
                                                    array('column' => "groups.name IN ('admin', 'lujan_pass_control', 'lujan_pass_beneficiario', 'lujan_pass_consulta_general')", 'value' => '', 'override' => TRUE),
                                                    array('column' => 'users_groups.user_id', 'value' => $usuario[0]->id)
                                                )
                                            )
                                    );
                                    if (empty($grupos_usuario)) {
                                        $this->load->model('Grupos_model');
                                        $this->load->model('Usuarios_grupos_model');
                                        $grupo = $this->Grupos_model->get(array('name' => 'lujan_pass_beneficiario'));
                                        $trans_ok &= $this->Usuarios_grupos_model->create(array(
                                            'user_id' => $usuario[0]->id,
                                            'group_id' => $grupo[0]->id
                                                ), FALSE);
                                    }
                                } else {
                                    $grupos_usuario = $this->Grupos_model->get(
                                            array(
                                                'join' => array(
                                                    array('users_groups', 'users_groups.group_id = groups.id', 'left')
                                                ),
                                                'where' => array(
                                                    array('column' => "groups.name IN ('admin', 'lujan_pass_control', 'lujan_pass_publico', 'lujan_pass_consulta_general')", 'value' => '', 'override' => TRUE),
                                                    array('column' => 'users_groups.user_id', 'value' => $usuario[0]->id)
                                                )
                                            )
                                    );
                                    if (empty($grupos_usuario)) {
                                        $this->load->model('Grupos_model');
                                        $this->load->model('Usuarios_grupos_model');
                                        $grupo = $this->Grupos_model->get(array('name' => 'lujan_pass_publico'));
                                        $trans_ok &= $this->Usuarios_grupos_model->create(array(
                                            'user_id' => $usuario[0]->id,
                                            'group_id' => $grupo[0]->id
                                                ), FALSE);
                                    }
                                }
                            }

                            if ($this->db->trans_status() && $trans_ok) {
                                $this->db->trans_commit();
                                $this->session->set_flashdata('message', '<br />Ya existe un usuario con su DNI. Si no recuerda su clave, puede recuperarla haciendo click <a href="auth/forgot_password">AQUÍ</a>');
                                redirect("auth/login/0/$tipo", 'refresh');
                            }
                            $trans_ok = FALSE;
                        } else {
                            $this->load->model('Grupos_model');
                            if (SIS_SUB_DOMAIN === 'tramites') {
                                $grupo = $this->Grupos_model->get(array('name' => 'tramites_online_publico'));
                                $group_data = array($grupo[0]->id);
                            } elseif (SIS_SUB_DOMAIN === 'mas_beneficios') {
                                $grupoto = $this->Grupos_model->get(array('name' => 'tramites_online_publico'));
                                if ($tipo === 'ben') {
                                    $grupomb = $this->Grupos_model->get(array('name' => 'mas_beneficios_beneficiario'));
                                } else {
                                    $grupomb = $this->Grupos_model->get(array('name' => 'mas_beneficios_publico'));
                                }
                                $group_data = array($grupoto[0]->id, $grupomb[0]->id);
                            } elseif (SIS_SUB_DOMAIN === 'lujan_pass') {
                                $grupoto = $this->Grupos_model->get(array('name' => 'tramites_online_publico'));
                                if ($tipo === 'ben') {
                                    $grupomb = $this->Grupos_model->get(array('name' => 'lujan_pass_beneficiario'));
                                } else {
                                    $grupomb = $this->Grupos_model->get(array('name' => 'lujan_pass_publico'));
                                }
                                $group_data = array($grupoto[0]->id, $grupomb[0]->id);
                            }
                            if (empty($group_data)) {
                                $trans_ok = FALSE;
                                $error_msg = '<br />Se ha producido un error con la base de datos.';
                            } else {
                                $additional_data = array('persona_id' => $persona_id, 'password_change' => 1);
                                $password = random_password(10, 1, "lower_case,upper_case,numbers");
                                $user_id = $this->ion_auth->register($dni, $password[0], strtolower($this->input->post('email')), $additional_data, $group_data);
                                if (!$user_id) {
                                    $trans_ok = FALSE;
                                } else {
                                    if (!$nueva_persona) {
                                        $trans_ok &= $this->Personas_model->update(array(
                                            'id' => $persona_id,
                                            'nombre' => $this->input->post('nombre'),
                                            'apellido' => $this->input->post('apellido'),
                                            'email' => $this->input->post('email')), FALSE);
                                    }
                                }

                                if (SIS_AUTH_MODE === 'auth0') {
                                    // AUTH0
                                    if ($this->db->trans_status() && $trans_ok) {
                                        $this->load->model('Auth0_model');
                                        $additional_data['nombre'] = $this->input->post('nombre');
                                        $additional_data['apellido'] = $this->input->post('apellido');
                                        $additional_data['email'] = strtolower($this->input->post('email'));
                                        $additional_data['username'] = $dni;
                                        $additional_data['password'] = $this->input->post('password');
                                        $trans_ok = $this->Auth0_model->create_user($user_id, $additional_data);
                                    }
                                }

                                if (SIS_ORO_ACTIVE) {
                                    // ORO CRM
                                    if ($this->db->trans_status() && $trans_ok) {
                                        $this->load->model('Oro_model');
                                        $datos['id'] = $persona_id;
                                        $datos['dni'] = $dni;
                                        $datos['sexo'] = $this->input->post('sexo');
                                        $datos['cuil'] = $cuil;
                                        $datos['nombre'] = $this->input->post('nombre');
                                        $datos['apellido'] = $this->input->post('apellido');
                                        $datos['celular'] = $celular;
                                        $datos['email'] = $this->input->post('email');
                                        // Agregar tags a nuevas personas
                                        if ($nueva_persona) {
                                            if (SIS_SUB_DOMAIN === 'tramites') {
                                                $datos['tags'] = 'Trámites Online';
                                            } elseif (SIS_SUB_DOMAIN === 'mas_beneficios') {
                                                $datos['tags'] = 'Más Beneficios';
                                            } elseif (SIS_SUB_DOMAIN === 'lujan_pass') {
                                                $datos['tags'] = 'Luján Pass';
                                            }
                                        }
                                        $this->Oro_model->send_data($datos);
                                    }
                                }
                            }
                        }
                    }

                    if ($this->db->trans_status() && $trans_ok) {
                        $this->db->trans_commit();
                        if (SIS_SUB_DOMAIN === 'tramites') {
                            $this->send_email('tramites_online/email/personas_alta', 'Cuenta creada', $this->input->post('email'), array('dni' => $dni, 'password' => $password[0]));
                            $this->session->set_flashdata('message', '<br />Cuenta creada con éxito. Recibirá un email con sus datos de acceso.');
                        } elseif (SIS_SUB_DOMAIN === 'mas_beneficios') {
                            $this->send_email('mas_beneficios/email/personas_alta', 'Cuenta creada', $this->input->post('email'), array('dni' => $dni, 'password' => $password[0]));
                            $this->session->set_flashdata('message', '<br />Cuenta creada con éxito. Recibirá un email con sus datos de acceso.');
                            redirect('auth/login/0/ben', 'refresh');
                        } elseif (SIS_SUB_DOMAIN === 'lujan_pass') {
                            $attachment = $this->generar_tarjeta("$apellido $nombre", $dni);
                            $this->send_email('lujan_pass/email/personas_alta', 'Cuenta creada', $this->input->post('email'), array('dni' => $dni, 'password' => $password[0]), $attachment);
                            $this->session->set_flashdata('message', '<br />Cuenta creada con éxito. Recibirá un email con sus datos de acceso.');
                            redirect('auth/login/0/ben', 'refresh');
                        }
                        redirect('auth/login', 'refresh');
                    } else {
                        $this->db->trans_rollback();
                        if ($this->Personas_model->get_error()) {
                            $error_msg .= $this->Personas_model->get_error();
                        }
                        if ($this->ion_auth->errors()) {
                            $error_msg .= $this->ion_auth->errors();
                        }
                    }
                } else {
                    $error_msg = '<br />Captcha inválido';
                }
            } else {
                $error_msg = '<br />Debe completar el captcha';
            }
        }
        // display the create user form
        // set the flash data error message if there is one
        if (SIS_SUB_DOMAIN === 'mas_beneficios' || SIS_SUB_DOMAIN === 'lujan_pass') {
            $this->data['error'] = json_encode((!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error')));
            $this->data['message'] = json_encode($this->session->flashdata('message'));
        } else {
            $this->data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
            $this->data['message'] = $this->session->flashdata('message');
        }

        if (SIS_SUB_DOMAIN === 'lujan_pass' && $tipo === 'ben') {
            $this->data['dni'] = array(
                'name' => 'dni',
                'id' => 'dni',
                'type' => 'text',
                'value' => $this->form_validation->set_value('dni'),
                'class' => 'form-control',
                'pattern' => '^(0|[1-9][0-9]*)$',
                'title' => 'Debe ingresar un DNI',
                'data-minlength' => 7,
                'maxlength' => 8,
                'placeholder' => 'DNI',
                'required' => 'required'
            );
        } else {
            $this->data['cuil'] = array(
                'name' => 'cuil',
                'id' => 'cuil',
                'type' => 'text',
                'value' => $this->form_validation->set_value('cuil'),
                'class' => 'form-control',
                'pattern' => '([0-9]{2})([-]?)(\d{8})([-]?)([0-9]{1})',
                'title' => 'Debe ingresar un CUIL',
                'data-minlength' => 11,
                'maxlength' => 13,
                'placeholder' => 'CUIL/CUIT',
                'required' => 'required'
            );
        }
        $this->data['nombre'] = array(
            'name' => 'nombre',
            'id' => 'nombre',
            'type' => 'text',
            'value' => $this->form_validation->set_value('nombre'),
            'class' => 'form-control',
            'maxlength' => 50,
            'placeholder' => 'Nombre',
            'required' => 'required'
        );
        $this->data['apellido'] = array(
            'name' => 'apellido',
            'id' => 'apellido',
            'type' => 'text',
            'value' => $this->form_validation->set_value('apellido'),
            'class' => 'form-control',
            'maxlength' => 50,
            'placeholder' => 'Apellido',
            'required' => 'required'
        );
        $this->data['sexo'] = array(
            'name' => 'sexo',
            'id' => 'sexo',
            'class' => 'form-control selectpicker',
            'required' => 'required',
            'data-live-search' => 'true',
            'title' => 'Seleccionar Sexo'
        );
        if (SIS_SUB_DOMAIN === 'mas_beneficios' || SIS_SUB_DOMAIN === 'lujan_pass') {    //NO USA BOOTSTRAP-SELECT
            $this->data['sexo_opt'] = array('' => 'Seleccionar Sexo') + $array_sexo;
        } else {
            $this->data['sexo_opt'] = $array_sexo;
        }
        $this->data['sexo_opt_selected'] = $this->form_validation->set_value('sexo');
        $this->data['email'] = array(
            'name' => 'email',
            'id' => 'email',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email'),
            'class' => 'form-control',
            'maxlength' => 50,
            'placeholder' => 'Domicilio Electrónico/Email (debe ser único para cada usuario)',
            'required' => 'required'
        );        
        $this->data['email_confirmation'] = array(
            'name' => 'email_confirmation',
            'id' => 'email_confirmation',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email_confirmation'),
            'class' => 'form-control',
            'maxlength' => 50,
            'placeholder' => 'Repetir Domicilio Electrónico/Email',
            'required' => 'required'
        );
        $this->data['celular'] = array(
            'name' => 'celular',
            'id' => 'celular',
            'type' => 'text',
            'value' => $this->form_validation->set_value('celular'),
            'class' => 'form-control',
            'pattern' => '(\d{3} \d{3} \d{4})|(\d{10})',
            'title' => 'Ingrese sólo números sin el 0 y sin el 15',
            'maxlength' => 12,
            'placeholder' => '261 555 5555',
            'required' => 'required'
        );
        if (SIS_SUB_DOMAIN === 'lujan_pass' && $tipo === 'ben') {
            $this->data['localidad'] = array(
                'name' => 'localidad',
                'id' => 'localidad',
                'class' => 'form-control selectpicker',
                'required' => 'required',
                'data-live-search' => 'true',
                'title' => 'Seleccionar Procedencia'
            );
        } else {
            $this->data['localidad'] = array(
                'name' => 'localidad',
                'id' => 'localidad',
                'class' => 'form-control selectpicker',
                'required' => 'required',
                'data-live-search' => 'true',
                'title' => 'Seleccionar Localidad'
            );
        }
        if (SIS_SUB_DOMAIN === 'mas_beneficios' || SIS_SUB_DOMAIN === 'lujan_pass') {    //NO USA BOOTSTRAP-SELECT
            $this->data['localidad_opt'] = array('' => 'Seleccionar Localidad') + $array_localidad;
        } else {
            $this->data['localidad_opt'] = $array_localidad;
        }
        $this->data['localidad_opt_selected'] = $this->form_validation->set_value('localidad');

        $this->data['recaptcha_widget'] = $this->recaptcha->getWidgetInvisible(array('data-callback' => 'submitForm'));
        $this->data['recaptcha_script'] = $this->recaptcha->getScriptTag();
        $this->data['title'] = TITLE . 'Registrate';

        if (SIS_SUB_DOMAIN === 'tramites') {
            // TODO ACA  
            $this->data['autenticado'] = FALSE;
            if (SIS_AUTEN_ACTIVE) {
                if ($this->session->has_userdata('user_info')) {
                    $user_info = $this->session->userdata('user_info');
                    $this->data['autenticado'] = TRUE;
                    $this->data['cuil'] = array(
                        'name' => 'cuil',
                        'id' => 'cuil',
                        'type' => 'text',
                        'value' => $user_info->cuit,
                        'class' => 'form-control',
                        'pattern' => '([0-9]{2})([-]?)(\d{8})([-]?)([0-9]{1})',
                        'title' => 'Debe ingresar un CUIL',
                        'data-minlength' => 11,
                        'maxlength' => 13,
                        'placeholder' => 'CUIL',
                        'required' => 'required',
                        'readonly' => 'readonly'
                    );
                    $this->data['nombre'] = array(
                        'name' => 'nombre',
                        'id' => 'nombre',
                        'type' => 'text',
                        'value' => $user_info->given_name,
                        'class' => 'form-control',
                        'maxlength' => 50,
                        'placeholder' => 'Nombre',
                        'required' => 'required',
                        'readonly' => 'readonly'
                    );
                    $this->data['apellido'] = array(
                        'name' => 'apellido',
                        'id' => 'apellido',
                        'type' => 'text',
                        'value' => $user_info->family_name,
                        'class' => 'form-control',
                        'maxlength' => 50,
                        'placeholder' => 'Apellido',
                        'required' => 'required',
                        'readonly' => 'readonly'
                    );

                    $this->data['url_autenticar_logout'] = $this->session->userdata('url_autenticar_logout');
                }
            }

            $this->_render_page('tramites_online/register', $this->data);
        } elseif (SIS_SUB_DOMAIN === 'consultas') {
            $this->_render_page('tramites_online/register', $this->data);
        } elseif (SIS_SUB_DOMAIN === 'mas_beneficios') {
            $this->data['image'] = 'img/mas_beneficios/login.jpeg';
            $this->data['menu'] = $this->load->view('mas_beneficios/front/template/menu', $this->data, TRUE);
            if ($tipo === 'ben') {
                $this->data['content'] = $this->load->view('mas_beneficios/front/register_ben', $this->data, TRUE);
            } else {
                $this->data['content'] = $this->load->view('mas_beneficios/front/register_com', $this->data, TRUE);
            }
            $this->data['footer'] = $this->load->view('mas_beneficios/front/template/footer', $this->data, TRUE);
            $this->load->view('mas_beneficios/front/template/template', $this->data);
        } elseif (SIS_SUB_DOMAIN === 'lujan_pass') {
            $this->data['image'] = 'img/lujan_pass/login.jpeg';
            $this->data['menu'] = $this->load->view('lujan_pass/front/template/menu', $this->data, TRUE);
            if ($tipo === 'ben') {
                $this->data['content'] = $this->load->view('lujan_pass/front/register_ben', $this->data, TRUE);
            } else {
                $this->data['content'] = $this->load->view('lujan_pass/front/register_com', $this->data, TRUE);
            }
            $this->data['footer'] = $this->load->view('lujan_pass/front/template/footer', $this->data, TRUE);
            $this->load->view('lujan_pass/front/template/template', $this->data);
        } else {
            show_404();
        }
    }

    private function send_email($template, $title, $to, $data, $attachment = NULL) {
        if (SIS_EMAIL_MODULO) {
            $this->email->initialize();
            $this->email->clear(TRUE);
            $this->email->set_mailtype("html");
            $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
            $this->email->to($to);
            $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $title);
            if (!empty($attachment)) {
                $this->email->attach($attachment, 'attachment', 'tarjeta.png', 'image/png', TRUE);
                $cid = $this->email->attachment_cid('tarjeta.png');
                $data['cid'] = $cid;
            }
            $message = $this->load->view($template, $data, TRUE);
            $this->email->message($message);

            if ($this->email->send()) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }

    //Perfil de usuario
    function perfil() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth', 'refresh');
        }

        $this->load->model('Usuarios_model');
        $usuario = $this->Usuarios_model->get_one($this->session->userdata('user_id'));
        if (empty($usuario)) {
            show_error('No se encontró el usuario', 500, 'Registro no encontrado');
        }

        $users_groups = $this->ion_auth->get_users_groups($usuario->id)->result();
        $grupos = '';
        if (!empty($users_groups)) {
            foreach ($users_groups as $Group) {
                $grupos .= $Group->name . PHP_EOL;
            }
        }

        // set the flash data error message if there is one
        $this->data['error'] = $this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('error');
        $this->data['message'] = $this->session->flashdata('message');

        $this->data['username'] = array(
            'name' => 'username',
            'id' => 'username',
            'type' => 'text',
            'value' => $usuario->username,
            'readonly' => 'readonly'
        );
        $this->data['nombre'] = array(
            'name' => 'nombre',
            'id' => 'nombre',
            'type' => 'text',
            'value' => $usuario->nombre,
            'readonly' => 'readonly'
        );
        $this->data['apellido'] = array(
            'name' => 'apellido',
            'id' => 'apellido',
            'type' => 'text',
            'value' => $usuario->apellido,
            'readonly' => 'readonly'
        );
        $this->data['email'] = array(
            'name' => 'email',
            'id' => 'email',
            'type' => 'text',
            'value' => $usuario->email,
            'readonly' => 'readonly'
        );
        $this->data['grupos'] = array(
            'name' => 'grupos',
            'id' => 'grupos',
            'type' => 'text',
            'value' => $grupos,
            'readonly' => 'readonly'
        );

        // render
        $this->data['title'] = TITLE . ' - Perfil';
        $this->load_template('auth/perfil', $this->data);
    }

    public function _get_csrf_nonce() {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

    public function _valid_csrf_nonce() {
        $csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
        if ($csrfkey && $csrfkey == $this->session->flashdata('csrfvalue')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function _render_page($view, $data = null, $returnhtml = false) {//I think this makes more sense
        $this->viewdata = (empty($data)) ? $this->data : $data;

        $view_html = $this->load->view($view, $this->viewdata, $returnhtml);

        if ($returnhtml)
            return $view_html; //This will return html on 3rd argument being true
    }

    public function control_password($password = '') {
        $password = trim($password);

        $regex_lowercase = '/[a-z]/';
        $regex_uppercase = '/[A-Z]/';
        $regex_number = '/[0-9]/';
        $regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';

        if (empty($password)) {
            $this->form_validation->set_message('control_password', 'El campo {field} es requerido.');

            return FALSE;
        }

        if (preg_match_all($regex_lowercase, $password) < 1) {
            $this->form_validation->set_message('control_password', 'El campo {field} debe contener al menos una minúscula.');

            return FALSE;
        }

        if (preg_match_all($regex_uppercase, $password) < 1) {
            $this->form_validation->set_message('control_password', 'El campo {field} debe contener al menos una mayúscula.');

            return FALSE;
        }

        if (preg_match_all($regex_number, $password) < 1) {
            $this->form_validation->set_message('control_password', 'El campo {field} debe contener al menos un número.');

            return FALSE;
        }

        /* if (preg_match_all($regex_special, $password) < 1)
          {
          $this->form_validation->set_message('control_password', 'El campo {field} debe contener al menos un caracter especial.' . ' ' . htmlentities('!@#$%^&*()\-_=+{};:,<.>§~'));

          return FALSE;
          } */

        if (strlen($password) < $this->config->item('min_password_length', 'ion_auth')) {
            $this->form_validation->set_message('control_password', 'El campo {field} debe contener ' . $this->config->item('min_password_length', 'ion_auth') . ' caracteres.');

            return FALSE;
        }

        if (strlen($password) > $this->config->item('max_password_length', 'ion_auth')) {
            $this->form_validation->set_message('control_password', 'El campo {field} no debe superar los ' . $this->config->item('max_password_length', 'ion_auth') . ' caracteres.');

            return FALSE;
        }

        return TRUE;
    }

    public function control_combo($opt, $type) {
        $array_name = 'array_' . $type . '_control';
        if (array_key_exists($opt, $this->$array_name)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_array($model, $desc = 'descripcion', $id = 'id', $options = array(), $array_registros = array()) {
        if (empty($options)) {
            $options['sort_by'] = $desc;
        }

        $registros = $this->{"{$model}_model"}->get($options);
        if (!empty($registros)) {
            foreach ($registros as $Registro) {
                $array_registros[$Registro->{$id}] = $Registro->{$desc};
            }
        }
        return $array_registros;
    }

    private function generate_cuit($dni, $sexo) {
        if (strlen($dni) === 7 || strlen($dni) === 8) {
            if (strlen($dni) === 7) {
                $dni = '0' . $dni;
            }
            switch ($sexo) {
                case 'Masculino':
                    $inicio = '20';
                    break;
                case 'Femenino':
                    $inicio = '27';
                    break;
                case 'Sociedad':
                    $inicio = '30';
                    break;
                default:
                    return NULL;
            }

            #############
            # Los numeros (excepto los dos primeros) que le tengo que
            # multiplicar a la cadena formada por el prefijo y por el
            # numero de documento los tengo almacenados en un arreglo.
            #############
            $mult = [3, 2, 7, 6, 5, 4, 3, 2];

            # Realizo las dos primeras multiplicaciones por separado.
            $calc = (substr($inicio, 0, 1) * 5) + (substr($inicio, 1, 1) * 4);
            for ($i = 0; $i < 8; $i++) {
                $calc += (substr($dni, $i, 1) * $mult[$i]);
            }

            # Mod is calculated here
            $rest = $calc % 11;

            #############
            # Llevo a cabo la evaluacion de las tres condiciones para
            # determinar el valor de C y conocer el valor definitivo de
            # AB.
            #############
            if ($sexo !== 'Sociedad' && $rest === 1) {
                if ($sexo === 'Masculino') {
                    $fin = '9';
                } else {
                    $fin = '4';
                }
                $inicio = '23';
            } elseif ($rest === 0) {
                $fin = '0';
            } else {
                $fin = 11 - $rest;
            }
            return "$inicio$dni$fin";
        } else {
            return NULL;
        }
    }

    private function generar_tarjeta($nombre, $numero) {
        $image = imagecreatefrompng("img/lujan_pass/tarjeta.png");
        $black = imagecolorallocate($image, 71, 71, 71);
        $white = imagecolorallocate($image, 255, 255, 255);
        $start_x = 30;
        $start_y = 260;
        $font_path = realpath('fonts/verdana.ttf');
        imagettftext($image, 20, 0, $start_x, $start_y, $black, $font_path, mb_strtoupper($nombre));
        $start_y += 50;
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

    private function load_template($contenido = NULL, $datos = NULL) {
        $controlador = $this->router->class;
        $url_actual = $this->router->class . '/' . $this->router->method;
        $usuario_sist = array(
            'nombre' => $this->session->userdata('nombre'),
            'apellido' => $this->session->userdata('apellido')
        );
        $datos['accesos_nav'] = load_permisos_nav($this->grupos, $controlador, $url_actual, $usuario_sist);
        $datos['menu_collapse'] = $this->session->userdata('menu_collapse');
        $data['nav'] = $this->load->view('template/nav', $datos, TRUE);
        $data['content'] = $this->load->view($contenido, $datos, TRUE);
        $this->load->view('template/general', $data);
    }

    public function autenticar($tipo = NULL) {
        $realm = "";
        $secret = "";
        switch ($tipo) {
            case 'afip':
                $realm = "mlc-afip";
                $secret = SIS_AUTEN_SECRET_AFIP;
                break;
            case 'anses':
                $realm = "mlc-anses";
                $secret = SIS_AUTEN_SECRET_ANSES;
                break;
            case 'miarg':
                $realm = 'mlc-miarg';
                $secret = SIS_AUTEN_SECRET_MIARG;
                break;
            case 'renaper':
                $realm = "";
                $secret = SIS_AUTEN_SECRET_RENAPER;
                break;
            default:
                die("Error de seleccion");
        }

        try {
            $oidc = new OpenIDConnectClient(
                    'https://tst.autenticar.gob.ar/auth/realms/' . $realm,
                    //'https://tst.autenticar.gob.ar/auth/realms/mlc-afip/protocol/openid-connect/auth',
                    'mlc',
                    $secret
            );

            $oidc->authenticate();
            /*
              echo "<pre>";
              print_r($oidc->requestUserInfo());
              echo "</pre>";
              echo '<a href="https://tst.autenticar.gob.ar/auth/realms/' . $realm . '/protocol/openid-connect/logout?redirect_uri=http://tad.mlc.local/auth/logout">Logout</a>';
             */
            $url = (ENVIRONMENT === 'production') ? SIS_AUTEN_TRAMITE_URL : 'http://tad.mlc.local/auth/logout';
            $url_autenticar_logout = 'https://tst.autenticar.gob.ar/auth/realms/' . $realm . '/protocol/openid-connect/logout?redirect_uri=' . $url;
            $this->userInfo = $oidc->requestUserInfo();

            if ($this->userInfo) {
                $this->session->set_userdata('user_info', $this->userInfo);
                $this->session->set_userdata('url_autenticar_logout', $url_autenticar_logout);
                //$this->register();
                redirect('auth/register', 'refresh');
                /*
                  if ($this->ion_auth->login_auth0($identity, $email)) {
                  redirect('escritorio', 'refresh');
                  } else {
                  echo "fallo";
                  }
                 * 
                 */
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            $this->logout();
            die('bye');
        }
        //exit;
    }

    /**
     * Pre Register para mostrar los botones de autenticar 
     */
    public function pre_register() {
        if ($this->ion_auth->logged_in()) {
            redirect('escritorio', 'refresh');
        }
        if (!SIS_AUTEN_ACTIVE) {
            redirect('auth/register', 'refresh');
        }
        if (SIS_SUB_DOMAIN === 'tramites') {
            $this->data['title'] = $this->lang->line('login_heading');
            $this->_render_page('tramites_online/pre_register', $this->data);
        } else {
            redirect('auth/register', 'refresh');
        }
    }

}
