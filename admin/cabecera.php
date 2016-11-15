<?php

/**
 * 
 * @author iberlot <@> ivanberlot@gmail.com
 * @todo FechaC 10/11/2016 - Lenguaje PHP 
 * 
 * @todo Cabecera de incluida en todos los archivos del area de administracion
 * @version 0.1	- Version de inicio 
 * @package CMS/admin
 * @category 
 *
 * @link ../includes/config.php - Archivo de variables de configuracion.
 * @link ../includes/funciones.php - Conjunto de funciones standard.
 * @link ../includes/variables.php - Inicializacion de las variables.
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

session_start();

if (!$_SESSION['id']) 
{
	header('Location:entrada.php');
}
else 
{
	$id = $_SESSION['id'];

	include '../includes/config.php';
	include '../includes/funciones.php';
	include '../includes/variables.php';
	
	// Llamamos a la variable global que maneja la base de datos
	global $db;
	
	session_start ();
	
	include '../admin/menu.php';
	
}


?>