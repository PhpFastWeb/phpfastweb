<?php

// Con esto evitamos a los parser sobre errores por falta de una extensión
if ( ! function_exists('ibase_connect()')) {
	function ibase_connect() 	{ throw new ExceptionDeveloper('Extensión de Interbase no instalada.'); }
	function ibase_close () 	{ throw new ExceptionDeveloper('Extensión de Interbase no instalada.'); }	
	function ibase_errmsg() 	{ throw new ExceptionDeveloper('Extensión de Interbase no instalada.'); }
	function ibase_fetch_assoc(){ throw new ExceptionDeveloper('Extensión de Interbase no instalada.'); }
	function ibase_num_fields() { throw new ExceptionDeveloper('Extensión de Interbase no instalada.'); }
	function ibase_pconnect() 	{ throw new ExceptionDeveloper('Extensión de Interbase no instalada.'); }
	function ibase_query() 		{ throw new ExceptionDeveloper('Extensión de Interbase no instalada.'); }
}

class database_interbase implements idatabase_provider {
	/**
	 * 
	 */
	public function connect($ip,$db_name,$user,$password,$charset='') {
		if (strpos($ip,':')!==false || strpos($db_name,':')!==false ) {
			throw new ExceptionDeveloper("Caracter ilegal ':' en IP o nombre de base de datos Interbase");
		}
		return ibase_connect($ip.':'.$db_name,$user,$password,$charset);
	}

	/**
	 * 
	 * @param resource $connection
	 * @return 
	 */
	public function close($conection) {
		return ibase_close($conection);
	}

	/**
	 * 
	 */
	public function errmsg($query_result) {
		return ibase_errmsg();
	}

	/**
	 * 
	 */
	public function fetch_assoc($query_result) {
		return ibase_fetch_assoc($query_result,IBASE_TEXT);
	}

	/**
	 * 
	 */
	public function num_rows($query_result) {
		return ibase_num_fields($query_result);
	}

	/**
	 * 
	 */
	public function pconnect($ip,$db_name,$user,$password,$charset) {
		if (strpos($ip,':')!==false || strpos($db_name,':')!==false ) {
			throw new ExceptionDeveloper("Caracter ilegal ':' en IP o nombre de base de datos Interbase");
		}
		return ibase_pconnect($ip.':'.$db_name,$user,$password,$charset);
	}

	/**
	 * 
	 */
	public function query($conection,$sql) {
		return ibase_query($this->conection,$sql);
	}
	public function query_ranged($conection,$sql,$top_limit=-1, $bottom_limit=-1, $sql_order='') {
		$sql .= " ".$sql_order;
		if ( $top_limit >= 0 && $bottom_limit >= 0) {
			$bottom_limit++;
			$top_limit = $bottom_limit + $top_limit;
			$sql .= " ROWS " . $bottom_limit." TO ". $top_limit;
		} elseif ( $top_limit > 0 ) {
			$top_limit++;
			$sql .= " ROWS " . $top_limit; //TODO: Comprobar
		}
		return $this->query($conection,$sql);
	}
	public function get_last_insert_id($conection) {
		throw new ExceptionDeveloper("Not Implemented");
	}
	public function get_affected_rows($conection) {
		throw new ExceptionDeveloper("Not Implemented");
	}
	public function reset($query_result) {
		throw new ExceptionDeveloper("Not Implemented");
	}
	public function query_columns($conection,$table) {
		$sql  = 'SELECT RDB$RELATION_NAME, RDB$FIELD_NAME FROM RDB$RELATION_FIELDS ';
		$sql .= 'WHERE RDB$RELATION_NAME=\''.$table.'\'';
		$result = $this->query($conection,$sql);
		$row = $this->fetch_assoc($result);
		while ( $row ) {
			$columns[] = trim($row['RDB$FIELD_NAME']);
			$row = $this->fetch_result($result);
		}
		return $columns;
	}
	public function query_primary_keys($conection,$table) {
		throw new ExceptionDeveloper("Not Implemented");
	}
	public function escape_column_name($col_name) {
		throw new ExceptionDeveloper("Not Implemented");
	}
	function escape_string($var) {
		throw new ExceptionDeveloper("Not Implemented");	
	}
		
	function query_tables($conection) {
		throw new ExceptionDeveloper("Not Implemented");	
	}

	public function get_last_error($conection) {
		throw new ExceptionDeveloper("Not Implemented");
	}
	/**
	 * @param unknown_type $conection
	 */
	public function get_last_error_num($conection) {
		throw new ExceptionDeveloper("Not Implemented");
	}


}


?>