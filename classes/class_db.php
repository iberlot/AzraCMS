<?php
/**
 * Esta clase se va a encargar de todo lo referente a las bases de datos.
 * Recuperacion e insercion de datos, conexiones ect.
 *
 * @author iberlot <@> ivanberlot@gmail.com
 *        
 * @version 3.1
 *          (A partir de la version 3.0 - Se actualizaron las funciones obsoletas y corrigieron algunos errores.)
 *          (A partir de la version 3.1 - Se incluye la opcion de parametrizar las consultas.)
 *         
 * @package clase DB
 * @category Edicion
 *          
 *          
 * @link config/includes.php - Archivo con todos los includes del sistema
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
 * totalHorasPerdidasAqui = 172
 *
 */

class class_db
{
	/**
	 * Muestra por pantalla diferentes codigos para facilitar el debug
	 *
	 * @var bool
	 */
	public $debug = false;
	
	/**
	 * Graba log con los errores de BD *
	 */
	public $grabarArchivoLogError = false;
	
	/**
	 * Graba log con todas las consultas realizadas *
	 */
	public $grabarArchivoLogQuery = false;
	
	/**
	 * Imprime cuando hay errores sql *
	 */
	public $mostrarErrores = true;
	
	/**
	 * Usar die() si hay un error de sql.
	 * Esto es util para etapa de desarrollo *
	 */
	public $dieOnError = false;
	
	/**
	 * Setear un email para enviar email cuando hay errores sql *
	 */
	public $emailAvisoErrorSql;

	/**
	 * Parametros basicos necesarios para el funcionamiento de la clase
	 *
	 * @param mixed $host
	 *        	Ip o nombre del servidor al que se va a conectar
	 * @param mixed $user
	 *        	Usuario de conexion a la base
	 * @param mixed $pass
	 *        	Contraseña de conexion a la base
	 * @param mixed $db        	
	 * @param mixed $charset
	 *        	Juego de caracteres de la conexion
	 * @param mixed $dbtype
	 *        	El tipo de DB (mysql, oracle o mssql)
	 */
	public function __construct($host, $user, $pass, $db, $charset = 'utf8', $dbtype = 'mysql')
	{
		$this->dbtype = $dbtype;
		$this->dbHost = $host;
		$this->dbUser = $user;
		$this->dbPass = $pass;
		$this->dbName = $db;
		$this->charset = $charset;
	}

	/**
	 * Realiza la coneccion a la base de datos
	 * cambia la coneccion dependiendo de $dbtype
	 */
	public function connect()
	{
		if ($this->dbtype == 'mysql')
		{
			$this->con = mysqli_connect ($this->dbHost, $this->dbUser, $this->dbPass) or die (mysqli_error ($this->con));
			mysqli_select_db ($this->con, $this->dbName) or die (mysqli_error ($this->con));
			// mysqli_set_charset ($this->con, $this->charset) or die (mysqli_error ($this->con));
		}
		elseif ($this->dbtype == 'oracle')
		{
			// Conectar al servicio XE (es deicr, la base de datos) en la maquina "localhost"
			
			$this->con = oci_connect ($this->dbUser, $this->dbPass, $this->dbHost, $this->charset);
			
			if (! $this->con)
			{
				$e = oci_error ();
				trigger_error (htmlentities ($e['message'], ENT_QUOTES), E_USER_ERROR);
			}
		}
		elseif ($this->dbtype == 'mssql')
		{
			/**
			 * Creamos la conexion con la base de datos SQLServer
			 */
			/*
			 * Esta coneccion esta obsoleta y hay que modificarla
			 */
			
			// Connect to MSSQL
			$this->con = mssql_connect ($this->dbHost, $this->dbUser, $this->dbPass);
			
			if (! $this->con)
			{
				die ('Algo fue mal mientras se conectaba a MSSQL');
			}
			else
			{
				mssql_select_db ($this->dbName, $this->con);
			}
		}
	}

	/**
	 * Funcion que devuelve el codigo de error de la consulta
	 *
	 * @return string Con el codigo del error
	 */
	public function errorNro()
	{
		// Grabamos el codigo de error en una variable
		if ($this->dbtype == 'mysql')
		{
			return mysqli_errno ($this->con);
		}
		elseif ($this->dbtype == 'oracle')
		{
			$e = oci_error ($this->con);
			return htmlentities ($e['code']);
		}
		elseif ($this->dbtype == 'mssql')
		{
			return "666";
		}
	}

