<?php
require_once(dirname(__FILE__)."/table_data_atributes.class.php");

class table_data extends table_data_atributes {

    public $inline_new_record = false;
    
    public $sql_from = '';
	// ------------------------------------------------------------------------------------
	// ------------------------------------------------------------------------------------

	function fetch_data() {
		if ( ! $this->initialised ) {
			$this->init_config();
		}
		if ( ! $this->initialised ) { throw new ExceptionDeveloper('table subclass doesn\'t call parent::init_config()'); }
		$this->process_sql();

		$this->table_data = website::$database->execute_query($this->sql." ".$this->sql_where." ".$this->sql_group_by, $this->row_end - $this->row_ini , $this->row_ini, $this->sql_order );
		if ( $this->table_data===false ) {
			throw new ExceptionDeveloper( website::$database->get_last_error() );
		}
		//Procesamos incluir información en la cabecera
		$this->filters->process_caption();
	}
	public function get_table_from_sql() {
		if ($this->sql_from != '') {
			return $this->sql_from;
		} else {			
			return website::$database->escape_table_name($this->table_name);
		}
	}
	
	function process_sql() {
		if ( ! $this->initialised ) {
			$this->init_config();
			if ( ! $this->initialised ) { throw new ExceptionDeveloper('table subclass doesn\'t call parent::init_config()'); }
		}

        $this->sql = $this->process_sql_select() ." FROM ".$this->get_table_from_sql();

		//Procesamos los filtros de la consulta
		$this->filters->process_filter();
		//Procesamos las búsquedas
		$this->filters->process_search();
		//Añadimos a los filtros las restricciones extra
		$this->filters->process_restrictions();
		//Procesamos tipos de opciones
		$this->filters->process_options();

		//Concatenamos todas las condiciones
        $sql_where = '';
		$sql_where = website::$database->concat_sql_restrictions($sql_where,$this->sql_filter);
		$sql_where = website::$database->concat_sql_restrictions($sql_where,$this->sql_options);
		$sql_where = website::$database->concat_sql_restrictions($sql_where,$this->sql_restriction);
		$sql_where = website::$database->concat_sql_restrictions($sql_where,$this->sql_search);
        $sql_where = website::$database->concat_sql_restrictions($sql_where,$this->sql_where_forced);
		
		if ( $sql_where != '' ) $sql_where = "WHERE $sql_where";
		
		$this->sql_where = $sql_where;
		
		//Procesamos el orden de la consulta
		$this->process_column_order();

		//Procesamos la paginación
		$this->process_pagination($sql_where);
        
        $this->process_group_by();
	}
    public $sql_group_by = '';
    public function process_group_by() {
        //Añadimos columnas a group by y comprobamos su necesidad
        $needs_group_by = false;
        $t = ''; $sql_columns = '';
		foreach ($this->columns_col as $col) {
            if ( $col instanceof column_count ) {
                $needs_group_by = true;
            } else {
    			$t = $col->get_db_columns_str();
    			if ($t != "" ) {
    				$sql_columns .= sql_str::get_instance(
                        '{#0}.{#1}',
                        $col->get_table_name(),
                        $col->get_original_column_name())
                            ->__toString().",";
    			}
            }
		}
        
        //If group by is not needed, we end now
        if ( ! $needs_group_by ) {
            $this->sql_group_by='';
            return '';
        }
		//Añadimos las claves primarias que no estuvieran como columnas
		$extra_cols = array();
		$extra_cols = array_diff($this->key_set->primary_keys,$this->columns);
		foreach ($extra_cols as $colname) {
			$sql_columns .= website::$database->escape_column_name($colname).",";
		}
        //Remove final comma
        if ( $sql_columns != '' ) $sql_columns = substr($sql_columns,0,-1);
        
        $this->sql_group_by = ' GROUP BY '.$sql_columns.' ';
        
        return $this->sql_group_by;
    }
    
	function db_count($sql_where_aditional='') {        
		$this->filters->process_restrictions();
        $this->process_group_by();
        $sql_where = website::$database->concat_sql_restrictions($sql_where_aditional,str_ireplace('where ','',$this->sql_where));        
		$sql_where = website::$database->concat_sql_restrictions($sql_where,$this->sql_restriction);
		$result = website::$database->count( $this->get_table_from_sql(),$sql_where, $this->sql_group_by, $this );
		return $result;
	}


