<?php

/**
 * Clase que genera automaticamente un listado y los formularios que modifican o agregan datos en una tabla de BD
 *
 * @uses class_paginado.php, class_orderby.php, class_db.php
 * @author Andres Carizza www.andrescarizza.com.ar
 * @author iberlot <@> ivanberlot@gmail.com
 *        
 * @version 3.92
 *         
 *          (A partir de la version 3.0 cambia la forma de aplicar los estilos css)
 *          (A partir de la version 3.4 cambia de usar MooTools a usar JQuery)
 *          (A partir de la version 3.5.8 cambia a UTF-8)
 *          (A partir de la version 3.9 Se actualizaron las funciones obsoletas)
 *         
 *         
 *          Datos para array de campos: (ver ejemplo de uso mas abajo)
 *         
 *          campo = nombre del campo en la bd
 *          tipo = tipo de elemento de formulario (texto, bit, textarea, combo, dbCombo, password, upload, moneda, numero rownum) Recordar que tiene que respetar mayusculas y minusculas.
 *          tipoBuscar = lo mismo que tipo pero solo para el formulario de busqueda
 *          titulo = texto para el campo en los formularios y listado
 *          tituloListado = si esta seteado usa este titulo en el listado
 *          tituloBuscar = si esta seteado usa este titulo en el formulario de busqueda
 *          maxLen = maximo de caracteres que permite ingresar el input del formulario
 *          requerido = el campo es requerido
 *          datos = Array("key" => "value"...) para el tipo de campo "combo"
 *          formItem = una funcion de usuario que reciba el parametro $fila. Es para poner un campo especial en el formulario de alta y modificacion para ese campo en particular. Esto es util por ejemplo para poner un editor WUSIWUG.
 *          colorearValores = Colorea el texto de esta columna en el listado segun el valor. Ej: Array("Hombre" => "blue", "Mujer" => "#FF00AE")
 *          colorearConEtiqueta = boolean. Agrega el class "label" cuando colorea un valor. Por defecto es FALSE
 *          customJoin = JOIN a agregar a la consulta
 *         
 *          textoBitTrue = Texto que pone cuando el tipo de campo es "bit" y este es true o =1. Si se deja vacio usa el por defecto definido en $this->textoBitTrue
 *          textoBitFalse = Texto que pone cuando el tipo de campo es "bit" y este es false o =0. Si se deja vacio usa el por defecto definido en $this->textoBitFalse
 *          ordenInversoBit = Boolean. Si esta en true muestra primero el false en los <select>. Por defecto es false
 *         
 *          uploadFunction = Funcion de usuario que se encarga del archivo subido. Recibe los parametros id y tabla. Debe retornar TRUE si la subida se realizo con exito.
 *          borrarSiUploadFalla = Boolean. Para el tipo de campo upload. Si falla el upload borra el registro recien creado. Por defecto es FALSE. No tiene efecto en el update.
 *         
 *          buscar = boolean. Si esta en true permite buscar por ese campo. No funciona si se usa la funcion generarAbm() con un query. (NOTA: el buscador funciona verificando variables de $_REQUEST con los nombres de los campos con prefijo "c_". Si se quisiera hacer un formulario de busqueda independiente sin usar el de la class se puede hacer usando los mismos nombres de los campos, o sea con el prefijo "c_".)
 *          buscarOperador = Operador que usa en el where. Ej. = , LIKE
 *          buscarUsarCampo = Si esta seteado usa ese campo en el where para buscar
 *          customFuncionBuscar = Funcion del usuario para poner un HTML especial en el lugar donde iria el form item del formulario de busqueda. La funcion no recibe ningun parametro.
 *         
 *          sqlQuery = para el tipo de campo dbCombo
 *          campoValor = (obligatorio para el tipo de campo dbCombo): campo de la tabla izquierda. Es el que tiene el valor que va en <option value='{ac&aacute;}'>
 *          campoTexto = (obligatorio para el tipo de campo dbCombo): campo de la tabla izquierda que tiene el texto que se muestra en el listado y que va en <option value=''>{ac&aacute;}</option>
 *          joinTable = (obligatorio para el tipo de campo dbCombo): tabla para hacer join en el listado (es la misma tabla de sqlQuery)
 *          joinCondition = (obligatorio para el tipo de campo dbCombo): Ej. INNER, LEFT. para hacer join en el listado. Si se deja vacio por defecto es INNER
 *         
 *          campoOrder = campo que usa para hacer el order by al cliquear el titulo de la columna, esto es ideal para cuando se usa un query en la funcion generarAbm()
 *          valorPredefinido = valor predefinido para un campo en el formulario de alta
 *          incluirOpcionVacia = para los tipo "combo" o "dbCombo", si esta en True incluye <option value=''></option>
 *          adicionalInput = para agregar html dentro de los tags del input. <input type='text' {ac&aacute;}>
 *         
 *          centrarColumna = centrar los datos de la columna en el listado
 *          anchoColumna = permite especificar un ancho a esa columna en el listado (ej: 80px)
 *         
 *          noEditar = no permite editar el campo en el formulario de edicion
 *          noListar = no mostrar el campo en el listado
 *          noNuevo = no incuye ni muestra ese campo en el formulario de alta
 *          noMostrarEditar = no muestra el campo en el formulario de edicion
 *          noOrdenar = no permite ordenar por ese campo haciendo click en el titulo de la columna
 *         
 *         
 *          customPrintListado = sprintf para imprimir en el listado. %s ser&aacute; el valor del campo y {id} se remplaza por el Id del registro definido para la tabla. Ej: <a href='ver_usuario.php?id={id}' target='_blank' title='Ver usuario'>%s</a>
 *          customEvalListado = para ejecutar PHP en cada celda del listado sin imprimir ni siquiera los tags <td></td>. Las variables utilizables son $id y $valor. Ej: echo "<td align='center'>"; if($valor=="admin"){echo "Si";}else{echo "No";}; echo "</td>";
 *          customFuncionListado = para ejecutar una funcion del usuario en cada celda del listado sin imprimir ni siquiera los tags <td></td>. La funcion debe recibir el parametro $fila que contendra todos los datos de la fila
 *          customFuncionValor = para ejecutar una funcion del usuario en el valor antes de usarlo para el query sql en las funciones de INSERT Y UPDATE. La funcion debe recibir el parametro $valor y retornar el nuevo valor
 *         
 *          exportar = Boolean. Incluye ese campo en la exportacion. Si al menos uno de los campos lo incluye entonces aparecen los iconos de exportar. ATENCION: Leer referencia de la funcion exportar_verificar()
 *          separador = String con el texto para mostrar en el separador. El separador aparece en los formularios de edicion y alta. Es un TH colspan='2' para separar la informacion visualmente.
 *          [campoTexto]
 *          [centrarColumna]
 *          [customEvalListado]
 *          [customPrintListado]
 *          [incluirOpcionVacia]
 *          [joinCondition]
 *          [joinTable]
 *          [noEditar]
 *          [noListar]
 *          [noNuevo]
 *          [noOrdenar]
 *          [separador]
 *          [sqlQuery]
 *          [valorPredefinido]
 *          $parametroUsr parametro a pasarle de forma manual a customEvalListado
 *         
 *         
 *          Ejemplo de uso:
 *         
 *          $abm = new class_abm();
 *          $abm->tabla = "usuarios";
 *          $abm->registros_por_pagina = 40;
 *          $abm->textoTituloFormularioAgregar = "Agregar usuario";
 *          $abm->textoTituloFormularioEdicion = "Editar usuario";
 *          $abm->campos = array(
 *          array (
 *          'campo' => 'ROWNUM',
 *          'tipo' => 'rownum',
 *          'exportar' => true,
 *          'titulo' => 'Nro',
 *          'noEditar' => true,
 *          'noNuevo' => true
 *          ),
 *          array("campo" => "usuario",
 *          "tipo" => "texto",
 *          "titulo" => "Usuario",
 *          "maxLen" => 30,
 *          "customPrintListado" => "<a href='ver_usuario.php?id={id}' target='_blank' title='Ver usuario'>%s</a>",
 *          "buscar" => true
 *          ),
 *          array("campo" => "pass",
 *          "tipo" => "texto",
 *          "titulo" => "Contraseña",
 *          "maxLen" => 30
 *          ),
 *          array("campo" => "activo",
 *          "tipo" => "bit",
 *          "titulo" => "Activo",
 *          "centrarColumna" => true,
 *          "valorPredefinido" => "0"
 *          ),
 *          array("campo" => "nivel",
 *          "tipo" => "combo",
 *          "titulo" => "Admin",
 *          "datos" => array("admin"=>"Si", ""=>"No"),
 *          "customEvalListado" => 'echo "<td align=\"center\">"; if($valor=="admin"){echo "Si";}else{echo "No";}; echo "</td>";'
 *          ),
 *          array("campo" => "paisId",
 *          "tipo" => "dbCombo",
 *          "sqlQuery" => "SELECT * FROM paises ORDER BY pais",
 *          "campoValor" => "id",
 *          "campoTexto" => "pais",
 *          "joinTable" => "paises",
 *          "joinCondition" => "LEFT",
 *          "titulo" => "Pais",
 *          "incluirOpcionVacia" => true
 *          ),
 *          array("campo" => "email",
 *          "tipo" => "textarea",
 *          "titulo" => "Email",
 *          "maxLen" => 70,
 *          "noOrdenar" => true
 *          ),
 *          array("separador" => "Un separador"
 *          ),
 *          array("campo" => "donde",
 *          "tipo" => "combo",
 *          "titulo" => "Donde nos conociste?",
 *          "tituloListado" => "Donde",
 *          "datos" => array("google"=>"Por Google", "amigo"=>"Por un amigo", "publicidad"=>"Por una publicidad", "otro"=>"Otro"),
 *          "colorearValores" => array('google'=>'#4990D7', 'amigo'=>'#EA91EA'),
 *          ),
 *          array (
 *          'campo' => 'GASTOS',
 *          'tipo' => 'moneda',
 *          'exportar' => true,
 *          'titulo' => 'GASTOS',
 *          'noEditar' => false,
 *          'buscar' => true
 *          ),
 *          array (
 *          'campo' => 'VALOR',
 *          'tipo' => 'numero',
 *          'cantidadDecimales' => '2',
 *          'exportar' => true,
 *          'titulo' => 'VALOR',
 *          'noEditar' => false,
 *          'buscar' => true
 *          ),
 *          array("campo" => "ultimoLogin",
 *          "tipo" => "texto",
 *          "titulo" => "Ultimo login",
 *          "noEditar" => true,
 *          "noListar" => true,
 *          "noNuevo" => true
 *          )
 *          );
 *          $abm->generarAbm("", "Administrar usuarios");
 *         
 *         
 *          Ejemplo para incluir una columna adicional personalizada en el listado:
 *         
 *          array("campo" => "",
 *          "tipo" => "",
 *          "titulo" => "Fotos",
 *          "customEvalListado" => 'echo "<td align=\"center\"><a href=\"admin_productos_fotos.php?productoId=$fila[id]\"><img src=\"img/camara.png\" border=\"0\" /></a></td>";'
 *          )
 *         
 *         
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
 * totalHorasPerdidasAqui = 1025
 *
 */
class class_abm
{
	/*
	 * *******************************************************
	 * VARIABLES RELACIONADAS AL ABM *
	 * *******************************************************
	 */
	
	/**
	 *
	 * @var string Directorio donde se guardan las imagenes
	 * @example $directorioImagenes = '/img/';
	 */
	public $directorioImagenes = '/img/';
	
	/**
	 * Nombre de la tabla en BD
	 */
	public $tabla;
	
	/**
	 * Campo ID de la tabla
	 */
	public $campoId = array ();
	
	/**
	 * Permite editar el campo que corresponde al ID, Por lo general no permite editarlo pq suele ser autoincremental, por defecto ni se muestra en el abm pq al usuario no le interesa, pero se puede forzar que sea editable con este parametro
	 */
	public $campoIdEsEditable = false;
	
	/**
	 *
	 * @var string JOIN personalizado para agragar a la consulta
	 * @example 'LEFT JOIN pais ON persona.idpais=pais.idpais';
	 */
	public $customJoin = "";
	
	/**
	 * Valor del atributo method del formulario
	 */
	public $formMethod = "POST";
	
	/**
	 * Agrega el atributo autofocus al primer campo del formulario de alta o modificacion
	 */
	public $autofocus = true;
	
	/**
	 * Para poder agregar codigo HTML en la botonera del listado, antes de los iconos "Exportar" y "Agregar"
	 */
	public $agregarABotoneraListado;
	
	/**
	 * Metodo que usa para hacer los redirect "header" (si no se envio contenido antes) o "html" de lo contrario
	 */
	public $metodoRedirect = "html";
	
	/**
	 * Texto que muestra el boton submit del formulario Nuevo
	 */
	public $textoBotonSubmitNuevo = "Guardar";
	
	/**
	 * Texto que muestra el boton submit del formulario Modificar
	 */
	public $textoBotonSubmitModificar = "Guardar";
	
	/**
	 * Texto que muestra el boton de Cancelar
	 */
	public $textoBotonCancelar = "Cancelar";
	
	/**
	 * Habilitacion del boton Extra
	 */
	public $extraBtn = 'false';
	
	/**
	 * Texto del titulo del boton Extra
	 */
	public $textoBotonExtraTitulo = "";
	
	/**
	 * Texto que muestra el boton Extra
	 */
	public $textoBotonExtra = "Extra";
	
	/**
	 * Adicionales al boton Extra
	 */
	public $adicionalesExtra;
	
	/**
	 * Texto que muestra cuando la base de datos retorna registro duplicado al hacer un insert.
	 * Si se deja el string vacio entonces muestra el mensaje de error del motor de bd.
	 */
	public $textoRegistroDuplicado = "Uno de los datos est&aacute; duplicado y no puede guardarse en la base de datos";
	
	/**
	 * Para asignar una accion diferente al boton de Cancelar del formulario de Edicion y Nuevo
	 */
	public $cancelarOnClickJS = "";
	
	/**
	 * Texto para mostrar en caso de que no exista el registro
	 *
	 * @var string $textoElRegistroNoExiste
	 */
	public $textoElRegistroNoExiste = "El registro no existe. <A HREF='javascript:history.back()'>[Volver]</A>";
	
	/**
	 * Texto para mostrar en caso de que no haya registros para mostrar
	 *
	 * @var string
	 */
	public $textoNoHayRegistros = "No hay registros para mostrar";
	
	/**
	 * Texto para mostrar en caso de que la busqueda no devuelva ningun valor
	 *
	 * @var string $textoNoHayRegistrosBuscando
	 */
	public $textoNoHayRegistrosBuscando = "No hay resultados para la b&uacute;squeda";
	
	/**
	 * Titulo del formulario de edicion *
	 */
	public $textoTituloFormularioEdicion;
	
	/**
	 * Titulo del formulario de agregar *
	 */
	public $textoTituloFormularioAgregar;
	
	/**
	 * Agregado al final del JOIN para espesificar un extra en el WHERE de la consulta
	 *
	 * @var varchar
	 */
	public $customCompare = "";
	
	/**
	 * Titulo del formulario de busqueda *
	 */
	public $textoTituloFormularioBuscar = "B&uacute;squeda";
	
	/**
	 * Muestra los encabezados de las columnas en el listado *
	 */
	public $mostrarEncabezadosListado = true;
	
	/**
	 * Muestra el total de registros al final del listado *
	 */
	public $mostrarTotalRegistros = true;
	
	/**
	 * Pagina a donde se redireccionan los formularios.
	 * No setear a menos que seapas lo que estas haciendo.
	 */
	public $formAction = "";
	
	/**
	 * Cantidad de registros que se van a ver por pagina
	 *
	 * @var int
	 */
	public $registros_por_pagina = 30;
	
	/**
	 * para agregar atributos al tag *
	 */
	public $adicionalesForm;
	
	/**
	 * para agregar atributos al tag *
	 */
	public $adicionalesTable;
	
	/**
	 * para agregar atributos al tag *
	 */
	public $adicionalesTableListado;
	
	/**
	 * para agregar atributos al tag *
	 */
	public $adicionalesSubmit;
	
	/**
	 *
	 * @example AND userId=2 *
	 */
	public $adicionalesWhereUpdate;
	
	/**
	 *
	 * @example AND userId=2 *
	 */
	public $adicionalesWhereDelete;
	
	/**
	 *
	 * @example , userId=2 *
	 */
	public $adicionalesInsert;
	
	/**
	 * (aplicable siempre y cuando no sea un select custom)
	 *
	 * @example AND userId=2
	 */
	public $adicionalesSelect;
	
	/**
	 * Esto es ultil cuando se necesita traer un campo para usar durante el listado y no esta como visible
	 *
	 * @example , campo2, campo3, campo4
	 */
	public $adicionalesCamposSelect;
	
	/**
	 * Genera el query del listado usando ese string.
	 * Esto es util por ejemplo cuando se necesitan hacer sub select *
	 *
	 * @example SELECT $sqlCamposSelect FROM...
	 */
	public $sqlCamposSelect;
	
	/**
	 * Campo order by por defecto para los select
	 */
	public $orderByPorDefecto;
	
	/**
	 * Funcion que se ejecuta antes al borrar un registro.
	 * (donde borrarUsuario es una funcion que debe recibir los parametros $id y $tabla) *
	 *
	 * @example callbackFuncDelete = "borrarUsuario"
	 */
	public $callbackFuncDelete;
	
	/**
	 * Funcion que se ejecuta despues al actualizar un registro.
	 * (donde actualizarDatosUsuario es una funcion que debe recibir los parametros $id, $tabla, $fueAfectado)
	 *
	 * @example callbackFuncUpdate = "actualizarDatosUsuario"
	 */
	public $callbackFuncUpdate;
	
	/**
	 * Funcion que se ejecuta despues de insertar un registro.
	 * (donde crearCarpetaUsuario es una funcion que debe recibir los parametros $id y $tabla)
	 *
	 * @example callbackFuncInsert = "crearCarpetaUsuario"
	 */
	public $callbackFuncInsert;
	
	/**
	 * Cantidad de filas total que retorno el query de listado.
	 * NOTA: Tiene que haberse llamado antes la funcion que genera el ABM. *
	 */
	public $totalFilas;
	
