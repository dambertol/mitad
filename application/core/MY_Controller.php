<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    /**
     * MY_Controller
     *
     * @package    CodeIgniter
     * @subpackage core
     * @category   controller
     * @version    1.1.0
     * @author     ZettaSys <info@zettasys.com.ar>
     * 
     */
    protected $auth = TRUE;

    function __construct()
    {
        parent::__construct();
        setlocale(LC_TIME, 'es_AR.utf8');
        date_default_timezone_set('America/Argentina/Mendoza');
        if ($this->auth)
        {
            if (!$this->ion_auth->logged_in())
            {
                if ((SIS_SUB_DOMAIN === 'mas_beneficios' || SIS_SUB_DOMAIN === 'lujan_pass') && empty($this->router->module) && $this->router->class === 'escritorio')
                {
                    redirect(SIS_SUB_DOMAIN . '/front/inicio');
                }
                $uri = str_replace('/', '%20', uri_string());
                redirect('auth/login' . (empty($uri) ? '' : '/' . $uri));
            }
            else
            {
                if (empty($this->session->userdata['password_change']))
                {
                    $this->session->set_flashdata('error', '<br />Su contraseña está vencida, debe modificarla para proteger su cuenta');
                    redirect('auth/change_password', 'refresh');
                }
            }
            $this->grupos = groups_names($this->ion_auth->get_users_groups()->result_array());
        }
    }

    public function index()
    {
        if (in_groups($this->grupos_permitidos, $this->grupos))
        {
            redirect(substr(uri_string(), 0, !empty(stripos(uri_string(), '/index')) ? stripos(uri_string(), '/index') : strlen(uri_string())) . '/listar', 'refresh');
        }
        else
        {
            show_404();
        }
    }

    protected function set_filtro_datos_listar($post_name, $all_string, $column_name, $user_data, &$where_array)
    {
        if (!empty($_POST[$post_name]) && $this->input->post($post_name) != $all_string)
        {
            $where['column'] = $column_name;
            $where['value'] = $this->input->post($post_name);
            $where_array[] = $where;
            $this->session->set_userdata($user_data, $this->input->post($post_name));
        }
        else if (empty($_POST[$post_name]) && $this->session->userdata($user_data) !== FALSE)
        {
            $where['column'] = $column_name;
            $where['value'] = $this->session->userdata($user_data);
            $where_array[] = $where;
        }
        else
        {
            $this->session->unset_userdata($user_data);
        }
    }

    public function control_combo($opt, $type)
    {
        $array_name = 'array_' . $type . '_control';
        if (array_key_exists($opt, $this->$array_name))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function control_password($password = '')
    {
        $password = trim($password);

        $regex_lowercase = '/[a-z]/';
        $regex_uppercase = '/[A-Z]/';
        $regex_number = '/[0-9]/';
        $regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';

        if (empty($password))
        {
            $this->form_validation->set_message('control_password', 'El campo {field} es requerido.');

            return FALSE;
        }

        if (preg_match_all($regex_lowercase, $password) < 1)
        {
            $this->form_validation->set_message('control_password', 'El campo {field} debe contener al menos una minúscula.');

            return FALSE;
        }

        if (preg_match_all($regex_uppercase, $password) < 1)
        {
            $this->form_validation->set_message('control_password', 'El campo {field} debe contener al menos una mayúscula.');

            return FALSE;
        }

        if (preg_match_all($regex_number, $password) < 1)
        {
            $this->form_validation->set_message('control_password', 'El campo {field} debe contener al menos un número.');

            return FALSE;
        }

        /* if (preg_match_all($regex_special, $password) < 1)
          {
          $this->form_validation->set_message('control_password', 'El campo {field} debe contener al menos un caracter especial.' . ' ' . htmlentities('!@#$%^&*()\-_=+{};:,<.>§~'));

          return FALSE;
          } */

        if (strlen($password) < $this->config->item('min_password_length', 'ion_auth'))
        {
            $this->form_validation->set_message('control_password', 'El campo {field} debe contener ' . $this->config->item('min_password_length', 'ion_auth') . ' caracteres.');

            return FALSE;
        }

        if (strlen($password) > $this->config->item('max_password_length', 'ion_auth'))
        {
            $this->form_validation->set_message('control_password', 'El campo {field} no debe superar los ' . $this->config->item('max_password_length', 'ion_auth') . ' caracteres.');

            return FALSE;
        }

        return TRUE;
    }

    public function get_array($model, $desc = 'descripcion', $id = 'id', $options = array(), $array_registros = array())
    {
        if (empty($options))
        {
            $options['sort_by'] = $desc;
        }

        $registros = $this->{"{$model}_model"}->get($options);
        if (!empty($registros))
        {
            foreach ($registros as $Registro)
            {
                $array_registros[$Registro->{$id}] = $Registro->{$desc};
            }
        }
        return $array_registros;
    }

    public function set_model_validation_rules($model)
    {
        foreach ($model->fields as $name => $field)
        {
            if (empty($field['name']))
            {
                $field['name'] = $name;
            }
            if (empty($field['input_type']))
            {
                $this->add_input_validation_rules($field);
            }
            elseif ($field['input_type'] === 'combo')
            {
                $this->add_combo_validation_rules($field);
            }
        }
    }

    public function add_input_validation_rules($field_opts)
    {
        $name = $field_opts['name'];
        if (!isset($field_opts['label']))
        {
            $label = ucfirst($name);
        }
        else
        {
            $label = $field_opts['label'];
        }
        $rules = ''; // xss_clean no se controla mas aca

        if (isset($field_opts['required']) && $field_opts['required'])
        {
            $rules .= '|required';
        }
        if (isset($field_opts['minlength']))
        {
            $rules .= '|min_length[' . $field_opts['minlength'] . ']';
        }
        if (isset($field_opts['maxlength']))
        {
            $rules .= '|max_length[' . $field_opts['maxlength'] . ']';
        }
        if (isset($field_opts['matches']))
        {
            $rules .= '|matches[' . $field_opts['matches'] . ']';
        }

        if (isset($field_opts['type']))
        {
            switch ($field_opts['type'])
            {
                case 'cuil':
                    $rules .= '|validate_cuil';
                    break;
                case 'integer':
                case 'integer_with_neg':
                case 'telefono':
                    $rules .= '|integer';
                    break;
                case 'natural':
                    $rules .= '|is_natural';
                    break;
                case 'numeric':
                case 'numeric_with_neg':
                    $rules .= '|numeric';
                    break;
                case 'decimal':
                    $rules .= '|decimal';
                    break;
                case 'money':
                    $rules .= '|money';
                    break;
                case 'monthyear':
                    $rules .= '|validate_monthyear';
                    break;
                case 'date':
                    $rules .= '|validate_date';
                    break;
                case 'time':
                    $rules .= '|validate_time';
                    break;
                case 'datetime':
                    $rules .= '|validate_datetime';
                    break;
                case 'cbu':
                    $rules .= '|validate_cbu';
                    break;
                case 'email':
                    $rules .= '|valid_email';
                    break;
                default:
                    break;
            }
        }
        if (empty($rules))
        {
            $rules = 'trim';
        }

        $this->form_validation->set_rules($name, $label, trim($rules, '|'));
    }

    public function add_combo_validation_rules($field_opts)
    {
        $name = $field_opts['name'];
        if (!isset($field_opts['arr_name']))
        {
            $arr_name = $field_opts['name'];
        }
        else
        {
            $arr_name = $field_opts['arr_name'];
        }

        if (!isset($field_opts['label']))
        {
            $label = ucfirst($name);
        }
        else
        {
            $label = $field_opts['label'];
        }

        $rules = "callback_control_combo[$arr_name]";
        if (isset($field_opts['type']) && ($field_opts['type'] === 'multiple' || $field_opts['type'] === 'list' || $field_opts['type'] === 'multiple_bselect'))
        {
            $this->form_validation->set_rules($name . '[]', $label, $rules);
        }
        else
        {
            $this->form_validation->set_rules($name, $label, $rules);
        }
    }

    public function add_input_field(&$field_array, $field_opts, $def_value = NULL, $template = NULL)
    {
        if ($def_value === NULL)
        {
            $field['value'] = $this->form_validation->set_value($field_opts['name']);
        }
        else
        {
            $field['value'] = $this->form_validation->set_value($field_opts['name'], $def_value);
        }

        foreach ($field_opts as $key => $field_opt)
        {
            $field[$key] = $field_opt;
        }

        $field['id'] = empty($field_opts['id_name']) ? $field_opts['name'] : $field_opts['id_name'];

        if (isset($template) && $template === 'front')
        {
            $field['class'] = (empty($field_opts['class']) ? "" : " {$field_opts['class']}");
            $field['placeholder'] = $field_opts['label'];
        }
        else
        {
            $field['class'] = "form-control" . (empty($field_opts['class']) ? "" : " {$field_opts['class']}");
        }

        if (isset($field_opts['type']))
        {
            switch ($field_opts['type'])
            {
                case 'natural':
                case 'cbu':
                    $field['pattern'] = '[0-9]*';
                    $field['title'] = 'Debe ingresar sólo números';
                    $field['type'] = 'text';
                    break;
                case 'cuil':
                    $field['pattern'] = '([0-9]{2})([-]?)(\d{8})([-]?)([0-9]{1})';
                    $field['title'] = 'Debe ingresar un CUIL';
                    $field['type'] = 'text';
                    break;
                case 'email':
                    $field['title'] = 'Debe ingresar un email';
                    $field['type'] = 'email';
                    break;
                case 'integer':
                    $field['pattern'] = '^(0|[1-9][0-9]*)$';
                    $field['title'] = 'Debe ingresar sólo números enteros positivos';
                    $field['type'] = 'text';
                    break;
                case 'integer_with_neg':
                    $field['pattern'] = '^([-+]?[1-9][0-9]*|0)$';
                    $field['title'] = 'Debe ingresar sólo números enteros';
                    $field['type'] = 'text';
                    break;
                case 'numeric':
                case 'decimal':
                    $field['class'] .= " numberFormat";
                    $field['pattern'] = '[0-9]*[.,]?[0-9]+';
                    $field['title'] = 'Debe ingresar sólo números decimales';
                    $field['type'] = 'text';
                    break;
                case 'numeric_with_neg':
                    $field['class'] .= " numberFormat";
                    $field['pattern'] = '[-]?[0-9]*[.,]?[0-9]+';
                    $field['title'] = 'Debe ingresar sólo números decimales';
                    $field['type'] = 'text';
                    break;
                case 'money':
                    $field['class'] .= " precioFormat";
                    $field['pattern'] = '[-]?[0-9]+([,\.][0-9]{1,2})?';
                    $field['title'] = 'Debe ingresar un importe';
                    $field['type'] = 'text';
                    if (!empty($field['value']))
                        break;
                case 'date':
                    if (empty($field_opts['class']))
                    {
                        $field['class'] .= ' dateFormat';
                    }
                    $field['type'] = 'text';
                    if ($def_value !== NULL)
                    {
                        $field['value'] = $this->form_validation->set_value($field_opts['name'], date_format(new DateTime($def_value), 'd/m/Y'));
                    }
                    break;
                case 'datetime':
                    if (empty($field_opts['class']))
                    {
                        $field['class'] .= ' dateTimeFormat';
                    }
                    $field['type'] = 'text';
                    if ($def_value !== NULL)
                    {
                        $field['value'] = $this->form_validation->set_value($field_opts['name'], date_format(new DateTime($def_value), 'd/m/Y H:i'));
                    }
                    break;
                case 'password':
                    $field['type'] = 'password';
                    break;
                case 'telefono':
                    $field['class'] .= " telefonoFormat";
                    $field['pattern'] = '(\d{3} \d{3} \d{4})|(\d{10})';
                    $field['placeholder'] = '261 555 5555';
                    $field['title'] = 'Ingrese sólo números sin el 0 y sin el 15';
                    $field['type'] = 'text';
                    break;
                default:
                    break;
            }
        }

        if (!isset($template) || $template !== 'table')
        {
            if (!empty($field_opts['required']) && $field_opts['required'])
            {
                $field['label'] = form_label($field_opts['label'] . ' *', $field_opts['name'], array('class' => "col-sm-2 control-label"));
            }
            else
            {
                $field['label'] = form_label($field_opts['label'], $field_opts['name'], array('class' => "col-sm-2 control-label"));
            }
        }
        else
        {
            if (!empty($field_opts['required']) && $field_opts['required'])
            {
                $field['label'] = form_label($field_opts['label'] . ' *', $field_opts['name'], array('class' => "control-label"));
            }
            else
            {
                $field['label'] = form_label($field_opts['label'], $field_opts['name'], array('class' => "control-label"));
            }
        }

        $field_array[$field_opts['name']] = $field;
        $form_type = empty($field['form_type']) ? 'input' : $field['form_type'];
        $is_multiple = empty($field['is_multiple']) ? FALSE : $field['is_multiple'];
        $extra_button = empty($field['extra_button']) ? FALSE : $field['extra_button'];
        $extra_button_click = empty($field['extra_button_click']) ? FALSE : $field['extra_button_click'];
        unset($field['disabled']);
        unset($field['form_type']);
        unset($field['label']);
        unset($field['required']);
        unset($field['minlength']);
        unset($field['matches']);
        unset($field['id_name']);
        unset($field['is_multiple']);
        unset($field['extra_button']);
        unset($field['extra_button_click']);

        if (!empty($field_opts['disabled']) && $field_opts['disabled'])
        {
            $field['disabled'] = '';
        }

        if (!empty($field_opts['required']) && $field_opts['required'])
        {
            $field['required'] = '';
        }

        if (!empty($field_opts['is_multiple']) && $field_opts['is_multiple'])
        {
            $field['multiple'] = '';
        }

        if (!empty($field_opts['error_text']))
        {
            $field['data-error'] = $field_opts['error_text'];
        }

        if (!empty($field_opts['minlength']))
        {
            $field['data-minlength'] = $field_opts['minlength'];
        }

        if (!empty($field_opts['val_match']))
        {
            if (!empty($field_opts['val_match_text']))
            {
                $field['data-match-error'] = $field_opts['val_match_text'];
            }
            $field['data-match'] = "#" . $field_opts['val_match'];
        }

        if ($form_type === 'input')
        {
            $form = form_input($field);
        }
        elseif ($form_type === 'textarea')
        {
            $form = form_textarea($field);
        }
        elseif ($form_type === 'file')
        {
            if (!empty($field_opts['disabled']) && $field_opts['disabled'])
            {
                if (!empty($field['value']))
                {
                    if ($is_multiple)
                    {
                        $form = '';
                        foreach ($field['value'] as $Fvalue)
                        {
                            $form .= "<div class='control-label' style='text-align:left;'>" . anchor_popup($Fvalue, 'Ver Archivo') . '</div>';
                        }
                    }
                    else
                    {
                        $form = "<div class='control-label' style='text-align:left;'>" . anchor_popup($field['value'], 'Ver Archivo') . '</div>';
                    }
                }
                else
                {
                    $form = "<div class='control-label' style='text-align:left;'>Sin Archivo</div>";
                }
            }
            else
            {
                if (!empty($field['value']))
                {
                    if ($is_multiple)
                    {
                        $form = '';
                        foreach ($field['value'] as $Fvalue)
                        {
                            $form .= "<div class='control-label' style='text-align:left;'>" . anchor_popup($Fvalue, 'Archivo anterior (* será reemplazado si sube uno nuevo)') . '</div>';
                        }
                    }
                    else
                    {
                        $form = "<div class='control-label' style='text-align:left;'>" . anchor_popup($field['value'], 'Archivo anterior (* será reemplazado si sube uno nuevo)') . '</div>';
                    }
                }
                else
                {
                    $form = "<div class='control-label' style='text-align:left;'>Sin archivo anterior</div>";
                }
                $field['value'] = NULL;
                $form .= form_input($field);
            }
        }

        if (isset($field_opts['type']) && $field_opts['type'] === 'money')
        {
            $form = '<div class="input-group"><span class="input-group-addon"><i class="fa fa-dollar"></i></span>' . $form . '</div>';
        }

        if (!isset($template) || $template !== 'table')
        {
            if (!$extra_button)
            {

                $field_array[$field_opts['name']]['form'] = '<div class="col-sm-10">' . $form . '</div>';
            }
            else
            {
                $field_array[$field_opts['name']]['form'] = '<div class="col-sm-8">' . $form . '</div>';
                if (isset($extra_button_click))
                {
                    $button = form_button($field_opts['name'] . '_extra_button', $extra_button, array('id' => $field_opts['name'] . '_extra_button', 'class' => 'btn btn-sm btn-primary', 'onclick' => $extra_button_click));
                } else {
                  $button = form_button($field_opts['name'] . '_extra_button', $extra_button, array('id' => $field_opts['name'] . '_extra_button', 'class' => 'btn btn-sm btn-primary'));
                }
                $field_array[$field_opts['name']]['form'] .= '<div class="col-sm-2 text-center">' . $button . '</div>';
            }
        }
        else
        {
            $field_array[$field_opts['name']]['form'] = $form;
        }
    }

    public function add_combo_field(&$field_array, $field_opts, $def_value = NULL, $template = NULL)
    {
        $values = $field_opts['array'];
        if ($def_value == NULL)
        {
            if (isset($field_opts['type']) && ($field_opts['type'] === 'multiple' || $field_opts['type'] === 'list' || $field_opts['type'] === 'multiple_bselect'))
            {
                $anterior = NULL;
                $field['value'][] = $existe = $this->form_validation->set_value($field_opts['name'] . '[]');
                while (!empty($existe) && $anterior !== $existe)
                {
                    $anterior = $existe;
                    $field['value'][] = $existe = $this->form_validation->set_value($field_opts['name'] . '[]');
                }
            }
            else
            {
                $field['value'] = $this->form_validation->set_value($field_opts['name']);
            }
        }
        else
        {
            if (isset($field_opts['type']) && ($field_opts['type'] === 'multiple' || $field_opts['type'] === 'list' || $field_opts['type'] === 'multiple_bselect'))
            {
                $anterior = NULL;
                $field['value'][] = $existe = $this->form_validation->set_value($field_opts['name'] . '[]', $def_value);
                if (is_array($existe))
                {
                    $field['value'] = $existe;
                }
                else
                {
                    $field['value'][] = $existe;
                    while (!empty($existe) && $anterior !== $existe)
                    {
                        $anterior = $existe;
                        $existe = $this->form_validation->set_value($field_opts['name'] . '[]', $def_value);
                        if (!is_array($existe))
                        {
                            $field['value'][] = $existe;
                        }
                    }
                }
            }
            else
            {
                $field['value'] = $this->form_validation->set_value($field_opts['name'], $def_value);
            }
        }

        $field_array[$field_opts['name']]['required'] = empty($field_opts['required']) ? FALSE : $field_opts['required'];
        if (!isset($field_opts['label']))
        {
            $field_opts['label'] = ucfirst($field_opts['name']);
        }

        unset($field['disabled']);

        if (!empty($field_opts['required']) && $field_opts['required'])
        {
            $label = form_label($field_opts['label'] . ' *', $field_opts['name'], array('class' => "col-sm-2 control-label"));
        }
        else
        {
            $label = form_label($field_opts['label'], $field_opts['name'], array('class' => "col-sm-2 control-label"));
        }

        $extras = "";
        $extra_class = "";
        if (!empty($field_opts['disabled']) && $field_opts['disabled'])
        {
            $extras .= " disabled";
        }

        if (!empty($field_opts['required']) && $field_opts['required'])
        {
            $extras .= " required";
        }

        if (!empty($field_opts['error_text']))
        {
            $extras .= ' data-error="' . $field_opts['error_text'] . '"';
        }
        
        if (!empty($field_opts['onchange']))
        {
            $extras .= ' onchange="' . $field_opts['onchange'] . '"';
        }

        if (!empty($field_opts['class']))
        {
            $extra_class .= ' ' . $field_opts['class'];
        }

        if (isset($field_opts['type']))
        {
            if ($field_opts['type'] === 'multiple')
            {
                $script = '<script>
							$(document).ready(function() {
								$("#' . $field_opts['name'] . '").select2({
									placeholder: "Seleccione ' . $field_opts['label'] . '"
								});
								$("#' . $field_opts['name'] . '").val([' . $def_value . ']).trigger("change");
							});
						</script>';
                $form = form_dropdown($field_opts['name'] . '[]', $values, $field['value'], 'class="form-control select2" id="' . $field_opts['name'] . '" multiple tabindex="-1" aria-hidden="true"' . $extras);
            }
            elseif ($field_opts['type'] === 'list')
            {
                if (!empty($field_opts['disabled']) && $field_opts['disabled'])
                {
                    $disable_list = '$(".bootstrap-duallistbox-container").find("*").prop("disabled", true);';
                }
                else
                {
                    $disable_list = '';
                }
                $script = '<script>
							$(document).ready(function() {
								$("#' . $field_opts['name'] . '").bootstrapDualListbox({
									nonSelectedListLabel: "Disponibles",
									selectedListLabel: "Seleccionados"
							});
								' . $disable_list . '
							});
						</script>';
                $form = form_dropdown($field_opts['name'] . '[]', $values, $field['value'], 'class="form-control" id="' . $field_opts['name'] . '" multiple' . $extras);
            }
            elseif ($field_opts['type'] === 'bselect')
            {
                $title = '';
                if (!empty($field_opts['bselect_title']))
                {
                    if ($field_opts['bselect_title'] === 'null')
                    {
                        $title = '';
                    }
                    else
                    {
                        $title = ' title="-- ' . $field_opts['bselect_title'] . ' --" ';
                    }
                }
                else // Para mantener comportamiento de select creados anteriormente sin bselect_title
                {
                    $title = ' title="-- Seleccionar --" ';
                }
                $script = '';
//				$form = form_dropdown($field_opts['name'], $values, $field['value'], 'class="form-control selectpicker" id="' . $field_opts['name'] . '" data-live-search="true"' . $title . $extras);
                is_array($field['value']) OR $field['value'] = array($field['value']);
                $form = '<select name="' . $field_opts['name'] . '" class="form-control selectpicker" id="' . $field_opts['name'] . '" data-live-search="true"' . $title . $extras . ">\n";
                foreach ($values as $key => $val)
                {
                    $key = (string) $key;
                    if (is_array($val))
                    {
                        if (empty($val))
                        {
                            continue;
                        }
                        $icon = !empty($val['icono']) ? ' data-icon="' . $val['icono'] . '"' : '';
                        $sel = in_array($key, $field['value']) ? ' selected="selected"' : '';
                        $form .= '<option value="' . html_escape($key) . '"' . $sel . $icon . '>' . (string) $val['opciones'] . "</option>\n";
                    }
                    else
                    {
                        $sel = in_array($key, $field['value']) ? ' selected="selected"' : '';
                        $form .= '<option value="' . html_escape($key) . '"' . $sel . '>' . (string) $val . "</option>\n";
                    }
                }
                $form .= "</select>\n";
            }
            elseif ($field_opts['type'] === 'multiple_bselect')
            {
                $select_all = '';
                if (!empty($field_opts['bselect_all']) && $field_opts['bselect_all'])
                {
                    $select_all = ' data-actions-box="true"';
                }
                is_array($field['value']) OR $field['value'] = array($field['value']);
                is_array($values) OR $values = array($values);
                $script = '';
                $form = '<select name="' . $field_opts['name'] . '[]" class="form-control selectpicker" title="-- Seleccionar --" data-selected-text-format="count>5" id="' . $field_opts['name'] . '" multiple data-live-search="true"' . $select_all . $extras . '>\n';
                foreach ($values as $key => $val)
                {
                    $key = (string) $key;
                    if (is_array($val))
                    {
                        if (empty($val))
                        {
                            continue;
                        }
                        if (!empty($val['limite']))
                        {
                            $form .= '<optgroup data-icon="' . $val['icono'] . '" label="' . $key . '" data-max-options="' . $val['limite'] . "\">\n";
                        }
                        else
                        {
                            $form .= '<optgroup data-icon="' . $val['icono'] . '" label="' . $key . "\">\n";
                        }
                        foreach ($val['opciones'] as $optgroup_key => $optgroup_val)
                        {
                            $sel = in_array($optgroup_key, $field['value']) ? ' selected="selected"' : '';
                            $form .= '<option data-subtext="' . (string) $optgroup_val['desc'] . '" value="' . html_escape($optgroup_key) . '"' . $sel . '>'
                                    . (string) $optgroup_val['name'] . "</option>\n";
                        }
                        $form .= "</optgroup>\n";
                    }
                    else
                    {
                        $form .= '<option value="' . html_escape($key) . '"'
                                . (in_array($key, $field['value']) ? ' selected="selected"' : '') . '>'
                                . (string) $val . "</option>\n";
                    }
                }
                $form .= "</select>\n";
            }
        }
        else
        {
            $script = '';
            $form = form_dropdown($field_opts['name'], $values, $field['value'], 'class="form-control' . $extra_class . '" id="' . $field_opts['name'] . '"' . $extras);
        }

        $field_array[$field_opts['name']]['label'] = $script . $label;
        if (!isset($template) || $template !== 'table')
        {
            $field_array[$field_opts['name']]['form'] = '<div class="col-sm-10">' . $form . '</div>';
        }
        else
        {
            $field_array[$field_opts['name']]['form'] = $form;
        }
    }

    protected function build_fields($model_fields, $registro = NULL, $readonly = FALSE, $template = NULL)
    {
        $fields = array();
        foreach ($model_fields as $name => $field)
        {
            if ($readonly)
            {
                $field['disabled'] = TRUE;
                if (!isset($field['type']) || ($field['type'] !== 'multiple' && $field['type'] !== 'list'))
                {
                    unset($field['input_type']);
                    unset($field['array']);
                    unset($field['value']);
                }
                unset($field['required']);
            }
            $field['name'] = $name;
            if (empty($field['input_type']))
            {
                $this->add_input_field($fields, $field, isset($registro) ? $registro->{$name} : NULL, $template);
            }
            elseif ($field['input_type'] == 'combo')
            {
                if (isset($field['id_name']))
                {
                    $this->add_combo_field($fields, $field, isset($registro) ? $registro->{$field['id_name']} : NULL, $template);
                }
                else
                {
                    $this->add_combo_field($fields, $field, isset($registro) ? $registro->{"{$name}_id"} : NULL, $template);
                }
            }
        }
        return $fields;
    }

    protected function get_date_sql($post = 'fecha', $src_format = 'd/m/Y', $dst_format = 'Y-m-d')
    {
        if ($this->input->post($post))
        {
            $fecha = DateTime::createFromFormat($src_format, $this->input->post($post));
            $fecha_sql = date_format($fecha, $dst_format);
        }
        else
        {
            $fecha_sql = 'NULL';
        }
        return $fecha_sql;
    }

    protected function get_datetime_sql($post = 'fecha', $src_format = 'd/m/Y H:i', $dst_format = 'Y-m-d H:i:s')
    {
        return $this->get_date_sql($post, $src_format, $dst_format);
    }

    protected function modal_error($error_msg = '', $error_title = 'Error general')
    {
        $data['error_msg'] = $error_msg;
        $data['error_title'] = $error_title;
        $this->load->view('errors/html/error_modal', $data);
    }

    protected function load_template($contenido = 'general', $datos = NULL)
    {
        $modulo = $this->router->module;
        if (!empty($modulo))
        {
            $controlador = $this->router->module . '/' . $this->router->class;
        }
        else
        {
            $controlador = $this->router->class;
        }
        $url_actual = $controlador . '/' . $this->router->method;
        $usuario_sist = array(
            'nombre' => $this->session->userdata('nombre'),
            'apellido' => $this->session->userdata('apellido')
        );
        $datos['accesos_nav'] = load_permisos_nav($this->grupos, $controlador, $url_actual, $usuario_sist);
        $datos['menu_collapse'] = $this->session->userdata('menu_collapse');
        $data['nav'] = $this->load->view('template/nav', $datos, TRUE);
        $data['content'] = $this->load->view($contenido, $datos, TRUE);
        $data['css'] = !empty($datos['css']) ? $datos['css'] : NULL;
        $data['js'] = !empty($datos['js']) ? $datos['js'] : NULL;
        $this->load->view('template/general', $data);
    }
}
/*lista de funciuones y atributos
protected $auth
__construct()
index()
set_filtro_datos_listar($post_name, $all_string, $column_name, $user_data, &$where_array)
control_password($password = '')
control_combo($opt, $type)
set_model_validation_rules($model)
get_array($model, $desc = 'descripcion', $id = 'id', $options = array(), $array_registros = array())
add_combo_validation_rules($field_opts)
add_input_validation_rules($field_opts)
set_model_validation_rules($model)
add_combo_field(&$field_array, $field_opts, $def_value = NULL, $template = NULL)
add_input_field(&$field_array, $field_opts, $def_value = NULL, $template = NULL)
get_date_sql($post = 'fecha', $src_format = 'd/m/Y', $dst_format = 'Y-m-d') 
build_fields($model_fields, $registro = NULL, $readonly = FALSE, $template = NULL)
get_date_sql($post = 'fecha', $src_format = 'd/m/Y', $dst_format = 'Y-m-d')
modal_error($error_msg = '', $error_title = 'Error general')
get_datetime_sql($post = 'fecha', $src_format = 'd/m/Y H:i', $dst_format = 'Y-m-d H:i:s')
load_template($contenido = 'general', $datos = NULL)

*/
/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */