<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{

	/**
	 * MY_Form_validation
	 *
	 * @package    CodeIgniter
	 * @subpackage libraries
	 * @category   library
	 * @version    1.0.0
	 * @author     ZettaSys <info@zettasys.com.ar>
	 * 
	 */
	public function validate_monthyear($date)
	{
		if (empty($date) || $date === -1)
		{
			return TRUE;
		}

		$partes = explode('/', $date);
		if (count($partes) > 2)
		{
			return FALSE;
		}

		if (!is_numeric($partes[0]) || !is_numeric($partes[1]))
		{
			return FALSE;
		}
		else if ((int) $partes[0] > 12 || (int) $partes[1] < 1970)
		{
			return FALSE;
		}

		return TRUE;
	}

	public function validate_date($date)
	{
		if (empty($date) || $date === -1)
		{
			return TRUE;
		}
		$date_format = 'd-m-Y';
		$new_date = str_replace('/', '-', trim($date));
		$time = strtotime($new_date);
		$is_valid = date($date_format, $time) === $new_date;
		if ($is_valid)
		{
			return TRUE;
		}
		return FALSE;
	}

	public function validate_time($time)
	{
		if (empty($time) || $time === -1)
		{
			return TRUE;
		}

		$partes = explode(':', $time);
		if (count($partes) > 2)
		{
			return FALSE;
		}

		if (!is_numeric($partes[0]) || !is_numeric($partes[1]))
		{
			return FALSE;
		}
		else if ((int) $partes[0] > 24 || (int) $partes[1] > 59)
		{
			return FALSE;
		}
		else if (mktime((int) $partes[0], (int) $partes[1]) === FALSE)
		{
			return FALSE;
		}

		return TRUE;
	}

	public function validate_datetime($datetime)
	{
		$date = substr($datetime, 0, strrpos($datetime, " "));
		$time = substr($datetime, strrpos($datetime, " ") + 1);
		return ($this->validate_date($date) && $this->validate_time($time));
	}

	public function money($value)
	{
		return (bool) preg_match('/^-?[0-9]+([\,\.][0-9]{1,2})?$/', $value);
	}

	public function money3($value)
	{
		return (bool) preg_match('/^-?[0-9]+([\,\.][0-9]{1,3})?$/', $value);
	}

	public function validate_cbu($cbu)
	{
		if (empty($cbu))
		{
			return TRUE;
		}
		if (strlen($cbu) !== 22) // Cadena debe tener 22 caracteres
		{
			return FALSE;
		}
		if (!ctype_digit($cbu)) // Todos los caracteres deben ser dígitos
		{
			return FALSE;
		}

		$peso = array(3, 1, 7, 9);

		$suma_8 = 0;
		$j_8 = 0;
		for ($i = 6; $i >= 0; $i--)
		{
			$suma_8 += (substr($cbu, $i, 1) * $peso[$j_8 % 4]);
			$j_8++;
		}
		$digito_8 = (10 - ($suma_8 % 10)) % 10;
		if ($digito_8 !== substr($cbu, 7, 1)) // Validación Dígito 8
		{
			return FALSE;
		}

		$suma_22 = 0;
		$j_22 = 0;
		for ($i = 20; $i >= 8; $i--)
		{
			$suma_22 += (substr($cbu, $i, 1) * $peso[$j_22 % 4]);
			$j_22++;
		}
		$digito_22 = (10 - ($suma_22 % 10)) % 10;
		if ($digito_22 !== substr($cbu, 21, 1)) // Validación Dígito 22
		{
			return FALSE;
		}
		return TRUE;
	}

	public function validate_cuil($cuit)
	{
		$digits = array();
		switch (strlen($cuit))
		{
			case 13:
				for ($i = 0; $i < strlen($cuit); $i++)
				{
					if ($i == 2 or $i == 11)
					{
						if ($cuit[$i] != '-')
						{
							return FALSE;
						}
					}
					else
					{
						if (!ctype_digit($cuit[$i]))
						{
							return FALSE;
						}
						if ($i < 12)
						{
							$digits[] = $cuit[$i];
						}
					}
				}
				$acum = 0;
				foreach (array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2) as $i => $multiplicador)
				{
					$acum += $digits[$i] * $multiplicador;
				}
				$cmp = 11 - ($acum % 11);
				if ($cmp == 11)
				{
					$cmp = 0;
				}
				if ($cmp == 10)
				{
					$cmp = 9;
				}
				return ($cuit[12] == $cmp);
				break;
			case 11:
				for ($i = 0; $i < strlen($cuit); $i++)
				{
					if (!ctype_digit($cuit[$i]))
					{
						return FALSE;
					}
					if ($i < 10)
					{
						$digits[] = $cuit[$i];
					}
				}
				$acum = 0;
				foreach (array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2) as $i => $multiplicador)
				{
					$acum += $digits[$i] * $multiplicador;
				}
				$cmp = 11 - ($acum % 11);
				if ($cmp == 11)
				{
					$cmp = 0;
				}
				if ($cmp == 10)
				{
					$cmp = 9;
				}
				return ($cuit[10] == $cmp);
				break;
			default:
				return FALSE;
				break;
		}
	}
}