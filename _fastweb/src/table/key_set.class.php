<?php

class key_set {
	/**
	 * Array de strings con los nombres de las claves primarias
	 * @var array
	 */
	public $primary_keys = array();
	/**
	 * Array con formato clave => valor del conjunto de las claves primarias
	 *
	 * @var array
	 */
	public $keys_values = array();
	
	public $table = '';
	
	public $dbname = '';
	/**
	 * Constructor, acepta nada, o un string, o un array de strings para las claves
	 *
	 * @param mixed $primary_keys
	 */
	//------------------------------------------------------------------------------
	function __construct( $primary_keys=null,$table='',$dbname='' ) {
		if ( isset($primary_keys)) {
			if (is_array($primary_keys)) {
				$this->primary_keys = $primary_keys;
			} elseif (is_string($primary_keys)) {
				$this->primary_keys = array($primary_keys);
			} else {
				throw new ExceptionDeveloper('**Error: No se pasaron claves primarias');
			}
		}
		if ( $table != '' ) {
			$this->table = $table;	
		}
		if ( $dbname != '' ) {
			$this->dbname = $dbname;
		}
	}
	public function is_fully_defined() {
		return $this->is_in_array($this->keys_values);
	}
	public function is_in_post_or_get() {
		$result = ( $this->is_in_array($_GET) || $this->is_in_array($_POST) );
		return $result;
	}
	/**
	 *
	 */
	public function is_in_columns(columns_collection $columns_col) {
		$result = true;
		$sufix = '';
		$found = array();
		foreach ($columns_col as $key => $col ) {
			if ( in_array($this->primary_keys,$key) ) {
				$c = $columns_col->get($key);
				$v = $c->get_value();
				if ( ! empty( $v ) ) {
					$found[] = $key;
				}
			}
		}
		if (count(array_intersect($found, $this->primary_keys))== count($this->primary_keys)) {
			$result = true;
		} else {
			$result = false;
		}
		return $result;
	}
	public function set_key_columns(columns_collection $columns_col) {
		foreach ($columns_col as $key => $col ) {
			if ( in_array($key,$this->primary_keys) ) {
				$c = $columns_col->get($key);
				//TODO: Throw exception if not defined as pk?
				$v = $c->get_value();
				$this->keys_values[$key] = $v;
				
			}
		}
	}
	/**
	 * Comprueba si en el array pasado se encuentran todos los elementos de la clave primaria
	 * @param array $a
	 */
	public function is_in_array($a, $sufix='') {
		$result = true;
		foreach ($this->primary_keys as $key) {
			if ( ! isset($a[$key.$sufix]) || ($a[$key.$sufix]==='') ) {
				//throw new ExceptionDeveloper("Clave no definida: {$key}{$sufix}<br />".print_r($a,true).'<br />keys:<br />'.print_r($this->primary_keys,true));
				//We must not throw exception, calling this method for testing is normal execution
				$result = false;
				break;
			}
		}
		return $result;
	}
	public function get_clean_array_values($a,$sufix='') {
		
		if ( ! $this->is_in_array($a,$sufix) ) {
			throw new ExceptionDeveloper('No está definida las claves en el array pasado:<br />'.print_r($a,true).'<br />keys:<br />'.print_r($this->primary_keys,true));
		}
		$result = array();

		foreach($this->primary_keys as $key) {
			$result[$key] = $a[$key.$sufix];
		}
		return $result;
	}
	/**
	 * Sets the values of the keys
	 * @param $array_key_values associative array with keys and values
	 * @param $sufix optinal sufix to be suppresed from key names 
	 */
	public function set_keys_values($array_key_values,$sufix='') {
		
		$this->keys_values = $this->get_clean_array_values($array_key_values,$sufix);
		
	}
	public function set_keys_values_col(columns_collection $columns_col,$sufix='') {
        $a = array();
        foreach($columns_col as $col) {
            $a[$col->get_column_name()] = $col->get_value();
        }
       
		$this->keys_values = $this->get_clean_array_values($a,$sufix);
	}
	/**
	 * Sets key values from an id string defined in an html element,
	 * where there are defined pairs of pk and value separated by colons ":".
	 * This method of identified html inputs are used by icolumns objects when
	 * not all columns are for the same row.
	 * @param $html_id string with pairs of key and values separated by colons ":"
	 */
	public function set_key_values_from_html_id($html_id) {
		$tokens = explode(":",$html_id);
		//if (count($tokens)%2 == 0) { throw new ExceptionDeveloper('Tokens not odd, las token must be column name'); }
		$result = array();
		for( $i=0 ; $i<(count($tokens)-1) ; $i += 2) {
			$result[$tokens[$i]] = $tokens[$i+1];
		}
		$this->set_keys_values($result);
	}
	
