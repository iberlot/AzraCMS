<?php

/**
 *
 * @author iberlot <@> ivanberlot@gmail.com
 * @todo FechaC 10/11/2016 - Lenguaje PHP
 *      
 * @todo
 *
 * @version 0.1 - Version de inicio
 * @package CMS/admin
 * @category
 *
 * @link ../includes/config.php - Archivo de variables de configuracion.
 * @link ../includes/funciones.php - Conjunto de funciones standard.
 * @link ../includes/variables.php - Inicializacion de las variables.
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
include '../includes/config.php';
include '../includes/funciones.php';
include '../includes/variables.php';

// Llamamos a la variable global que maneja la base de datos
global $db;

$nombre = iniVarForm ('nombre');
$usuario = iniVarForm ('usuario');
$contrasena = iniVarForm ('contrasena');
$email = iniVarForm ('email');

if ($_POST)
{
	if ($nombre == "" or $usuario == "" or $contrasena == "" or $email == "")
	{
		$mensaje = sprintf ("Hay algún campo vacío");
	}
	else
	{
		
		// print_r (" * ");
		// print_r ($nombre);
		// print_r (" - ");
		// print_r (" * ");
		// print_r ($usuario);
		// print_r (" - ");
		// print_r (" * ");
		// print_r ($contrasena);
		// print_r (" - ");
		// print_r (" * ");
		// print_r ($email);
		// print_r (" - ");
		
		$nombre = $db->real_escape_string ($nombre);
		$usuario = $db->real_escape_string ($usuario);
		$contrasena = $db->real_escape_string ($contrasena);
		$email = $db->real_escape_string ($email);
		
		$sql = sprintf ("INSERT INTO usuarios VALUES (NULL,'$nombre','$usuario', md5('$contrasena'), '$email')");
		
		if (! $db->query ($sql))
		{
			die ('Invalid query: ' . $db->error ());
		}
		
		$mensaje = sprintf ("Usuario registrado correctamente");
	}
}
?>


<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<link rel="stylesheet" type="text/css" href="../estilos/estilo.css" />

</head>
<body>
	<div id="registro">
    	<?php if (isset($mensaje)) { ?>
        <div class="error">
            <?php echo $mensaje?>
        </div>
    	<?php } ?>
    	<form method="post" action="registro.php">
			<label>Nombre: </label>
			<input type="text" name="nombre" value="<?php echo $nombre ?>">
			<br>
			<label>Nombre de usuario: </label>
			<input type="text" name="usuario" value="<?php echo $usuario ?>">
			<br>
			<label>Contraseña </label>
			<input type="password" name="contrasena">
			<br>
			<label>Email: </label>
			<input type="text" name="email" value="<?php echo $email ?>">
			<br>
			<div class="submit">
				<input type="submit" value="Registrar">
			</div>
		</form>
	</div>

</body>
</html>