	/**
	 * Funcion que devuelve el texto del error de la consulta
	 *
	 * @return string Con el texto del error
	 */
	public function error()
	{
		// Grabamos el codigo de error en una variable
		if ($this->dbtype == 'mysql')
		{
			return mysqli_error ($this->con);
		}
		elseif ($this->dbtype == 'oracle')
		{
			$e = oci_error ($this->con);
			return htmlentities ($e['message']);
		}
		elseif ($this->dbtype == 'mssql')
		{
			return mssql_get_last_message ();
		}
	}

	/**
	 *
	 * @todo Funcion que se encarga de ejecutar las cunsultas SELECT
	 *      
	 * @todo A tener en cuenta, por el momento se recomienda no usar texto entre comillas
	 *       con el simbolo dos puntos ( : ) dentro de la consulta, por lo menos dentro de las consultas parametrizadas.
	 *      
	 * @param string $str_query
	 *        	codigo de la query a ejecutar
	 * @param bool $esParam
	 *        	Define si la consulta va a ser parametrizada o no. (por defecto false)
	 * @param array $parametros
	 *        	Array con los parametros a pasar.
	 *        	
	 * @return unknown
	 */
	public function query($str_query, $esParam = false, $parametros = "")
	{
		global $debug, $debugsql, $sitio;
		
		/**
		 *
		 * @var mixed $result Consulata a la base de datos ya compilada
		 */
		$result = "";
		
		if ($this->dbtype == 'mysql')
		{
			
			$result = mysqli_query ($this->con, $str_query);
		}
		elseif ($this->dbtype == 'oracle')
		{ // Recuperamos los datos del estado del requerimiento
			$result = oci_parse ($this->con, $str_query);
			
			if ($esParam == true)
			{
				$cantidad = substr_count ($str_query, ':');
				
				$para = explode (':', $str_query);
				
				for($i = 0; $i < $cantidad; $i ++)
				{
					$e = $i + 1;
					
					$paraY = explode (' ', $para[$e]);


					$paraY[0] = str_replace (")", "", $paraY[0]);
					$paraY[0] = str_replace (";", "", $paraY[0]);
					$paraY[0] = trim (str_replace (",", "", $paraY[0]));
					
					oci_bind_by_name ($result, ":$paraY[0]", $parametros[$i]);
				}
			}
			
			oci_execute ($result);
		}
		elseif ($this->dbtype == 'mssql')
		{
			// preguntamos si ese ususario ya esta registrado en la tabla
			$result = mssql_query ($str_query, $this->con);
		}
		
		// Empezamos el debug de la consulta
		if ($this->debug)
		{
			echo "<div style='background-color:#E8E8FF; padding:10px; margin:10px; font-family:Arial; font-size:11px; border:1px solid blue'>";
			echo $this->format_query_imprimir ($str_query);
			
			if ($esParam == true)
			{
				echo "<Br /><Br />";
				
				if ($this->dbtype == 'mysql')
				{
				}
				elseif ($this->dbtype == 'oracle')
				{
					
					$para = explode (':', $str_query);
					
					for($i = 0; $i < $cantidad; $i ++)
					{
						$e = $i + 1;
						
						$paraY = explode (' ', $para[$e]);
						
						$paraY[0] = trim (str_replace (",", "", $paraY[0]));
						
						echo ":" . $paraY[0] . " = " . $parametros[$i] . "<Br />";
					}
				}
			}
			
			echo "</div>";
		}
		
		if (isset ($this->debugsql))
		{
			consola ($str_query);
		}
		
		if ($this->grabarArchivoLogQuery)
		{
			$str_log = date ("d/m/Y H:i:s") . " " . getenv ("REQUEST_URI") . "\n";
			$str_log .= $str_query;
			$str_log .= "\n------------------------------------------------------\n";
			error_log ($str_log);
		}
		
		$errorNo = $this->errorNro ();
		if ($errorNo != 0 and $errorNo != 1062)
		{ // el error 1062 es "Duplicate entry"
			if ($this->mostrarErrores)
			{
				echo "<div style='background-color:#FFECEC; padding:10px; margin:10px; font-family:Arial; font-size:11px; border:1px solid red'>";
				echo "<B>Error:</B> " . $this->error () . "<br><br>";
				echo "<B>P&aacute;gina:</B> " . getenv ("REQUEST_URI") . "<br>";
				echo "<br>" . $this->format_query_imprimir ($str_query);
				echo "</div>";
			}
			else
			{
				echo "DB Error";
			}
			if ($this->dieOnError)
			{
				die ("class_db die()");
			}
			
			if ($this->grabarArchivoLogError)
			{
				$str_log = "******************* ERROR ****************************\n";
				$str_log .= date ("d/m/Y H:i:s") . " " . getenv ("REQUEST_URI") . "\n";
				$str_log .= "IP del visitante: " . getenv ("REMOTE_ADDR") . "\n";
				$str_log .= "Error: " . $this->error () . "\n";
				$str_log .= $str_query;
				$str_log .= "\n------------------------------------------------------\n";
				error_log ($str_log);
			}
			
			// envio de aviso de error
			if ($this->emailAvisoErrorSql != "")
			{
				@mail ($this->emailAvisoErrorSql, "Error MySQL", "Error: " . $this->error () . "\n\nP&aacute;gina:" . getenv ("REQUEST_URI") . "\n\nIP del visitante:" . getenv ("REMOTE_ADDR") . "\n\nQuery:" . $str_query);
			}
		}
		
		return $result;
	}

