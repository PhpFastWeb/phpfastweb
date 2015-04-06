<?php

class filter {
	/**
	 * @var data_set
	 */
	public $data_set = null;
	/**
	 * Objeto table_data sobre el que se definen los filtros
	 * @var table_data
	 */
	public $table = null;
	/**
	 * Objeto URL que contiene la información de filtrado. Deberá ser compuesto mediante llamadas a set_url
	 * y a get_url para obtener una url con información adicional al filtrado (por ejemplo, la ordenación).
	 * @var url
	 */
	public $url = null;
	public $search_string = '';
	public $search_input_name = 'q';
    public $submit_search_button_msg = 'Buscar';
    public $submit_filter_button_msg = 'Filtrar';
    public $decode_utf8 = false;
	public $initialised = false;
	//--------------------------------------------------------------------------
//	public function init_config() {
//		if ($this->initialised)
//			return;
//
//		//Preparamos la cadena de búsqueda
//		$s = url::read ( $this->search_input_name );
//		if ($s) {
//			$this->search_string = $s;
//		}
//		if ($this->table == null && $this->data_set != null) {
//			$this->table = $this->data_set->data_source;
//		}
//	
//	}
//	public function __toString() {
//		//Primero inicializamos el filtro
//		$this->init_config ();
//		//Segundo procesamos los parámetros
//		//...
//		//Tercero enviamos el resultado
//		
//
//		$result = '';
//		
//		$result .= $this->get_search ();
//		return $result;
//	}
	//-------------------------------------------------------------------------------
	public function get_url() {
		return $this->url->clone ();
	}
	public function set_url($url) {
		$this->url = $url;
	}
	private function process_url() {
		if ($this->url == null) {
			$this->url = new url ();
		}
		// 
		$this->url->set_var ( $this->search_input_name, $this->search_string );
		
		$table = $this->data_set->data_source;
		foreach ( $table->filter_data as $key => $value ) {
			$this->url->set_var ( $key, $value );
		}
	}
	//--------------------------------------------------------------------------
	//--------------------------------------------------------------------------
	private function get_search() {
		$result = '';
		$result .= '<form style="margin:0 0;" class="form_busqueda" action="' . html_template::get_php_self(). '">';
		$result .= $this->get_plain_search ();
		$result .= $this->get_filters ();
		$result .= '<input type="submit" value="'.$this->submit_search_button_msg.'" class="submit_buscar" /><br />';
		$result .= $this->get_search_results ();
		$result .= '</form>';
		$result .= '<hr />';
		return $result;
	}
	private function get_plain_search() {
		$result = '';
		$name = $this->search_input_name;
		$result .= "Búsqueda: <input type=\"text\" name=\"$name\" id=\"$name\" value=\"{$this->search_string}\" /> ";
		return $result;
	}

