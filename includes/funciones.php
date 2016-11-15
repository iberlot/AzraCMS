<?php

/**
 *
 * @author iberlot <@> ivanberlot@gmail.com
 * @todo FechaC 14/11/2016 - Lenguaje PHP
 *      
 * @todo funciones.php
 *      
 * @todo Conjunto de funciones de uso standard
 * @version 0.1 - Version de inicio
 * @package CMS/includes
 *
 * @category Basics
 *
 */

/*
 * Querido programador:
 *
 * Cuando escribi este codigo, solo Dios y yo sabiamos como funcionaba.
 * Ahora, Solo Dios lo sabe!!!
 *
 * Asi que, si esta tratando de 'optimizar' esta rutina y fracasa (seguramente),
 * por favor, incremente el siguiente contador como una advertencia para el
 * siguiente colega:
 *
 * totalHorasPerdidasAqui = 1
 *
 */

/**
 * Devuelve el valos del campo en caso de existir y nulo en caso de que no.
 * 
 * @param string $nombreCampo
 * @param string $tipoForm - Puede tomar cualquiera de los siguientes valores POST o GET, 
 * 				en caso de no tener ser uno de ellos considerara que es del tipo $_REQUEST
 * @return Ambigous <string, unknown>
 */
function iniVarForm($nombreCampo, $tipoForm="")
{
	switch ($tipoForm)
	{
		case "POST" :
			if (isset ($_POST[$nombreCampo]))
			{
				$campo = $_POST[$nombreCampo];
			}
			else
			{
				$campo = "";
			}
			break;
		case "GET" :
			if (isset ($_GET[$nombreCampo]))
			{
				$campo = $_GET[$nombreCampo];
			}
			else
			{
				$campo = "";
			}
			break;
		default:
			if (isset ($_REQUEST[$nombreCampo]))
			{
				$campo = $_REQUEST[$nombreCampo];
			}
			else
			{
				$campo = "";
			}
			break;
	}

	return $campo;
}

function convertir_especiales_html($str)
{
	if (! isset ($GLOBALS["carateres_latinos"]))
	{
		$todas = get_html_translation_table (HTML_ENTITIES, ENT_NOQUOTES);
		$etiquetas = get_html_translation_table (HTML_SPECIALCHARS, ENT_NOQUOTES);
		$GLOBALS["carateres_latinos"] = array_diff ($todas, $etiquetas);
	}
	$str = strtr ($str, $GLOBALS["carateres_latinos"]);
	return $str;
}

// Function to sanitize values received from the form. Prevents SQL injection
function calcularDigitoVerificador($str)
{
	$digito = 0;
	$digito_array = "9713";
	$indice_array = 0;
	
	// Adjudicaci�n del digito - (Ponderador 9713).
	// 1. Cada digito de los componentes a verificar deber� multiplicarse por:
	
	for($i = 0; $i < strlen ($str); $i ++)
	{
		
		// 2. Se efectuara la suma de los productos parciales del punto 1).
		$digito += ($str[$i] * $digito_array[$indice_array]);
		$indice_array ++;
		if ($indice_array >= strlen ($digito_array))
		{
			$indice_array = 0;
		}
		// echo $i." ".$str[$i]." * ".$digito_array[$indice_array]."= ".($str[$i]*$digito_array[$indice_array])."<BR/>";
	}
	// echo $digito."<BR/>";
	
	// 3. Del resultado de dicha suma se considerara solo el ultimo digito.
	$digito = substr ($digito, - 1);
	// echo "3 ".$digito."<BR/>";
	
	// 4. Se obtendr� el digito verificador, realizando la diferencia entre el numero 10 y el digito se�alado en el punto 3).
	
	$digito = (10 - $digito);
	// echo "4 ".$digito."<BR/>";
	
	// 5. Si el digito verificador obtenido fuera "10", se adjudicara por convenci�n el valor "0".
	$digito = substr ($digito, - 1);
	// echo "5 ".$digito."<BR/>";
	
	return ($digito);
}