	/**
	 * Para ejecutar PHP en cada tag <TR {aca}>.
	 * Esta disponible el array $fila.
	 *
	 * @example if($fila["nivel"]=="admin")echo "style='background:red'"; *
	 */
	public $evalEnTagTR;
	
	/**
	 * texto del confirm() antes de borrar (escapar las comillas dobles si se usan) *
	 */
	public $textoPreguntarBorrar = "¿Confirma que desea borrar el elemento seleccionado?";
	
	/**
	 * Muestra el boton Editar en el listado
	 */
	public $mostrarEditar = true;
	
	/**
	 * Muestra el boton Nuevo en el listado
	 */
	public $mostrarNuevo = true;
	
	/**
	 * Muestra el boton Borrar en el listado
	 */
	public $mostrarBorrar = true;
	
	/**
	 * Muestra los datos del listado
	 */
	public $mostrarListado = true;
	
	/**
	 * El titulo de la columna Editar del listado *
	 */
	public $textoEditarListado = "Editar";
	
	/**
	 * El titulo de la columna Borrar del listado *
	 */
	public $textoBorrarListado = "Borrar";
	
	/**
	 * Texto del boton submit del formulario de busqueda *
	 */
	public $textoBuscar = "Buscar";
	
	/**
	 * Texto del boton limpiar del formulario de busqueda *
	 */
	public $textoLimpiar = "Limpiar";
	
	/**
	 * La palabra (plural) que pone al lado del total del registros en el pie de la tabla del listado *
	 */
	public $textoStrRegistros = "registros";
	
	/**
	 * La palabra (singular) que pone al lado del total del registros en el pie de la tabla del listado *
	 */
	public $textoStrRegistro = "registro";
	
	/**
	 * El palabra "Total" que pone al lado del total del registros en el pie de la tabla del listado *
	 */
	public $textoStrTotal = "Total";
	
	/**
	 * Texto para el title de los links de los numeros de pagina *
	 */
	public $textoStrIrA = "Ir a la p&aacute;gina";
	
	/**
	 * Cantidad de columnas de inputs en el formulario de busqueda *
	 */
	public $columnasFormBuscar = 1;
	
	/**
	 * Redireccionar a $redireccionarDespuesInsert despues de hacer un Insert.
	 *
	 * @example archivo.php?id=%d (si el ID de la tabla no fuera un numero usar %s) *
	 */
	public $redireccionarDespuesInsert;
	
	/**
	 * Redireccionar a $redireccionarDespuesUpdate despues de hacer un Update.
	 *
	 * @example archivo.php?id=%d (si el ID de la tabla no fuera un numero usar %s) *
	 */
	public $redireccionarDespuesUpdate;
	
	/**
	 * Redireccionar a $redireccionarDespuesDelete despues de hacer un Delete.
	 *
	 * @example archivo.php?id=%d (si el ID de la tabla no fuera un numero usar %s) *
	 */
	public $redireccionarDespuesDelete;
	
	/**
	 * Icono editar del listado.
	 */
	public $iconoEditar = "<a href=\"%s\"><img src='/img/editar.gif' title='Editar' alt='Editar' border='0' /></a>";
	
	/**
	 * Icono borrar del listado.
	 */
	public $iconoBorrar = "<a href=\"javascript:void(0)\" onclick=\"%s\"><img src='/img/eliminar.gif' title='Eliminar' alt='Eliminar' border='0' /></a>";
	
	/**
	 * Icono de Agregar para crear un registro nuevo.
	 */
	public $iconoAgregar = "<input type='button' class='btnAgregar' value='Agregar' title='Atajo: ALT+A' accesskey='a' onclick='window.location=\"%s\"'/>";
	
	/**
	 * Icono de exportar a Excel.
	 */
	// public $iconoExportarExcel = "<input type='button' class='btnExcel' title='Exportar a Excel' onclick='window.location=\"%s\"'/>";
	public $iconoExportarExcel = "<input type='button' class='btnExcel' title='Exportar a Excel' onclick='javascript:window.open(\"%s\", \"_blank\")'/>";
	
	/**
	 * Icono de exportar a CSV.
	 */
	public $iconoExportarCsv = "<input type='button' class='btnCsv' title='Exportar a CSV' onclick='javascript:window.open(\"%s\", \"_blank\")'/>";
	
	/**
	 * Direccion a la que se tiene que dirigir en caso de que el formulario para agregar un nuevo registro no sea el standar
	 *
	 * @var String
	 */
	public $direNuevo = "";
	
	/**
	 * Texto sprintf para el mensaje de campo requerido *
	 */
	public $textoCampoRequerido = "El campo \"%s\" es requerido.";
	
	/**
	 * Lo que agrega al lado del nombre del campo para indicar que es requerido *
	 */
	public $indicadorDeCampoRequerido = "<div class='indRequerido'></div>";
	
	/**
	 * Aparece despues del nombre del campo en los formularios de Alta y Modificacion.
	 * Ej: ":" *
	 */
	public $separadorNombreCampo = "";
	
	/**
	 * Coleccion de links necesarios para que el datepiker funcione correctamente
	 *
	 * @var unknown
	 */
	public $jslinksCampoFecha = '<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"> <script src="//code.jquery.com/jquery-1.10.2.js"></script> <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>';
	
	/**
	 * Codigo JS para poner en window.onload para cada uno de los campos de fecha *
	 */
	public $jsIniciadorCamposFecha = '
    <script>
    $(function(){
        $("#%IDCAMPO%").datepicker({
            regional: "es",
            showAnim: "fade",
            dateFormat: "yy-mm-dd",
            altField: "#display_%IDCAMPO%",
            altFormat: "dd/mm/yy"
        });
        $("#display_%IDCAMPO%").focus(function(){$("#%IDCAMPO%").datepicker("show")});
        if("%VALOR%" != "") $("#%IDCAMPO%").datepicker("setDate", "%VALOR%");
    });
    </script>
    ';
	
	/**
	 * Direccion donde tiene que ir a buscar el archivo encargado de procesar los selects dinamicos
	 */
	public $direDinamic = 'dinamic/selectDinamico.php';
	
	/**
	 * Codigo JS para poner en window.onload para cada uno de los campos de select dinamicos *
	 */
	public $jsIniciadorSelectDinamico = '
    <script>
			$(document).ready(function(){
				$("#%CAMPOPADRE%").change(function(){
					$.ajax({
						url:"%DIREDINAMIC%",
						type: "POST",
						data:"%WHERE%,
						success: function(opciones){
							$("#%CAMPO%").html(opciones);
						}
					})
			
			console.log("change");
				});
				$(window).load(function(){
					$.ajax({
						url:"%DIREDINAMIC%",
						type: "POST",
						data:"%WHEREINI%",
						success: function(opciones){
							$("#%CAMPO%").html(opciones);
						}
					})
				});
			});
		</script>
    ';
	
	/**
	 * Adicional para el atributo class de los input para el chequeo de los campos requeridos *
	 */
	public $chequeoInputRequerido = 'validate[required]';
	
	/**
	 * Formato de fecha a utilizar en los campos tipo fecha del listado.
	 * Usa la funcion date() de PHP *
	 */
	public $formatoFechaListado = "d/m/Y";
	
	/**
	 * Indica si colorea las filas del listado cuando se pasa por arriba con el puntero *
	 */
	public $colorearFilas = true;
	
	/**
	 * Color de la fila del listado cuando se para el puntero por arriba (ver $colorearFilas) *
	 */
	public $colorearFilasColor = '#f5fcff';
	
	/**
	 * Define si se va a colorear la fila con un degrade
	 *
	 * @var booleans
	 */
	public $colorearFilasDegrade = true;
	
	/**
	 * Segundo color del degrade de la fila del listado cuando se para el puntero por arriba (ver $colorearFilas) *
	 */
	public $colorearFilasColorSecundario = '#d5eefb';
	
	/**
	 * Nombre que le pone al archivo que exporta (no incluir la extension) *
	 */
	public $exportar_nombreArchivo = "exportar";
	
	/**
	 * El caracter separador de campos cuando exporta CSV *
	 */
	public $exportar_csv_separadorCampos = ",";
	
	/**
	 * El caracter delimitador de campos cuando exporta CSV *
	 */
	public $exportar_csv_delimitadorCampos = "\"";
	
	/**
	 * Usar este query sql para la funcion de exportar *
	 */
	public $exportar_sql;
	
	/**
	 * Los formatos en los que se puede exportar, o sea los botones que muestra (siempre y cuando haya campos con exportar=true) *
	 */
	public $exportar_formatosPermitidos = array (
			'excel',
			'csv' 
	);
	
	/**
	 * El JS que se agrega cuando un campo es requerido *
	 */
	public $jsIniciadorChequeoForm = '
        <script type="text/javascript">
        $(function(){
          $("#formularioAbm").validationEngine({promptPosition:"topLeft"});
        });
        </script>
    ';
	public $jsHints = '
        <script type="text/javascript">
        $( document ).tooltip({
            position: {
                my: "center bottom-20",
                at: "center top",
                using: function( position, feedback ) {
                    $( this ).css( position );
                    $( "<div>" )
                        .addClass( "arrow" )
                        .addClass( feedback.vertical )
                        .addClass( feedback.horizontal )
                        .appendTo( this );
                }
            }
        });
        </script>
    ';
	
	/**
	 * codigo Js a insertar en los form de alta y modificacion
	 */
	public $jsMonedaInput = '
        <script type="text/javascript">
			(function($) {
				  $.fn.currencyInput = function() {
				    this.each(function() {
				      var wrapper = $("<div class=\'currency-input\' />");
				      $(this).wrap(wrapper);
				      $(this).before("<span class=\'currency-symbol\'>$</span>");
				      $(this).change(function() {
				        var min = parseFloat($(this).attr("min"));
				        var max = parseFloat($(this).attr("max"));
				        var value = this.valueAsNumber;
				        if(value < min)
				          value = min;
				        else if(value > max)
				          value = max;
				        $(this).val(value.toFixed(2)); 
				      });
				    });
				  };
				})(jQuery);
				