	function get_input_select($field, $prefix = '', $options = null, $filter_data = null) {
		$result = '';
		//Comprobamos si incluye el valor vacio
		
		
		if (in_array ( '', $options ) || in_array ( ' ', $options )) {
			//Añadimos un campo "Mostrar todos"
			//Si existe una "sin etiqueta", le ponemos un texto más explicativo
			$key = null;
			if (in_array ( '', $options )) {
				$key = array_search ( '', $options );
			} elseif (in_array ( ' ', $options )) {
				$key = array_search ( '', $options );
			}
			if ($key !== null) {
				$options[$key] = '-en blanco-';
				//TODO: Leer pala linked column el caso de nullify empties
			}
		}
		
		
		
		$options = array ('*' => '-cualquiera-' ) + $options;
		$result .= "<select id=\"FILTER_$prefix$field\" name=\"FILTER_$prefix$field\">";
		foreach ( $options as $key => $text ) {
			$result .= "<option value=\"$key\"";
			if (isset ( $filter_data )) {
				if ("$filter_data" == "$key") {
					$result .= " selected=\"selected\" ";
				}
			}
			$result .= ">$text</option>\r\n";
		}
		$result .= "</select>\r\n";
		
		return $result;
	}
	private function get_search_results() {
		$table = $this->data_set->data_source;
		$result = '';
		$q = url::read ( 'q' );
		if ($q) {
			$result .= "Resultados de la búsqueda: <b>" . $q . "</b><br />";
			$q = url::read ( 'q' );
		}
		$i = 0;
		if (count ( $table->filters_caption ) > 0) {
			foreach ( $table->filters_caption as $key => $value ) {
				$result .= $value . "<br />\r\n";
				$i ++;
			}
		
		//if ($i>0) $this->table_caption = substr($this->table_caption,0,-8); //Borramos el último <br />
		}
		if ($result !== '') {
			$result2 = "<div style=\"background-color:#D4FFAA; padding: 4px 4px 4px 4px; margin: 4px 4px 4px 4px;\" >";
			$result2 .= "<a href=\"" . html_template::get_php_self() . "\" style=\"float:right;\">Ver todos</a>";
			$result = $result2 . $result . "</div>\r\n";
		}
		return $result;
	}
	//--------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------
	//------------------------------------------------
    public function load_filters_from_session() {
        //Intentamos cargar los filtros
		if ( isset ( $_SESSION ) && isset ( $_SESSION ['filter_persist'] ) && isset ( $_SESSION ['filter_persist'] [$_SERVER ['PHP_SELF']] )) {
		  $this->table->filters_data = $_SESSION ['filter_persist'] [$_SERVER ['PHP_SELF']];
		  //TODO: Validar los filtros, en caso de que haya cambiado la estructura de la tabla
		}
    }
	function process_filter() {
		global $_GET;

		//Inicializamos los datos en vacio
		

		foreach ( $this->table->filter_fields as $field ) {
			if (! isset ( $this->table->filters_data ['FILTER_' . $field] )) {
				$this->table->filters_data ['FILTER_' . $field] = false; //No asignamos vacio ya que puede ser un tipo de filtro
			}
		}
		foreach ( $this->table->filter_ranges as $field ) {
			if (! isset ( $this->table->filters_data ['FILTER_FROM_' . $field] )) {
				$this->table->filters_data ['FILTER_FROM_' . $field] = false;
			}
			if (! isset ( $this->table->filters_data ['FILTER_TO_' . $field] )) {
				$this->table->filters_data ['FILTER_TO_' . $field] = false;
			}
		}
		foreach ( $this->table->filter_searchs as $field ) {
			
			if ( ! isset ( $this->table->filters_data ['FILTER_SEARCH_' . $field] ) ) {
				$this->table->filters_data ['FILTER_SEARCH_' . $field] = false;
			}
		}
		
		foreach ( $this->table->filter_bools as $key => $params ) {
			if (! isset ( $this->table->filters_data ['FILTER_BOOL_' . $field] )) {
				$this->table->filters_data ['FILTER_BOOL_' . $key] = false;
			}
		}
		
		//Capturamos los datos desde el get
		$nfil = 0;
		if (isset ( $_GET )) {
			foreach ( $_GET as $key => $value ) {
				if (substr ( $key, 0, 6 ) == 'FILTER' && $value !== '') { //} && $value !== '*') {
					$this->table->filters_url .= "$key=$value" . url::$url_separator;
					$this->table->filters_data [$key] = $value;
					$nfil ++;
				}
			}
		}
		
		if ($nfil == 0 && count ( $this->table->filter_default ) > 0) {
			foreach ( $this->table->filter_default as $key => $value ) {
				$this->table->filters_url .= "$key=$value" . url::$url_separator;
				$this->table->filters_data ["FILTER_" . $key] = $value;
			}
		}
		
		//TODO: Integrar mejor el quitar los filtros
		if ( isset ( $_GET ['FILTER_REMOVE_'] ) || //Se han quitado los filtros, o había datos persistidos no útiles
             ( ! $this->table->filter_persist &&  isset($_SESSION ['filter_persist'] [$_SERVER ['PHP_SELF']]) ) ) {
			try {
				unset ( $_SESSION ['filter_persist'] [$_SERVER ['PHP_SELF']] );
			} catch ( Exception $e ) {	}
			try {
				unset ( $_SESSION ['order_persist'] [$_SERVER ['PHP_SELF']] ['order_order'] );
			} catch ( Exception $e ) {  }
			try {
				unset ( $_SESSION ['order_persist'] [$_SERVER ['PHP_SELF']] ['order_column'] );
			} catch ( Exception $e ) {	}
			//header ( 'location: ' . $_SERVER ['PHP_SELF'] );
			//echo "<a href=\"" . $_SERVER ['PHP_SELF'] . "\">Continuar</a>";
			
			//exit ();

		}
		
		//Si los filtros persisten, los guardamos, o si están vacios, cargamos los de la sesión
		if ($this->table->filter_persist) {
			if ($nfil == 0 && count ( $this->table->filter_default ) == 0) {
				$this->load_filters_from_session();
			} else {
				//Almacenamos los filtros
				$_SESSION ['filter_persist'] [$_SERVER ['PHP_SELF']] = $this->table->filters_data;
			}
		}
		
		//Construimos sql y caption
		$result = "";
		$sql = new sql_str();
		
		foreach ( $this->table->filter_fields as $field ) {
            if ( $this->table->filters_data ['FILTER_' . $field] !== false && 
                 $this->table->filters_data ['FILTER_' . $field] != '' ) {
                $col = $this->table->columns_col->get($field);
    			if ($col instanceof column_date ||
    				$col instanceof column_datetime ||
    				$col instanceof column_time
    			) {
    				//Se trata de una fecha, fecha-hora u hora, comparamos con LIKE

   					$d = column_date::convert_format_user_to_db ( $this->table->filters_data ['FILTER_' . $field] );
   					$sql = new sql_str("{#0} LIKE '%{1}%' AND ",$field,$d);
   					$result .= $sql->__toString();
   					//$result .= "$field LIKE '%" . $d . "%' AND ";
   					$this->table->filters_caption ['FILTER_' . $field] = $col->get_short_title() . " : " . $this->table->filters_data ['FILTER_' . $field];			
    				
    			} else {
    				//Se trata de otra cosa, comparamos con =
    				//Si el parametro es '*', mostramos todos (no filtramos)
    				if ($this->table->filters_data ['FILTER_' . $field] !== '*') {
                        $col->set_formatted_value($this->table->filters_data ['FILTER_' . $field]);                     
                        if ($col instanceof column_select || $col instanceof column_number) {
                            $result .= $col->get_db_equal_str();
                        } else {
                        	$sql = new sql_str("{#0} LIKE '%{1}%'",
                        		$field,
                        		$this->table->filters_data ['FILTER_' . $field]);
                        	$result .= $sql;
                        	//$d = $this->table->filters_data ['FILTER_' . $field];
                            //$d = website::$database->escape_string($d);
                            //$result .= "$field LIKE '%" . $d . "%' ";
                        }
                        $result .= " AND ";

    					$this->table->filters_caption ['FILTER_' . $field] = 
    						$col->get_short_title() . " : " . $col->get_formatted_value ();
    				}
    			
    			}
            }
		}
		foreach ( $this->table->filter_ranges as $field ) {
			if ($this->table->filters_data ['FILTER_FROM_' . $field] !== false) {
				$d = $this->table->filters_data ['FILTER_FROM_' . $field];
				if ($this->table->columns_col->get($field) instanceof column_date || 
						$this->table->columns_col->get($field) instanceof column_datetime) {
					$d = column_date::convert_format_user_to_db ( $d );
				}
				$sql = new sql_str("{#0} >= '{1}' AND ", $field, $d);
				$result .= $sql;		
				//$result .= "$field >= '" . $d . "' AND ";
				$this->table->filters_caption ['FILTER_FROM_' . $field] = "Desde " . $this->table->columns_col->get($field)->get_short_title() . " : " . $this->table->filters_data ['FILTER_FROM_' . $field];
			
			}
			if ($this->table->filters_data ['FILTER_TO_' . $field] !== false) {
				$d = $this->table->filters_data ['FILTER_TO_' . $field];
				if ($this->table->columns_col->get($field) instanceof column_date || 
						$this->table->columns_col->get($field) instanceof column_datetime) {
					$d = column_date::convert_format_user_to_db ( $d );
				}
				$sql = new sql_str("{#0} <= '{1}' AND ", $field, $d);
				$result .= $sql;
				//$result .= "$field <= '" . $d . "' AND ";
				$this->table->filters_caption ['FILTER_TO_' . $field] = "Hasta " . $this->table->columns_col->get($field)->get_short_title() . " : " . $this->table->filters_data ['FILTER_TO_' . $field];
			}
		
		}
		foreach ( $this->table->filter_bools as $key => $params ) {
			//echo 'isset($this->table->filter_bools['.$key.']["values"])';
			assert ( 'isset($this->table->filter_bools["' . $key . '"]["values"])' );
			assert ( 'is_array($this->table->filter_bools["' . $key . '"]["values"])' );
			assert ( 'isset($this->table->filter_bools["' . $key . '"]["text"])' );
			if ($this->table->filters_data ['FILTER_BOOL_' . $key] !== false) {
				$value = $this->table->filters_data ['FILTER_BOOL_' . $key];
				assert ( 'isset($this->table->filter_bools["' . $key . '"]["values"]["' . $value . '"])' );
				$sql = $this->table->filter_bools [$key] ['values'] [$value];
				//TODO: Use sql_str class with implicit escaping
				$result .= '( ' . $sql . ' ) AND ';
				$this->table->filters_caption ['FILTER_BOOL_' . $key] = $this->table->filter_bools [$key] ["text"];
			}
		}
		
		if ( count ( $this->table->filter_searchs ) > 0 ) {
			
			foreach ( $this->table->filter_searchs as $field ) {
			 
				if ( $this->table->filters_data ['FILTER_SEARCH_' . $field] !== false && 
                     $this->table->filters_data ['FILTER_SEARCH_' . $field] != '' ) {
					$sql = new sql_str("( {#0} LIKE '%{1}%' ) AND ",
							$field,
							$this->table->filters_data ['FILTER_SEARCH_' . $field]);
					$result .= $sql;
					$this->table->filters_caption ['FILTER_SEARCH_' . $field] = $this->table->columns_title [$field] . " : " . $this->table->filters_data ['FILTER_SEARCH_' . $field];
				}
			}
		
		}
		//Elminamos trailing and
		$result = substr ( $result, 0, - 1 * strlen ( 'AND ' ) );

		//Devolvemos el resultado
		$this->table->sql_filter = $result;
		
		//throw new ExceptionDeveloper($result);
		return $result;
	}
	//------------------------------------------------
	function process_caption() {
		if ($this->table->filter) {
			//Construimos la expresión legible para Caption
			if (count ( $this->table->filters_caption ) > 0) {
				if ($this->table->table_caption != "")
					$this->table->table_caption .= "<br />";
				
		//$this->table->table_caption .= "\t\t\t<div>\r\n<p align=\"center\">";
				$this->table->table_caption = "<span style=\"display:block; float:left; font-size:9px;color:#888;\">&nbsp;Filtros</span>";
				$this->table->table_caption .= "<a href=\"#\" style=\"display:block;\" onclick=\"javascript: showhide('show_filter',false); showhide('filter',true); document.forms['form_filter'].elements[0].focus(); return false;\" >";
				$i = 0;
				foreach ( $this->table->filters_caption as $key => $value ) {
					$this->table->table_caption .= $value . "<br />\r\n";
					$i ++;
				}
				if ($i > 0)
					$this->table->table_caption = substr ( $this->table->table_caption, 0, - 8 ); //Borramos el último <br />
				$this->table->table_caption .= "\t\t\t</a>";
			
		//$this->table->table_caption .= "</p></div>";
			}
		}
	}
	//------------------------------------------------
	function process_search() {
		$this->table->sql_search = '';
		if ($this->table->search_method == 'get' || $this->table->search_method == 'GET') {
			if (! isset ( $_GET [$this->table->search_varname] )) {
				return;
			}
			$search = $_GET [$this->table->search_varname];
		} else {
			if (! isset ( $_POST [$this->table->search_varname] )) {
				return;
			}
			$search = $_POST [$this->table->search_varname];
		}
        if ($search == '') return;
        
		$sql_search = '';
		$keywords = explode ( ' ', $search );
		
		if ($this->table->search_fields == '*') {
			$search_fields = $this->table->columns;
		} else {
			$search_fields = $this->table->search_fields;
			if (! is_array ( $search_fields )) {
				$search_fields = array ($search_fields );
			}
		}
		
		foreach ( $search_fields as $col_name ) {
			foreach ( $keywords as $word ) {
                if ( $this->decode_utf8 ) 
				    $word = website::$database->escape_string(utf8_decode ( $word ));
                else
                    $word = website::$database->escape_string($word);
                    
				$sql_search .= "$col_name LIKE '%$word%' " . $this->table->search_connector . " ";
			}
			$sql_search = substr ( $sql_search, 0, - 1 * (strlen ( $this->table->search_connector ) + 1) );
			$sql_search .= ' OR ';
		}
		$sql_search = substr ( $sql_search, 0, - 1 * (strlen ( ' OR ' ) + 1) );
		$this->table->sql_search = $sql_search;
		$this->table->search_string = $search;
	}