	public function validate() {
		$ok = true;
		foreach($this->primary_keys as $key) {
			if ( ! isset($this->keys_values[$key]) || empty($this->keys_values[$key]) ) {
				$ok = false;
			}			
		}
		return $ok;
	}
//	public function get_url_keys_values($a=null) {
//		if ( $a == null ) {
//			$a = $this->keys_values;
//		}
//		$s = url::$url_separator;
//		$result = '';
//		foreach($this->primary_keys as $key) {
//			$result .= $key . '=' . $a[$key] . $s;
//		}
//		$result = substr($result,0,-strlen($s));
//		return $result;
//	}

	/**
	 * @return url
	 */
	public function get_url() {
		$url = new url();
		foreach($this->keys_values as $key => $value) {
			$url->set_var($key,$value);
		}
		if ($this->table != '' ) {
			//TODO: Aun no implementamos esto
			$url->set_var('_table',$this->table);
		}
		return $url;
	}
	/**
	 * Obtiene un objeto de clase url con las claves primarias asignadas
	 * como parámetros.
	 * @param array $data Opcionalmente, array del que obtener las claves primarias.
	 * @return url
	 */
	public function get_url_from_array($data) {
		$url = new url();
		foreach($data as $key => $value) {
			if (in_array($key,$this->primary_keys)) {
				$url->set_var($key,$value);
			}
		}
		return $url;
	}
	public function get_input_hiddens() {
		return $this->get_input_hiddens_from_array($this->keys_values);	
	}
	public function get_input_hiddens_from_array($data) {
		$result = '';
		
		foreach($data as $key => $value) {
			if (in_array($key,$this->primary_keys)) {
				$result .= "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\r\n";
			}
		}
		return $result;
	}
//	public static function get_key_set_from_url($data) {
//		$result = $this->clone;
//		foreach ($this->primary_keys as $pk) {
//			if (isset($_GET[$pk])) {
//				$result->keys_values[$pk] = $_GET[$pk];
//			} else {
//				return false;
//			}
//		}
//		if (isset($_GET['_table'])) {
//			$result->table = $_GET['_table'];
//		}
//	}
	function get_pks_string($separator='_') {
		return $this->get_pks_string_from_row($this->keys_values,$separator);
	}
	
	public static function get_column_name_from_html_id($html_id) {
		$tokens = explode(":",$html_id);
		return end($tokens);
	}
	
	function get_pks_string_from_row( $row, $separator='_' ) {
		
		$pks = $this->primary_keys;
		$ok = true;
		$result = '';
		foreach ($pks as $pk) {
			if (isset($row[$pk]) && $row[$pk]!='' ) {
				$result .= $row[$pk] . $separator;
			} else {
				$ok = false;
			}
		}	
		if ( !$ok ) {
			throw new ExceptionDeveloper('Not all primary keys values defined');
		}
		$result = substr($result,0,-1*strlen($separator));
		return $result;
	}
	public function get_input_id($row=null) {
		if ($row == null ) $row = $this->keys_values;
		$id = '';
		$pks = $this->primary_keys;
		foreach ($pks as $pk) {
			$value = '';
			if (isset($row[$pk])) {
				$value = $row[$pk];
			}
			$id .= "$pk:".$value.":";
		}
		$id = substr($id,0,-1);
		return $id;
	}
	public function get_title_text($row=null) {
		if (is_null($row)) {
			$myrow = $this->keys_values;
		} else {
			$k = clone($this);
			$k->set_keys_values($row);
			$myrow = $k->keys_values;
		}
		$result = '';
		foreach($myrow as $key => $value) {
			$result .= $key." : ".$value.", ";
		}
		$result = substr($result,0,-2);
		return $result;
	}
	public function get_sql_where(columns_collection $columns_col) {
		$result = '';
		foreach($this->primary_keys as $key ) {
			if ( ! isset( $this->keys_values[$key] ) || $this->keys_values[$key]==='' ) {
				throw new ExceptionDeveloper( 'Not all values defined for key set' );
			}
			$value = $this->keys_values[$key];
			if ( ! isset($columns_col[$key]) ) throw new ExceptionDeveloper( 'Not all PK values defined in $columns_col' );
			if ( $columns_col->get($key)->get_value() != $value ) {
				
				$c = clone( $columns_col->get($key) );
				$c->set_value($value);
				$result .= $c->get_db_where_str()." AND ";
			} else {
				$result .= $columns_col->get($key)->get_db_where_str()." AND ";
			}
		}
		$result = substr($result,0,-1*strlen(" AND "));
		return $result;
	}
}