// Function to sanitize values received from the form. Prevents SQL injection
function clean($str)
{
	$str = @trim ($str);
	if (get_magic_quotes_gpc ())
	{
		$str = stripslashes ($str);
	}
	// return mysql_real_escape_string($str);
	return ($str);
}

function validarEmail($str)
{
	// $string = "first.last@domain.co.uk";
	if (preg_match ('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $str))
	{
		return 1;
	}
	return 0;
}

function fecha_DD_MM_YYYY_Oracle($fecha_inicio)
{
	$fecha_inicio = str_replace ('-', '', $fecha_inicio);
	$fecha_inicio = str_replace ('/', '', $fecha_inicio);
	// $dato_post_fecha_recibo_usal = preg_replace('([^0-9])', '', $dato_post_valorcuota);
	$dd = substr ($fecha_inicio, - 2);
	$mm = substr ($fecha_inicio, 4, 2);
	$yyyy = substr ($fecha_inicio, 0, 4);
	
	if ($fecha_inicio)
	{
		$fecha_inicio = $dd . "/" . $mm . "/" . $yyyy;
	}
	return $fecha_inicio;
}

/**
 * invierte el orden de la fecha para que quede en el formato dia-mes-a�o
 *
 * @param date $fecha
 *        	fecha con el formato ano-mes-dia
 * @return string $aux
 */
function invertirFecha($fecha)
{
	list ($ano, $mes, $dia) = explode ('-', $fecha);
	$aux = $dia . "-" . $mes . "-" . $ano;
	
	return $aux;
}

/**
 * devuelve el dia correspondiente de la semana en formato de tres letras
 *
 * @param date $fecha
 *        	fecha con el formato ano-mes-dia
 * @return string $dias
 */
function nombreDiacorto($fecha)
{
	list ($ano, $mes, $dia) = explode ('-', $fecha);
	$dias = array (
			'Dom',
			'Lun',
			'Mar',
			'Mie',
			'Jue',
			'Vie',
			'Sab',
			'86776' 
	);
	
	return $dias[date ("w", mktime (0, 0, 0, $mes, $dia, $ano))];
}

/**
 * devuelve la suma de dias
 *
 * @param date $fecha
 *        	fecha con el formato ano-mes-dia
 * @param int $dia
 *        	numero de dias a sumar
 * @return date fecha con los dias sumados
 */
function sumaDia($fecha, $dia)
{
	list ($year, $mon, $day) = explode ('-', $fecha);
	
	return date ('Y-m-d', mktime (0, 0, 0, $mon, $day + $dia, $year));
}

/**
 * Diferencia de d�as - Fecha mayor, Fecha menor
 *
 * @param array $fecha2
 *        	fecha mayor con el formato ano-mes-dia
 * @param array $fecha1
 *        	fecha menor con el formato ano-mes-dia
 * @return date $dias_diferencia fecha con los dias restados
 */
function diferenciaDias($fecha2, $fecha1)
{
	list ($ano2, $mes2, $dia2) = explode ('-', $fecha1);
	list ($ano1, $mes1, $dia1) = explode ('-', $fecha2);
	
	// calculo timestam de las dos fechas
	$timestamp1 = mktime (0, 0, 0, $mes1, $dia1, $ano1);
	$timestamp2 = mktime (0, 0, 0, $mes2, $dia2, $ano2);
	
	// resto a una fecha la otra
	$segundos_diferencia = $timestamp1 - $timestamp2;
	
	// convierto segundos en d�as
	$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);
	
	return round ($dias_diferencia);
}

/**
 * Chequea que la fecha ingresada sea correcta
 *
 * @param int $d
 *        	El d�a que est� dentro del n�mero de d�as del mes m dado. Los a�os a bisiestos son tomados en consideraci�n.
 * @param int $m
 *        	El mes entre 1 y 12 inclusive.
 * @param int $a
 *        	El a�o entre 1 y 32767 inclusive.
 *        	
 * @return bool puede ser 0 o 1 dependiendo si la fecha es correcta o no
 */
function fechaCorrecta($d, $m, $a)
{
	$c = checkdate ($m, $d, $a);
	if ($c)
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

?>