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


$nombre = '';
$usuario = '';
$email = '';

// Llamamos a la variable global que maneja la base de datos
global $db;

if ($_POST)
{
	$nombre = $db->real_escape_string ($_POST['nombre']);
	$usuario = $db->real_escape_string ($_POST['usuario']);
	$contrasena = $db->real_escape_string ($_POST['contrasena']);
	$email = $db->real_escape_string ($_POST['email']);
	
	if ($nombre == "" or $usuario == "" or $contrasena == "" or $email == "")
	{
		$mensaje = sprintf ("Hay algún campo vacío");
	}
	else
	{
		include '../includes/config.php';
		
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
    	<?php if ($mensaje) { ?>
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