	/* ------------------------------------------------------------------------- */
	/* Funciones a redefinir en objetos hijo para personalizar el funcionamiento */
	/* ------------------------------------------------------------------------- */
    function process_sql_select() {
        $sql_columns = '';
        //Añadimos columnas
		foreach ($this->columns_col as $col) {
			$t = $col->get_db_columns_str();
			if ($t != "" ) {
				$sql_columns .= $t.",";
			}
		}
		//Añadimos las claves primarias que no estuvieran como columnas
		$extra_cols = array();
		$extra_cols = array_diff($this->key_set->primary_keys,$this->columns);
		foreach ($extra_cols as $colname) {
			$sql_columns .= website::$database->escape_column_name($colname).",";
		}
		$sql_columns = substr($sql_columns,0,-1);
		
		return "SELECT " . $sql_columns;
    }
    
	function process_column_order() {
		global $_GET, $_POST;

		$this->sql_order = '';
		if (isset($_GET['order_column']) && isset($_GET['order_order'])) {
			//We now permit expression in order, like 'length(cno)' or '(cno + 0)'
			//in_array($_GET['order_column'],$this->columns)
			if ( ($_GET['order_order'] == "ASC" || $_GET['order_order'] =="DESC" ) ) {
				//TODO
				$this->order_column = $_GET['order_column'];
				$this->order_order  = $_GET['order_order'];
				$this->sql_order = "ORDER BY $this->order_column $this->order_order";
			} else {
				die("**Error al procesar parámetro de URL de ordenado. \$_GET['order_column']=$_GET[order_column] , \$_GET['order_order']=$_GET[order_order]<br />\r\n[".__FILE__.":".__LINE__."]");
			}
		} else {
			
			if (isset($this->order_default_sql)) {
				$this->sql_order = "ORDER BY ".$this->order_default_sql;
				//TODO: Sanitizar SQL
				return;
			}
			
			//$hash = substr("/","_",$_SERVER['PHP_SELF']);
			if ($this->filter_persist == true) {
				if ( ! isset($_GET['FILTER_REMOVE_']) &&
				     isset($_SESSION) 
					&& isset($_SESSION['order_persist']) 
					&& isset($_SESSION['order_persist'][$_SERVER['PHP_SELF']]) 
					&& isset($_SESSION['order_persist'][$_SERVER['PHP_SELF']]['order_column']) 
					&& isset($_SESSION['order_persist'][$_SERVER['PHP_SELF']]['order_order'])
				) {
					$order = $_SESSION['order_persist'][$_SERVER['PHP_SELF']]['order_order'];
					$or_col = $_SESSION['order_persist'][$_SERVER['PHP_SELF']]['order_column'];
					if (in_array($or_col,$this->columns) && ($order == 'ASC' || $order == 'DESC')) {
						$this->order_column = $or_col;
						$this->order_order = $order;
						
						$this->sql_order = "ORDER BY ";
						$this->sql_order .= website::$database->escape_column_name($this->order_column);
						$this->sql_order .= " ".$this->order_order;
					}
				}
			} else if ($this->order_default_column == '' ) {
				if ( $this->has_primary_keys() ) {
					$this->order_column = $this->key_set->primary_keys[0];
					//TODO: Ordenar por más de una columna
					$this->order_order = $this->order_default_order;
					$this->sql_order = "ORDER BY $this->order_column $this->order_order";
				}
			} else {
				$this->order_column = $this->order_default_column ;
				$this->order_order = $this->order_default_order;
				$this->sql_order = "ORDER BY $this->order_column $this->order_order";
			}
		}
		//$hash = substr("/","_",$_SERVER['PHP_SELF']);
		if ($this->filter_persist==true) {
			$_SESSION['order_persist'][$_SERVER['PHP_SELF']]['order_column'] = $this->order_column;
			$_SESSION['order_persist'][$_SERVER['PHP_SELF']]['order_order'] = $this->order_order;
		}
		//TODO: Validar que order_order es solo ASC o DESC, y order_column está formateado
		return $this->sql_order;
	
	}