	/**
	 * Devuelve el fetch_assoc de una consulta dada
	 *
	 * @param mixed $result
	 *        	consulta de la cual devolver el fetch_assoc
	 * @param string $limpiarEntidadesHTML
	 *        	true/false
	 * @return Devuelve el fetch_assoc de $result
	 */
	public function fetch_assoc($result, $limpiarEntidadesHTML = false)
	{
		if ($this->dbtype == 'mysql')
		{
			if ($limpiarEntidadesHTML)
			{
				return limpiarEntidadesHTML (mysqli_fetch_assoc ($result));
			}
			else
			{
				return mysqli_fetch_assoc ($result);
			}
		}
		elseif ($this->dbtype == 'oracle')
		{
			if ($limpiarEntidadesHTML)
			{
				return limpiarEntidadesHTML (oci_fetch_assoc ($result));
			}
			else
			{
				return oci_fetch_assoc ($result);
			}
		}
		elseif ($this->dbtype == 'mssql')
		{
			if ($limpiarEntidadesHTML)
			{
				return limpiarEntidadesHTML (mssql_fetch_assoc ($result));
			}
			else
			{
				return mssql_fetch_assoc ($result);
			}
		}
	}

	/**
	 * Devuelve el fetch_array de una consulta dada
	 *
	 * @param mixed $result
	 *        	consulta de la cual devolver el fetch_array
	 * @param string $limpiarEntidadesHTML
	 *        	true/false
	 * @return Devuelve el fetch_array de $result
	 */
	public function fetch_array($result, $limpiarEntidadesHTML = false)
	{
		if ($this->dbtype == 'mysql')
		{
			if ($limpiarEntidadesHTML)
			{
				return limpiarEntidadesHTML (mysqli_fetch_array ($result));
			}
			else
			{
				return mysqli_fetch_array ($result);
			}
		}
		elseif ($this->dbtype == 'oracle')
		{
			if ($limpiarEntidadesHTML)
			{
				return limpiarEntidadesHTML (oci_fetch_array ($result));
			}
			else
			{
				return oci_fetch_array ($result);
			}
		}
		elseif ($this->dbtype == 'mssql')
		{
			if ($limpiarEntidadesHTML)
			{
				return limpiarEntidadesHTML (mssql_fetch_array ($result));
			}
			else
			{
				return mssql_fetch_array ($result);
			}
		}
	}