	//------------------------------------------------
    
	function process_restrictions() {
		foreach ( $this->table->columns_restriction as $key => $value ) {
			if ( isset( $this->table->columns_col[$key] ) ) {
				//Añadimos la restricción a la columna, que será procesada más adelante
				$this->table->columns_col[$key]->set_restricted_value($value);
			} else {
				//Procesamos la restricción a mano
				$columns_restriction = $this->get_restriction($key, $value);
				$this->table->sql_restriction = website::$database->concat_sql_restrictions ( $this->table->sql_restriction, $columns_restriction );			
			}
		}
		foreach ( $this->table->columns_col as $key => $col ) {
			if ( ! $col->is_restricted() ) continue;
			//Procesamos restricciones en columnas
            
			$value = $col->get_restricted_value();
            
			$columns_restriction = $this->get_restriction($key, $value);
			$this->table->sql_restriction = website::$database->concat_sql_restrictions ( $this->table->sql_restriction, $columns_restriction );			
		}
	}
	protected function get_restriction($key, $value) {
		//Null value
		if ( $value == 'NULL' || $value == 'null' || is_null($value) ) {
			$sql = new sql_str( "{#0} is NULL", $key );
			return $sql->__toString(); 
		} 
        
		//Array value
		if ( is_array($value) ) {
			$t = "";          
			foreach ($value as $subvalue) {          
				$sql = new sql_str("{#0} = '{1}' OR ", $key, $subvalue);
				$t .= $sql->__toString();               
			}
            $result = substr($t, 0, -1 * strlen(" OR "));
            return $result;
		} 
        
		//Simple value
		$sql = new sql_str("{#0} = '{1}'", $key, $value);
		return $sql->__toString();
        
	}
	//------------------------------------------------
	function process_options() {
		$result = '';
		if (! $this->table->columns_options_filter_table)
			return;
		foreach ( $this->table->columns_options as $col => $option ) {
			foreach ( array_keys ( $option ) as $key ) {
				$result .= "$col = '$key' OR ";
			}
		}
		$result = substr ( $result, 0, - 4 );
		$this->table->sql_options = $result;
	}
	//------------------------------------------------
	//------------------------------------------------
	function print_filter_form() {
		echo $this->get_filter_form ();
	}
	
