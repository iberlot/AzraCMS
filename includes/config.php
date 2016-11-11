<?php

/**
 * 
 * @author iberlot <@> ivanberlot@gmail.com
 * @todo FechaC 10/11/2016 - Lenguaje PHP 
 * 
 * @todo includes/config.php
 * 
 * @todo Archivo con todas aquellas variables de configuracion del sistema
 * 
 * @version 0.1	- Version de inicio 
 * @package CMS/includes
 * @category Configs
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


$dbhost = 'localhost';
$dbuser = 'iberlot';
$dbpass = 'JuliaMatilde';
$dbname = 'AzraCMS';

require ("../classes/class_db.php");

// conexion $host, $user, $pass, $db, $charset, $dbtype)
$db = new class_db ($dbhost, $dbuser, $dbpass, $dbname, "utf8", "mysql");
$db->connect ();

$db->dieOnError = false;
$db->mostrarErrores = true;
$db->debug = true; // True si quiero que muestre el Query en por pantalla



?>