	/**
	 * Devuelve el fetch_object de una consulta dada
	 *
	 * @param mixed $result
	 *        	consulta de la cual devolver el fetch_object
	 * @return Devuelve el fetch_object de $result
	 */
	public function fetch_object($result)
	{
		if ($this->dbtype == 'mysql')
		{
			return mysqli_fetch_object ($result);
		}
		elseif ($this->dbtype == 'oracle')
		{
			return oci_fetch_object ($result);
		}
		elseif ($this->dbtype == 'mssql')
		{
			return mssql_fetch_object ($result);
		}
	}

	/**
	 * Devuelve la cantidad de filas de la consulta
	 *
	 * @param mixed $result
	 *        	consulta de la cual devolver el num_rows
	 */
	public function num_rows($result)
	{
		if ($this->dbtype == 'mysql')
		{
			return mysqli_num_rows ($result);
		}
		elseif ($this->dbtype == 'oracle')
		{
			return oci_fetch_all ($result, $res);
		}
		elseif ($this->dbtype == 'mssql')
		{
			return mssql_num_rows ($result);
		}
	}

	/**
	 * Devuelve la cantidad de campos de la consulta
	 *
	 * @param mixed $result
	 *        	consulta de la cual devolver el num_fields
	 */
	public function num_fields($result)
	{
		if ($this->dbtype == 'mysql')
		{
			return mysqli_num_fields ($result);
		}
		elseif ($this->dbtype == 'oracle')
		{
			return oci_num_fields ($result);
		}
		elseif ($this->dbtype == 'mssql')
		{
			return mssql_num_fields ($result);
		}
	}

	/**
	 * Devuelve el número de registros afectado por la última sentencia SQL de escritura
	 */
	public function affected_rows()
	{
		if ($this->dbtype == 'mysql')
		{
			return mysqli_affected_rows ($this->con);
		}
		elseif ($this->dbtype == 'oracle')
		{
			return oci_num_rows ($this->con);
		}
		elseif ($this->dbtype == 'mssql')
		{
			return mssql_rows_affected ($this->con);
		}
	}

	/**
	 * Obtiene el ultimo (o mayor) valor de id de una tabla determinada
	 * en caso de tratarse de MySQL la ultima tabla con campo autoIncremental
	 *
	 * @param string $campoId
	 *        	Nombre del campo id a utilizar
	 * @param string $tabla
	 *        	Tabla de la que obtener el id
	 * @return int Valor maximo del campo id
	 */
	public function insert_id($campoId, $tabla)
	{
		if ($this->dbtype == 'mysql')
		{
			return mysqli_insert_id ($this->con);
		}
		else
		{
			$sql = 'SELECT MAX(' . $campoId . ') ID FROM ' . $tabla;
			
			$result = $this->query ($sql);
			
			$id = $this->fetch_array ($result);
			
			return $id['ID'];
		}
	}

	/**
	 * Cierra las conecciones a la base de datos
	 */
	public function close()
	{
		if ($this->dbtype == 'mysql')
		{
			return mysqli_close ($this->con);
		}
		elseif ($this->dbtype == 'oracle')
		{
			return oci_close ($this->con);
		}
		elseif ($this->dbtype == 'mssql')
		{
			return mssql_close ($this->con);
		}
	}

	/**
	 * Escapa los caracteres especiales de una cadena para usarla en una sentencia SQL,
	 * tomando en cuenta el conjunto de caracteres actual de la conexion
	 *
	 * @param string $string
	 *        	Cadena a ecapar
	 */
	public function real_escape_string($string)
	{
		// print_r($this->con." - ".$string);
		// return mysqli_real_escape_string ($this->con, $string);
		return addslashes ($string);
		
		// exit ("db".$string);
	}