				$(document).ready(function() {
				  $(\'input.currency\').currencyInput();
				});
        </script>';
	
	/**
	 * Establece si los formularios del abm se separaran en solapas o no
	 *
	 * @var bit
	 */
	public $formularioSolapa = false;
	
	/**
	 * En caso de dividirse el formulario en solapas cuantas deberian ser
	 *
	 * @var int
	 */
	public $cantidadSolapa = 0;
	
	/**
	 * El texto identificado que llevara cada solapa.
	 * Hay que recordar que el index del array sera siempre uno menos que el id de la solapa.
	 *
	 * @var array
	 */
	public $tituloSolapa = array ();
	
	/**
	 * establece si agregar o no un campo de busqueda general
	 * para poder buscar un texto x en todos los campos de la consulta.
	 * 
	 * @var bit
	 */
	public $busquedaTotal = false;
	/*
	 * *******************************************************
	 * VARIABLES RELACIONADAS AL CAMPO *
	 * *******************************************************
	 */
	
	/**
	 * Los campos de la BD y preferencias para cada uno.
	 * (Ver el ejemplo de la class)
	 */
	public $campos;
	
	/**
	 * Texto por defecto que se usa cuando el tipo de campo es "bit"
	 */
	public $textoBitTrue = "SI";
	
	/**
	 * Texto por defecto que se usa cuando el tipo de campo es "bit"
	 */
	public $textoBitFalse = "NO";
	
	/**
	 * Indica si un campo en particular imprime o no su join
	 * por defecto es true pero se puede usar para imprimir joins personalizados
	 *
	 * @var bit
	 */
	public $omitirJoin = false;

	/*
	 * ************************************************************************
	 * Aca empiezan las funciones de la clase
	 * ************************************************************************
	 */
	
	/**
	 * Devuelve un string con el concat de los campos Id
	 *
	 * @param array $array
	 *        	--> array con todos los campos a utilizar para generar el id compuesto
	 * @param string $tabla
	 *        	--> tabla que contendria el array compuesto
	 * @return string $arrayId --> concatenacion de los campos del array
	 */
	public function convertirIdMultiple($array, $tabla)
	{
		global $db;
		
		if ($db->dbtype == 'mysql')
		{
			
			$arrayId = "CONCAT (";
			
			foreach ($array as &$valor)
			{
				// print_r ("<br>" . $valor . "<br>");
				
				$arrayId .= $tabla . "." . $valor . ", ";
			}
			
			$arrayId = substr ($arrayId, 0, - 2);
			
			$arrayId .= ") AS id";
			
			return $arrayId;
		}
		elseif ($db->dbtype == 'oracle')
		{
			
			$tot = count ($array);
			if ($tot < 3)
			{
				$arrayId = "CONCAT (";
				
				foreach ($array as &$valor)
				{
					$arrayId .= $tabla . "." . $valor . ", ";
				}
			}
			else
			{
				$arrayId = " (";
				foreach ($array as &$valor)
				{
					$arrayId .= $tabla . "." . $valor . "||";
				}
			}
			$arrayId = substr ($arrayId, 0, - 2);
			
			$arrayId .= ") AS id";
			
			return $arrayId;
		}
		elseif ($db->dbtype == 'mssql')
		{
			$arrayId = "(";
			
			foreach ($array as &$valor)
			{
				$arrayId .= "convert(varchar, " . $tabla . "." . $valor . ")+";
			}
			
			$arrayId = substr ($arrayId, 0, - 1);
			
			$arrayId .= ") AS ID";
			
			return $arrayId;
		}
	}

	/**
	 * Devuelve un string con los campos Id de forma individual
	 *
	 * @param array $array
	 *        	--> array con todos los campos a utilizar para generar el id compuesto
	 * @param string $tabla
	 *        	--> tabla que contendria el array compuesto
	 * @return string $camp --> todos los campos de un id compuesto
	 */
	public function convertirIdMultipleSelect($array, $tabla)
	{
		global $db;
		
		if ($db->dbtype == 'mysql')
		{
			foreach ($array as &$valor)
			{
				$camp .= ", " . $tabla . "." . $valor;
			}
			
			return $camp;
		}
		elseif ($db->dbtype == 'oracle')
		{
			$tot = count ($array);
			
			foreach ($array as &$valor)
			{
				$camp .= ", " . $tabla . "." . $valor;
			}
			
			return $camp;
		}
		elseif ($db->dbtype == 'mssql')
		{
			
			foreach ($array as &$valor)
			{
				$camp .= ", " . $tabla . "." . $valor;
			}
			
			return $camp;
		}
	}

	/**
	 * Para saber que formulario esta mostrando (listado, alta, editar, dbDelete, dbUpdate, dbInsert), esto es util cuando queremos hacer diferentes en la pagina segun el estado.
	 */
	public function getEstadoActual()
	{
		if (isset ($_GET['abm_nuevo']))
		{
			return "alta";
		}
		elseif (isset ($_GET['abm_editar']))
		{
			return "editar";
		}
		elseif (isset ($_GET['abm_borrar']))
		{
			return "dbDelete";
		}
		elseif (isset ($_GET['abm_exportar']))
		{
			return "exportar";
		}
		elseif ($this->formularioEnviado ())
		{
			if ($_GET['abm_modif'])
			{
				return "dbUpdate";
			}
			elseif ($_GET['abm_alta'])
			{
				return "dbInsert";
			}
		}
		else
		{
			return "listado";
		}
	}

	/**
	 * Funcion encargada de generar el formulario de alta
	 */
	public function generarFormAlta($titulo = "")
	{
		global $db;
		
		$_POST = $this->limpiarEntidadesHTML ($_POST);
		
		// genera el query string de variables previamente existentes
		$get = $_GET;
		unset ($get['abm_nuevo']);
		$qsamb = http_build_query ($get);
		
		if ($qsamb != "")
		{
			$qsamb = "&" . $qsamb;
		}
		
		// agregar script para inicar FormCheck ?
		foreach ($this->campos as $campo)
		{
			if ($campo['requerido'])
			{
				echo $this->jsIniciadorChequeoForm;
				break;
			}
		}
		
		// agregar script para inicar los Hints ?
		foreach ($this->campos as $campo)
		{
			if ($campo['hint'] != "")
			{
				echo $this->jsHints;
				break;
			}
		}
		
		echo "<div class='mabm'>";
		
		if (isset ($_GET['abmsg']))
		{
			echo "<div class='merror'>" . urldecode ($_GET['abmsg']) . "</div>";
		}
		
		echo $this->jslinksCampoFecha;
		echo $jsMonedaInput;
		echo "<form enctype='multipart/form-data' method='" . $this->formMethod . "' id='formularioAbm' action='" . $this->formAction . "?abm_alta=1$qsamb' $this->adicionalesForm> \n";
		echo "<input type='hidden' name='abm_enviar_formulario' value='1' /> \n";
		echo "<table class='mformulario' $this->adicionalesTable> \n";
		
		if (isset ($titulo) or isset ($this->textoTituloFormularioAgregar))
		{
			echo "<thead><tr><th colspan='2'>" . (isset ($this->textoTituloFormularioAgregar) ? $this->textoTituloFormularioAgregar : $titulo) . "&nbsp;</th></tr></thead>";
		}
		
		echo "<tbody>\n";
		echo "<tr>\n";
		echo "<td>\n";
		echo "<div id='content'>\n";
		echo "<div id='cuerpo'>\n";
		echo "<div id='contenedor'>\n";
		
		if ($this->formularioSolapa == true)
		{
			for($e = 1; $e <= $this->cantidadSolapa; $e ++)
			{
				echo "<input id='tab-" . $e . "' type='radio' name='radio-set' class='tab-selector-" . $e . " folio' />";
				echo "<label for='tab-" . $e . "' class='tab-label-" . $e . " folio'>" . $this->tituloSolapa[$e - 1] . "</label>";
				
				$imprForm .= "<div class='content mabm'>";
				$imprForm .= "<div class='content-" . $e . "'>\n";
				$imprForm .= "<section>\n";
				$imprForm .= "<div id='form'>\n";
				
				$i = 0;
				
				foreach ($this->campos as $campo)
				{
					if ($campo['enSolapa'] == "")
					{
						$campo['enSolapa'] = 1;
					}
					
					if ($campo['enSolapa'] == $e)
					{
						if ($campo['noNuevo'] == true)
						{
							continue;
						}
						if ($campo['tipo'] == '' and $campo['formItem'] == '' and ! isset ($campo['separador']))
						{
							continue;
						}
						
						$i ++;
						
						if ($i == 1 and $this->autofocus)
						{
							$autofocusAttr = "autofocus='autofocus'";
						}
						else
						{
							$autofocusAttr = "";
						}
						
						if ($campo[requerido])
						{
							$requerido = $this->chequeoInputRequerido;
						}
						else
						{
							$requerido = "";
						}
						
						$imprForm .= "<div class='elementForm'>\n";
						
						if (isset ($campo['separador']))
						{
							$imprForm .= "<div colspan='2' class='separador'>" . $campo['separador'] . "&nbsp;</div> \n";
						}
						else
						{
							$imprForm .= "<div class='tituloItemsForm'>";
							$imprForm .= "<label for='" . $campo['campo'] . "'>" . ($campo['titulo'] != '' ? $campo['titulo'] : $campo['campo']) . $this->separadorNombreCampo . ($campo[requerido] ? " " . $this->indicadorDeCampoRequerido : "");
							$imprForm .= "</div> \n";
							
							$imprForm .= "<div class='itemsForm'> \n";
							
							if ($campo['formItem'] != "" and function_exists ($campo['formItem']))
							{
								call_user_func_array ($campo['formItem'], array (
										$fila 
								));
							}
							else
							{
								switch ($campo['tipo'])
								{
									case "texto" :
										if ($campo['campo'] == $this->campoId)
										{
											$idVal = $db->insert_id ($this->campoId, $this->tabla);
											$idVal = $idVal + 1;
											
											$imprForm .= "<input type='text' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr value='" . $idVal . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " class='input-text $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
										}
										else
										{
											$imprForm .= "<input type='text' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr value='" . ($_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo[valorPredefinido]) . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " class='input-text $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
										}
										break;
									
									case "moneda" :
										$imprForm .= "<input type='number' class='currency' min='0.01' max='250000000.00'  name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr value='" . ($_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo[valorPredefinido]) . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " class='input-text $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
										break;
									
									case "numero" :
										$imprForm .= "<input type='number' class='currency' min='0.01' max='250000000.00'  name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr value='" . ($_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo[valorPredefinido]) . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " class='input-text $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
										break;
									
									case "password" :
										$imprForm .= "<input type='password' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr value='" . ($_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo[valorPredefinido]) . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " class='input-text $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
										break;
									
									case "textarea" :
										$imprForm .= "<textarea name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-textarea $requerido' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]>" . ($_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo[valorPredefinido]) . "</textarea>\n";
										break;
									
									case "dbCombo" :
										$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]> \n";
										if ($campo[incluirOpcionVacia])
										{
											$imprForm .= "<option value=''></option> \n";
										}
										
										$result = $db->query ($campo[sqlQuery]);
										while ($fila = $db->fetch_array ($result))
										{
											if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == $fila[$campo[campoValor]]) or $campo[valorPredefinido] == $fila[$campo[campoValor]])
											{
												$sel = "selected='selected'";
											}
											else
											{
												$sel = "";
											}
											$imprForm .= "<option value='" . $fila[$campo['campoValor']] . "' $sel>" . $fila[$campo['campoTexto']] . "</option> \n";
										}
										$imprForm .= "</select> \n";
										break;
									
									case "combo" :
										$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]> \n";
										if ($campo['incluirOpcionVacia'])
										{
											$imprForm .= "<option value=''></option> \n";
										}
										
										foreach ($campo['datos'] as $valor => $texto)
										{
											if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == $valor) or $campo['valorPredefinido'] == $valor)
											{
												$sel = "selected='selected'";
											}
											else
											{
												$sel = "";
											}
											$imprForm .= "<option value='$valor' $sel>$texto</option> \n";
										}
										$imprForm .= "</select> \n";
										break;
									
									case "bit" :
										$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]> \n";
										
										if ($campo[ordenInversoBit])
										{
											if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == false) or $campo['valorPredefinido'] == false)
											{
												$sel = "selected='selected'";
											}
											else
											{
												$sel = "";
											}
											$imprForm .= "<option value='0' $sel>" . ($campo['textoBitFalse'] != "" ? $campo['textoBitFalse'] : $this->textoBitFalse) . "</option> \n";
											
											if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == true) or $campo['valorPredefinido'] == true)
											{
												$sel = "selected='selected'";
											}
											else
											{
												$sel = "";
											}
											$imprForm .= "<option value='1' $sel>" . ($campo['textoBitTrue'] != "" ? $campo['textoBitTrue'] : $this->textoBitTrue) . "</option> \n";
										}
										else
										{
											
											if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == true) or $campo['valorPredefinido'] == true)
											{
												$sel = "selected='selected'";
											}
											else
											{
												$sel = "";
											}
											$imprForm .= "<option value='1' $sel>" . ($campo['textoBitTrue'] != "" ? $campo['textoBitTrue'] : $this->textoBitTrue) . "</option> \n";
											
											if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == false) or $campo[valorPredefinido] == false)
											{
												$sel = "selected='selected'";
											}
											else
											{
												$sel = "";
											}
											$imprForm .= "<option value='0' $sel>" . ($campo['textoBitFalse'] != "" ? $campo['textoBitFalse'] : $this->textoBitFalse) . "</option> \n";
										}
										
										$imprForm .= "</select> \n";
										break;
									
									case "fecha" :
										$valor = $_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo['valorPredefinido'];
										if (strlen ($valor) > 10)
										{
											$valor = substr ($valor, 0, 10); // sacar hora:min:seg
										}
										if ($valor == '0000-00-00')
										{
											$valor = "";
										}
										$jsTmp = str_replace ('%IDCAMPO%', $campo['campo'], $this->jsIniciadorCamposFecha);
										$jsTmp = str_replace ('%VALOR%', $valor, $jsTmp);
										
										$imprForm .= $jsTmp;
										$imprForm .= "<input type='text' style='position:absolute' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' value='" . ($fila[$campo['campo']] != "" ? $fila[$campo['campo']] : $campo[valorPredefinido]) . "'/> \n";
										$imprForm .= "<input type='text' style='position:relative;top:0px;left;0px' $autofocusAttr name='display_" . $campo['campo'] . "' id='display_" . $campo['campo'] . "' class='input-fecha $requerido' $disabled $campo[adicionalInput] " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " readonly='readonly'/> \n";
										break;
									
									case "upload" :
										$imprForm .= "<input type='file' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='$requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
										break;
									
									default :
										$imprForm .= $campo['nombre'];
										break;
								}
							}
							
							$imprForm .= "</div> \n";
						}
						
						$imprForm .= "</div> \n";
					}
				}
				$imprForm .= "</div>\n";
				$imprForm .= "</section>\n";
				$imprForm .= "</div>\n";
				$imprForm .= "</div>\n";
			}
			echo $imprForm;
		}
		// en caso de que no sea de tipo solapa
		else
		{
			$imprForm .= "<div class='content mabm'>";
			$imprForm .= "<section>\n";
			$imprForm .= "<div id='form'>\n";
			
			$i = 0;
			
			foreach ($this->campos as $campo)
			{
				if ($campo['noNuevo'] == true)
				{
					continue;
				}
				if ($campo['tipo'] == '' and $campo['formItem'] == '' and ! isset ($campo['separador']))
				{
					continue;
				}
				
				$i ++;
				
				if ($i == 1 and $this->autofocus)
				{
					$autofocusAttr = "autofocus='autofocus'";
				}
				else
				{
					$autofocusAttr = "";
				}
				
				if ($campo['requerido'])
				{
					$requerido = $this->chequeoInputRequerido;
				}
				else
				{
					$requerido = "";
				}
				
				$imprForm .= "<div class='elementForm'>\n";
				
				if (isset ($campo['separador']))
				{
					$imprForm .= "<div colspan='2' class='separador'>" . $campo['separador'] . "&nbsp;</div> \n";
				}
				else
				{
					$imprForm .= "<div class='tituloItemsForm'>";
					$imprForm .= "<label for='" . $campo['campo'] . "'>" . ($campo['titulo'] != '' ? $campo['titulo'] : $campo['campo']) . $this->separadorNombreCampo . ($campo[requerido] ? " " . $this->indicadorDeCampoRequerido : "");
					$imprForm .= "</div> \n";
					
					$imprForm .= "<div class='itemsForm'> \n";
					
					if ($campo['formItem'] != "" and function_exists ($campo['formItem']))
					{
						call_user_func_array ($campo['formItem'], array (
								$fila 
						));
					}
					else
					{
						switch ($campo['tipo'])
						{
							case "texto" :
								if ($campo['campo'] == $this->campoId)
								{
									$idVal = $db->insert_id ($this->campoId, $this->tabla);
									$idVal = $idVal + 1;
									
									$imprForm .= "<input type='text' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr value='" . $idVal . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " class='input-text $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
								}
								else
								{
									$imprForm .= "<input type='text' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr value='" . ($_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo[valorPredefinido]) . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " class='input-text $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
								}
								break;
							
							case "moneda" :
								$imprForm .= "<input type='number' class='input-text $requerido currency' step='0.01' min='0.01' max='250000000.00'  name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr value='" . ($_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo[valorPredefinido]) . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
								break;
							
							case "numero" :
								$imprForm .= "<input type='number' class='input-text $requerido currency' step='0.01' min='0.01' max='250000000.00'  name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr value='" . ($_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo[valorPredefinido]) . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
								break;
							
							case "password" :
								$imprForm .= "<input type='password' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr value='" . ($_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo[valorPredefinido]) . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " class='input-text $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
								break;
							
							case "textarea" :
								$imprForm .= "<textarea name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-textarea $requerido' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]>" . ($_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo[valorPredefinido]) . "</textarea>\n";
								break;
							
							case "dbCombo" :
								$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]> \n";
								if ($campo['incluirOpcionVacia'])
								{
									$imprForm .= "<option value=''></option> \n";
								}
								
								$result = $db->query ($campo['sqlQuery']);
								while ($fila = $db->fetch_array ($result))
								{
									if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == $fila[$campo['campoValor']]) or $campo[valorPredefinido] == $fila[$campo[campoValor]])
									{
										$sel = "selected='selected'";
									}
									else
									{
										$sel = "";
									}
									$imprForm .= "<option value='" . $fila[$campo[campoValor]] . "' $sel>" . $fila[$campo['campoTexto']] . "</option> \n";
								}
								$imprForm .= "</select> \n";
								break;
							
							case "combo" :
								$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]> \n";
								if ($campo[incluirOpcionVacia])
								{
									$imprForm .= "<option value=''></option> \n";
								}
								
								foreach ($campo[datos] as $valor => $texto)
								{
									if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == $valor) or $campo[valorPredefinido] == $valor)
									{
										$sel = "selected='selected'";
									}
									else
									{
										$sel = "";
									}
									$imprForm .= "<option value='$valor' $sel>$texto</option> \n";
								}
								$imprForm .= "</select> \n";
								break;
							
							case "bit" :
								$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]> \n";
								
								if ($campo[ordenInversoBit])
								{
									if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == false) or $campo[valorPredefinido] == false)
									{
										$sel = "selected='selected'";
									}
									else
									{
										$sel = "";
									}
									$imprForm .= "<option value='0' $sel>" . ($campo['textoBitFalse'] != "" ? $campo['textoBitFalse'] : $this->textoBitFalse) . "</option> \n";
									
									if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == true) or $campo[valorPredefinido] == true)
									{
										$sel = "selected='selected'";
									}
									else
									{
										$sel = "";
									}
									$imprForm .= "<option value='1' $sel>" . ($campo['textoBitTrue'] != "" ? $campo['textoBitTrue'] : $this->textoBitTrue) . "</option> \n";
								}
								else
								{
									
									if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == true) or $campo[valorPredefinido] == true)
									{
										$sel = "selected='selected'";
									}
									else
									{
										$sel = "";
									}
									$imprForm .= "<option value='1' $sel>" . ($campo[textoBitTrue] != "" ? $campo[textoBitTrue] : $this->textoBitTrue) . "</option> \n";
									
									if ((isset ($_POST[$campo['campo']]) and $_POST[$campo['campo']] == false) or $campo[valorPredefinido] == false)
									{
										$sel = "selected='selected'";
									}
									else
									{
										$sel = "";
									}
									$imprForm .= "<option value='0' $sel>" . ($campo[textoBitFalse] != "" ? $campo[textoBitFalse] : $this->textoBitFalse) . "</option> \n";
								}
								
								$imprForm .= "</select> \n";
								break;
							
							case "fecha" :
								$valor = $_POST[$campo['campo']] != "" ? $_POST[$campo['campo']] : $campo[valorPredefinido];
								if (strlen ($valor) > 10)
								{
									$valor = substr ($valor, 0, 10); // sacar hora:min:seg
								}
								if ($valor == '0000-00-00')
								{
									$valor = "";
								}
								$jsTmp = str_replace ('%IDCAMPO%', $campo['campo'], $this->jsIniciadorCamposFecha);
								$jsTmp = str_replace ('%VALOR%', $valor, $jsTmp);
								
								$imprForm .= $jsTmp;
								$imprForm .= "<input type='text' style='position:absolute' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' value='" . ($fila[$campo['campo']] != "" ? $fila[$campo['campo']] : $campo[valorPredefinido]) . "'/> \n";
								$imprForm .= "<input type='text' style='position:relative;top:0px;left;0px' $autofocusAttr name='display_" . $campo['campo'] . "' id='display_" . $campo['campo'] . "' class='input-fecha $requerido' $disabled $campo[adicionalInput] " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " readonly='readonly'/> \n";
								break;
							
							case "upload" :
								$imprForm .= "<input type='file' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='$requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
								break;
							
							default :
								$imprForm .= $campo['nombre'];
								break;
						}
					}
					
					$imprForm .= "</div> \n";
				}
				
				$imprForm .= "</div> \n";
			}
			$imprForm .= "</div>\n";
			$imprForm .= "</section>\n";
			$imprForm .= "</div>\n";
			$imprForm .= "</div>\n";
			
			echo $imprForm;
		}
		
		echo "</div>\n";
		echo "</div>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</tbody>\n";
		
		echo "<tfoot>";
		echo "    <tr>";
		echo "        <th colspan='2'>";
		echo "			<div class ='divBtnCancelar'><input type='button' class='input-button' title='Atajo: ALT+C' accesskey='c' value='$this->textoBotonCancelar' onclick=\"" . ($this->cancelarOnClickJS != "" ? $this->cancelarOnClickJS : "window.location='$_SERVER[PHP_SELF]?$qsamb'") . "\"/></div> ";
		
		if ($this->extraBtn == 'true')
		{
			echo "			<div class='divBtnExtra'><input type='button' class='input-button' title='$this->textoBotonExtraTitulo' value='$this->textoBotonExtra' $this->adicionalesExtra /></div>";
		}
		echo "			<div class='divBtnAceptar'><input type='submit' class='input-submit' title='Atajo: ALT+G' accesskey='G' value='$this->textoBotonSubmitNuevo' $this->adicionalesSubmit /></div>";
		echo "		  </th>";
		echo "    </tr>";
		echo "</tfoot>";
		
		echo "</table> \n";
		echo "</form> \n";
		echo "</div>";
	}

	/**
	 * Genera el formulario de modificacion de un registro
	 *
	 * @param string $id
	 *        	id por el que debe identificarse el registro a modificar
	 * @param string $titulo
	 *        	en caso de que el formulario deba tener un titulo especial
	 *        	
	 * @return string
	 */
	public function generarFormModificacion($id, $titulo = "")
	{
		global $db;
		
		// por cada campo...
		for($i = 0; $i < count ($this->campos); $i ++)
		{
			
			if ($this->campos[$i]['campo'] == "")
			{
				continue;
			}
			if ($this->campos[$i]['noMostrarEditar'] == true)
			{
				continue;
			}
			if ($this->campos[$i]['tipo'] == "upload")
			{
				continue;
			}
			
			// campos para el select
			if ($camposSelect != "")
			{
				$camposSelect .= ", ";
			}
			
			// $camposSelect .= $this->campos [$i] ['selectPersonal'] . " AS " . $tablaJoin . "_" . $this->campos [$i] ['campoTexto'];
			
			if (($this->campos[$i]['joinTable'] != '') and ($this->campos[$i]['omitirJoin'] == false))
			{
				if (isset ($this->campos[$i]['selectPersonal']) and $this->campos[$i]['selectPersonal'] != "")
				{
					$tablaJoin = $this->campos[$i]['joinTable'];
					
					$tablaJoin = explode (".", $tablaJoin);
					$tablaJoin = $tablaJoin[count ($tablaJoin) - 1];
					
					$camposSelect .= $this->campos[$i]['selectPersonal'] . " AS " . $tablaJoin . "_" . $this->campos[$i]['campoTexto'];
				}
				else
				{
					$tablaJoin = $this->campos[$i]['joinTable'];
					
					$tablaJoin = explode (".", $tablaJoin);
					$tablaJoin = $tablaJoin[count ($tablaJoin) - 1];
					
					$camposSelect .= $this->campos[$i]['joinTable'] . "." . $this->campos[$i]['campoTexto'] . " AS " . $tablaJoin . "_" . $this->campos[$i]['campoTexto'];
				}
			}
			elseif (($this->campos[$i]['joinTable'] != '') and ($this->campos[$i]['omitirJoin'] == true))
			{
				if (isset ($this->campos[$i]['selectPersonal']) and $this->campos[$i]['selectPersonal'] == true)
				{
					$tablaJoin = $this->campos[$i]['joinTable'];
					
					$tablaJoin = explode (".", $tablaJoin);
					$tablaJoin = $tablaJoin[count ($tablaJoin) - 1];
					
					$camposSelect .= $this->campos[$i]['selectPersonal'] . " AS " . $this->campos[$i]['campo'];
				}
				else
				{
					if (isset ($this->campos[$i]['selectPersonal']) and $this->campos[$i]['selectPersonal'] == true)
					{
						$tablaJoin = $this->campos[$i]['joinTable'];
						
						$tablaJoin = explode (".", $tablaJoin);
						$tablaJoin = $tablaJoin[count ($tablaJoin) - 1];
						
						$camposSelect .= $this->campos[$i]['campo'];
					}
					else
					{
						$tablaJoin = $this->campos[$i]['joinTable'];
						
						$tablaJoin = explode (".", $tablaJoin);
						$tablaJoin = $tablaJoin[count ($tablaJoin) - 1];
						
						$camposSelect .= $this->campos[$i]['joinTable'] . "." . $this->campos[$i]['campo'];
					}
				}
			}
			else
			{
				$camposSelect .= $this->tabla . "." . $this->campos[$i]['campo'];
			}
			
			// Si existe agregamos los datos del campo select
			if ($this->sqlCamposSelect != "")
			{
				$camposSelect .= ", " . $this->sqlCamposSelect;
			}
			
			// tablas para sql join
			if ($this->campos[$i]['joinTable'] != '' and ($this->campos[$i]['omitirJoin'] != true))
			{
				if ($this->campos[$i]['joinCondition'] != '')
				{
					$joinCondition = $this->campos[$i]['joinCondition'];
				}
				else
				{
					$joinCondition = "INNER";
				}
				
				$joinSql .= " $joinCondition JOIN " . $this->campos[$i]['joinTable'] . " ON " . $this->tabla . '.' . $this->campos[$i]['campo'] . '=' . $this->campos[$i]['joinTable'] . '.' . $this->campos[$i]['campoValor'];
				
				if ($this->campos[$i]['customCompare'] != "")
				{
					// $joinSql .= " ".$this->campos [$i] ['customCompare'];
					$joinSql .= " AND " . $this->campos[$i]['customCompareCampo'] . " = " . $this->tabla . '.' . $this->campos[$i]['customCompareValor'];
				}
			}
		}
		// hace el select para mostrar los datos del formulario de edicion
		$id = $this->limpiarParaSql ($id);
		
		if (is_array ($this->campoId))
		{
			$camposSelect .= $this->convertirIdMultipleSelect ($this->campoId, $this->tabla);
			$this->campoId = $this->convertirIdMultiple ($this->campoId, $this->tabla);
			
			$sql = "SELECT $this->campoId, $camposSelect FROM " . $this->tabla . " " . $joinSql . " " . $this->customJoin . " WHERE " . substr ($this->campoId, 0, - 6) . " = '" . $id . "'";
		}
		else
		{
			$sql = "SELECT $this->tabla.$this->campoId AS id, $camposSelect FROM " . $this->tabla . " " . $joinSql . " " . $this->customJoin . " WHERE " . $this->tabla . "." . $this->campoId . "='" . $id . "'";
		}
		
		$result = $db->query ($sql);
		
		$fila = $db->fetch_array ($result);
		
		if ($db->num_rows ($result) == 0)
		{
			if (($fila < 0) or ($fila == "") or ($fila == NULL))
			{
				// print_r ("SELECT $this->campoId, $camposSelect FROM " . $this->tabla . " WHERE " . $this->campoId . "='" . $id . "'");
				echo $this->textoElRegistroNoExiste;
				return;
			}
		}
		
		// genera el query string de variables previamente existentes
		$get = $_GET;
		unset ($get['abm_editar']);
		$qsamb = http_build_query ($get);
		
		if ($qsamb != "")
		{
			$qsamb = "&" . $qsamb;
		}
		
		// agregar script para inicar FormCheck ?
		foreach ($this->campos as $campo)
		{
			if ($campo['requerido'])
			{
				echo $this->jsIniciadorChequeoForm;
				break;
			}
		}
		
		// agregar script para iniciar los Hints ?
		foreach ($this->campos as $campo)
		{
			if ($campo['hint'] != "")
			{
				echo $this->jsHints;
				break;
			}
		}
		
		// Imprimimos la llamada a los js correspondientes para que funcionen los datepikcer
		echo $this->jslinksCampoFecha;
		
		echo "<div class='mabm'>";
		if (isset ($_GET['abmsg']))
		{
			echo "<div class='merror'>" . urldecode ($_GET['abmsg']) . "</div>";
		}
		echo "<form enctype='multipart/form-data' method='" . $this->formMethod . "' id='formularioAbm' action='" . $this->formAction . "?abm_modif=1&$qsamb' $this->adicionalesForm> \n";
		echo "<input type='hidden' name='abm_enviar_formulario' value='1' /> \n";
		echo "<input type='hidden' name='abm_id' value='" . $id . "' /> \n";
		echo "<table class='mformulario' $this->adicionalesTable> \n";
		
		if (isset ($titulo) or isset ($this->textoTituloFormularioEdicion))
		{
			echo "<thead><tr><th colspan='2'>" . (isset ($this->textoTituloFormularioEdicion) ? $this->textoTituloFormularioEdicion : $titulo) . "&nbsp;</th></tr></thead>";
		}
		
		echo "<tbody>\n";
		echo "<tr>\n";
		echo "<td>\n";
		echo "<div id='content'>\n";
		echo "<div id='cuerpo'>\n";
		echo "<div id='contenedor'>\n";
		
		if ($this->formularioSolapa == true)
		{
			for($e = 1; $e <= $this->cantidadSolapa; $e ++)
			{
				echo "<input id='tab-" . $e . "' type='radio' name='radio-set' class='tab-selector-" . $e . " folio' />";
				echo "<label for='tab-" . $e . "' class='tab-label-" . $e . " folio'>" . $this->tituloSolapa[$e - 1] . "</label>";
				
				$imprForm .= "<div class='content mabm'>";
				$imprForm .= "<div class='content-" . $e . "'>\n";
				$imprForm .= "<section>\n";
				$imprForm .= "<div id='form'>\n";
				
				$i = 0;
				
				// por cada campo... arma el formulario
				foreach ($this->campos as $campo)
				{
					if ($campo['enSolapa'] == "")
					{
						$campo['enSolapa'] = 1;
					}
					
					if ($campo['enSolapa'] == $e)
					{
						
						if ($campo['noMostrarEditar'] == true)
						{
							continue;
						}
						if ($campo['tipo'] == '' and $campo['formItem'] == '' and ! isset ($campo['separador']))
						{
							continue;
						}
						
						$i ++;
						
						if ($i == 1 and $this->autofocus)
						{
							$autofocusAttr = "autofocus='autofocus'";
						}
						else
						{
							$autofocusAttr = "";
						}
						
						if ($campo['noEditar'] == true)
						{
							$disabled = "disabled='disabled'";
						}
						else
						{
							$disabled = "";
						}
						
						if ($campo['requerido'])
						{
							$requerido = $this->chequeoInputRequerido;
						}
						else
						{
							$requerido = "";
						}
						
						$imprForm .= "<div class='elementForm'>\n";
						
						if (isset ($campo['separador']))
						{
							$imprForm .= "<div colspan='2' class='separador'>" . $campo['separador'] . "&nbsp;</div> \n";
						}
						else
						{
							$imprForm .= "<div class='tituloItemsForm'>";
							$imprForm .= "<label for='" . $campo['campo'] . "'>" . ($campo['titulo'] != '' ? $campo['titulo'] : $campo['campo']) . $this->separadorNombreCampo . ($campo[requerido] ? " " . $this->indicadorDeCampoRequerido : "");
							$imprForm .= "</div> \n";
							
							$imprForm .= "<div class='itemsForm'> \n";
							
							if ($campo[formItem] != "" and function_exists ($campo['formItem']))
							{
								call_user_func_array ($campo['formItem'], array (
										$fila 
								));
							}
							else
							{
								if (($this->campos[$i]['customCompare'] != "") and ($campo['campo'] == $this->campos[$i]['customCompareValor']))
								{
									$customCompareValor = $fila[$campo['campo']];
								}
								
								switch ($campo['tipo'])
								{
									case "texto" :
										$imprForm .= "<input type='text' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-text $requerido' $disabled value='" . $fila[$campo['campo']] . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . (($campo['campo'] == $this->campoId and ! $this->campoIdEsEditable) ? "readonly='readonly' disabled='disabled'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
										break;
									
									case "moneda" :
										$imprForm .= "<input type='number' class='input-text $requerido currency' step='0.01' min='0.01' max='250000000.00'  name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr $disabled value='" . $fila[$campo['campo']] . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . (($campo['campo'] == $this->campoId and ! $this->campoIdEsEditable) ? "readonly='readonly' disabled='disabled'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
										break;
									
									case "numero" :
										$imprForm .= "<input type='number' class='input-text $requerido currency' step='0.01' min='0.01' max='250000000.00'  name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr $disabled value='" . $fila[$campo['campo']] . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . (($campo['campo'] == $this->campoId and ! $this->campoIdEsEditable) ? "readonly='readonly' disabled='disabled'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
										break;
									
									case "password" :
										$imprForm .= "<input type='password' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-text $requerido' $disabled value='" . $fila[$campo['campo']] . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . (($campo['campo'] == $this->campoId and ! $this->campoIdEsEditable) ? "readonly='readonly' disabled='disabled'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
										break;
									
									case "textarea" :
										$imprForm .= "<textarea name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr $disabled class='input-textarea $requerido' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]>" . $fila[$campo['campo']] . "</textarea>\n";
										break;
									
									case "dbCombo" :
										$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' $disabled $campo[adicionalInput]> \n";
										if ($campo[incluirOpcionVacia])
										{
											$imprForm .= "<option value=''></option> \n";
										}
										
										$sqlQuery = $campo['sqlQuery'];
										
										if ($campo['customCompare'] != "")
										{
											$sqlQuery .= " WHERE 1=1 AND " . $campo['customCompareCampo'] . " = " . $customCompareValor;
											
											if ($this->campos[$i]['customOrder'] != "")
											{
												$sqlQuery .= " ORDER BY " . $tabla . '.' . $campo['customOrder'];
											}
										}
										
										$resultCombo = $db->query ($sqlQuery);
										while ($filaCombo = $db->fetch_array ($resultCombo))
										{
											$filaCombo = $this->limpiarEntidadesHTML ($filaCombo);
											if ($filaCombo[$campo['campoValor']] == $fila[$campo['campo']])
											{
												$selected = "selected";
											}
											else
											{
												$selected = "";
											}
											$imprForm .= "<option value='" . $filaCombo[$campo['campoValor']] . "' $selected>" . $filaCombo[$campo['campoTexto']] . "</option> \n";
										}
										$imprForm .= "</select> \n";
										break;
									
									case "combo" :
										$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' $disabled " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]> \n";
										if ($campo['incluirOpcionVacia'])
										{
											$imprForm .= "<option value=''></option> \n";
										}
										
										foreach ($campo['datos'] as $valor => $texto)
										{
											if ($fila[$campo['campo']] == $this->limpiarEntidadesHTML ($valor))
											{
												$sel = "selected='selected'";
											}
											else
											{
												$sel = "";
											}
											$imprForm .= "<option value='$valor' $sel>$texto</option> \n";
										}
										$imprForm .= "</select> \n";
										break;
									
									case "bit" :
										$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' $disabled " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]> \n";
										
										if ($campo['ordenInversoBit'])
										{
											if (! $fila[$campo['campo']])
											{
												$sel = "selected='selected'";
											}
											else
											{
												$sel = "";
											}
											$imprForm .= "<option value='0' $sel>" . ($campo['textoBitFalse'] != "" ? $campo['textoBitFalse'] : $this->textoBitFalse) . "</option> \n";
											
											if ($fila[$campo['campo']])
											{
												$sel = "selected='selected'";
											}
											else
											{
												$sel = "";
											}
											$imprForm .= "<option value='1' $sel>" . ($campo['textoBitTrue'] != "" ? $campo['textoBitTrue'] : $this->textoBitTrue) . "</option> \n";
										}
										else
										{
											
											if ($fila[$campo['campo']])
											{
												$sel = "selected='selected'";
											}
											else
											{
												$sel = "";
											}
											$imprForm .= "<option value='1' $sel>" . ($campo['textoBitTrue'] != "" ? $campo['textoBitTrue'] : $this->textoBitTrue) . "</option> \n";
											
											if (! $fila[$campo['campo']])
											{
												$sel = "selected='selected'";
											}
											else
											{
												$sel = "";
											}
											$imprForm .= "<option value='0' $sel>" . ($campo['textoBitFalse'] != "" ? $campo['textoBitFalse'] : $this->textoBitFalse) . "</option> \n";
										}
										
										$imprForm .= "</select> \n";
										break;
									
									case "fecha" :
										$valor = $fila[$campo['campo']];
										if (strlen ($valor) > 10)
										{
											$valor = substr ($valor, 0, 10); // sacar hora:min:seg
										}
										if ($valor == '0000-00-00')
										{
											$valor = "";
										}
										$jsTmp = str_replace ('%IDCAMPO%', $campo['campo'], $this->jsIniciadorCamposFecha);
										$jsTmp = str_replace ('%VALOR%', $valor, $jsTmp);
										
										$imprForm .= $jsTmp;
										$imprForm .= "<input type='text' style='position:absolute' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' value='" . ($fila[$campo['campo']] != "" ? $fila[$campo['campo']] : $campo[valorPredefinido]) . "'/> \n";
										$imprForm .= "<input type='text' style='position:relative;top:0px;left;0px'  $autofocusAttr name='display_" . $campo['campo'] . "' id='display_" . $campo['campo'] . "' class='input-fecha $requerido' $disabled " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput] readonly='readonly'/> \n";
										break;
									
									case "upload" :
										$imprForm .= "<input type='file' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='$requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
										break;
									
									default :
										$imprForm .= $campo['nombre'];
										break;
								}
							}
							
							$imprForm .= "</div> \n";
						}
						
						$imprForm .= "</div> \n";
					}
				}
				$imprForm .= "</div>\n";
				$imprForm .= "</section>\n";
				$imprForm .= "</div>\n";
				$imprForm .= "</div>\n";
			}
			echo $imprForm;
		}
		else
		{
			// En caso de que no se requiera la utilizacion de solapas
			
			$imprForm .= "<div class='content mabm'>";
			$imprForm .= "<section>\n";
			$imprForm .= "<div id='form'>\n";
			
			$i = 0;
			
			// por cada campo... arma el formulario
			foreach ($this->campos as $campo)
			{
				if ($campo['enSolapa'] == "")
				{
					$campo['enSolapa'] = 1;
				}
				
				if ($campo['noMostrarEditar'] == true)
				{
					continue;
				}
				if ($campo['tipo'] == '' and $campo['formItem'] == '' and ! isset ($campo['separador']))
				{
					continue;
				}
				
				$i ++;
				
				if ($i == 1 and $this->autofocus)
				{
					$autofocusAttr = "autofocus='autofocus'";
				}
				else
				{
					$autofocusAttr = "";
				}
				
				if ($campo['noEditar'] == true)
				{
					$disabled = "disabled='disabled'";
				}
				else
				{
					$disabled = "";
				}
				
				if ($campo['requerido'])
				{
					$requerido = $this->chequeoInputRequerido;
				}
				else
				{
					$requerido = "";
				}
				
				$imprForm .= "<div class='elementForm'>\n";
				
				if (isset ($campo['separador']))
				{
					$imprForm .= "<div colspan='2' class='separador'>" . $campo['separador'] . "&nbsp;</div> \n";
				}
				else
				{
					$imprForm .= "<div class='tituloItemsForm'>";
					$imprForm .= "<label for='" . $campo['campo'] . "'>" . ($campo['titulo'] != '' ? $campo['titulo'] : $campo['campo']) . $this->separadorNombreCampo . ($campo[requerido] ? " " . $this->indicadorDeCampoRequerido : "");
					$imprForm .= "</div> \n";
					
					$imprForm .= "<div class='itemsForm'> \n";
					
					if ($campo['formItem'] != "" and function_exists ($campo['formItem']))
					{
						call_user_func_array ($campo['formItem'], array (
								$fila 
						));
					}
					else
					{
						if (($this->campos[$i]['customCompare'] != "") and ($campo['campo'] == $this->campos[$i]['customCompareValor']))
						{
							$customCompareValor = $fila[$campo['campo']];
						}
						
						switch ($campo['tipo'])
						{
							case "texto" :
								$imprForm .= "<input type='text' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-text $requerido' $disabled value='" . $fila[$campo['campo']] . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . (($campo['campo'] == $this->campoId and ! $this->campoIdEsEditable) ? "readonly='readonly' disabled='disabled'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
								break;
							
							case "moneda" :
								$imprForm .= "<input type='number' class='input-text $requerido currency' step='0.01' min='0.01' max='250000000.00'  name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr $disabled value='" . $fila[$campo['campo']] . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . (($campo['campo'] == $this->campoId and ! $this->campoIdEsEditable) ? "readonly='readonly' disabled='disabled'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
								break;
							
							case "numero" :
								$imprForm .= "<input type='number' class='input-text $requerido currency' step='0.01' min='0.01' max='250000000.00'  name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr $disabled value='" . $fila[$campo['campo']] . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . (($campo['campo'] == $this->campoId and ! $this->campoIdEsEditable) ? "readonly='readonly' disabled='disabled'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
								break;
							
							case "password" :
								$imprForm .= "<input type='password' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-text $requerido' $disabled value='" . $fila[$campo['campo']] . "' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . (($campo['campo'] == $this->campoId and ! $this->campoIdEsEditable) ? "readonly='readonly' disabled='disabled'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
								break;
							
							case "textarea" :
								$imprForm .= "<textarea name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr $disabled class='input-textarea $requerido' " . ($campo[maxLen] > 0 ? "maxlength='$campo[maxLen]'" : "") . " " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]>" . $fila[$campo['campo']] . "</textarea>\n";
								break;
							
							case "dbCombo" :
								$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' $disabled $campo[adicionalInput]> \n";
								if ($campo['incluirOpcionVacia'])
								{
									$imprForm .= "<option value=''></option> \n";
								}
								
								$sqlQuery = $campo['sqlQuery'];
								
								if ($campo['customCompare'] != "")
								{
									$sqlQuery .= " WHERE 1=1 AND " . $campo['customCompareCampo'] . " = " . $customCompareValor;
									
									if ($this->campos[$i]['customOrder'] != "")
									{
										$sqlQuery .= " ORDER BY " . $tabla . '.' . $campo['customOrder'];
									}
								}
								
								$resultCombo = $db->query ($sqlQuery);
								while ($filaCombo = $db->fetch_array ($resultCombo))
								{
									$filaCombo = $this->limpiarEntidadesHTML ($filaCombo);
									
									if ($filaCombo[$campo['campoValor']] == $fila[$campo['campo']])
									{
										$selected = "selected";
									}
									else
									{
										$selected = "";
									}
									$imprForm .= "<option value='" . $filaCombo[$campo['campoValor']] . "' $selected>" . $filaCombo[$campo['campoTexto']] . "</option> \n";
								}
								$imprForm .= "</select> \n";
								break;
							
							case "dbComboDinamic" :
								$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' $disabled $campo[adicionalInput]> \n";
								
								if ($campo['incluirOpcionVacia'])
								{
									$imprForm .= "<option value=''></option> \n";
								}
								
								$campoWere = "campoValor=" . $campo['campoValor'] . "&campoTexto=" . $campo['campoTexto'] . "&limitarTamanio=" . $campo['limitarTamaño'] . "&mensaje=" . $campo['mensaje'] . "&where=" . $campo['where'] . "&tabla=" . $campo['tabla'] . "&campoValor=" . $campo['campoValor'] . "&incluirValor=" . $campo['incluirValor'] . "&campo=" . $campo['campo'] . "&campoPadre=\"+$(\"#" . $campo['campoPadre'] . "\").val()";
								/*
								 * &campoValor=IDMODULO
								 * &campoTexto=TITULO
								 * &limitarTamanio=50
								 * &mensaje=seleccione
								 * &where=idaplicacion = " . $_POST ["IDAPLICACION"] . " AND agrupa = 0
								 * &tabla=APPADMUSU.MODULO
								 * &campoValor=IDMODULO
								 * &incluirValor=1
								 * &campo=PADRE
								 *
								 * "IDAPLICACION="+$("#apliRela").val(),
								 */
								
								//print_r ($campoWere);
								
								$jsSelDin = str_replace ('%CAMPO%', $campo['campo'], $this->jsIniciadorSelectDinamico);
								$jsSelDin = str_replace ('%CAMPOPADRE%', $campo['campoPadre'], $jsSelDin);
								$jsSelDin = str_replace ('%DIREDINAMIC%', $this->direDinamic, $jsSelDin);
								// $jsSelDin = str_replace ('%WHERE%', 'IDAPLICACION="+$("#IDAPLICACION").val()+"'.$campoWere.'"', $jsSelDin);
								$jsSelDin = str_replace ('%WHERE%', $campoWere, $jsSelDin);
								$jsSelDin = str_replace ('%WHEREINI%', $campo['campo'], $jsSelDin);
								
								//print_r ("XXX");
								$sqlQuery = $campo['sqlQuery'];
								
								if ($campo['customCompare'] != "")
								{
									$sqlQuery .= " WHERE 1=1 AND " . $campo['customCompareCampo'] . " = " . $customCompareValor;
									
									if ($this->campos[$i]['customOrder'] != "")
									{
										$sqlQuery .= " ORDER BY " . $tabla . '.' . $campo['customOrder'];
									}
								}
								
								$resultCombo = $db->query ($sqlQuery);
								
								while ($filaCombo = $db->fetch_array ($resultCombo))
								{
									$filaCombo = $this->limpiarEntidadesHTML ($filaCombo);
									
									if ($filaCombo[$campo[campoValor]] == $fila[$campo['campo']])
									{
										// exit();
										$selected = "selected";
									}
									else
									{
										$selected = "";
									}
									$imprForm .= "<option value='" . $filaCombo[$campo[campoValor]] . "' $selected>" . $filaCombo[$campo['campoTexto']] . "</option> \n";
								}
								$imprForm .= "</select> \n";
								$imprForm .= $jsSelDin;
								break;
							
							case "combo" :
								$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' $disabled " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]> \n";
								if ($campo[incluirOpcionVacia])
								{
									$imprForm .= "<option value=''></option> \n";
								}
								
								foreach ($campo[datos] as $valor => $texto)
								{
									if ($fila[$campo['campo']] == $this->limpiarEntidadesHTML ($valor))
									{
										$sel = "selected='selected'";
									}
									else
									{
										$sel = "";
									}
									$imprForm .= "<option value='$valor' $sel>$texto</option> \n";
								}
								$imprForm .= "</select> \n";
								break;
							
							case "bit" :
								$imprForm .= "<select name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='input-select $requerido' $disabled " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]> \n";
								
								if ($campo[ordenInversoBit])
								{
									if (! $fila[$campo['campo']])
									{
										$sel = "selected='selected'";
									}
									else
									{
										$sel = "";
									}
									$imprForm .= "<option value='0' $sel>" . ($campo[textoBitFalse] != "" ? $campo[textoBitFalse] : $this->textoBitFalse) . "</option> \n";
									
									if ($fila[$campo['campo']])
									{
										$sel = "selected='selected'";
									}
									else
									{
										$sel = "";
									}
									$imprForm .= "<option value='1' $sel>" . ($campo[textoBitTrue] != "" ? $campo[textoBitTrue] : $this->textoBitTrue) . "</option> \n";
								}
								else
								{
									
									if ($fila[$campo['campo']])
									{
										$sel = "selected='selected'";
									}
									else
									{
										$sel = "";
									}
									$imprForm .= "<option value='1' $sel>" . ($campo[textoBitTrue] != "" ? $campo[textoBitTrue] : $this->textoBitTrue) . "</option> \n";
									
									if (! $fila[$campo['campo']])
									{
										$sel = "selected='selected'";
									}
									else
									{
										$sel = "";
									}
									$imprForm .= "<option value='0' $sel>" . ($campo[textoBitFalse] != "" ? $campo[textoBitFalse] : $this->textoBitFalse) . "</option> \n";
								}
								
								$imprForm .= "</select> \n";
								break;
							
							case "fecha" :
								$valor = $fila[$campo['campo']];
								if (strlen ($valor) > 10)
								{
									$valor = substr ($valor, 0, 10); // sacar hora:min:seg
								}
								if ($valor == '0000-00-00')
								{
									$valor = "";
								}
								$jsTmp = str_replace ('%IDCAMPO%', $campo['campo'], $this->jsIniciadorCamposFecha);
								$jsTmp = str_replace ('%VALOR%', $valor, $jsTmp);
								
								$imprForm .= $jsTmp;
								$imprForm .= "<input type='text' style='position:absolute' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' value='" . ($fila[$campo['campo']] != "" ? $fila[$campo['campo']] : $campo[valorPredefinido]) . "'/> \n";
								$imprForm .= "<input type='text' style='position:relative;top:0px;left;0px'  $autofocusAttr name='display_" . $campo['campo'] . "' id='display_" . $campo['campo'] . "' class='input-fecha $requerido' $disabled " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput] readonly='readonly'/> \n";
								break;
							
							case "upload" :
								$imprForm .= "<input type='file' name='" . $campo['campo'] . "' id='" . $campo['campo'] . "' $autofocusAttr class='$requerido' " . ($campo[hint] != "" ? 'title="' . $campo[hint] . '"' : "") . " $campo[adicionalInput]/> \n";
								break;
							
							default :
								$imprForm .= $campo[nombre];
								break;
						}
					}
					
					$imprForm .= "</div> \n";
				}
				
				$imprForm .= "</div> \n";
			}
			$imprForm .= "</div>\n";
			$imprForm .= "</section>\n";
			$imprForm .= "</div>\n";
			
			echo $imprForm;
		}
		
		echo "</div>\n";
		echo "</div>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</tbody>\n";
		
		/*
		 * echo "<tfoot>";
		 * echo " <tr>";
		 * echo " <th colspan='2'><div class='divBtnCancelar'><input type='button' class='input-button' title='Atajo: ALT+C' accesskey='c' value='$this->textoBotonCancelar' onclick=\"".($this->cancelarOnClickJS != "" ? $this->cancelarOnClickJS : "window.location='$_SERVER[PHP_SELF]?$qsamb'")."\"/></div> <div class='divBtnAceptar'><input type='submit' class='input-submit' title='Atajo: ALT+G' accesskey='G' value='$this->textoBotonSubmitModificar' $this->adicionalesSubmit /></div></th>";
		 * echo " </tr>";
		 * echo "</tfoot>";
		 *
		 * echo "</table> \n";
		 * echo "</form> \n";
		 * echo "</div>";
		 */
		/**
		 * 2015/11/12
		 * Modificado por @iberlot para poder agregar btns extra
		 */
		
		echo "<tfoot>";
		echo "    <tr>";
		echo "        <th colspan='2'>";
		echo "			<div class ='divBtnCancelar'><input type='button' class='input-button' title='Atajo: ALT+C' accesskey='c' value='$this->textoBotonCancelar' onclick=\"" . ($this->cancelarOnClickJS != "" ? $this->cancelarOnClickJS : "window.location='$_SERVER[PHP_SELF]?$qsamb'") . "\"/></div> ";
		echo "			<div class='divBtnAceptar'><input type='submit' class='input-submit' title='Atajo: ALT+G' accesskey='G' value='$this->textoBotonSubmitNuevo' $this->adicionalesSubmit /></div>";
		if ($this->extraBtn == 'true')
		{
			echo "			<div class='divBtnExtra'><input type='button' class='input-button' title='$this->textoBotonExtraTitulo' value='$this->textoBotonExtra' $this->adicionalesExtra /></div>";
		}
		echo "		  </th>";
		echo "    </tr>";
		echo "</tfoot>";
		
		echo "</table> \n";
		echo "</form> \n";
		echo "</div>";
	}

	/**
	 * Funcion que exporta datos a formatos como Excel o CSV
	 *
	 * @param string $formato
	 *        	(uno entre: excel, csv)
	 */
	private function exportar($formato, $camposWhereBuscar="")
	{
		global $db;

		
		$camposWhereBuscar = htmlspecialchars_decode($camposWhereBuscar, ENT_QUOTES);
		$camposWhereBuscar = str_replace("|||", " ", $camposWhereBuscar);

		if (strtolower ($formato) == 'excel')
		{
			header ('Content-type: application/vnd.ms-excel');
			header ("Content-Disposition: attachment; filename={$this->exportar_nombreArchivo}.xls");
			header ("Pragma: no-cache");
			header ("Expires: 0");
			
			echo "<table border='1'>\n";
			echo "    <tr>\n";
		}
		elseif (strtolower ($formato) == 'csv')
		{
			header ('Content-type: text/csv');
			header ("Content-Disposition: attachment; filename={$this->exportar_nombreArchivo}.csv");
			header ("Pragma: no-cache");
			header ("Expires: 0");
		}
		
		// contar el total de campos que tienen el parametro "exportar"
		$totalCamposExportar = 0;
		
		for($i = 0; $i < count ($this->campos); $i ++)
		{
			if (! isset ($this->campos[$i]['exportar']) or $this->campos[$i]['exportar'] != true)
			{
				continue;
			}
			$totalCamposExportar ++;
		}
		
		// Por cada campo...
		for($i = 0; $i < count ($this->campos); $i ++)
		{
			if (! isset ($this->campos[$i]['exportar']) or $this->campos[$i]['exportar'] != true)
			{
				continue;
			}
			if ($this->campos[$i]['campo'] == "")
			{
				continue;
			}
			if ($this->campos[$i]['tipo'] == "upload")
			{
				continue;
			}
			
			// campos para el select
			if (isset ($camposSelect) and $camposSelect != "")
			{
				$camposSelect .= ", ";
			}
			else
			{
				$camposSelect = "";
			}
			
			if ($this->campos[$i]['tipo'] == 'rownum')
			{
				// Si el campo es de tipo rownum le decimos que no le agregue la tabla en la consulta
				$camposSelect .= $this->campos[$i]['campo'];
			}
			else
			{
				// tablas para sql join
				if (isset ($this->campos[$i]['joinTable']) and $this->campos[$i]['joinTable'] != '')
				{
					$tablaJoin = $this->campos[$i]['joinTable'];
					
					$tablaJoin = explode (".", $tablaJoin);
					$tablaJoin = $tablaJoin[count ($tablaJoin) - 1];
					
					if (isset ($this->campos[$i]['selectPersonal']) and $this->campos[$i]['selectPersonal'] != "")
					{
						$camposSelect .= $this->campos[$i]['selectPersonal'] . " AS " . $this->campos[$i]['campoTexto'];
					}
					else
					{
						$camposSelect .= $this->campos[$i]['joinTable'] . "." . $this->campos[$i]['campoTexto'] . " AS " . $this->campos[$i]['campoTexto'];
					}
					
					if (! isset ($this->campos[$i]['omitirJoin']) or $this->campos[$i]['omitirJoin'] == false)
					{
						if (isset ($this->campos[$i]['joinCondition']) and $this->campos[$i]['joinCondition'] != '')
						{
							$joinCondition = $this->campos[$i]['joinCondition'];
						}
						else
						{
							$joinCondition = "INNER";
						}
						
						if (! isset ($joinSql))
						{
							$joinSql = "";
						}
						
						$joinSql .= " $joinCondition JOIN " . $this->campos[$i]['joinTable'] . " ON " . $this->tabla . '.' . $this->campos[$i]['campo'] . '=' . $this->campos[$i]['joinTable'] . '.' . $this->campos[$i]['campoValor'];
						
						if (isset ($this->campos[$i]['customCompare']) and $this->campos[$i]['customCompare'] != "")
						{
							// $joinSql .= " ".$this->campos [$i] ['customCompare'];
							$joinSql .= " AND " . $this->campos[$i]['customCompareCampo'] . " = " . $this->tabla . '.' . $this->campos[$i]['customCompareValor'];
						}
					}
				}
				else
				{
					$camposSelect .= $this->tabla . "." . $this->campos[$i]['campo'];
				}
			}
			
			// Encabezados
			if (strtolower ($formato) == 'excel')
			{
				echo "        <th>";
			}
			
			if (isset ($this->campos[$i]['tituloListado']) and $this->campos[$i]['tituloListado'] != "")
			{
				echo $this->campos[$i]['tituloListado'];
			}
			elseif ($this->campos[$i]['titulo'] != '')
			{
				echo $this->campos[$i]['titulo'];
			}
			else
			{
				echo $this->campos[$i]['campo'];
			}
			
			// echo (isset($this->campos[$i]['tituloListado']) and $this->campos [$i] ['tituloListado'] != "" ? $this->campos [$i] ['tituloListado'] : ($this->campos [$i] ['titulo'] != '' ? $this->campos [$i] ['titulo'] : $this->campos [$i] ['campo']));
			
			if (strtolower ($formato) == 'excel')
			{
				echo "</th>\n";
			}
			elseif (strtolower ($formato) == 'csv')
			{
				if ($i < $totalCamposExportar - 1)
				{
					echo $this->exportar_csv_separadorCampos;
				}
			}
		}
		
		if (strtolower ($formato) == 'excel')
		{
			echo "    </tr>\n";
		}
		
		// Datos
		if ($this->exportar_sql != "")
		{
			$sql = $this->exportar_sql;
		}
		else if ($this->sqlCamposSelect != "")
		{
			if ($this->orderByPorDefecto != "")
			{
				$orderBy = " ORDER BY " . $this->orderByPorDefecto;
			}
			// $sql = "SELECT " . $this->sqlCamposSelect . " FROM $this->tabla $joinSql WHERE 1=1 $camposWhereBuscar $this->adicionalesSelect $orderBy";
			$sql = "SELECT " . $this->sqlCamposSelect . " FROM $this->tabla $joinSql $this->customJoin  WHERE 1=1 $camposWhereBuscar $this->adicionalesSelect $orderBy";
		}
		else
		{
			if ($this->orderByPorDefecto != "")
			{
				$orderBy = " ORDER BY " . $this->orderByPorDefecto;
			}
			
			// if (is_array ($this->campoId))
			// {
			// $this->campoId = $this->convertirIdMultiple ($this->campoId, $this->tabla);
			// }
			
			// $sql = "SELECT $this->campoId AS id, $camposSelect FROM $this->tabla $joinSql WHERE 1=1 $camposWhereBuscar $this->adicionalesSelect $orderBy";
			if (is_array ($this->campoId))
			{
				$this->campoId = $this->convertirIdMultiple ($this->campoId, $this->tabla);
			}
			else
			{
				$this->campoId = $this->tabla . "." . $this->campoId . " AS id ";
			}
			
			if (! isset ($joinSql))
			{
				$joinSql = "";
			}
			if (!isset ($camposWhereBuscar))
			{
				$camposWhereBuscar = "";
			}
			else
			{
				$camposWhereBuscar = " AND (".$camposWhereBuscar.") ";
			}
			if (!isset ($orderBy))
			{
				$orderBy = "";
			}
			//$sql = "SELECT $this->campoId , $camposSelect FROM $this->tabla $joinSql $this->customJoin WHERE 1=1 $camposWhereBuscar $this->adicionalesSelect $orderBy";
			$sql = "SELECT $this->campoId , $camposSelect FROM $this->tabla $joinSql $this->customJoin WHERE 1=1 AND 2=2 $this->adicionalesSelect $orderBy";
		}

		$result = $db->query ($sql);
		$i = 0;


		while ($fila = $db->fetch_array ($result))
		{
print_r("<Br />*******************<Br />");
			$fila = $this->limpiarEntidadesHTML ($fila);
			$i ++;
			
			if (strtolower ($formato) == 'excel')
			{
				echo "    <tr>\n";
			}
			elseif (strtolower ($formato) == 'csv')
			{
				echo "\n";
			}
			
			$c = 0;
			foreach ($this->campos as $campo)
			{
				$c ++;
				if (! isset ($campo['exportar']) or $campo['exportar'] != true)
				{
					continue;
				}
				
				if (isset ($campo['campoOrder']) and $campo['campoOrder'] != "")
				{
					$campo['campo'] = $campo['campoOrder'];
				}
				else
				{
					if (isset ($campo['joinTable']) and $campo['joinTable'] != '')
					{
						// $campo ['campo'] = $campo ['joinTable'] . '_' . $campo ['campoTexto'];
						$campo['campo'] = $campo['campoTexto'];
					}
				}
				
				if (strtolower ($formato) == 'excel')
				{
					
					echo '        <td>';
				}
				
				if ($campo['tipo'] == "bit")
				{
					if ($fila[$campo['campo']])
					{
						echo ($campo['textoBitTrue'] != '' ? $campo['textoBitTrue'] : $this->textoBitTrue);
					}
					else
					{
						echo ($campo['textoBitFalse'] != '' ? $campo['textoBitFalse'] : $this->textoBitFalse);
					}
				}
				else
				{
					
					// si es tipo fecha lo formatea
					if ($campo['tipo'] == "fecha")
					{
						if ($fila[$campo['campo']] != "" and $fila[$campo['campo']] != "0000-00-00" and $fila[$campo['campo']] != "0000-00-00 00:00:00")
						{
							if (strtotime ($fila[$campo['campo']]) !== - 1)
							{
								$fila[$campo['campo']] = date ($this->formatoFechaListado, strtotime ($fila[$campo['campo']]));
							}
						}
					}
					elseif ($campo['tipo'] == "moneda")
					{
						// setlocale(LC_MONETARY, 'es_AR');
						// $fila [$campo ['campo']] = money_format('%.2n', $fila [$campo ['campo']]);
						// number_format($número, 2, ',', ' ');
						$fila[$campo['campo']] = number_format ($fila[$campo['campo']], 2, ',', '.');
					}
					elseif ($campo['tipo'] == "numero")
					{
						// setlocale(LC_MONETARY, 'es_AR');
						// $fila [$campo ['campo']] = money_format('%.2n', $fila [$campo ['campo']]);
						// number_format($número, 2, ',', ' ');
						$fila[$campo['campo']] = number_format ($fila[$campo['campo']], $campo['cantidadDecimales'], ',', '.');
					}
					
					$str = $fila[$campo['campo']];
					
					// si es formato csv...
					if (strtolower ($formato) == 'csv')
					{
						// quito los saltos de linea que pueda tener el valor
						$str = ereg_replace (chr (13), "", $str);
						$str = ereg_replace (chr (10), "", $str);
						
						// verifico que no este el caracter separador de campos en el valor
						if (strpos ($str, $this->exportar_csv_separadorCampos) !== false)
						{
							$str = $this->exportar_csv_delimitadorCampos . $str . $this->exportar_csv_delimitadorCampos;
						}
					}
					
					$str = $this->strip_selected_tags ($str, "br");
					
					$str = str_ireplace ("\<br", "", $str);
					// $str= $this->limpiarEntidadesHTML($str);
					// $str= str_ireplace("Br", "", $str);
					// $str= str_ireplace("lt", "", $str);
					// echo str_ireplace("<Br>", "", $str);
					
					echo $str;
				}
				
				if (strtolower ($formato) == 'excel')
				{
					echo "</td>\n";
				}
				elseif (strtolower ($formato) == 'csv')
				{
					if ($c < $totalCamposExportar)
					{
						echo $this->exportar_csv_separadorCampos;
					}
				}
			}
			
			if (strtolower ($formato) == 'excel')
			{
				echo "    </tr>\n";
			}
		}
		
		if (strtolower ($formato) == 'excel')
		{
			echo "</table>";
		}
		
		// exit ();
	}

	/**
	 * Genera el listado ABM con las funciones de editar, nuevo y borrar (segun la configuracion).
	 * NOTA: Esta funcion solamente genera el listado, se necesita usar la funcion generarAbm() para que funcione el ABM.
	 *
	 * @param string $sql
	 *        	Query SQL personalizado para el listado. Usando este query no se usa $adicionalesSelect
	 * @param string $titulo
	 *        	Un titulo para mostrar en el encabezado del listado
	 */
	public function generarListado($sql = "", $titulo)
	{
		global $db;
		
		$agregarFormBuscar = false;
		
		// por cada campo...
		for($i = 0; $i < count ($this->campos); $i ++)
		{
			if ((! isset ($this->campos[$i]['campo'])) or $this->campos[$i]['campo'] == "")
			{
				continue;
			}
			if ($this->campos[$i]['tipo'] == "upload")
			{
				continue;
			}
			if (isset ($this->campos[$i]['noListar']))
			{
				continue;
			}
			
			if ($this->campos[$i]['exportar'] == true)
			{
				$mostrarExportar = true;
			}
			
			// para la class de ordenar por columnas
			if (((! isset ($this->campos[$i]['noListar'])) or $this->campos[$i]['noListar'] == false) and ((! isset ($this->campos[$i]['noOrdenar']) or $this->campos[$i]['noOrdenar'] == false)))
			{
				if (isset ($camposOrder) and $camposOrder != "")
				{
					$camposOrder .= "|";
				}
				else
				{
					$camposOrder = "";
				}
				
				if (isset ($this->campos[$i]['campoOrder']) and $this->campos[$i]['campoOrder'] != "")
				{
					$camposOrder .= $this->campos[$i]['campoOrder'];
				}
				else
				{
					if ($this->campos[$i]['tipo'] == 'rownum')
					{
						$camposOrder .= $this->campos[$i]['campo'];
					}
					elseif (! isset ($this->campos[$i]['joinTable']) or $this->campos[$i]['joinTable'] == '')
					{
						$camposOrder .= $this->tabla . "." . $this->campos[$i]['campo'];
					}
					else
					{
						if (isset ($this->campos[$i]['selectPersonal']) and $this->campos[$i]['selectPersonal'] != "")
						{
							$camposOrder .= $this->campos[$i]['selectPersonal'] . " AS " . $this->campos[$i]['campo'];
						}
						else
						{
							$camposOrder .= $this->campos[$i]['joinTable'] . "." . $this->campos[$i]['campo'];
						}
					}
				}
			}
			
			// campos para el select
			if ((! isset ($this->campos[$i]['noListar']) or ($this->campos[$i]['noListar'] == false)) or (isset ($this->campos[$i]['buscar']) and ($this->campos[$i]['buscar'] == true)))
			{
				
				if ((isset ($this->campos[$i]['joinTable']) and $this->campos[$i]['joinTable'] != '') and ((! isset ($this->campos[$i]['omitirJoin'])) or $this->campos[$i]['omitirJoin'] == false))
				{
					if (isset ($camposSelect) and $camposSelect != "")
					{
						$camposSelect .= ", ";
					}
					else
					{
						$camposSelect = "";
					}
					
					$tablaJoin = $this->campos[$i]['joinTable'];
					
					$tablaJoin = explode (".", $tablaJoin);
					$tablaJoin = $tablaJoin[count ($tablaJoin) - 1];
					
					if (isset ($this->campos[$i]['selectPersonal']) and $this->campos[$i]['selectPersonal'] == true)
					{
						$camposSelect .= $this->campos[$i]['selectPersonal'] . " AS " . $tablaJoin . "_" . $this->campos[$i]['campoTexto'];
					}
					else
					{
						$camposSelect .= $this->campos[$i]['joinTable'] . "." . $this->campos[$i]['campoTexto'] . " AS " . $tablaJoin . "_" . $this->campos[$i]['campoTexto'];
					}
				}
				elseif ((isset ($this->campos[$i]['joinTable']) and $this->campos[$i]['joinTable'] != '') and ($this->campos[$i]['omitirJoin'] == true))
				{
					if ($camposSelect != "")
					{
						$camposSelect .= ", ";
					}
					
					$tablaJoin = $this->campos[$i]['joinTable'];
					
					$tablaJoin = explode (".", $tablaJoin);
					$tablaJoin = $tablaJoin[count ($tablaJoin) - 1];
					
					if (isset ($this->campos[$i]['selectPersonal']) and $this->campos[$i]['selectPersonal'] == true)
					{
						$camposSelect .= $this->campos[$i]['selectPersonal'] . " AS " . $this->campos[$i]['campoTexto'];
					}
					else
					{
						$camposSelect .= $this->campos[$i]['joinTable'] . "." . $this->campos[$i]['campo'];
					}
				}
				else
				{
					if (isset ($camposSelect) and ($camposSelect != ""))
					{
						$camposSelect .= ", ";
					}
					else
					{
						$camposSelect = "";
					}
					
					if ($this->campos[$i]['tipo'] == 'rownum')
					{
						$camposSelect .= $this->campos[$i]['campo'];
					}
					else
					{
						$camposSelect .= $this->tabla . "." . $this->campos[$i]['campo'];
					}
				}
			}
			
			// para el where de buscar
			if ((isset ($this->campos[$i]['buscar'])) and ($this->campos[$i]['buscar'] == true))
			{
				$agregarFormBuscar = true;
				// }
				
				if ((isset ($_REQUEST['c_' . $this->campos[$i]['campo']]) and (trim ($_REQUEST['c_' . $this->campos[$i]['campo']]) != '')) or (isset ($_REQUEST['c_busquedaTotal']) and (trim ($_REQUEST['c_busquedaTotal']) != '')))
				{
					if (isset ($_REQUEST['c_' . $this->campos[$i]['campo']]))
					{
						$valorABuscar = $this->limpiarParaSql ($_REQUEST['c_' . $this->campos[$i]['campo']]);
						
						if (isset ($camposWhereBuscar))
						{
							$camposWhereBuscar .= " AND ";
						}
						else
						{
							$camposWhereBuscar = " ";
						}
					}
					elseif (isset ($_REQUEST['c_busquedaTotal']))
					{
						$valorABuscar = $this->limpiarParaSql ($_REQUEST['c_busquedaTotal']);
						
						if (isset ($camposWhereBuscar))
						{
							$camposWhereBuscar .= " OR ";
						}
						else
						{
							$camposWhereBuscar = " ";
						}
					}
					
					$estaBuscando = true;
					
					// quita la variable de paginado, ya que estoy buscando y no se aplica
					unset ($_REQUEST['r']);
					unset ($_POST['r']);
					unset ($_GET['r']);
					
					if (isset ($this->campos[$i]['buscarUsarCampo']) and ($this->campos[$i]['buscarUsarCampo'] != ""))
					{
						$camposWhereBuscar .= "UPPER(" . $this->campos[$i]['buscarUsarCampo'] . ")";
					}
					else
					{
						if ($this->campos[$i]['tipo'] == 'fecha')
						{
							$camposWhereBuscar .= "TO_CHAR(" . $this->tabla . "." . $this->campos[$i]['campo'] . ", 'DD/MM/YYYY')";
// 							$camposWhereBuscar .= "TO_CHAR(" . $this->tabla . "." . $this->campos[$i]['campo'] . ", 'YYYY-MM-DD')"; // @iberlot 2016/10/18 se cambia para que funcionen los nuevos parametros de busqueda
						
							$valorABuscar =  str_replace("/", "%", $valorABuscar);
							$valorABuscar =  str_replace("-", "%", $valorABuscar);
							$valorABuscar =  str_replace(" ", "%", $valorABuscar);
						}
						else
						{
							$camposWhereBuscar .= "UPPER(" . $this->tabla . "." . $this->campos[$i]['campo'] . ")";
						}
					}
					
					$camposWhereBuscar .= " ";
					
					if (isset ($this->campos[$i]['buscarOperador']) and (($this->campos[$i]['buscarOperador'] != '')) and strtolower ($this->campos[$i]['buscarOperador']) != 'like')
					{
						$camposWhereBuscar .= $this->campos[$i]['buscarOperador'] . " UPPER('" . $valorABuscar . "')";
					}
					else
					{
						$camposWhereBuscar .= "LIKE UPPER('%" . $valorABuscar . "%')";
					}
				}
			}
			// tablas para sql join
			if ((isset ($this->campos[$i]['joinTable']) and $this->campos[$i]['joinTable'] != '') and ((! isset ($this->campos[$i]['omitirJoin'])) or $this->campos[$i]['omitirJoin'] == false))
			{
				if (isset ($this->campos[$i]['joinCondition']) and $this->campos[$i]['joinCondition'] != '')
				{
					$joinCondition = $this->campos[$i]['joinCondition'];
				}
				else
				{
					$joinCondition = "INNER";
				}
				
				if (! isset ($joinSql))
				{
					$joinSql = "";
				}
				
				$joinSql .= " $joinCondition JOIN " . $this->campos[$i]['joinTable'] . " ON " . $this->tabla . '.' . $this->campos[$i]['campo'] . '=' . $this->campos[$i]['joinTable'] . '.' . $this->campos[$i]['campoValor'];
				
				if (isset ($this->campos[$i]['customCompare']) and $this->campos[$i]['customCompare'] != "")
				{
					// $joinSql .= " ".$this->campos [$i] ['customCompare'];
					$joinSql .= " AND " . $this->campos[$i]['customCompareCampo'] . " = " . $this->tabla . '.' . $this->campos[$i]['customCompareValor'];
				}
			}
		}
		
		$camposSelect .= $this->adicionalesCamposSelect;
		
		// class para ordenar por columna
		$o = new class_orderby ($this->orderByPorDefecto, $camposOrder);
		
		if ($o->getOrderBy () != "")
		{
			$orderBy = " ORDER BY " . $o->getOrderBy ();
		}
		
		if (! isset ($joinSql))
		{
			$joinSql = "";
		}
		
		if (! isset ($camposWhereBuscar))
		{
			$camposWhereBuscar = "1=1";
		}
		
		if (! isset ($orderBy))
		{
			$orderBy = "";
		}
		
		// query del select para el listado
		if ($sql == "" and $this->sqlCamposSelect == "")
		{
			if (is_array ($this->campoId))
			{
				$this->campoId = $this->convertirIdMultiple ($this->campoId, $this->tabla);
			}
			else
			{
				$this->campoId = $this->tabla . "." . $this->campoId . " AS id ";
			}
			
			$sql = "SELECT $this->campoId , $camposSelect FROM $this->tabla $joinSql $this->customJoin WHERE 1=1 AND ($camposWhereBuscar) $this->adicionalesSelect $orderBy";
		}
		else if ($this->sqlCamposSelect != "")
		{
			$sql = "SELECT " . $this->sqlCamposSelect . " FROM $this->tabla $joinSql $this->customJoin WHERE 1=1 AND ($camposWhereBuscar) $this->adicionalesSelect $orderBy";
			// $sql = $this->sqlCamposSelect;
		}
		else
		{
			$sql = $sql . " " . $orderBy;
		}
		// print_r ($sql);
		// class paginado
		$paginado = new class_paginado ();
		$paginado->registros_por_pagina = $this->registros_por_pagina;
		$paginado->str_registros = $this->textoStrRegistros;
		$paginado->str_registro = $this->textoStrRegistro;
		$paginado->str_total = $this->textoStrTotal;
		$paginado->str_ir_a = $this->textoStrIrA;
		if ($this->mostrarListado)
		{
			$result = $paginado->query ($sql);
		}
		$this->totalFilas = $paginado->total_registros;
		
		// genera el query string de variables previamente existentes
		$get = $_GET;
		unset ($get['abmsg']);
		$qsamb = http_build_query ($get);
		if ($qsamb != "")
		{
			$qsamb = "&" . $qsamb;
		}
		
		echo "<div class='mabm'>";
		
		echo "\n<script>
		        function abmBorrar(id, obj){
		            var colorAnt = obj.parentNode.parentNode.style.border;
		            obj.parentNode.parentNode.style.border = '3px solid red';";
		
		echo 'if (confirm("' . $this->textoPreguntarBorrar . '")){
		                window.location = "' . $_SERVER['PHP_SELF'] . "?" . $qsamb . "&abm_borrar=" . '" + id;
		            }
		            obj.parentNode.parentNode.style.border = colorAnt;
		            return void(0);
		        }';
		
		if ($this->colorearFilas)
		{
			echo "\n\n
					var colorAntTR;
					\n\n
					
		            function cambColTR(obj,sw){
		                if(sw){
		                    colorAntTR=obj.style.backgroundColor;";
			
			if ($this->colorearFilasDegrade == true)
			{
				echo "obj.style.background='-webkit-linear-gradient(top, $this->colorearFilasColor,$this->colorearFilasColorSecundario )';"; /* For Safari 5.1 to 6.0 */
				echo "obj.style.background='-o-linear-gradient(top, $this->colorearFilasColor,$this->colorearFilasColorSecundario )';"; /* For Opera 11.1 to 12.0 */
				echo "obj.style.background='-moz-linear-gradient(top, $this->colorearFilasColor,$this->colorearFilasColorSecundario )';"; /* For Firefox 3.6 to 15 */
				echo "obj.style.background='linear-gradient(top, $this->colorearFilasColor,$this->colorearFilasColorSecundario )';"; /* Standard syntax */
			}
			else
			{
				echo "obj.style.background='$this->colorearFilasColor';";
			}
			echo "       
		                }else{
		                    obj.style.background=colorAntTR;
		                }
		            }
		            ";
		}
		
		echo "</script>";
		
		if (isset ($_GET['abmsg']))
		{
			echo "<div class='merror'>" . urldecode ($_GET['abmsg']) . "</div> \n";
		}
		
		echo "<table class='mlistado' $this->adicionalesTableListado> \n";
		
		// titulo, botones, form buscar
		echo "<thead> \n";
		echo "<tr><th colspan='" . (count ($this->campos) + 2) . "'> \n";
		
		echo "<div class='mtitulo'>$titulo</div>";
		
		echo "<div class='mbotonera'> \n";
		echo $this->agregarABotoneraListado;
		if ($mostrarExportar and $this->mostrarListado)
		{

			$WBuscar = str_replace(" ", "|||", $camposWhereBuscar);
			$WBuscar = htmlspecialchars($WBuscar, ENT_QUOTES);
			if (in_array ('excel', $this->exportar_formatosPermitidos))
			{
				echo sprintf ($this->iconoExportarExcel, "$_SERVER[PHP_SELF]?abm_exportar=excel&buscar=$WBuscar");
			}
			if (in_array ('csv', $this->exportar_formatosPermitidos))
			{
				echo sprintf ($this->iconoExportarCsv, "$_SERVER[PHP_SELF]?abm_exportar=csv");
			}
		}
		if ($this->mostrarNuevo)
		{
			if ($this->direNuevo)
			{
				echo sprintf ($this->iconoAgregar, $this->direNuevo);
			}
			else
			{
				echo sprintf ($this->iconoAgregar, "$_SERVER[PHP_SELF]?abm_nuevo=1$qsamb");
			}
		}
		echo "</div> \n";
		
		echo "</th></tr> \n";
		
		// formulario de busqueda
		if ((isset ($agregarFormBuscar) and $this->mostrarListado) and $this->busquedaTotal == false)
		{
			echo "<tr class='mbuscar'><th colspan='" . (count ($this->campos) + 2) . "'> \n";
			echo "<fieldset><legend>$this->textoTituloFormularioBuscar</legend> \n";
			echo "<form method='POST' action='$this->formAction?$qsamb' id='formularioBusquedaAbm'> \n";
			
			$iColumna = 0;
			$maxColumnas = $this->columnasFormBuscar;
			
			foreach ($this->campos as $campo)
			{
				if (! isset ($campo['buscar']))
				{
					continue;
				}
				
				if (isset ($campo['requerido']))
				{
					$requerido = $this->chequeoInputRequerido;
				}
				else
				{
					$requerido = "";
				}
				
				if ((isset ($campo['noEditar'])) and $campo['noEditar'] == true)
				{
					$disabled = "disabled='disabled'";
				}
				else
				{
					$disabled = "";
				}
				
				if (! isset ($campo['adicionalInput']))
				{
					$campo['adicionalInput'] = "";
				}
				
				$iColumna ++;
				echo "<div>\n";
				// echo "<label>" . ($campo ['tituloBuscar'] != "" ? $campo ['tituloBuscar'] : ($campo ['tituloListado'] != "" ? $campo ['tituloListado'] : ($campo ['titulo'] != '' ? $campo ['titulo'] : $campo ['campo']))) . "</label>";
				echo "<label>" . (((isset ($campo['tituloBuscar']) and ($campo['tituloBuscar'] != "")) ? $campo['tituloBuscar'] : (isset ($campo['tituloListado']) and ($campo['tituloListado'] != "")) ? $campo['tituloListado'] : ($campo['titulo'] != '' ? $campo['titulo'] : $campo['campo']))) . "</label>";
				
				if ((isset ($campo['tipoBuscar'])) and ($campo['tipoBuscar'] != ""))
				{
					$campo['tipo'] = $campo['tipoBuscar'];
				}
				
				if ((isset ($campo['customFuncionBuscar'])) and ($campo['customFuncionBuscar'] != ""))
				{
					call_user_func_array ($campo['customFuncionBuscar'], array ());
				}
				else
				{
					switch ($campo['tipo'])
					{
						case "dbCombo" :
							echo "<select name='c_" . $campo['campo'] . "' id='c_" . $campo['campo'] . "' class='input-select'> \n";
							echo "<option value=''></option> \n";
							
							$resultdbCombo = $db->query ($campo['sqlQuery']);
							while ($filadbCombo = $db->fetch_array ($resultdbCombo))
							{
								if ((isset ($_REQUEST['c_' . $campo['campo']]) and $_REQUEST['c_' . $campo['campo']] == $filadbCombo[$campo[campoValor]]))
								{
									$sel = "selected='selected'";
								}
								else
								{
									$sel = "";
								}
								echo "<option value='" . $filadbCombo[$campo['campoValor']] . "' $sel>" . $filadbCombo[$campo['campoTexto']] . "</option> \n";
							}
							echo "</select> \n";
							break;
						
						case "combo" :
							echo "<select name='c_" . $campo['campo'] . "' id='c_" . $campo['campo'] . "' class='input-select'> \n";
							echo "<option value=''></option> \n";
							
							foreach ($campo['datos'] as $valor => $texto)
							{
								if ((isset ($_REQUEST['c_' . $campo['campo']]) and $_REQUEST['c_' . $campo['campo']] == $valor))
								{
									$sel = "selected='selected'";
								}
								else
								{
									$sel = "";
								}
								echo "<option value='$valor' $sel>$texto</option> \n";
							}
							echo "</select> \n";
							break;
						
						case "bit" :
							echo "<select name='c_" . $campo['campo'] . "' id='c_" . $campo['campo'] . "' class='input-select'> \n";
							echo "<option value=''></option> \n";
							
							if ($campo['ordenInversoBit'])
							{
								if ((isset ($_REQUEST['c_' . $campo['campo']]) and $_REQUEST['c_' . $campo['campo']] == "0"))
								{
									$sel = "selected='selected'";
								}
								else
								{
									$sel = "";
								}
								echo "<option value='0' $sel>" . ($campo['textoBitFalse'] != "" ? $campo['textoBitFalse'] : $this->textoBitFalse) . "</option> \n";
								
								if ((isset ($_REQUEST['c_' . $campo['campo']]) and $_REQUEST['c_' . $campo['campo']] == true))
								{
									$sel = "selected='selected'";
								}
								else
								{
									$sel = "";
								}
								echo "<option value='1' $sel>" . ($campo['textoBitTrue'] != "" ? $campo['textoBitTrue'] : $this->textoBitTrue) . "</option> \n";
							}
							else
							{
								
								if ((isset ($_REQUEST['c_' . $campo['campo']]) and $_REQUEST['c_' . $campo['campo']] == true))
								{
									$sel = "selected='selected'";
								}
								else
								{
									$sel = "";
								}
								echo "<option value='1' $sel>" . ($campo['textoBitTrue'] != "" ? $campo['textoBitTrue'] : $this->textoBitTrue) . "</option> \n";
								
								if ((isset ($_REQUEST['c_' . $campo['campo']]) and $_REQUEST['c_' . $campo['campo']] == "0"))
								{
									$sel = "selected='selected'";
								}
								else
								{
									$sel = "";
								}
								echo "<option value='0' $sel>" . ($campo['textoBitFalse'] != "" ? $campo['textoBitFalse'] : $this->textoBitFalse) . "</option> \n";
							}
							
							echo "</select> \n";
							break;
						
						case "fecha" :
							if (isset ($_REQUEST['c_' . $campo['campo']]))
							{
								$valor = $this->limpiarEntidadesHTML ($_REQUEST['c_' . $campo['campo']]);
							}
							
							if (strlen ($valor) > 10)
							{
								$valor = substr ($valor, 0, 10); // sacar hora:min:seg
							}
							if ($valor == '0000-00-00')
							{
								$valor = "";
							}
							$jsTmp = str_replace ('%IDCAMPO%', 'c_' . $campo['campo'], $this->jsIniciadorCamposFecha);
							$jsTmp = str_replace ('%VALOR%', $valor, $jsTmp);
							
							echo $jsTmp;
							echo "<input type='text' style='position:absolute' class='input-fecha' name='c_" . $campo['campo'] . "' id='c_" . $campo['campo'] . "' value='" . ($valor) . "'/> \n";
							echo "<input type='text' style='position:relative;top:0px;left;0px'  name='display_c_" . $campo['campo'] . "' id='display_c_" . $campo['campo'] . "' class='input-fecha " . $requerido . "' " . $disabled . " " . $campo['adicionalInput'] . " readonly='readonly'/> \n";
							break;
						
						case "moneda" :
							if (isset ($_REQUEST['c_' . $campo['campo']]))
							{
								$valor = $this->limpiarEntidadesHTML ($_REQUEST['c_' . $campo['campo']]);
							}
							else
							{
								$valor = "";
							}
							
							echo "<input type='number' class='input-text $requerido currency' step='0.01' min='0.01' max='250000000.00'  name='c_" . $campo['campo'] . "' value='" . $valor . "' /> \n";
							break;
						
						case "numero" :
							if (isset ($_REQUEST['c_' . $campo['campo']]))
							{
								$valor = $this->limpiarEntidadesHTML ($_REQUEST['c_' . $campo['campo']]);
							}
							else
							{
								$valor = "";
							}
							echo "<input type='number' class='input-text $requerido currency' step='0.01' min='0.01' max='250000000.00'  name='c_" . $campo['campo'] . "' value='" . $valor . "' /> \n";
							break;
						
						default :
							if (isset ($_REQUEST['c_' . $campo['campo']]))
							{
								$valor = $this->limpiarEntidadesHTML ($_REQUEST['c_' . $campo['campo']]);
							}
							else
							{
								$valor = "";
							}
							
							echo "<input type='text' class='input-text' name='c_" . $campo['campo'] . "' value='" . $valor . "' /> \n";
							break;
					}
				}
				
				echo "</div>";
				if ($iColumna == $maxColumnas)
				{
					$iColumna = 0;
					echo "<div class='mNuevaLinea'></div>\n";
				}
			}
			
			echo "<div class='mBotonesB'> \n";
			echo "<input type='submit' class='mBotonBuscar' value='$this->textoBuscar'/> \n";
			echo "<input type='button' class='mBotonLimpiar' value='$this->textoLimpiar' onclick='window.location=\"$this->formAction?$qsamb\"'/> \n";
			echo "</div> \n";
			echo "</form> \n";
			echo "</fieldset> \n";
			echo "</th></tr> \n";
		}
		elseif ($this->busquedaTotal == true)
		{
			$formBuscar = "<tr class='mbuscar'><th colspan='" . (count ($this->campos) + 2) . "'> \n";
			$formBuscar .= "<fieldset><legend>$this->textoTituloFormularioBuscar</legend> \n";
			$formBuscar .= "<form method='POST' action='$this->formAction?$qsamb' id='formularioBusquedaAbm'> \n";
			$formBuscar .= "<div>\n";
			$formBuscar .= "<label>Busqueda</label>";
			if (isset ($_REQUEST['c_busquedaTotal']))
			{
				$formBuscar .= "<input type='text' class='input-text' name='c_busquedaTotal' value='" . $this->limpiarEntidadesHTML ($_REQUEST['c_busquedaTotal']) . "' /> \n";
			}
			else
			{
				$formBuscar .= "<input type='text' class='input-text' name='c_busquedaTotal' value='' /> \n";
			}
			$formBuscar .= "</div>";
			//$formBuscar .= "<div class='mNuevaLinea'></div>\n";
			$formBuscar .= "<div class='mBotonesB'> \n";
			$formBuscar .= "<input type='submit' class='mBotonBuscar' value='" . $this->textoBuscar . "'/> \n";
			$formBuscar .= "<input type='button' class='mBotonLimpiar' value='" . $this->textoLimpiar . "' onclick='window.location=\"$this->formAction?$qsamb\"'/> \n";
			$formBuscar .= "</div> \n";
			$formBuscar .= "</form> \n";
			$formBuscar .= "</fieldset> \n";
			$formBuscar .= "</th></tr> \n";
			
			echo $formBuscar;
		}
		// fin formulario de busqueda
		
		if ($paginado->total_registros > 0)
		{
			// columnas del encabezado
			if ($this->mostrarEncabezadosListado)
			{
				echo '<tr class="tablesorter-headerRow"> ';
				foreach ($this->campos as $campo)
				{
					if (isset ($campo['noListar']) and ($campo['noListar'] == true))
					{
						continue;
					}
					if (isset ($campo['tipo']) and ($campo['tipo'] == "upload"))
					{
						continue;
					}
					if (isset ($campo['separador']))
					{
						continue;
					}
					
					$styleTh = "";
					
					if (isset ($campo['centrarColumna']))
					{
						$styleTh .= "text-align:center;";
					}
					if (isset ($campo['anchoColumna']) and ($campo['anchoColumna'] != ""))
					{
						$styleTh .= "width:$campo[anchoColumna];";
					}
					
					if ($campo['campo'] == "" or isset ($campo['noOrdenar']))
					{
						echo "<th " . ($styleTh != "" ? "style='$styleTh'" : "") . ">" . ((isset ($campo['tituloListado']) and $campo['tituloListado']) != "" ? $campo['tituloListado'] : ($campo['titulo'] != '' ? $campo['titulo'] : $campo['campo'])) . "</th> \n";
					}
					else
					{
						if (isset ($campo['campoOrder']) and ($campo['campoOrder'] != ""))
						{
							$campoOrder = $campo['campoOrder'];
						}
						else
						{
							if ((isset ($campo['joinTable']) and $campo['joinTable'] != '') and (@$campo['omitirJoin'] == false))
							{
								$campoOrder = $campo['joinTable'] . '.' . $campo['campoTexto'];
							}
							elseif ((isset ($campo['joinTable']) and $campo['joinTable'] != '') and ($campo['omitirJoin'] == true))
							{
								$campoOrder = $campo['joinTable'] . '.' . $campo['campo'];
							}
							else
							{
								$campoOrder = $this->tabla . '.' . $campo['campo'];
							}
						}
						
						//
						echo "<th " . ($styleTh != "" ? "style='$styleTh'" : "") . ">" . $o->linkOrderBy (((isset ($campo['tituloListado']) and $campo['tituloListado'] != "") ? $campo['tituloListado'] : ($campo['titulo'] != '' ? $campo['titulo'] : $campo['campo'])), $campoOrder) . "</th> \n";
					}
				}
				if ($this->mostrarEditar)
				{
					echo "<th class='mtituloColEditar'>$this->textoEditarListado</th> \n";
				}
				if ($this->mostrarBorrar)
				{
					echo "<th class='mtituloColBorrar'>$this->textoBorrarListado</th> \n";
				}
				echo "</tr> \n";
			} // fin columnas del encabezado
			echo "</thead> \n";
			// filas de datos
			$i = 0;
			while ($fila = $db->fetch_array ($result))
			{
				if (! isset ($rallado))
				{
					$rallado = "";
				}
				$fila = $this->limpiarEntidadesHTML ($fila);
				
				$i ++;
				$rallado = ! $rallado;
				
				echo "<tr class='rallado$rallado' ";
				if ($this->colorearFilas)
				{
					echo " onmouseover=\"cambColTR(this,1)\" onmouseout=\"cambColTR(this,0)\" ";
				}
				if (isset ($this->evalEnTagTR))
				{
					eval ($this->evalEnTagTR);
				}
				echo "> \n";
				
				foreach ($this->campos as $campo)
				{
					if (isset ($campo['noListar']) and ($campo['noListar'] == true))
					{
						continue;
					}
					
					if (isset ($campo['tipo']) and ($campo['tipo'] == "upload"))
					{
						continue;
					}
					if (isset ($campo['separador']))
					{
						continue;
					}
					
					if (isset ($campo['campoOrder']) and ($campo['campoOrder'] != ""))
					{
						$campo['campo'] = $campo['campoOrder'];
					}
					else
					{
						if (isset ($campo['joinTable']) and $campo['joinTable'] != '' and (@$campo['omitirJoin'] == false))
						{
							$tablaJoin = $campo['joinTable'];
							$tablaJoin = explode (".", $tablaJoin);
							$tablaJoin = $tablaJoin[count ($tablaJoin) - 1];
							
							$campo['campo'] = $tablaJoin . "_" . $campo['campoTexto'];
						}
					}
					
					if (isset ($campo['centrarColumna']) and ($campo['centrarColumna']))
					{
						$centradoCol = 'align="center"';
					}
					else
					{
						$centradoCol = '';
					}
					
					if ((isset ($campo['colorearValores'])) and (is_array ($campo['colorearValores'])))
					{
						$arrayValoresColores = $campo['colorearValores'];
						if (array_key_exists ($fila[$campo['campo']], $arrayValoresColores))
						{
							$spanColorear = "<span class='" . ($campo['colorearConEtiqueta'] ? "label" : "") . "' style='" . ($campo['colorearConEtiqueta'] ? "background-" : "") . "color:" . $arrayValoresColores[$fila[$campo['campo']]] . "'>";
							$spanColorearFin = "</span>";
						}
					}
					else
					{
						$spanColorear = "";
						$spanColorearFin = "";
					}
					
					if (isset ($campo['customEvalListado']) and ($campo['customEvalListado'] != ""))
					{
						/*
						 * echo "-|";
						 * print_r($campo['campo']);
						 * echo "|-";
						 */
						extract ($GLOBALS);
						$id = $fila['ID'];
						
						if (isset ($campo['campo']) and $campo['campo'] != "")
						{
							$valor = $fila[$campo['campo']];
						}
						
						if (isset ($campo['parametroUsr']))
						{
							$parametroUsr = $campo['parametroUsr'];
						}
						
						eval ($campo['customEvalListado']);
					}
					elseif (isset ($campo['customFuncionListado']) and ($campo['customFuncionListado'] != ""))
					{
						call_user_func_array ($campo['customFuncionListado'], array (
								$fila 
						));
					}
					elseif (isset ($campo['customPrintListado']) and ($campo['customPrintListado'] != ""))
					{
						if (is_array ($this->campoId))
						{
							$this->campoId = $this->convertirIdMultiple ($this->campoId, $this->tabla);
							
							$this->campoId = substr ($this->campoId, 0, - 6);
						}
						
						echo "<td $centradoCol>$spanColorear";
						$campo['customPrintListado'] = str_ireplace ('{id}', $fila['ID'], $campo['customPrintListado']);
						echo sprintf ($campo['customPrintListado'], $fila[$campo['campo']]);
						echo "$spanColorearFin</td> \n";
					}
					else
					{
						if ($campo['tipo'] == "bit")
						{
							if ($fila[$campo['campo']])
							{
								echo "<td $centradoCol>$spanColorear" . ($campo['textoBitTrue'] != '' ? $campo['textoBitTrue'] : $this->textoBitTrue) . "$spanColorearFin</td> \n";
							}
							else
							{
								echo "<td $centradoCol>$spanColorear" . ($campo['textoBitFalse'] != '' ? $campo['textoBitFalse'] : $this->textoBitFalse) . "$spanColorearFin</td> \n";
							}
						}
						// si es tipo combo le decimos que muestre el texto en vez del valor
						elseif ($campo['tipo'] == "combo")
						{
							if ($fila[$campo['campo']])
							{
								echo "<td $centradoCol>$spanColorear" . $campo['datos'][$fila[$campo['campo']]] . "$spanColorearFin</td> \n";
							}
						}
						elseif ($campo['tipo'] == "moneda")
						{
							setlocale (LC_MONETARY, 'es_AR');
							echo "<td style='text-align: right;'>$spanColorear" . money_format ('%.2n', $fila[$campo['campo']]) . "$spanColorearFin</td> \n";
						}
						elseif ($campo['tipo'] == "numero")
						{
							echo "<td style='text-align: right;'>$spanColorear" . number_format ($fila[$campo['campo']], $campo['cantidadDecimales'], ',', '.') . "$spanColorearFin</td> \n";
						}
						else
						{
							// si es tipo fecha lo formatea
							if ($campo['tipo'] == "fecha")
							{
								if ($fila[$campo['campo']] != "" and $fila[$campo['campo']] != "0000-00-00" and $fila[$campo['campo']] != "0000-00-00 00:00:00")
								{
									if (strtotime ($fila[$campo['campo']]) !== - 1)
									{
										$fila[$campo['campo']] = date ($this->formatoFechaListado, strtotime ($fila[$campo['campo']]));
									}
								}
							}
							
							if (isset ($campo['noLimpiar']) and $campo['noLimpiar'] == true)
							{
								echo "<td $centradoCol>$spanColorear" . html_entity_decode ($fila[$campo['campo']]) . "$spanColorearFin</td> \n";
							}
							else
							{
								echo "<td $centradoCol>$spanColorear" . $fila[$campo['campo']] . "$spanColorearFin</td> \n";
							}
						}
					}
				}
				
				if ($this->mostrarEditar)
				{
					echo "<td class='celdaEditar'>" . sprintf ($this->iconoEditar, $_SERVER['PHP_SELF'] . "?abm_editar=" . $fila['ID'] . $qsamb) . "</td> \n";
				}
				if ($this->mostrarBorrar)
				{
					echo "<td class='celdaBorrar'>" . sprintf ($this->iconoBorrar, "abmBorrar('" . $fila['ID'] . "', this)") . "</td> \n";
				}
				echo "</tr> \n";
			}
			
			echo "<tfoot> \n";
			echo "<tr> \n";
			echo "<th colspan='" . (count ($this->campos) + 2) . "'>";
			if (! $this->mostrarTotalRegistros)
			{
				$paginado->mostrarTotalRegistros = false;
			}
			
			$paginado->mostrar_paginado ();
			echo "</th> \n";
			echo "</tr> \n";
			echo "</tfoot> \n";
		}
		else
		{
			echo "<td colspan='" . (count ($this->campos) + 2) . "'><div class='noHayRegistros'>" . ($estaBuscando ? $this->textoNoHayRegistrosBuscando : $this->textoNoHayRegistros) . "</div></td>";
		}
		
		echo "</table> \n";
		echo "</div>";
		
		if ($this->mostrarNuevo)
		{
			// genera el query string de variables previamente existentes
			$get = $_GET;
			unset ($get['abmsg']);
			unset ($get[$o->variableOrderBy]);
			$qsamb = http_build_query ($get);
			if ($qsamb != "")
			{
				$qsamb = "&" . $qsamb;
			}
		}
	}

	/**
	 * Genera el listado ABM con las funciones de editar, nuevo y borrar (segun la configuracion)
	 *
	 * @param string $sql
	 *        	Query SQL personalizado para el listado. Usando este query no se usa $adicionalesSelect
	 * @param string $titulo
	 *        	Un titulo para mostrar en el encabezado del listado
	 */
	public function generarAbm($sql = "", $titulo)
	{
		global $db;
		
		$estado = $this->getEstadoActual ();
		
		switch ($estado)
		{
			case "listado" :
				$this->generarListado ($sql, $titulo);
				break;
			
			case "alta" :
				if (! $this->mostrarNuevo)
				{
					die ("Error"); // chequeo de seguridad, necesita estar activado mostrarNuevo
				}
				
				$this->generarFormAlta ("Nuevo");
				break;
			
			case "editar" :
				if (! $this->mostrarEditar)
				{
					die ("Error"); // chequeo de seguridad, necesita estar activado mostrarEditar
				}
				$this->generarFormModificacion ($_GET['abm_editar'], "Editar");
				break;
			
			case "dbInsert" :
				if (! $this->mostrarNuevo)
				{
					die ("Error"); // chequeo de seguridad, necesita estar activado mostrarNuevo
				}
				
				$r = $this->dbRealizarAlta ();
				
				if ($r != 0)
				{
					
					// el error 1062 es "Duplicate entry"
					if ($db->errorNro () == 1062 and $this->textoRegistroDuplicado != "")
					{
						$abmsg = "&abmsg=" . urlencode ($this->textoRegistroDuplicado);
					}
					else
					{
						$abmsg = "&abmsg=" . urlencode ($db->error ());
					}
				}
				
				unset ($_POST['abm_enviar_formulario']);
				unset ($_GET['abm_alta']);
				unset ($_GET['abmsg']);
				
				if ($r == 0 && $this->redireccionarDespuesInsert != "")
				{
					$this->redirect (sprintf ($this->redireccionarDespuesInsert, $db->insert_id ()));
				}
				else
				{
					$qsamb = http_build_query ($_GET); // conserva las variables que existian previamente
					
					$this->redirect ("$_SERVER[PHP_SELF]?$qsamb$abmsg");
				}
				
				break;
			
			case "dbUpdate" :
				if (! $this->mostrarEditar)
				{
					die ("Error"); // chequeo de seguridad, necesita estar activado mostrarEditar
				}
				
				$r = $this->dbRealizarModificacion ($_POST['abm_id']);
				if ($r != 0)
				{
					// el error 1062 es "Duplicate entry"
					if ($db->errorNro () == 1062 and $this->textoRegistroDuplicado != "")
					{
						$abmsg = "&abmsg=" . urlencode ($this->textoRegistroDuplicado);
					}
					else
					{
						$abmsg = "&abmsg=" . urlencode ($db->error ());
					}
				}
				
				unset ($_POST['abm_enviar_formulario']);
				unset ($_GET['abm_modif']);
				unset ($_GET['abmsg']);
				
				if ($r == 0 && $this->redireccionarDespuesUpdate != "")
				{
					$this->redirect (sprintf ($this->redireccionarDespuesUpdate, $_POST[$fila['ID']]));
				}
				else
				{
					$qsamb = http_build_query ($_GET); // conserva las variables que existian previamente
					$this->redirect ("$_SERVER[PHP_SELF]?$qsamb$abmsg");
				}
				
				break;
			
			case "dbDelete" :
				if (! $this->mostrarBorrar)
				{
					die ("Error"); // chequeo de seguridad, necesita estar activado mostrarBorrar
				}
				
				$r = $this->dbBorrarRegistro ($_GET['abm_borrar']);
				if ($r != 0)
				{
					$abmsg = "&abmsg=" . urlencode ($db->error ());
				}
				
				unset ($_GET['abm_borrar']);
				
				if ($r == 0 && $this->redireccionarDespuesDelete != "")
				{
					$this->redirect (sprintf ($this->redireccionarDespuesDelete, $_GET['abm_borrar']));
				}
				else
				{
					$qsamb = http_build_query ($_GET); // conserva las variables que existian previamente
					$this->redirect ("$_SERVER[PHP_SELF]?$qsamb$abmsg");
				}
				
				break;
			
			case "exportar" :
				$this->exportar_verificar ($camposWhereBuscar);
				break;
			
			default :
				$this->generarListado ($sql, $titulo);
				break;
		}
	}

	private function dbRealizarAlta()
	{
		global $db;
		
		if (! $this->formularioEnviado ())
		{
			return;
		}
		
		$_POST = $this->limpiarParaSql ($_POST);
		
		foreach ($this->campos as $campo)
		{
			if (isset ($campo['joinTable']))
			{
				$tablas[] = $campo['joinTable'];
			}
			else
			{
				$tablas[] = $this->tabla;
			}
		}
		
		$tablas = array_unique ($tablas);
		
		foreach ($tablas as $tabla)
		{
			$camposSql = "";
			$valoresSql = "";
			$sql = "";
			
			$sql = "INSERT INTO " . $tabla . "  \n";
			
			foreach ($this->campos as $campo)
			{
				if ($campo['joinTable'] == "")
				{
					$campo['joinTable'] = $this->tabla;
				}
				if ($campo['joinTable'] == $tabla)
				{
					if ($campo['campo'] === $this->campoId)
					{
						$hayID = true;
					}
					
					if ($campo['noNuevo'] == true)
					{
						continue;
					}
					if ($campo['tipo'] == '' or $campo['tipo'] == 'upload')
					{
						continue;
					}
					
					$valor = $_POST[$campo['campo']];
					
					// chequeo de campos requeridos
					if ($campo[requerido] and trim ($valor) == "")
					{
						// genera el query string de variables previamente existentes
						$get = $_GET;
						unset ($get['abmsg']);
						unset ($get['abm_alta']);
						$qsamb = http_build_query ($get);
						if ($qsamb != "")
						{
							$qsamb = "&" . $qsamb;
						}
						
						$this->redirect ("$_SERVER[PHP_SELF]?abm_nuevo=1$qsamb&abmsg=" . urlencode (sprintf ($this->textoCampoRequerido, $campo['titulo'])));
					}
					
					if ($camposSql != "")
					{
						$camposSql .= ", \n";
					}
					
					if ($valoresSql != "")
					{
						$valoresSql .= ", \n";
					}
					
					if ($campo['customFuncionValor'] != "")
					{
						$valor = call_user_func_array ($campo['customFuncionValor'], array (
								$valor 
						));
					}
					
					$camposSql .= $campo['campo'];
					
					if (trim ($valor) == '')
					{
						$valoresSql .= " NULL";
					}
					else
					{
						// Se agrega la comparativa para que en caso de sel bases de oracle haga la conversion del formato de fecha
						if ($campo['tipo'] == 'fecha' and $db->dbtype == 'oracle')
						{
							$valoresSql .= "TO_DATE('" . $valor . "', 'RRRR-MM-DD')";
						}
						else
						{
							$valoresSql .= " '" . $valor . "' ";
						}
					}
				}
			}
			
			if (strpos ($camposSql, $this->campoId) == false)
			{
				if ($camposSql != "")
				{
					$camposSql .= ", \n";
				}
				
				if ($valoresSql != "")
				{
					$valoresSql .= ", \n";
				}
				
				if ($hayID == false)
				{
					$camposSql .= $this->campoId;
					
					$idVal = $db->insert_id ($this->campoId, $this->tabla);
					$idVal = $idVal + 1;
					$valoresSql .= " '" . $idVal . "' ";
				}
			}
			
			$camposSql = trim ($camposSql, ", \n");
			$valoresSql = trim ($valoresSql, ", \n");
			
			$sql .= " (" . $camposSql . ")";
			
			$sql .= $this->adicionalesInsert;
			
			$sql .= " VALUES \n (" . $valoresSql . ")";
			
			if ($camposSql != "")
			{
				// print_r ($sql);
				// echo "<Br /><Br />";
				// exit();
				$db->query ($sql);
				
				if (isset ($this->callbackFuncInsert))
				{
					call_user_func_array ($this->callbackFuncInsert, array (
							$id,
							$this->tabla 
					));
				}
			}
		}
		return $db->errorNro ();
	}

	private function dbRealizarModificacion($id)
	{
		global $db;
		
		if (trim ($id) == '')
		{
			die ('Parametro id vacio en dbRealizarModificacion');
		}
		if (! $this->formularioEnviado ())
		{
			return;
		}
		
		$id = $this->limpiarParaSql ($id);
		
		$_POST = $this->limpiarParaSql ($_POST);
		
		foreach ($this->campos as $campo)
		{
			if (isset ($campo['joinTable']))
			{
				$tablas[] = $campo['joinTable'];
			}
			else
			{
				$tablas[] = $this->tabla;
			}
		}
		
		$tablas = array_unique ($tablas);
		
		foreach ($tablas as $tabla)
		{
			
			$sql = "";
			$camposSql = "";
			
			$sql = "UPDATE " . $tabla . " SET \n";
			// por cada campo...
			foreach ($this->campos as $campo)
			{
				if ($campo['joinTable'] == "")
				{
					$campo['joinTable'] = $this->tabla;
				}
				
				if ($campo['joinTable'] == $tabla)
				{
					if ($campo['noEditar'] or $campo['noMostrarEditar'])
					{
						continue;
					}
					if ($campo['tipo'] == '' or $campo['tipo'] == 'upload')
					{
						continue;
					}
					
					$valor = $_POST[$campo['campo']];
					
					// chequeo de campos requeridos
					if ($campo['requerido'] and trim ($valor) == "")
					{
						// genera el query string de variables previamente existentes
						$get = $_GET;
						unset ($get['abmsg']);
						unset ($get['abm_modif']);
						$qsamb = http_build_query ($get);
						if ($qsamb != "")
						{
							$qsamb = "&" . $qsamb;
						}
						
						$this->redirect ("$_SERVER[PHP_SELF]?abm_editar=$id$qsamb&abmsg=" . urlencode (sprintf ($this->textoCampoRequerido, $campo['titulo'])));
					}
					
					if ($camposSql != "")
					{
						$camposSql .= ", \n";
					}
					
					if ($campo[customFuncionValor] != "")
					{
						$valor = call_user_func_array ($campo['customFuncionValor'], array (
								$valor 
						));
					}
					
					if (trim ($valor) == '')
					{
						$camposSql .= $campo['campo'] . " = NULL";
					}
					else
					{
						if ($campo['tipo'] == 'fecha')
						{
							$camposSql .= $campo['campo'] . " = TO_DATE('" . $valor . "', 'yyyy-mm-dd')";
						}
						else
						{
							$camposSql .= $campo['campo'] . " = '" . $valor . "'";
						}
					}
				}
			}
			
			$sql .= $camposSql;
			
			if (is_array ($this->campoId))
			{
				$this->campoId = $this->convertirIdMultiple ($this->campoId, $this->tabla);
				
				$this->campoId = substr ($this->campoId, 0, - 6);
			}
			
			$sql .= $this->adicionalesUpdate . " WHERE " . $this->campoId . "='" . $id . "' " . $this->adicionalesWhereUpdate;
			
			// ////////////////////////////////
			if ($camposSql != "")
			{
				$db->query ($sql);
				
				if ($db->affected_rows () == 1)
				{
					$fueAfectado = true;
					
					// si cambio la id del registro
					if ($this->campoIdEsEditable and isset ($_POST[$this->campoId]) and $id != $_POST[$this->campoId])
					{
						$id = $_POST[$this->campoId];
					}
				}
				
				// upload
				if ($id !== false)
				{
					foreach ($this->campos as $campo)
					{
						if (! $campo['tipo'] == 'upload')
						{
							continue;
						}
						
						if (isset ($campo['uploadFunction']))
						{
							$r = call_user_func_array ($campo['uploadFunction'], array (
									$id,
									$this->tabla 
							));
						}
					}
				}
				
				if (isset ($this->callbackFuncUpdate))
				{
					call_user_func_array ($this->callbackFuncUpdate, array (
							$id,
							$this->tabla,
							$fueAfectado 
					));
				}
			}
			// ///////////////////
		}
		return $db->errorNro ();
	}

	/**
	 * Elimina un registro con un id dado
	 */
	private function dbBorrarRegistro($id)
	{
		global $db;
		
		$id = $this->limpiarParaSql ($id);
		
		if (isset ($this->callbackFuncDelete))
		{
			call_user_func_array ($this->callbackFuncDelete, array (
					$id,
					$this->tabla 
			));
		}
		
		if (is_array ($this->campoId))
		{
			$this->campoId = $this->convertirIdMultiple ($this->campoId, $this->tabla);
			
			$this->campoId = substr ($this->campoId, 0, - 6);
		}
		
		$sql = "DELETE FROM " . $this->tabla . " WHERE " . $this->campoId . "='" . $id . "' " . $this->adicionalesWhereDelete;
		
		$db->query ($sql);
		
		return $db->errorNro ();
	}

	/**
	 * Verifica el query string para ver si hay que llamar a la funcion de exportar
	 * Esta funcion debe llamarse despues de setear los valores de la classe y antes de que se envie cualquier
	 * salida al navegador, de otra manera no se podrian enviar los Headers
	 * Nota: El nombre de la funcion quedo por compatibilidad
	 */
	public function exportar_verificar($camposWhereBuscar="")
	{
		$estado = $this->getEstadoActual ();
		if ($estado == "exportar" and $this->mostrarListado)
		{
			$this->exportar ($_GET['abm_exportar'], $_GET['buscar']);
		}
	}

	/**
	 * Retorna true si el formulario fue enviado y estan disponibles los datos enviados
	 *
	 * @return boolean
	 */
	private function formularioEnviado()
	{
		if (isset ($_POST['abm_enviar_formulario']))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Convierte de un array todas las entidades HTML para que sea seguro mostrar en pantalla strings ingresados por los usuarios
	 *
	 * @example $_REQUEST = limpiarEntidadesHTML($_REQUEST);
	 *         
	 * @param
	 *        	Array o String $param Un array o un String
	 * @return Depende del parametro recibido, un array con los datos remplazados o un String
	 */
	private function limpiarEntidadesHTML($param)
	{
		global $sitio;
		
		if (is_array ($param))
		{
			// print_r("|1 - ");
			// print_r(array_map (array ($this, __FUNCTION__ ), $param));
			// print_r("<Br/>");
			// Hay veces que devuelve error aca =(
			return array_map (array (
					$this,
					__FUNCTION__ 
			), $param);
		}
		else
		{
			// print_r("|2 - ");
			// print_r(htmlentities ($param, ENT_QUOTES, $sitio->charset));
			// print_r("<Br/>");
			
			if (isset ($sitio->charset))
			{
				return htmlentities ($param, ENT_QUOTES, $sitio->charset);
			}
			else
			{
				return htmlentities ($param, ENT_QUOTES);
			}
		}
	}

	/**
	 * Escapa de un array todos los caracteres especiales de una cadena para su uso en una sentencia SQL
	 *
	 * @example $_REQUEST = limpiarParaSql($_REQUEST);
	 *         
	 * @param
	 *        	Array o String $param Un array o un String
	 * @return Depende del parametro recibido, un array con los datos remplazados o un String
	 */
	private function limpiarParaSql($param)
	{
		global $db;
		
		if (is_array ($param))
		{
			$result = array_map (array (
					$this,
					__FUNCTION__ 
			), $param);
		}
		else
		{
			$result = $db->real_escape_string ($param);
		}
		
		return $result;
		// return is_array($param) ? array_map (array ($this, __FUNCTION__ ), $param) : $db->real_escape_string ($param);
	}
	
	// eliminamos cualquier etiqueta html que pueda haber
	private function strip_selected_tags($text, $tags = array())
	{
		$args = func_get_args ();
		$text = array_shift ($args);
		$tags = func_num_args () > 2 ? array_diff ($args, array (
				$text 
		)) : (array) $tags;
		foreach ($tags as $tag)
		{
			while (preg_match ('/<' . $tag . '(|\W[^>]*)>(.*)<\/' . $tag . '>/iusU', $text, $found))
			{
				$text = str_replace ($found[0], $found[2], $text);
			}
		}
		
		return preg_replace ('/(<(' . join ('|', $tags) . ')(|\W.*)\/>)/iusU', '', $text);
	}

	/**
	 * Redirecciona a $url
	 */
	private function redirect($url)
	{
		if ($this->metodoRedirect == "header")
		{
			header ("Location:$url");
			exit ();
		}
		else
		{
			echo "<HTML><HEAD><META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=$url\"></HEAD></HTML>";
			exit ();
		}
	}
}

?>