    //-------------------------------------------------------------------------------
    /**
     * @return string
     */
    public function get_ddd() {
		$this->init_config();
		
		$result = '<ul>';
		foreach ($this->columns_col as $col) {
			if ( $col->get_visible() ) {
				$result .= '<li>'.$col->get_column_name().' : '.$col->get_short_title();
				
                if ($col instanceof column_select ) {
					$result .= '<ul>';
					$options = $col->get_options();
					foreach ( $options as $key => $value) {
						$result .= "<li>$key => $value</li>";
					}
					$result .= '</ul>';
				}
                
                if ( $col instanceof column_number && $col->get_unit() != '' ) {
                    $result .= "<ul><li>(unidad: ".$col->get_unit().")</li></ul>";
                }
				$result .= '</li>';
			}
		}
		$result .= '</ul>';
		
		return $result;
	}
	//-------------------------------------------------------------------------------

	
	function get_row_ini($pag) {
		return ( ( $pag - 1 ) * $this->pag_items_pag );
	}
	function get_row_end( $pag ) {
		$result = ( $pag * $this->pag_items_pag ) - 1;
		//REVISAR
		if ($result > $this->pag_items_total ) { $result = $this->pag_items_total - 1; }
		return $result;
	}
	

	function process_pagination($sql_where) {
		if ( ! $this->pag ) {
			$this->row_ini = -1;
			$this->row_end = -1;
			return '';
		}

		//Procesamos la paginación
		if ( isset($_GET['pag_items_pag']) ) {
			$this->pag_items_pag = $_GET['pag_items_pag'];
			//if (isset($_GET['pag_jump'])) unset($_GET['pag_jump']);
			//if (isset($_GET['row_ini'])) unset($_GET['row_ini']);
			//if (isset($_GET['row_iniend'])) unset($_GET['row_iniend']);
		} else if ( isset($_POST['pag_items_pag']) ) {
			$this->pag_items_pag = $_POST['pag_items_pag'];
			if (isset($_POST['pag_jump'])) unset($_POST['pag_jump']);
			if (isset($_GET['row_ini'])) unset($_GET['row_ini']);
			if (isset($_GET['row_iniend'])) unset($_GET['row_iniend']);			
		}
		
		//Si se ha cambiado el número de items por página, esto se ignoraría
		if ( isset($_GET['row_ini']) ) {
			$this->row_ini = $_GET['row_ini'];
		}
		if ( isset($_GET['row_end']) ) {
			$this->row_end = $_GET['row_end'];
		}

		if ( $this->row_end < 0 ) {
			$this->row_end = $this->row_ini + $this->pag_items_pag - 1;
		}
		if ( ($this->row_end - $this->row_ini + 1) > $this->pag_items_pag ) {
			$this->row_end = $this->row_ini + $this->pag_items_pag - 1;
		}

		if ( $this->pag_read_items_total ) {
			//Si aun no lo sabemos, averiguamos cuantas filas hay en total
			if ($this->pag_items_total == -1 ) {
				if ( $this->pag_cache_items_total && isset($_GET['items_total']) ) {
					$this->pag_items_total = $_GET['items_total'];
				} else {
					$this->pag_items_total = $this->db_count();
				}
			}
			//Averiguamos cual es la página actual
			//Si se especificó un salto de página, lo hacemos e ignoramos el row_ini, pero solo si no
			if (isset($_POST['pag_jump'])) {
				$this->pag_ini = $_POST['pag_jump'];
				$this->row_ini = ( $this->pag_ini -1 ) * $this->pag_items_pag;
			} else {
				$this->pag_ini = round( $this->row_ini / $this->pag_items_pag , 0 ) + 1;
			}

			//this->row_end ya ha sido establecido, no?
			$this->row_end = min( ( $this->row_ini + $this->pag_items_pag -1 ) , ( $this->pag_items_total - 1 ) );
			//Calculamos la página final
			if ( $this->pag_items_total >= 0 ) {
				$this->pag_end = ceil( $this->pag_items_total / $this->pag_items_pag );
			} else {
				$this->pag_end = '';
			}

		} else {

			//No averiguamos cuántas filas hay en total
			$this->pag_items_total = -1;
			if ( $this->row_ini == -1 ) $this->row_ini = 0;
			$this->row_end = $this->row_ini + $this->pag_items_pag - 1;
			$this->pag_ini = round( $this->row_ini / $this->pag_items_pag , 0 ) + 1;
			$this->pag_end = '';

		}
		
		return true;
	}
	