	/**
	 * Formatea una query para su visualizacion por pantalla
	 *
	 * @param mixed $str_query
	 *        	La query a tratar
	 * @return mixed La query formateada para su vista en la web
	 */
	private function format_query_imprimir($str_query)
	{
		$str_query_debug = nl2br (htmlentities ($str_query));
		$str_query_debug = str_replace ("SELECT", "<span style='color:green;font-weight:bold;'>SELECT</span>", $str_query_debug);
		$str_query_debug = str_replace ("INSERT", "<span style='color:#660000;font-weight:bold;'>INSERT</span>", $str_query_debug);
		$str_query_debug = str_replace ("UPDATE", "<span style='color:#FF6600;font-weight:bold;'>UPDATE</span>", $str_query_debug);
		$str_query_debug = str_replace ("REPLACE", "<span style='color:#FF6600;font-weight:bold;'>UPDATE</span>", $str_query_debug);
		$str_query_debug = str_replace ("DELETE", "<span style='color:#CC0000;font-weight:bold;'>DELETE</span>", $str_query_debug);
		$str_query_debug = str_replace ("FROM", "<br/><B>FROM</B>", $str_query_debug);
		$str_query_debug = str_replace ("WHERE", "<br/><B>WHERE</B>", $str_query_debug);
		$str_query_debug = str_replace ("ORDER BY", "<br/><B>ORDER BY</B>", $str_query_debug);
		$str_query_debug = str_replace ("GROUP BY", "<br/><B>GROUP BY</B>", $str_query_debug);
		$str_query_debug = str_replace ("INTO", "<br/><B>INTO</B>", $str_query_debug);
		$str_query_debug = str_replace ("VALUES", "<br/><B>VALUES</B>", $str_query_debug);
		return $str_query_debug;
	}

