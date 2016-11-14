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


session_start ();


if ($_POST)
{
	$usuario = $_POST['usuario'];
	$contrasena = $_POST['contrasena'];
	
	include '../includes/config.php';

	global $db;
	
	$sql = sprintf ("INSERT INTO usuarios VALUES (NULL,'$nombre','$usuario', md5('$contrasena'), '$email')");
	
	$sql = sprintf ("SELECT id FROM usuarios WHERE usuario = '%s' and contrasena =  '%s'", mysql_real_escape_string ($username), mysql_real_escape_string (md5 ($contrasena)));
	
	$res = $db->query ($sql);
	
	if (! $res)
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