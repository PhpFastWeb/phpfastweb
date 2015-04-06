<?php
/**
 * Esta clase agrupa los atributos utilizados por table_data.
 * No debe ser utilizada directamente.
 */

abstract class table_data_atributes {
    
	public $row = array();
    
	/*** ATRIBUTOS PRINCIPALES ******************************************************************/
	
	/** Nombre de la tabla en la base de datos
	 * @var string
	 */ 
	public $table_name = '';
	/** Titulo a imprimir en la cabecera de la tabla
	 * @var string
	 */
	public $table_title = '';
	
	/** Titulo a imprimir al visualizar un solo registro
	 * @var string
	 */
	public $record_title = '';	
	
	
	/** Prefijo explicativo a imprimir antes de la cabecera de la tabla
	 * @var string
	 */
	public $table_caption = '';
	/** Sumario explicativo del contenido de la tabla */
	public $table_summary = '';
	/**
	 * Establece si los campos que tienen restricciones de opciones, cuando
	 * los valores que toman se salen de esas opciones, deben ser filtrados 
	 * para no ser mostrados en la vista de tabla
	 * @var array
	 */
	public $columns_options_filter_table = false; //
	/*** ATRIBUTOS DE COLUMNAS ******************************************************************/	
	/** Array con las columnas a utilizar de la tabla en la bd
	 * @var array
	 */
	public $columns = array();
	/**
	 * Array con los títulos descriptivos de cada columna
	 * @var string
	 */
	public $columns_title = array();
	/**
	 * Array con los tipos que corresponden a cada columna
	 */
  	public $columns_format = array();
  	/**
  	 * Array con las unidades a presentar tras el valor almacenado en cada columa.
  	 * (Ejemplo: X cm, valor en base de datos X)
  	 * @var array
  	 */
	public $columns_units = array();
	
	/**
	 * Array indicativo de los tipos de columna que son fecha
	 * @var unknown_type
	 * @deprecated
	 */
	//public $columns_dates = array();
	
	/**
	 * Longitud máxima a mostrar en modo tabla.
	 * No afecta a la longitud máxima almacenada en el campo
	 * @var array
	 */
	public $columns_limit_len = array();
	/**
	 * Ancho preferido a mostrar en modo tabla.
	 * No afecta a la longitud mï¿½xima almacenada en el campo
	 * @var array
	 */
	public $columns_width = array();
	/**
	 * Longitud máxima de cadena a aceptar al introducirla en el campo
	 * @var array
	 */
	public $columns_max_len = array();
	/**
	 * Alineación del contenido de cada columna en modo tabla: 'left', 'right', 'center'
	 * @var array
	 */
	public $columns_align = array();
	/**
	 * Array de opciones posibles de valores para cada columna. Estas serán a su vez otro array de cadenas.
	 * @var array
	 */
	public $columns_options = array();
	/**
	 * Propiedades especiales para los campos option, por ejemplo, ['allow_empty']=true
	 * @var unknown_type
	 */
	public $columns_options_properties = array();
	/**
	 * Activa el intento de adivinar de qué tipo es una columna si no se especifica
	 * @var boolean
	 */
	public $columns_auto_type = true;
	/** Array de restricciones en la obtencion de datos y la inserción.
	 * Al establecer $table->columns_restriction[key]=value 
	 *  solo se mostrarán datos coincidentes en row[key]=value ,
	 *  y en inserción, se insertará automáticamente row[key]=value .
	 * @var array
	 */
	public $columns_restriction = array();
	/**
	 * Array de strings con los valores por defecto que tendrá cada columna al establecer un nuevo registro
	 * @var array
	 */
	public $columns_new_value = array();
	/**
	 * Array de booleanos especificando si una columna es obligatoria a la hora de crear un nuevo registro
	 * @var array
	 */
	public $columns_required = array();
	/**
	 * Establece de forma global si todas las columnas son obligatorias.
	 * @var bool
	 */
	public $columns_required_all = false;
    
    /**
     * Show required fields info footer on editing form.
     * @var bool
     */
    public $show_required_footer = true;
	/**
	 * Array con los mensajes de ayuda descriptivos para cada campo a la hora de insertar un nuevo registro. 
	 * @var array
	 */
	public $columns_help_insert = array();
	
	/**
	 * Array de objetos tipo icolumn
	 * @var columns_collection
	 */
	public $columns_col;
	/**
	 * (No está en uso) Fuente SQL a emplear en lugar de la tabla y columnas por defecto.
	 * @var string
	 */
	public $sql_source = '';
	
	/**
	 * Establece si ya se ha inicializado el objeto, con idea de que se inicialice automáticamente la primera
	 * vez que es utilizado, pero no en las sucesivas ocasiones.
	 * @var boolean
	 */
	public $initialised = false;
	
