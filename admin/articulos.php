<?php

/**
 * 
 * @author iberlot <@> ivanberlot@gmail.com
 * @todo FechaC 10/11/2016 - Lenguaje PHP 
 * 
 * @todo Administrador de articulos
 * @version 0.1	- Version de inicio 
 * @package CMS
 * @category 
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

require_once 'cabecera.php';

// Llamamos a la variable global que maneja la base de datos
global $db;


$post = iniVarForm('id', 'GET');
$post = $db->real_escape_string ($post);

if ($post > 0)
{
	$sql = sprintf("SELECT * FROM articulos WHERE id = '%s'", $post);
	
	$res = $db->query ($sql);
	
	if (!$res)
	{
		die ('Invalid query: ' . $db->error ());
	}
	
	$fila = $db->fetch_array ($res);
	
}

?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script src="text/wysiwyg.js"></script>
<script src="text/articulos.js"></script>
<script>
	var wysiwyg = new Wysiwyg;
	wysiwyg.el.insertBefore('.area');
</script>
<link rel="stylesheet" href="text/wysiwyg.css" type="text/css" charset="utf-8">



<h2>Escribir un articulo</h2>
<form>
	<div class="escritura">
		<label for="titulo">Titulo</label>
		<input type="text" value="<?php if ($post > 0) {echo $res['titulo'];} ?>" name="titulo">
		<input type="hidden" value="<?php echo $id; ?>" name="autor">
		<input type="hidden" value="<?php echo $post; ?>" name="post">
		<div class="area" contenteditable><?php if ($post > 0) {echo $res['contenido'];} ?></div>
		<?php if ($post > 0) { ?>
		<div class="actualizar">Actualizar</div>
		<?php } else { ?>
		<div class="publicar">Publicar</div>
		<?php } ?>
	</div>
</form>