	// ------------------------------------------------------------------------------------
	// ------------------------------------------------------------------------------------
	function assert_column($possible_column) {
		if ( ! in_array($possible_column, $this->columns) ) {
			throw new ExceptionDeveloper("Error al procesar parámetro: "+$possible_column);
		}
	}
	// ------------------------------------------------------------------------------------
	function get_table_name() {
		if ($this->table_name != '' ) return $this->table_name;
		return get_class($this);	
	}
	function set_table_name($table_name) {
		$this->table_name = $table_name;
	}
	public function get_table_title() {
		if ($this->table_title != '' ) return $this->table_title;
        $result = $this->get_table_name();
        $result = str_replace("_"," ",$result);
		$result = ucfirst(strtolower($result));
        //TODO: Auto naming
		return $result;
	}
	public function set_table_title($table_title) {
		$this->table_title = $table_title;
	}
	public function get_record_title() {
		if ( $this->record_title != '' ) return $this->record_title;
		return $this->get_table_title();	
	}
	public function set_record_title($record_title) {
		$this->record_title = $record_title;
	}
    /**
     * Returns icolumn instance registered with that name
     * @returns icolumn
     */
    public function get($col_name) {
        if ( ! $this->initialised ) $this->init_config();
        if ( ! isset( $this->columns_col[$col_name] ) ) throw new ExceptionDeveloper("Index $col_name not found in columns collection");
        return $this->columns_col[$col_name];
    }
    /**
     * Adds a column to the table on given column name
     * Returns a reference to said column, so it can be used to chain setters.
     * @return icolumn reference to passed icolumn
     */
    public function set($col_name, icolumn $column) {
        $this->columns_col[$col_name] = $column;
        return $this->get($col_name);
    }
	//-------------------------------------------------------------------------------------
    function init_webobject() {
        website::$current_page->add_web_object(new table_data_webobject());
    }
	function init_config() {
		if ($this->initialised) return;

        $this->init_webobject();
        
		//key_set
		if ( $this->key_set == null ) {
			$this->key_set = new key_set();
		}
		
		//If not set, table name is same as class name
		if ($this->table_name == '') {
			$this->table_name = get_class($this);
		}

		//If table data is obtained by specified SQL, we retrieve it
		if ($this->sql_source != '' ) {
			throw new ExceptionDeveloper('**Error: Not implemented $this->sql_source');
			//$this->fetch_data();
		}
		
		//If no command specified, we add default ones
		if (count($this->commands)==0) {
			commands_factory::add_all_commands_to($this);
			$this->default_click_command=commands_factory::get_default_click_command_name();
		}

       
        
		$this->process_config_array($this->columns_limit_len,-1);
		$this->process_config_array($this->columns_max_len,-1);
		$this->process_config_array($this->columns_width,'');
		$this->process_config_array($this->columns_unit,'');
        
        

		//Types for columns
		//Auto
		if ( $this->columns_auto_type && count( $this->columns_format < $this->columns ) ) {
			$col_auto_type = new columns_auto_type();
			$col_auto_type->set_all_columns_types($this);
		}
        
        

		//Auto select from format to options
		foreach($this->columns as $col) {
			if (isset($this->columns_format[$col]) && is_array($this->columns_format[$col])) {
				if (isset($this->columns_options[$col])) throw new ExceptionDeveloper('Column options specification duplicated '.$col);
				$this->columns_options[$col] = $this->columns_format[$col];
				$this->columns_format[$col] = 'select';
			}
		}
        
		//Auto select from options to format
		foreach($this->columns_options as $col => $value) {
			if( ! isset($this->columns_format[$col]) || $this->columns_format[$col]=='text' ) {
				$this->columns_format[$col] = 'select';
			}
		}
        
		//Process rest of formats
		$this->process_config_array($this->columns_format ,'text',
			array('text','date','time','datetime','textarea','checkbox','number',
				  'select','email','url','password','file','image','hidden','readonly')
			);

        $this->process_config_array($this->columns_title);      

        
        
		$auto_dic = new auto_dictionary();

        $auto_dic->process_table_data($this);

		if ($this->table_summary == '' ) {
			$this->table_summary = $this->table_title;
		}

		//Filters setup
		if ( is_null($this->filter) ) {
			//If not set, we enable all
			if (count($this->filter_fields)>0 || count($this->filter_ranges)>0 || count($this->filter_searchs)>0) {
				$this->filter = true;
			} else {
				$this->filter = false;
			}
		}
		$this->filters = new filter();
		$this->filters->table = $this;

		//TODO: Deprecate
		if ( $this->print_actions == true ) {
			$this->permissions['new'] = true;
			$this->permissions['edit'] = true;
			$this->permissions['print'] = true;
			$this->permissions['delete'] = true;
			$this->permissions['view'] = true;
			$this->permissions['list'] = true;
		} elseif ( count($this->permissions) > 1 ) {
			$this->print_actions = true;
		}


		//Upload folder setup
		if ( $this->upload_dir == '' ) {
			$this->upload_dir = website::$base_dir."/__upload/". strtolower($this->table_name)."/";
		}
		if ( $this->image_dir_upload == '' ) {
			$this->image_dir_upload = website::$base_url."/__upload/". strtolower($this->table_name)."/";
		}
		if ( $this->image_dir == '' ) {
			$this->image_dir = website::$base_url."/__upload/". strtolower($this->table_name)."/";
		}
		

		//Columns_col setup
		$this->process_columns_col();
			
		$this->process_control_group();
		
		$this->process_command_name();
		
		//We mark the object as initialised so no further calls sets it up again
		$this->initialised = true;

	}
    public function get_command_name() {
        if ($this->command_name == '') {
            $this->process_command_name();
            if ($this->command_name == '') $this->command_name=$this->default_command;
        }
        return $this->command_name;
    }
	public function process_command_name() {
	   //Establecemos el comando que se está lanzando
		if (isset($_GET[acommand::get_command_label()])) {
			//Comando tipo GET
			$this->command_name = $_GET[acommand::get_command_label()];
			$this->command_type = 'get';
		} else if (isset($_POST[acommand::get_command_label()])) {
			$this->command_name = $_POST[acommand::get_command_label()];
			$this->command_type = 'post';
            //TODO: Use header redirect for POST processing, so BACK can't be used to repost a form
			//Comando tipo POST: redirigimos
//			$_SESSION['_POST'] = $_POST;
//			$url = url::get_request_url();
//			$command_name = $_POST[acommand::get_command_label()];
//			document('location: '.$url.'&'.acommand::get_command_label().'='.)
		} else {
			$this->command_type = 'default';
		}
    }
	protected function process_columns_col() {
	   if (is_null($this->columns_col)) {
		  $this->columns_col = new columns_collection($this);
        }
		foreach($this->columns as $col_name) {
			$col = columns_factory::create($this->columns_format[$col_name]);
			$col->set_column_name($col_name);
			$col->set_table($this);
			$col->set_primary_key(in_array($col_name,$this->key_set->primary_keys));
			$col->set_primary_key_editable($this->edit_pk);			
			
			if (isset($this->columns_new_value[$col_name])) {
				$col->set_default_value($this->columns_new_value[$col_name]);
			}
			if ( $this->columns_required_all && ! $col instanceof column_checkbox ) {
				$col->set_required(true);
			} else {
				$col->set_required(in_array($col->get_column_name(),$this->columns_required));
			}
			//$col->set_required_html($this->);
			if (isset($this->columns_restriction[$col_name])) {
				$col->set_restricted_value($this->columns_restriction[$col_name]);
			}
			$col->set_title($this->columns_title[$col_name]);
			//Consideraciones especiales para cada tipo de columna
			switch( $this->columns_format[$col_name]) {
				case 'select':
					$col = column_select::cast($col);
					//If there are options set, we use them
                    if ( key_exists($col_name, $this->columns_options) )
					   $col->set_options($this->columns_options[$col_name]);
					break;
				case 'numer':
					$col = column_number::cast($col);
					if (isset($this->columns_units[$col->get_column_name()]) && $this->columns_units[$col->get_column_name()] !== '');
					$col->set_unit($this->columns_units[$col->get_column_name()]);
				break;
			}
			$this->columns_col[] = $col;
		}
	}
	function process_config_array(&$source_array, $default_value=null, $allowed_values=null) {
		$original_array = $source_array;
		$source_array = array(); //Borramos el array para regenerarlo.
		//Comprobamos que el valor por defecto se encuentre dentro de los valores permitidos
		if ( $allowed_values && $default_value && ! in_array($default_value,$allowed_values) ) {
			die("**Error de programación: valor por defecto $default_value no incluido en los valores permitidos: ". var_export($allowed_values,true));
		}
		//Primero buscamos por orden numerico las definiciones.
		$i=0;
		foreach( $this->columns as $col_name ) {
			if (isset( $original_array[$i] ) && !isset( $original_array[$col_name] ) ) {
				if ($allowed_values && ! in_array($original_array[$i],$allowed_values)) {
						
					die("**Error de configuraci&oacute;n [$i]=".
					$original_array[$i]."; Valor $original_array[$i] no permitido.\n  Se permite: ".
					var_export($allowed_values,true));
				}
				$source_array[$col_name] = $original_array[$i];

			}
			$i++;
		}
		//Despues añadimos las referencias que utilicen directamente
		//el nombre de la columna.
		foreach($this->columns as $col_name) {
			if ( isset($original_array[$col_name]) ) {
				if ( isset($source_array[$col_name])) {
					throw new ExceptionDeveloper("<pre>**Error de configuración: se intentó definir dos veces el atributo [$col_name]");
				}
				if ($allowed_values && ! in_array($original_array[$col_name],$allowed_values)) {
					//echo "<pre>";var_dump($this->columns_format);die;
					//echo "<pre>";
					
					throw new ExceptionDeveloper("**Error de configuración: $col_name <-> $original_array[$col_name]\r\n".
						"Valor \"$original_array[$col_name]\" no permitido.\r\n Se permite: ".
						var_export($allowed_values,true));
				}
				$source_array[$col_name] = $original_array[$col_name];
			}
		}
		//Establecemos valores por defecto si no se han definido para una columna
		if (isset($default_value)) {
			foreach($this->columns as $col_name) {
				if ( !isset($source_array[$col_name]) ) {
					$source_array[$col_name] = $default_value;
				}
			}

		}
		//Por último, buscamos configuraciones erroneas
		if ($original_array != null) {
			foreach($original_array as $key => $value ) {
				if ( ! in_array($key,$this->columns)
				&& ! ($key >= 0 && $key <= count($this->columns)) ) {
					throw new ExceptionDeveloper("**Error de configuraciï¿½n: no valido [$key] = $value; No consta como columna o indice de columna");
				}
			}
		}
	}
	//-- Funciones para procesar primary key -----------------------------------------------
	public function has_primary_keys() {
		if ( !isset($this->key_set) || $this->key_set == null ) return false;
		return ( count($this->key_set->primary_keys) > 0 );
	}
	function fetch_row(key_set $key_set) {
		if ( $key_set===null ) throw new ExceptionDeveloper('**Error: key_set nula');
		$col_names = array();
		foreach($this->columns_col as $col) {
			if ( $col->get_table_name() == $this->table_name ) $col_names[] = $col->get_column_name();
		}
		$result = website::$database->fetch_row3( $this->table_name , $key_set, $col_names );
		//if ($result == false ) die( '**Error: No se pudo obtener la fila' );
		return $result;
	}
    // ------------------------------------------------------------------------------------
    /**
     * Checks if only one insert is allowed. In case a second insert has been attempted and it is restricted, it eithers redirect to url and exits,
     * or echoes message and returns false
     * @return bool true if second insert attempted, but not allowed
     */
    public function only_one_insert_check() {   
        //We check if you can only do one insert per computer
        if ( ! $this->only_one_new == true ) return false;
        
        if ( isset( $_SESSION[$this->only_one_new_sesion_id] ) ) {
            if ( $this->only_one_new_redirect != '' ) {
                header('location: '.$this->only_one_new_redirect);
                exit;
            }
            echo $this->only_one_new_message;
            return true;
        }
        return false;
    }
    /**
     * If only one insert is allowed, it sets that an successful insert is made by means of a cookie.
     * Must be called only it a successful insert has been made.
     */
    public function only_one_insert_sets() {
        if ( $this->only_one_new )
            $_SESSION[$this->only_one_new_sesion_id] = true;
    }
    public function after_init_values() { }
    