	/**
	 * (No está en uso) Relaciones de la tabla actual con otras tablas de la base de dato, navegables por
	 * interfaz de usuario desde aquí.
	 * @var array
	 */
	public $relations = null;
	
	/**
	 * (no está en uso) Definiciones de campos para nuevo formato para columnas.
	 * @var array.
	 */
	public $field_definitions = null;
	
	//---- Primary keys -------------------------------------------------------------------------
	/**
	 * Conjunto de definición de clave o claves primarias
	 * @var key_set
	 */
	public $key_set;

	/*** ATRIBUTOS DE DATOS *********************************************************************/
	
	/** Objeto base de datos a utilizar
	 * @var database  
	 * @deprecated
	 * */
	public $db = null;
	/** Recurso de la db con el resultado del query
	 * @var resource
	 */
	public $table_data = null;
	/** Último query utilizado, para consulta interna
	 * @var string
	 */
	public $sql = '';
	
	
	/*** ATRIBUTOS DE ASPECTO ********************************************************************/
	/**
	 * Objeto tema a utilizar
	 * @var theme_config
	 */
	public $theme;
	

	
	/*** ATRIBUTOS SOBRE EL ORDEN DE LAS COLUMNAS ************************************************/
	/**
	 * Columna por defecto para ordenar en modo tabla
	 * @var string
	 */
	public $order_default_column = '';
	/**
	 * Orden por defecto para el modo tabla: 'ASC' o 'DESC'
	 * @var unknown_type
	 */
	public $order_default_order = "ASC";
	/**
	 * (Hacer privado) Columna por la que se ordena actualmente
	 * @var string
	 */
	public $order_column = '';
	/**
	 * (Hacer privado) Orden actual
	 * @var string
	 */	
	public $order_order = '';
	/**
	 * (Hacer privado) instruccion SQL de ordenación utilizada
	 * @var string
	 */
	public $sql_order = '';
	
	/**
	 * Default order given by SQL instrucction
	 */
	
	public $order_default_sql;
	
	/**
	 * Columnas que no mostrarán control para reordenar
	 * @var array
	 */
	public $columns_no_order = array(); //Estas columnas no muestran enlace a ordenar

	/*** Atributos para cuando se utiliza como diálogo de selección */
	public $select_dialog = false;
	public $select_dialog_js_function = 'select_item';
	
	/*** ATRIBUTOS SOBRE LA PAGINACION ***********************************************************/
	/**
	 * Establece si se deben paginar los resultados.
	 * Advertencia: si se pone a false y hay muchos resultados, el servidor de bd, el servidor web, y el navegador pueden colapsarse 
	 * durante el tiempo de ejecuciï¿½n mï¿½xima (30 segundos por defecto).
	 *
	 * @var bool
	 */
	/**
	 * Utilizar paginación
	 * @var bool
	 */
	public $pag = true;
	/**
	 * Items por página por defecto
	 * @var integer
	 */
	public $pag_items_pag = 20;
	/**
	 * (Hacer privado) Número de items recontado actualmente en la página
	 * @var integer
	 */
	public $pag_items_total = -1;
	/**
	 * (No terminado) Establece si se deben guardar en cache el número de items que existe en la tabla, 
	 * para evitar repetidos accesos que consuman tiempo, en contra de perder precisión en 
	 * la forma de mostrar el número de registros de la tabla.
	 * @var bool
	 */	
	public $pag_cache_items_total = true;
	/**
	 * Establece si se debe intentar calcular el número total de registros de la tabla.
	 * En caso de existir muchos registros, es mejor ponerlo a false, a la vez que se activa 
	 * la variable $this->pag. De esta forma se consigue una paginación sin intentar nunca averiguar
	 * cual es el límite de la tabla.
	 * @var bool
	 */
	public $pag_read_items_total = true;
	
	/** 
	 * (Hacer privado) Fila inicial
	 * @var int
	 */
	public $row_ini = 0;
	/** 
	 * (Hacer privado) Fila final
	 * @var int
	 */	
	public $row_end = -1;
	/** 
	 * (Hacer privado) Página inicial
	 * @var int
	 */	
	public $pag_ini = '';
	/** 
	 * (Hacer privado) Página final
	 * @var int
	 */	
	public $pag_end = '';
    
    /**
     * Show allways page footer, even if there's only one page
     * @var boolean
     */
      
    public $pag_show_allways = false;
    
    /**
     * Name to use when showing the count of rows in pagination
     * @var string
     */
    public $pag_row_count_name = 'filas';

