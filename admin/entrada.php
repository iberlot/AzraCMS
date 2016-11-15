<?php

/**
 *
 * @author iberlot <@> ivanberlot@gmail.com
 * @todo FechaC 10/11/2016 - Lenguaje PHP
 *      
 * @todo Modulo de acceso a la seccion de admin
 *
 * @version 0.1 - Version de inicio
 * @package CMS/admin
 * @category Login
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

include '../includes/config.php';
include '../includes/funciones.php';
include '../includes/variables.php';

// Llamamos a la variable global que maneja la base de datos
global $db;

session_start ();

if ($_POST)
{
	$usuario = iniVarForm ('usuario');
	$contrasena = iniVarForm ('contrasena');
	
	if ($usuario == "" or $contrasena == "" )
	{
		$mensaje = sprintf ("Hay algún campo vacío");
	}
	else
	{
		$usuario = $db->real_escape_string ($usuario);
		$contrasena = $db->real_escape_string ($contrasena);
		
		$sql = sprintf ("SELECT id FROM usuarios WHERE usuario = '%s' and contrasena =  '%s'", $usuario, md5 ($contrasena));
		
		$res = $db->query ($sql);
		
		if (!$res)
		{
			die ('Invalid query: ' . $db->error ());
		}
		
		list ($count) = $db->fetch_array ($res);
		
		if (!$count)
		{
			$mensaje = sprintf ("Usuario o contraseña equivocados");
		}
		else
		{
			$_SESSION['entrado'] = true;
			$_SESSION['id'] = $count;
			header ('Location:index.php');
		}
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
    	<form method="post" action="entrada.php">
			<label>Nombre de usuario: </label>
			<input type="text" name="usuario" value="<?php echo $usuario ?>">
			<br>
			<label>Contraseña </label>
			<input type="password" name="contrasena">
			<br>
			<div class="submit">
				<input type="submit" value="Entrar">
			</div>
		</form>
	</div>
</body>
</html>