    public function after_insert_or_update() { }
    
    public function after_command() { }
    
	// ------------------------------------------------------------------------------------
	public function __toString() {
		throw new ExceptionDeveloper("Not implemented");
	}
	public function __echo() {
		$this->print_data();
	}
	public function print_data() {
		
		$this->init_config();
		if ( ! $this->initialised ) { throw new ExceptionDeveloper('table subclass doesn\'t call parent::init_config()'); }
		
		if ( count($this->columns_col) == 0 ) {
			//Columns may be specified after init_config directly over columns_col
			throw new ExceptionDeveloper('Columns not defined');			
		}
		
		
		//Si podemos gestionarlo con comandos, lo hacemos
        if ($this->command_name == '') $this->command_name = $this->default_command;
		if ( isset($this->commands[$this->command_name]) ) {
			$this->command_ok = $this->execute($this->commands[$this->command_name]);
            $this->after_command();
			return true;
		} else {
			throw new ExceptionDeveloper("Comando '$this->command_name' no aceptado");
		}
		
	}
    protected $command_ok = '';
	/**
	 * @param icommand
	 */
	public function execute(icommand $cmd) {
		if ($cmd instanceof icommand_row && ! ($cmd instanceof command_set)) {
			$cmd = acommand_row::cast($cmd);
			$pkvals=array();
			if ($this->key_set->is_in_array($_GET) ) {
				$pkvals = $_GET;
			} else if  ($this->key_set->is_in_array($_POST) ) {
				$pkvals = $_POST;
			}
			if ( $cmd->get_key_set() == null ) {
				$cmd->set_key_set($this->key_set,$pkvals);
			} else {
				if ( ! $cmd->get_key_set()->is_fully_defined() ) {
					$cmd->get_key_set()->set_keys_values($pkvals);
				}
			}
		}
		//$cmd->set_table($this);
		$ok = $cmd->execute();
		
		if ($this->log_commands) {
			$cmd->log_command();
		}
        return $ok;
	}
	public $log_commands = false;	
	