	public function get_filter_form() {
		$result = '';
		$result .= '<center>
					<form name="form_filter" id="form_filter" action="' . html_template::get_php_self() . '" method="get">		  
						<table class="filter_table" summary="Filtro" align="center">';
		
		foreach ( $this->table->filter_fields as $filter_field ) {
            $result .= $this->get_filter_row($filter_field,'',''); //No title prefix nor prefix id
		}
		
		foreach ( $this->table->filter_ranges as $filter_field ) {
            $result .= $this->get_filter_row($filter_field,'Desde ','FROM_'); //No title prefix nor prefix id
            $result .= $this->get_filter_row($filter_field,'Hasta ','TO_'); //No title prefix nor prefix id			
		}
		foreach ( $this->table->filter_bools as $key => $params ) {
			die ( "filters_bools deprecated" );
		
		}
		foreach ( $this->table->filter_searchs as $filter_field ) {
			$result .= $this->get_filter_row($filter_field,'','SEARCH_');
		}
		
		$result .= '
	  				<tr>
	  					<td colspan="2" style="text-align:center">
	  						<input type="button" style="width:120px;height:24px;margin:5px 0 0 0;" value="Reiniciar filtro" 
	  						onclick="window.location.href=\'' . html_template::get_php_self() . '?FILTER_REMOVE_=1\';" /> 
	  						<input type="submit" style="width:120px;height:24px;margin:5px 0 0 0;" value="'.$this->submit_filter_button_msg.'" /><br /><br />
	  					</td>
	  				</tr>
  				</table>
	  		</form>
	  		</center>
		 ';
		return $result;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------------
	//TODO: Propagar esta funcionalidad a las columnas
	function get_filter_row($filter_field, $title_prefix = '', $prefix = '') {
		
		
		$filter_data = null;
		
		if (isset($this->table->filters_data ["FILTER_$prefix$filter_field"])) {
			$filter_data = $this->table->filters_data ["FILTER_$prefix$filter_field"];
		}
		$col = $this->table->columns_col->get($filter_field);
		$result = '';
		
		if ( $col instanceof column_select ) {
			$result .= $this->get_filter_row_select ( $title_prefix . $col->get_short_title(), $filter_field, $prefix, $filter_data );
		} else if ( $col instanceof column_checkbox ) {
			$result .= $this->get_filter_row_bool ( $title_prefix . $col->get_short_title(), $filter_field, $prefix, $filter_data );
		} else if ( $col instanceof column_date || $col instanceof column_datetime ) { //|| $col instanceof column_datetime ) {
			$result .= $this->get_filter_row_date( $title_prefix . $col->get_short_title(), $filter_field, $prefix, $filter_data );
		} else if ( $col instanceof column_linked ) {
			$result .= $this->get_filter_row_linked ( $title_prefix . $col->get_short_title(), $filter_field, $prefix, $filter_data );
		} else {
			$result .= $this->get_filter_row_input( $title_prefix . $col->get_short_title(), $filter_field, $prefix, $filter_data );
		}
		return $result;
	}
	function get_filter_row_input($text, $field, $prefix = '',$filter_data=null) {
		$result = "";
		$result .= "<tr>\r\n";
		$result .=  "<td style=\"text-align:right\"><label for=\"FILTER_$prefix$field\">$text :</label>&nbsp;</td>\r\n";
		$result .=  "<td style=\"text-align:left\">\r\n";
		$result .=  "<input id=\"FILTER_$prefix$field\" name=\"FILTER_$prefix$field\" type=\"text\" value=\"" . $filter_data . "\" class=\"form_edit_row_inputtext\" />\r\n";
		$result .=  "</td>\r\n";
		$result .=  "</tr>\r\n";
		return $result;
	}
	function get_filter_row_date($text, $field, $prefix = '',$filter_data=null) {
		$result = "";
		$result .= "<tr>\r\n";
		$result .=  "<td style=\"text-align:right\"><label for=\"FILTER_$prefix$field\">$text :</label>&nbsp;</td>\r\n";
		$result .=  "<td style=\"text-align:left\">\r\n";
		$result .=  column_date::get_input_plain_static ( "FILTER_$prefix$field", $filter_data );
		$result .=  "</td>\r\n";
		$result .=  "</tr>\r\n";		
		return $result;
	}	
	function get_filter_row_linked($text, $field, $prefix = '', $filter_data=null) {
		$c = clone($this->table->columns_col->get($field));
		$c->set_column_name("FILTER_$prefix$field");
		$c->set_title($text);
		//$c->set_value($filter_data;)
		
		$result = "";
		$result .=  "<tr>\r\n";
		$result .=  "<td style=\"text-align:right\"><label for=\"FILTER_$prefix$field\">$text :</label>&nbsp;</td>\r\n";
		$result .=  "<td style=\"text-align:left\">\r\n";
		$result .= $c->get_input_plain();
		$result .=  "</td>\r\n";
		$result .=  "</tr>\r\n";
		return $result;		
				
	}	
	function get_filter_row_select($text, $field, $prefix = '', $filter_data=null) {
		$result = "";
		$result .=  "<tr>\r\n";
		$result .=  "<td style=\"text-align:right\"><label for=\"FILTER_$prefix$field\">$text :</label>&nbsp;</td>\r\n";
		$result .=  "<td style=\"text-align:left\">\r\n";
		$result .=  $this->get_input_select ( $field, $prefix, $this->table->columns_col->get($field)->get_options(),$filter_data );
		$result .=  "</td>\r\n";
		$result .=  "</tr>\r\n";
		return $result;
	}
	function get_filter_row_bool($text, $field, $prefix = '', $filter_data=null) {
		$result = "";
		$result .=  "<tr>\r\n";
		$result .=  "<td style=\"text-align:right\"><label for=\"FILTER_$prefix$field\">$text :</label>&nbsp;</td>\r\n";
		$result .=  "<td style=\"text-align:left\">\r\n";
		$result .=  $this->get_input_select ( $field, $prefix, array ('0' => 'No', '1' => 'Si' ),$filter_data );
		$result .=  "</td>\r\n";
		$result .=  "</tr>\r\n";
		return $result;
	}
//------------------------------------------------


}

?>