	/**
	 * Obtiene el valor de un campo de una tabla.
	 * Si no obtiene una sola fila retorna FALSE
	 *
	 * @param string $table
	 *        	Tabla
	 * @param string $field
	 *        	Campo
	 * @param string $id
	 *        	Valor para seleccionar con el campo clave
	 * @param string $fieldId
	 *        	Campo clave de la tabla
	 * @return string o false
	 */
	public function getValue($table, $field, $id, $fieldId = "id")
	{
		$sql = "SELECT $field FROM $table WHERE $fieldId='$id'";
		$result = query ($sql);
		
		if ($result and num_rows ($result) == 1)
		{
			if ($fila = fetch_assoc ($result))
			{
				if ($this->dbtype == 'oracle')
				{
					return $fila[strtoupper ($field)];
				}
				else
				{
					return $fila[$field];
				}
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Obtiene una fila de una tabla.
	 * Si no obtiene una sola fila retorna FALSE
	 *
	 * @param string $table
	 *        	Tabla
	 * @param string $id
	 *        	Valor para seleccionar con el campo clave
	 * @param string $fieldId
	 *        	Campo clave de la tabla
	 * @return array mysqli_fetch_assoc o false
	 */
	public function getRow($table, $id, $fieldId = "id", $limpiarEntidadesHTML = false)
	{
		$sql = "SELECT * FROM $table WHERE $fieldId='$id'";
		$result = query ($sql);
		
		if ($result and num_rows ($result) == 1)
		{
			if ($limpiarEntidadesHTML)
			{
				return limpiarEntidadesHTML (fetch_array ($result));
			}
			else
			{
				return fetch_array ($result);
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Retorna un array con el arbol jerarquico a partir del nodo indicado (0 si es el root)
	 * Esta funcion es para ser usada en tablas con este formato de campos: id, valor, idPadre
	 *
	 * @param string $tabla
	 *        	Nombre de la tabla
	 * @param string $campoId
	 *        	Nombre del campo que es id de la tabla
	 * @param string $campoPadreId
	 *        	Nombre del campo que es el FK sobre la misma tabla
	 * @param string $campoDato
	 *        	Nombre del campo que tiene el dato
	 * @param string $orderBy
	 *        	Para usar en ORDER BY $orderBy
	 * @param int $padreId
	 *        	El id del nodo del cual comienza a generar el arbol, o 0 si es el root
	 * @param int $nivel
	 *        	No enviar (es unicamente para recursividad)
	 * @return array Formato: array("nivel" => X, "dato" => X, "id" => X, "padreId" => X);
	 *        
	 *         Un codigo de ejemplo para hacer un arbol de categorias con links:
	 *        
	 *         for ($i=0; $i<count($arbol); $i++){
	 *         echo str_repeat("&nbsp;&nbsp;&nbsp;", $arbol[$i][nivel])."<a href='admin_categorias.php?c=".$arbol[$i][id]."'>".$arbol[$i][dato]."</a><br/>";
	 *         }
	 */
	public function getArbol($tabla, $campoId, $campoPadreId, $campoDato, $orderBy, $padreId = 0, $nivel = 0)
	{
		$tabla = real_escape_string ($tabla);
		$campoId = real_escape_string ($campoId);
		$campoPadreId = real_escape_string ($campoPadreId);
		$campoDato = real_escape_string ($campoDato);
		$orderBy = real_escape_string ($orderBy);
		$padreId = real_escape_string ($padreId);
		
		$result = $this->query ("SELECT * FROM $tabla WHERE $campoPadreId='$padreId' ORDER BY $orderBy");
		
		$arrayRuta = array ();
		
		while ($fila = $this->fetch_array ($result))
		{
			$arrayRuta[] = array (
					"nivel" => $nivel,
					"dato" => $fila[$campoDato],
					"id" => $fila[$campoId],
					"padreId" => $fila[$campoPadreId] 
			);
			$retArrayFunc = $this->getArbol ($tabla, $campoId, $campoPadreId, $campoDato, $orderBy, $fila[$campoId], $nivel + 1);
			$arrayRuta = array_merge ($arrayRuta, $retArrayFunc);
		}
		
		return $arrayRuta;
	}

	/**
	 * Retorna un array con la ruta tomada de un arbol jerarquico a partir del nodo indicado en $id.
	 * Ej: array("33"=>"Autos", "74"=>"Ford", "85"=>"Falcon")
	 * Esta funcion es para ser usada en tablas con este formato de campos: id, valor, idPadre
	 *
	 * @param string $tabla
	 *        	Nombre de la tabla
	 * @param string $campoId
	 *        	Nombre del campo que es id de la tabla
	 * @param string $campoPadreId
	 *        	Nombre del campo que es el FK sobre la misma tabla
	 * @param string $campoDato
	 *        	Nombre del campo que tiene el dato
	 * @param
	 *        	int El id del nodo del cual comienza a generar el path
	 * @return array Formato: array("33"=>"Autos", "74"=>"Ford", "85"=>"Falcon")
	 */
	public function getArbolRuta($tabla, $campoId, $campoPadreId, $campoDato, $id)
	{
		$tabla = real_escape_string ($tabla);
		$campoId = real_escape_string ($campoId);
		$campoPadreId = real_escape_string ($campoPadreId);
		$campoDato = real_escape_string ($campoDato);
		$id = real_escape_string ($id);
		
		if ($id == 0)
			return;
		
		$arrayRuta = array ();
		
		$result = $this->query ("SELECT $campoId, $campoDato, $campoPadreId FROM $tabla WHERE $campoId='$id'");
		
		while ($this->num_rows ($result) == 1 or $fila[$campoId] == '0')
		{
			$fila = $this->fetch_assoc ($result);
			$arrayRuta[$fila[$campoId]] = $fila[$campoDato];
			$result = $this->query ("SELECT $campoId, $campoDato, $campoPadreId FROM $tabla WHERE $campoId='" . $fila[$campoPadreId] . "'");
		}
		
		$arrayRuta = array_reverse ($arrayRuta, true);
		
		return $arrayRuta;
	}

	/**
	 * Realiza un INSERT en una tabla usando los datos que vienen por POST, donde el nombre de cada campo es igual al nombre en la tabla.
	 * Esto es especialmente util para backends, donde con solo agregar un campo al <form> ya estamos agregandolo al query automaticamente
	 *
	 * Ejemplos:
	 *
	 * Para casos como backend donde no hay que preocuparse por que el usuario altere los campos del POST se puede omitir el parametro $campos
	 * $db->insertFromPost("usuarios");
	 *
	 * Si ademas queremos agregar algo al insert
	 * $db->insertFromPost("usuarios", "", "fechaAlta=NOW()");
	 *
	 * Este es el caso mas seguro, se indican cuales son los campos que se tienen que insertar
	 * $db->insertFromPost("usuarios", array("nombre", "email"));
	 *
	 * @param string $tabla
	 *        	Nombre de la tabla en BD
	 * @param array $campos
	 *        	Campos que vienen por $_POST que queremos insertar, ej: array("nombre", "email")
	 * @param string $adicionales
	 *        	Si queremos agregar algo al insert, ej: fechaAlta=NOW()
	 * @return boolean El resultado de la funcion query
	 */
	public function insertFromPost($tabla, $campos = array(), $adicionales = "")
	{
		
		// campos de $_POST
		foreach ($_POST as $campo => $valor)
		{
			if (is_array ($campos) and count ($campos) > 0)
			{
				// solo los campos indicados
				if (in_array ($campo, $campos))
				{
					if ($camposInsert != "")
					{
						$camposInsert .= ", ";
					}
					$camposInsert .= "`$campo`='" . real_escape_string ($valor) . "'";
				}
			}
			else
			{
				// van todos los campos que vengan en $_POST
				if ($camposInsert != "")
				{
					$camposInsert .= ", ";
				}
				$camposInsert .= "`$campo`='" . real_escape_string ($valor) . "'";
			}
		}
		
		// campos adicionales
		if ($adicionales != "")
		{
			if ($camposInsert != "")
			{
				$camposInsert .= ", ";
			}
			$camposInsert .= $adicionales;
		}
		
		return $this->query ("INSERT INTO $tabla SET $camposInsert");
	}

	/**
	 * Realiza un UPDATE en una tabla usando los datos que vienen por POST, donde el nombre de cada campo es igual al nombre en la tabla.
	 * Esto es especialmente util para backends, donde con solo agregar un campo al <form> ya estamos agregandolo al query automaticamente
	 *
	 * Ejemplos:
	 *
	 * Para casos como backend donde no hay que preocuparse por que el usuario altere los campos del POST se puede omitir el parametro $campos
	 * $db->updateFromPost("usuarios");
	 *
	 * Si ademas queremos agregar algo al update
	 * $db->updateFromPost("usuarios", "", "fechaModificacion=NOW()");
	 *
	 * Este es el caso mas seguro, se indican cuales son los campos que se tienen que insertar
	 * $db->updateFromPost("usuarios", array("nombre", "email"));
	 *
	 * @param string $tabla
	 *        	Nombre de la tabla en BD
	 * @param string $where
	 *        	Condiciones para el WHERE. Ej: id=2. Tambien puede agregarse un LIMIT para los casos donde solo se necesita actualizar un solo registro. Ej: id=3 LIMIT 1. El limit en este caso es por seguridad
	 * @param array $campos
	 *        	Campos que vienen por $_POST que queremos insertar, ej: array("nombre", "email")
	 * @param string $adicionales
	 *        	Si queremos agregar algo al insert, ej: fechaAlta=NOW()
	 * @return boolean El resultado de la funcion query
	 */
	public function updateFromPost($tabla, $where, $campos = array(), $adicionales = "")
	{
		
		// campos de $_POST
		foreach ($_POST as $campo => $valor)
		{
			if (is_array ($campos) and count ($campos) > 0)
			{
				// solo los campos indicados
				if (in_array ($campo, $campos))
				{
					if ($camposInsert != "")
						$camposInsert .= ", ";
					$camposInsert .= "`$campo`='" . real_escape_string ($valor) . "'";
				}
			}
			else
			{
				// van todos los campos que vengan en $_POST
				if ($camposInsert != "")
					$camposInsert .= ", ";
				$camposInsert .= "`$campo`='" . real_escape_string ($valor) . "'";
			}
		}
		
		// campos adicionales
		if ($adicionales != "")
		{
			if ($camposInsert != "")
				$camposInsert .= ", ";
			$camposInsert .= $adicionales;
		}
		
		return $this->query ("UPDATE $tabla SET $camposInsert WHERE $where");
	}

	/**
	 * Devuelve el valor de un campo de la fila obtenida
	 *
	 * @param unknown $result        	
	 * @param unknown $row        	
	 * @param string $field        	
	 */
	public function result($result, $row, $field = null)
	{
		if ($this->dbtype == 'mysql')
		{
			return mysqli_result ($result, $row, $field);
		}
		elseif ($this->dbtype == 'oracle')
		{
			return oci_result ($result, $field);
		}
		elseif ($this->dbtype == 'mssql')
		{
			return mssql_result ($result, $row, $field);
		}
	}

	public function data_seek($result, $row_number)
	{
		if ($this->dbtype == 'mysql')
		{
			return mysqli_data_seek ($result, $row_number);
		}
		elseif ($this->dbtype == 'mssql')
		{
			return mssql_data_seek ($result, $row_number);
		}
	}
}
?>