    /**
     * Adds a command to the table.
     * Returns a reference to said command, so it can be used to chain setters.
     * @return icommand reference to passed icommand object
     */
	function add_command(icommand $cmd) {
		$cmd->set_table($this);
		$this->commands[$cmd->get_key()] = $cmd;
        return $this->commands[$cmd->get_key()];
	}

	/**
	 * 
	 * @var control_group
	 */
	public $control_group = null;
	function process_control_group() {
		if ( $this->control_group == null ) {
			$this->control_group = new control_group($this);
		}
	}
	
	
//----------------------------------------------------------------------------------------

public static function get_select_array_table( $allow_empty = false, $table_data_class_name = null) {
	if ( ! isset( $table_data_class_name ) ) {
		throw new ExceptionDeveloper('Late static bindings not implemented yet');
		//$table_data_class_name = get_called_class();
	}
	$reflClass = new ReflectionClass($table_data_class_name);
	$props = $reflClass->getDefaultProperties();

	$sc = new table_data_select_config();
	$sc->table_name = $props['table_name'];
	$sc->primary_keys = $props['primary_keys'];
	$sc->columns_select = $props['columns_select_view'];
	$sc->order_default_column = $props['order_default_column'];
	foreach ($sc->columns_select as $col) {
		if ( isset ($props['columns_format'][$col]) && is_array($props['columns_format'][$col]) ) {
			$sc->columns_select_options[$col] = $props['columns_format'][$col];
		}
	}
	if ( $sc->order_default_column == '') $sc->order_default_column = reset($sc->columns_select);	
	if ( count ( $sc->columns_select ) < 1 ) {
		throw new ExceptionDeveloper ( 'No se han definido algunas propiedades por defecto necesarias: columns_table_view' );
	}	
	if (count ( $sc->primary_keys ) < 1 ) {
		throw new ExceptionDeveloper ( 'No se han definido algunas propiedades por defecto necesarias: primary_keys' );
	}
	if ( $sc->table_name == '') {
		throw new ExceptionDeveloper ( 'No se han definido algunas propiedades por defecto necesarias: table_name' );
	}		
	return self::get_select_array_internal($allow_empty,$sc);	
}

public static function get_column_select_table($allow_empty = false, $table_data_class_name = null) {
	if ( ! isset( $table_data_class_name ) ) {
		throw new ExceptionDeveloper('Late static bindings not implemented yet');
		//$table_data_class_name = get_called_class();
	}
	$reflClass = new ReflectionClass($table_data_class_name);
	$props = $reflClass->getDefaultProperties();	
	$options = self::get_select_array_table($allow_empty,$table_data_class_name);
	$c = new column_select();
	if ( isset($props['record_title']) && $props['record_title'] != '' ) $c->set_title(ucfirst($props['record_title']));
	$c->set_options($options);
	return $c;
}
//----------------------------------------------------------------------------------------
	/**
	 * @return table_data_select_config
	 */
	public function init_select_config(table_data_select_config $sc) {
		if ($sc==null) throw new ExceptionDeveloper('Parametro nulo');
		$this->key_set = new key_set ( $sc->primary_keys );
		$this->table_name = $sc->table_name;
		$this->order_default_column = $sc->order_default_column;
		$this->order_default_order = $sc->order_default_order;

	}
	private static $cache_select_array = array();
	public static function get_select_array_internal($allow_empty = false, table_data_select_config $select_config = null) {
		$sc = $select_config;
		if ($sc == null || count ( $sc->columns_select ) < 1 || count ( $sc->primary_keys ) < 1 || $sc->table_name == '') {
			throw new ExceptionDeveloper ( 'No se han definido la configuración de clase estática' );
		}
		if ( isset(self::$cache_select_array[$sc->table_name]) && self::$cache_select_array[$sc->table_name] != null ) {
			$result2 = self::$cache_select_array[$sc->table_name];
		} else {
			$table_name_esc = website::$database->escape_column_name ( $sc->table_name );
			$id_field = reset ( $sc->primary_keys );
			$id_field_esc = website::$database->escape_column_name ( $id_field );
			$order_col_esc = website::$database->escape_column_name ( $sc->order_default_column );
			$order = $sc->order_default_order == 'DESC' ? 'DESC' : 'ASC';
			
			$sql = "SELECT " . $id_field_esc . ", ";
			$sql1 = "";
			foreach ( $sc->columns_select as $field ) {
				$sql1 .= website::$database->escape_column_name ( $field ) . ", ";
			}
			$sql = $sql . substr ( $sql1, 0, - 2 ) . " FROM " . $table_name_esc;
			if ($sc->order_default_column != '') {
				$sql .= " ORDER BY " . $order_col_esc . ' ' . $order;
			}
			 
			$result = website::$database->execute_get_array ( $sql );
			$result2 = array();
            
			foreach($result as $row) {
				$id = $row[$id_field];
				$text = '';
                $i=1;
				foreach($sc->columns_select as $col) {
				    if ($i!=1) $text .= $select_config->separator;
				    if ( isset( $select_config->columns_select_options[$col] ) ) {
				    	$text .= $select_config->columns_select_options[$col][$row[$col]];
			    	} else {
						$text .= $row[$col];
					}
                    $i++;
				}

				$result2[$id] = $text;
			}
			reset($result2);
			self::$cache_select_array[$sc->table_name] = $result2;
		}
		if (!is_array($result2)) {
			throw new ExceptionDeveloper('Result not array:'.print_r($result2,true));
		}
		if ($allow_empty) {
			$result2 = array (0 => '' ) + $result2 ;
		}
		
		return $result2;
	}
	
}
?>