	/*** ATRIBUTOS DE FILTROS ***************************************************************/
	/**
	 * Establece una restricción que se incluirá en todas las clausulas WHERE de las sentencias en sql 
	 * para obtener, paginar y filtrar los datos.
	 * Si es una restricción compuesta, debe estar convenientemente rodeada de paréntesis, para que al 
	 * añadir condiciones adicionales con AND y OR la semántica funcione correctamente.
	 * @var string
	 */
	public $sql_restriction='';
	/**
	 * (Hacer privado) SQL generado para satisfacer condiciones OPTIONS
	 * @var string
	 */
	public $sql_options = '';
	/**
	 * (Hacer privado) SQL generado como WHERE
	 * @var string
	 */
	public $sql_where = '';
    
    /**
     * Condition to be anexed with AND to any other WHERE condition
     * Defined ad hoc by user on table definition (like a table view).
     */
    public $sql_where_forced = '';
	/** Establece si se mostrarán los filtros.
	 * Si se deja como null, se establece automáticamente si detecta filtros configurados en init_config.
	 * Si se establece como false, no se emplearán filtros.
	 * @var array
	 */
	public $filter = null;
	/**
	 * Array de columnas y valores por los que se filtrará por defecto si no se especifica ningún filtro,
	 * ni se quita éste filtro por defecto.
	 * @var array
	 */
	public $filter_default = array();
	/**
	 * Array de nombres de campos a filtrar con filtro igual.
	 * @var array
	 */
	public $filter_fields = Array();
	/**
	 * Array de nombres de campos a filtrar con un rango.
	 * @var array
	 */
	public $filter_ranges = Array();
	/**
	 * Array de nombres de campos a filtrar con búsqueda.
	 * @var array
	 */
	public $filter_searchs = Array();
	/**
	 * (Antiguo) Array de nombres de campos a filtrar con booleano/checkbox.
	 * Usar en su lugar directamente filter_fields
	 * @deprecated
	 * @var array
	 */
	public $filter_bools = Array();
	/**
	 * Not implemented. Shoul be an array of filter objects
	 * @var Array
	 */
	public $filters = Array();
	/**
	 * (Hacer privado) Datos de los filtros procesados
	 * @var Array
	 */
	
	public $filters_caption = array();
	
	public $filters_data = array();
	/**
	 * (Hacer privado) Url para conservar los filtros en un siguiente click en la tabla.
	 * @var Array
	 */
	public $filters_url = '';
	/**
	 * Contiene la secuencia SQL generada a partir de los filtros, y que será utilizada en todas las consultas.
	 * @var string
	 */
	public $sql_filter = '';
	/**
	 * Contiene la secuencia SQL generada a partir de la búsqueda
	 * @var string
	 */	
	public $sql_search = '';
	
	/**
	 * Establece si se utilizan los últimos filtros cuando no se haya especificado ninguno.
	 * @var bool
	 */
	public $filter_persist = false;
    
    /**
     * Sets if, when persistent filters are applied, when new record command is invoked, it uses filter data for default columns
     */
     public $filter_persist_use_for_new = false;
     
	/**
	 * Conector por defecto al emplear varios términos en una búsqueda, puede ser 'OR' o 'AND'
	 * @var string
	 */
	public $search_connector = 'OR';
	/**
	 * Nombre de la variable a utilizar en la búsqueda, para poder hacerla compatible con otros sistemas
	 * @var string
	 */
	public $search_varname = 'q';
	/**
	 * Método por defecto de las búsquedas, puede ser 'get' o 'post'.
	 * Si se hace por POST, es posible crear un marcado de una búsqueda en concreto.
	 * @var string
	 */
	public $search_method = 'get';

	public $search_fields = '*';
	
	public $search_string = '';
	
	
	/*** Atributos de lista ***/
	public $can_edit_use = false;
	public $list_columns_collapsed = array(); /* Por defecto, todas menos la 1ï¿½ que no sea PK */
	public $list_columns_expanded  = array(); /* Por defecto, la 1ï¿½ que no sea PK */
	public $list_view_details = true;
	public $list_fields_resume = array();
	
	/*** Atributos de edición de campos ****/
	/**
	 * Especifica si es posible editar la clave primaria en un registro existente
	 * @var bool
	 */
	public $edit_pk = false;
	/**
	 * Especifica si es posible establecer la clave primaria al crear un registro
	 * @var unknown_type
	 */
	public $insert_pk = false;
	/**
	 * Especifica qué columnas son no editables.
	 * Aunque estén definidas no editables, es posible establecerles valores y filtros por defecto.
	 * @var array
	 */
	public $edit_non_editable = array();
	
	
	public $discard_changes_show_button = true;
    
	public $discard_changes_message = "<< Descartar cambios <<";

	public $send_button_mesage = "Guardar";
	
	public $show_row_after_save = false;
	
	public $save_message = "Informaci&oacute;n guardada";
	
