<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper de Helpers
 * Autor: Gustavo
 * Creado: 05/09/2013
 * Modificado: 28/11/2019 (Leandro)
 */
if (!function_exists('lm'))
{

	function lm($message)
	{
		log_message('error', print_r($message, TRUE));
	}
}

if (!function_exists('lq'))
{

	function lq()
	{
		log_message('error', get_instance()->db->last_query());
	}
}

if (!function_exists('hash_mlc'))
{

	function hash_mlc($id)
	{
		return hash('crc32', $id);
	}
}

if (!function_exists('auto_ver'))
{

	function auto_ver($url)
	{
		$path = pathinfo($url);
		$string = $path['basename'];
		$ver = '.version' . filemtime($_SERVER['DOCUMENT_ROOT'] . SIS_AUTO_VER_PATH . $url) . '.';
		$str = '.';
		if (( $pos = strrpos($string, $str) ) !== false)
		{
			$search_length = strlen($str);
			$str = substr_replace($string, $ver, $pos, $search_length);
			return $path['dirname'] . '/' . $str;
		}
		else
			return $url;
	}
}

if (!function_exists('random_password'))
{

	function random_password($length, $count, $characters)
	{

		// $length - the length of the generated password
		// $count - number of passwords to be generated
		// $characters - types of characters to be used in the password
		$symbols = array();
		$passwords = array();
		$used_symbols = '';
		$pass = '';

		$symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
		$symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$symbols["numbers"] = '1234567890';
		$symbols["special_symbols"] = '!?~@#-_+<>[]{}';

		$characters = explode(",", $characters);
		foreach ($characters as $key => $value)
		{
			$used_symbols .= $symbols[$value];
		}
		$symbols_length = strlen($used_symbols) - 1;

		for ($p = 0; $p < $count; $p++)
		{
			$pass = '';
			for ($i = 0; $i < $length; $i++)
			{
				$n = rand(0, $symbols_length);
				$pass .= $used_symbols[$n];
			}
			$passwords[] = $pass;
		}

		return $passwords;
	}
}
/* End of file helper_helper.php */
/* Location: ./application/helpers/helper_helper.php */