	/**
	 * If set to true, an email is sent afterwards each row insertion
	 */
	public $send_email_after_save = false;
	/**
	 * Email destination to be notified after row insertion if activated
	 */
	public $send_email_after_save_destination = "nobody@example.com";
	/**
	 * Email origin for the notifications
	 */
	public $send_email_after_save_origin = "robot@example.com";	
	/**
	 * URL to jump to afterwards row insertion
	 */
	public $save_continue_url = '';
	/**
	 * If set to true, no confirmation message is shown after insertion,
	 * and the user is taken to save_continue_url inmidiately afer it.
	 * There it may be shown a floating message indicating that data is
	 * saved, if such component is added to page layout.
	 */
	public $save_continue_immediately = false;
	/**
	 * If set to true, the ID of the current inserted row is appended to
	 * the save url enclosed by directory slashes "/"
	 */
	public $save_continue_append_id = false;
	/*** Atributos de subida de ficheros ****/
	
	/**
	 * Directorio destino de los ficheros enviados por campos tipo 'file'.
	 * También está siendo utilizada para campos tipo 'image'.
	 * Si no se especifica, se tomará la carpeta URL BASE + '/upload/' + NOMBRE TABLA (en minúsculas)
	 * 
	 * @var string
	 */
	public $upload_dir = '';
	public $upload_size_limit = -1;
	public $upload_dir_size_limit = -1;
	public $upload_allow_overwrite = true;
	public $upload_use_redirect = true;
	public $upload_use_pk = true;
	public $upload_detect_old_non_pk = true;
	
	
	/*** Atributos d subida de imágenes ****/
	public $image_dir = '';
	public $image_dir_upload = '';
	public $image_thumb_cache_dir = 'images_thumb_cache/';
//	var $image_thumb_max_width  = 100;
//	var $image_thumb_max_height = 100;
//	var $image_size_limit = -1;
//	var $image_max_width  = -1;
//	var $image_max_height = -1;


    /*--------------------------------------------------------------------------------------------------*/
    
    public $only_one_new = false;
    
    public $only_one_new_sesion_id = 'only_one_new';
    
    public $only_one_new_message = 'La información ya fue suministrada desde este ordenador. No es necesario que la suministre de nuevo.';

    public $only_one_new_redirect = ''; 
    

	/*--------------------------------------------------------------------------------------------------*/
	
	
	public $action_command = '_command';
	
	
	/*--------------------------------------------------------------------------------------------------*/
	/**
	 * Objeto para establecer el usuario actual. Se utiliza para la moderación.
	 * @var user
	 */
	public $user = null;
	
	//--------------------------------------------------------------------------------------------

	/** 
	 * (Por cambiar) Modo de vista por defecto para varios datos, puede ser 'table' o 'list'
	 * @var string
	 */
	public $view_mode = 'table';
	/** 
	 * Indica si se mostraran enlaces de control (edición, inserción, borrado, etc), en la cabecera 
	 * y en cada celda en general.
	 * @var bool
	 */
	public $print_actions = false;	
	public $print_action_new = false;
	public $submit_links = false;
	public $submit_form_name = '';
	/**
	 * String name of command to be used by default if none specified
	 * @var string
	 */
	public $default_command = 'table';
	
    /**
     * String name of default command to be used on click on row
     * @var string
     */
	public $default_click_command = '';
	
	/**
	 * (No se usa) Permitir la edición en linea para el modo tabla
	 * @var bool
	 */
	public $inline_edit_use = false; //Imprimir o no el formulario de la tabla de datos
	/**
	 * (No se usa) Columnas con inputs de edición en la propia tabla 
	 * @var unknown_type
	 */
	public $inline_edit = array(); 
	

	/**
	 * Columnas a visualizar en modo tabla
	 * @var array
	 */
	public $columns_table_view = array();
	
	/**
	 * Columnas a visualizar en modo select
	 * @var array
	 */
	public $columns_select_view = array();
	
	/**
	 * Permisos correspondientes a cada acción posible
	 * @var array
	 */
	public $permissions = array();
	
	
	public $columns_command = array();
	
    /**
     * Ordered array of commands to be enabled on global table or on individual rows
     * @var array of icommands
     */ 
    public $commands = array();
    
    /**
     * On init_config, stores de key string name of the command that is going to be executed
     * @var string
     */
	public $command_name = '';
    
	public $use_commands_icons = true;
	public $use_print_record = true;
 
    
    /**
     * If defined, when an error for unable to delete a record that has a foreign key with related records on other tables,
     * instead of showing generic database message, this customized message is shown.
     * @var string
     */
    public $error_unable_delete_cascade_msg = "";
     /**
     * If defined, when an error for unable to delete a record that has a foreign key with related records on other tables,
     * this heading text is shown on error, instead of original heading.
     * @var string
     */   
    public $error_unable_delete_cascade_heading = "";
    
    /**
     * Saves in database in a log table all commands activations with related information
     */
    public $log_commands = false;
	
    /**
     * Tries to set autocomplete on or off.
     * This is very browser dependent.
     */
    public $autocomplete = true; 
    
}
