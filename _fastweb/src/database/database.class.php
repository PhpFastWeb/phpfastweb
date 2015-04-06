<?php

class database extends database_wrapper {

	public $capitalize_values_use = false;
	public $force_lowercase_columns_names = true;
    /**
   	 * @var resource
     */
    public $conection = null;
    public $use_persistent_conection = true;
    public $date_format = 'Y-m-d';
    public $time_format = 'G:i:s';    
    public $initialised = false;
    /**
     * @var database_config
     */
    public $db_config = null;

    
    public function get_last_sql() {
    	return $sql;
   	}

    //-- Constructor --------------------

  	public function __construct($config=null) {
  		//TODO: Dejar de utilizar $config
	   	if ($config!=null) {
			$this->db_config = $config;
	   	}
  	}

  	public function init_config() {
  		if ($this->initialised) return;
  		if ($this->db_config != null) {
  			$this->db_provider = database_factory::create($this->db_config->type);
  		} else {
  			$this->init_config_deprecated();
  		}
        $this->connect();
  		$this->initialised = true;
  	}

	// DEPRECATED METHODS ----------------------------------------------------
  	/**
  	 * @deprecated
  	 */
  	public function init_config_deprecated() {
  		$this->db_config = new database_config();
		$this->db_config->caller_host = '';
		$this->db_config->ip = $this->db_cfg['ip'];
		$this->db_config->user = $this->db_cfg['user'];
		$this->db_config->password = $this->db_cfg['password'];
		$this->db_config->db_name = $this->db_cfg['db_name'];
		if ( $this->db_cfg['type'] == 'mysql' ) {
			$this->db_config->type = database_config::TYPE_MYSQL;
		}
  	}

  	public function __destruct() {
  		$this->close();
  	}

	/**
	 * @return array
	 */
	public function get_last_insert_id() {
		$id = $this->db_provider->get_last_insert_id($this->conection);
		if ($this->force_lowercase_columns_names) {
			$this->lowercase_keys($id);
		}
		return $id;
	}  	
 	public function connect() {
		//$this->init_config();
 		if ( $this->conection ) return $this->conection;
 		if ( $this->use_persistent_conection ) {
 			return $this->connect_persistent();
 		} else {
 			return $this->connect_non_persistent();
 		}
 	}
 	public function connect_non_persistent() {
 		if ( $this->conection ) return $this->conection;
 		$this->conection = $this->db_provider->connect(
 			$this->db_config->ip,
 			$this->db_config->db_name,
 			$this->db_config->user,
 			$this->db_config->password
 		);	
 		return $this->conection;
 	}
 	public function connect_persistent() {
 		if ( $this->conection ) return $this->conection;
 		$this->conection = $this->db_provider->pconnect(
 			$this->db_config->ip,
 			$this->db_config->db_name,
 			$this->db_config->user,
 			$this->db_config->password
 		);	
 		return $this->conection;
 	}

 	public function close() {
 		if ( $this->conection ) {
 			return $this->db_provider->close($this->conection);
 		}
 		return true;
 	}

	public function num_rows($query_result) {
        if ( $query_result !== false ) {
			$this->num_rows = $this->db_provider->num_rows($query_result);
		} else {
  			$this->num_rows = 0;
		}
		return $this->num_rows;
	}

	public function fetch_result($query_result) {
		$result = $this->db_provider->fetch_assoc($query_result);
		if (is_array($result)) {
			if ( $this->capitalize_values_use ) {
				$this->capitalize_keys($result);
			}
		}
		return $result;
	}

	/**
	 * @deprecated
	 */
	public function execute_sql($sql) {
 		if ( ! $this->conection ) $this->connect();
 		$this->sql = $sql;
 		$result = $this->db_provider->execute_nonquery($this->conection,$sql);
		return $result;
	}

	public function execute_query($sql,$top_limit=-1, $bottom_limit=-1, $sql_order='') {	
		if ( ! $this->initialised ) $this->init_config();
		if ( ! $this->conection ) $this->connect();
		$this->sql = $sql;
		if ( $sql instanceof sql_str ) $sql = $sql->__toString();
		//--
        if ($top_limit==-1 && $bottom_limit==-1 && $sql_order=='') {
            $query_result = $this->db_provider->execute_query($this->conection,$sql);
        } else {
            $query_result = $this->db_provider->execute_query_ranged($this->conection,$sql,$top_limit,$bottom_limit,$sql_order);    
        }
        if ( $query_result !== false ) {
			$this->num_rows = $this->db_provider->num_rows($query_result);
		} else {
  			$this->num_rows = 0;
		}		
        return $query_result;
	}
	
	/**
	 * Returns false if error occured, true if no error
	 * @return bool
	 */
	public function execute_nonquery($sql) {
		if ( ! $this->initialised ) $this->init_config();
 		if ( ! $this->conection ) $this->connect();
 		$this->sql = $sql;
 		if ( $sql instanceof sql_str ) $sql = $sql->__toString();
 		//--
 		$result = $this->db_provider->execute_nonquery($this->conection,$sql);
 		return $result;
	}

	/**
	 * Export SQL query as an CSV excel friendly file and send inmediatly
	 * @param $result execute_query result
	 * @param $filename name of file to send
	 */
	public function export_csv($result,$filename='') {
		if ( ! $result ) {
			return false;
		}
		if ($filename=='') $filename = 'datos_'.Date('Y-m-d_H_i').'.csv';
		$out = '';
		$row = website::$database->fetch_result($result);
		
		$fields = array_keys($row);
		$n_fields = count($fields);
	 	$separator = ";";
		// Put the name of all fields
		for ($i = 0; $i < $n_fields; $i++) {
			$text = $fields[$i];
			$text = str_replace('\\','\\\\',$text);
			$text = str_replace('"','\\"',$text);
			$out .= '"'.$text.'"';
			if (($i+1)!= $n_fields) $out .= $separator;
		}
		$out .="\n";
		// Add all values in the table
	
		while ($row) {
			for ($i = 0; $i < $n_fields; $i++) {
				$text = $row[$fields[$i]];
				$text = str_replace('\\','\\\\',$text);
				$text = str_replace('"','\\"',$text);
				$out .='"'.$text.'"';
				if (($i+1)!= $n_fields) $out .= $separator;
			}
			$out .="\n";
			$row = website::$database->fetch_result($result);
		}
		// Output to browser with appropriate mime type, you choose ;)
		ob_clean();
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Disposition: attachment; filename = $filename");
		header("Content-Length: " . strlen($out));
		header("Content-type: text/x-csv");
		//header("Content-type: text/csv");
		//header("Content-type: application/csv");
		echo $out;
	}

	//-----------------------------------------------------------------------------------------
	public function exist_row($table_name, key_set $key_set) {
	   
		if( ! $key_set->is_fully_defined() ) {
			throw new ExceptionDatabase("No se definieron completamente las claves del registro a buscar");
		}
		
		$result = $this->fetch_row3($table_name,$key_set,array(reset($key_set->primary_keys)));
		if ($result===false || (is_array($result) && count($result)==0 ) ) return false;
		return true;
	}
	public function exist_row2($table_name,$field_name,$field_value) {
		$table_name = $this->escape_column_name($table_name);
		$field_name = $this->escape_column_name($field_name);
		$field_value = $this->escape_string($field_value);
		
		$sql = "SELECT COUNT(1) FROM $table_name WHERE $field_name = '$field_value'";
		$result = $this->execute_get_simple_value($sql);
		if ($result>=1) return true;
		return false;
	}
	/**
	 * Inserta en una tabla en las columnas especificadas los valores especificados como datos.
	 * Si el array de datos contiene valores con claves distintas a los especificados en el array de columnas, son ignorados.
	 * Devuelve el ID del registro recien insertado en caso de que sea generado automï¿½ticamente.
	 * @param string $table_name Nombre de la tabla
	 * @param Array $columns Array con la lista de columnas a insertar
	 * @param Array $data_array Array con los datos a insertar con las columnas correspondientes como claves
	 * @return string id del registro recién insertado, o falso en caso de error
	 */
	public function insert($table_name,$columns,$data_array) {
		$tn = $this->escape_column_name($table_name);
		$keys=""; $values="";
		foreach ($columns as $col_name) {
			if (isset($data_array[$col_name])) {
				$keys .= $this->escape_column_name($col_name).",";

				$values .= "'".$this->escape_string($data_array[$col_name])."',";
			}
		}
		$keys = substr($keys, 0, -1);
		$values = substr($values, 0, -1);
		$sql = "INSERT INTO $tn ($keys) VALUES ($values)";

		$result = $this->execute_nonquery($sql);
		if ($result) {
			$id = $this->get_last_insert_id();
		} else $id = false;
		return $id;
		//SELECT LAST_INSERT_ID()
		//INSERT INTO table (a,b,c) VALUES (1,2,3),(4,5,6)
		// -> ON DUPLICATE KEY UPDATE c=VALUES(a)+VALUES(b);
	}
	/**
	 * Insert a row or, if primary keys values exists, update the row.
	 * Check and insert/update is atomic.
	 * @param $table_name Name of the table where to insert the data
	 * @param $pk_fields_array array of primary keys columns names
	 * @param $data_array Asociative array where keys are column names, and values are data
	 */
	public function insert_or_update($table_name,$pk_fields_array,$data_array) {
		if ( count( array_diff ($pk_fields_array, array_keys($data_array))) >0 ) {
			throw new ExceptionDatabase('keys values not defined in data');
		}
		$keys=""; $values=""; $updates = "";
		foreach ($data_array as $col_name => $value) {
			$keys .= $this->escape_column_name($col_name).",";
            if (is_null($value)) {
                 $values .= "NULL,";
            } else {
			     $values .= "'".$this->escape_string($value)."',";
			}
            $updates .= $this->escape_column_name($col_name) ."='".$this->escape_string($value)."',";
		}
		$keys = substr($keys, 0, -1);
		$values = substr($values, 0, -1);
		$updates = substr($updates, 0, -1);
		$tn = $this->escape_column_name($table_name);
		$sql = "INSERT INTO $tn ($keys) VALUES ($values) ON DUPLICATE KEY UPDATE $updates ";	
		//var_dump($sql); die;
		$result = $this->execute_nonquery($sql);
		if ($result) {
			$result = $this->get_last_insert_id();
		}
		return $result;
	}
	public function insert2($table_name,$associative_array) {
	   	$parameter = $associative_array;
		$keys=""; $values="";
		foreach ($parameter as $key => $value) {
			$keys .= $this->escape_column_name($key).",";
			//if ( ! get_magic_quotes_runtime() && ! get_magic_quotes_gpc() ) {
			//  $value = addslashes($value);
			//}
			$values .= "'".$this->escape_string($value)."',";
		}
		$keys = substr($keys, 0, -1);
		$values = substr($values, 0, -1);
		$sql = "INSERT INTO $table_name ($keys) VALUES ($values)";

		return $this->execute_nonquery($sql);
	}
	public function update($table_name,$columns, $data_array,$primary_key_name,$primary_key_value) {
		$set="";
		foreach ($columns as $col_name) {
			if (isset($data_array[$col_name])) {
				$set .= $col_name."='$data_array[$col_name]',";
			}
		}
		$set = substr($set, 0, -1);
		$sql = "UPDATE $table_name SET $set WHERE $primary_key_name='$primary_key_value';";
		$result = $this->execute_nonquery($sql);
		if ( ! $result ) return $result;
		if ( $this->get_affected_rows() != 1 ) $result = false;
		return $result;
	}	
	public function update3($table_name, $columns_array, $data_array, key_set $key_set) {
		//var_dump($data_array);
		//die;
		if (empty($table_name)) {
			throw new ExceptionDatabase("Nombre de tabla no definida");
		}
		$set="";
		foreach ($columns_array as $col_name) {
			if (isset($data_array[$col_name])) {
				$set .= $this->escape_column_name($col_name)."='".$this->escape_string($data_array[$col_name])."',";
			}
		}
		$set = substr($set, 0, -1);
		//
		$where='';
		foreach ($key_set->primary_keys as $pk) {
			if (!isset($key_set->keys_values[$pk]) || $key_set->keys_values[$pk]==="") {
				throw new ExceptionDatabase('Falta/n valor(es) para primary key');
			}	
			$pkv = $key_set->keys_values[$pk];
			$where .=$this->escape_column_name($pk)."='".$this->escape_string($pkv)."' AND";
		}
		$where = substr($where,0,-4);
		//
		$sql = "UPDATE $table_name SET $set WHERE $where;";
		//die($sql);
		$result = $this->execute_nonquery($sql);
		if ( ! $result ) return $result;
		if ( $this->get_affected_rows() > 1 ) {
			//Se puede actualizar 0 filas si por ejemplo se sobreescribe un fichero
			//con otro del mismo nombre
			//TODO: Detectar que no hay cambios y no ejecutar UPDATE en base de datos
			throw new ExceptionDatabase("Número de registros actualizado $n > 1");
		}
		return $result;
	}
	public function update2($table_name, $parameter, $pks_list) {
		$sets=""; $pks="";
		$pks_array = explode(',',$pks_list);
		foreach ($parameter as $key => $value) {
			if (in_array($key,$pks_array)) {
				$pks .= "$key = '$value' AND ";
			} else {
				
				//if (!get_magic_quotes_runtime() && !get_magic_quotes_gpc()) {
				//  $value = addslashes($value);
				//}
				//$keys .= $key.",";
				//$values .= "'".addslashes($value)."',";
				$sets .="$key='".addslashes($value)."',";
			}
		}
		$sets = substr($sets, 0, -1);
		$pks = substr($pks,0,-5);
		//$values = substr($values, 0, -1);
		$sql = "UPDATE $table_name SET $sets WHERE $pks";
		//$sql = "UPDATE $table_name SET $sets VALUES ($values) WHERE $pks";
		$result = $this->execute_nonquery($sql);
	}
	public function insert_table_data(table_data &$table) {
        //Intentamos primero insertar las relaciones
        if ( count ( $table->relations ) ) {
            foreach ( $table->relations as $rel ) {
                if ( $rel->get_table_name_b() == $table->get_table_name() ) {
                    $this->insert_table_relation($table, $rel);
                }
            }
        }
        		
		$keys="";$values="";
		foreach ($table->columns_col as $col) {
			if ( ! $col instanceof column_file && $col->get_table_name() == $table->get_table_name() ) {
				$col = acolumn::cast($col);
				$k = $col->get_db_columns_str();
				$v = $col->get_db_values_str(true);
				$keys = ($k!='' && $v!='')? $keys.$k." , " : $keys; 
				$values = ($k!='' && $v!='')? $values.$v." , " : $values; 
			}
		}
		$keys = substr($keys,0,-3);
		$values = substr($values, 0, -3);
		$table_name = $this->escape_column_name($table->table_name);
		$sql = "INSERT INTO $table_name ($keys) VALUES ($values)";
		$result = $this->execute_nonquery($sql);
        
		//ATENCIÓN!!! $result puede ser false por un warning de insertar números escapados con ''
        //if ($result==false) { 
        //    $result = ( $this->get_last_error_num() != 0 );
        //}
        
        
        //if ( count($table->key_set->primary_keys) != 1 ) throw new ExceptionDeveloper(); 
		if ( $result ) {
            if ( ! $table->edit_pk && count($table->key_set->primary_keys) == 1) {
                //Devolvemos la clave primaria única insertada
                $id = $this->get_last_insert_id();
                $pk = reset($table->key_set->primary_keys);
    			if ( is_array( $id ) && isset($id['id'])) { //It allways returns 'id' as key no matter what
                    $table->key_set->keys_values[$pk] = $id['id'];
                } else {
    				//throw new ExceptionDeveloper('PK not defined in last insert id');
                    
                    //Cuando PK no es automática, last_insert_id no devuelve nada, pero no debería ser problema,
                    //Ya que las keys deberían ya tener valores correctos.
                    
                    //Para perfilar mejor esto, habría que especificar en cada key si es autonumérica
                    
                    //Lanzar aquí una excepción puede ser considerado por el llamante como una vulneración de las restricciones
                    //en la estructura de la base de datos, y enmascarar el error con un mensaje genérico al usuario.
    			}
            }
		}
        
		return $result;
	}
    
    protected function insert_table_relation(table_data &$table, table_relation &$relation) {
        //We check that the relation has our table as second designation
        if ( ! $relation->get_table_name_b() == $table->get_table_name() )
            throw new ExceptionDeveloper('Relations don\'t containt reference to current table');
        //We check that the table has previous table id set
        if ( ! isset ( $table->columns_col[$relation->get_id_a()] ) )
            throw new ExceptionDeveloper('Could not insert relation, insufficent data');
               
        //We check that the previous table id is set to previous table name
        if ( $table->columns_col->get( $relation->get_id_rel_a() )->get_table_name() 
            != 
            $relation->get_table_name_a() )
            throw new ExceptionDeveloper('Parent table id not defined in table');
        //We check that previous table column has defined insertion data
        if ( $table->columns_col->get( $relation->get_id_rel_a() )->get_value() == '' )
            throw new ExceptionDeveloper('Parent table id has no data in table');
        //We check that the current table id exists
        if ( ! isset ( $table->columns_col[$relation->get_id_b()] ) )
            throw new ExceptionDeveloper('Current id from relation not defined in table');
        //We check that the current table id contains data
        //if ( $table->columns_col[$relation->get_id_b()]->get_value() == '' )
            //throw new ExceptionDeveloper('Current id from relation has no data');
        //die;
        $sql = new sql_str( "INSERT INTO {#0} ( {#1}, {#2} ) VALUES ( '{3}', '{4}' )",
            $relation->get_table_name_rel(),
            $relation->get_id_rel_a(),
            $relation->get_id_rel_b(),
            $table->columns_col->get( $relation->get_id_rel_a() )->get_value(),
            '');
        $result = $this->execute_nonquery( $sql );
        if ( $result ) {
            $v = $this->get_last_insert_id();
            if ( ! isset( $v[ 'id' ] ) ) 
                throw new ExceptionDeveloper("Current table id not set from parent relation");
            $table->columns_col->get( $relation->get_id_b() )->set_value( $v['id'] ); 
        }
            
    }
    
	/**
	 * Updates a row.
	 * Throws exception if error or more than one row affected.
	 * @param $table table_data
	 * @return number of rows affected (1), 0 if same data on row
	 */
	public function update_table_data(table_data $table) {
		$sets="";
		foreach ($table->columns_col as $col) {
			//Filters out external table columns
			if ( ! $col instanceof column_file && 
                $col->get_table_name() == $table->get_table_name() &&
                $col->has_changed() 
                ) {
				$s = $col->get_db_asignation_str();
				$sets = ($s!='')? $sets.$s." , " : $sets;
			}
		}
		$sets = substr($sets,0,-3);
		
		$sql = new sql_str("UPDATE {#0} SET {@1} WHERE {@2}",
			$table->table_name,
            $sets,
			$table->key_set->get_sql_where($table->columns_col));
   
        //die($sql);

		$result = $this->execute_nonquery($sql);

		if ( $result === false ) {
			throw new ExceptionDatabaseQuery('Error executing update',$sql);
		} else {
			$result = $this->get_affected_rows();
		}
		if ( $result > 1 ) {
			throw new ExceptionDatabaseQuery('More than one row affected',$sql);
		}

		return $result;
	}
	/**
	 * Ejecuta una sentencia SQL y devuelve un array con el resultado.
	 * Cada fila estaría estructurada como un array numérico, y dentro de cada uno
	 * de estos, cada columna sería otro array, con los identificadores de columna
	 * como claves del mismo.
	 * @param string $sql
	 * @return array
	 */
	public function execute_get_array($sql) { //},$top_limit=-1, $bottom_limit=-1, $sql_order='') {
		$result = $this->execute_query($sql); //,$top_limit=-1, $bottom_limit=-1, $sql_order='');
		$a = $this->fetch_array($result);
		return $a;
	}
	/**
	 * Retrieves a single row from database and returns it as an array
	 * @param string sql
	 * @return array
	 */
	public function execute_get_row_array($sql) {
		$result = $this->execute_get_array($sql);

		if ($result && is_array($result) && count($result)>=1) {
			//TODO: Raise error for count>1
			$result = $result[0];
		}
		return $result;
	}
    
    /**
     * 
     */
	public function execute_get_associative_array($sql) {
		$result = $this->execute_query($sql);
		$a = $this->fetch_associative_array($result);
		return $a;
	}
    /**
     * Execute sql query and returns an array of rows, where the row index is
     * equal to the first value got from the query.
     * The user must ensure that value has no repetitions or data will be lost.
	 * @param string $sql
	 * @return array
     */
    public function execute_get_id_key_array($sql) {
		$result = $this->execute_query($sql);
		$a = $this->fetch_id_key_array($result);
		return $a;
	}
		
	/**
	 * Ejecuta una sentencia SQL para obtener un ï¿½nico valor.
	 * Devuelve el valor encontrado, en caso de que sea ï¿½nico, de lo contrario
	 * se produce un error. Si no encuentra ningun valor, no se produce error.
	 * @param string $sql
	 */
	public function execute_get_value($sql) {
		$result = $this->execute_get_array($sql);
		if ( count($result) == 1 && isset($result[0])) {
			return $result[0];
		} else {
			if ( count($result) > 1 ) {
				throw new ExceptionDatabase("**Error: database::execute_get_value deber&iacute;a encontrar un &uacute;nico valor, encontrados m&uacute;ltiples.<br />SQL: ".$sql);
			} else {
				return $result;
			}
		}
	}
	public function execute_get_simple_value($sql) {
		$result = $this->execute_get_value($sql);
		if ( ! is_null($result) && is_array($result) ) {
			$result = current($result);
		}
		return $result;
	}
	public function count($sql_from, $where='', $group_by='', table_data $table=null ) {
        if ( ! $this->initialised ) $this->init_config();
        
        if ($group_by != '') {
            //If we have grouping, we need a less efficient approach.
            //But keep the other case as it might be faster for the general case.            
            assert::is_set($table);
            assert::is_instance_of($table,'table_data');
            assert::is_set($table->key_set);
            if ( count($table->key_set->primary_keys) != 1 ) throw new ExceptionDeveloper('Count for multiple PK not implemented');
            
            $sql = new sql_str('SELECT COUNT( DISTINCT {#0}.{#1}) AS COUNT_ROWS FROM {@2}',
                $table->table_name,
                $table->key_set->primary_keys[0],
                $sql_from);
        } else {
            $sql = "SELECT COUNT(1) AS COUNT_ROWS FROM $sql_from";
            
            if ( $where != '' ) {
                $where = trim($where);
                if ( substr($where,0,5)=='where' || substr($where,0,5)=='WHERE' ) {
                	$sql .= " $where";
                } else {
                	$sql .= " WHERE $where";
                }
            }
        }
        
        $res = $this->execute_query($sql);
        $fet = $this->fetch_result($res);
        
        if ( $fet === false || ! isset( $fet['COUNT_ROWS'] ) ) {
            throw new ExceptionDatabase('Error counting records');
        }
        return $fet['COUNT_ROWS'];
	}
	//-- Funciones de busqueda de datos en resultado -----------------------------------	
	public function fetch_array($query_result) {
		if ($query_result===false) return $query_result; //Error
		$result = array();
		$i=0;
		$item = $this->fetch_result($query_result);
		while ( $item ) {
			$result[$i] = $item;
			$item = $this->fetch_result($query_result);	
			$i++;
		}
		return $result;
	}
	public function fetch_id_key_array($query_result) {
		$result = array();
		$i=0;
		$item = $this->fetch_result($query_result);
        if (!$item) return $result;
        $keys = array_keys($item);        
		while ( $item ) {
            $new_row = array_slice($item,1,count($item)-1,true);
            $result[reset($item)] = $new_row;
			$item = $this->fetch_result($query_result);	
		}
		return $result;	   
	}
	public function fetch_associative_array($query_result) {
		$result = array();
		$i=0;
		$item = $this->fetch_result($query_result);
		while ( $item ) {
			
			$key = current($item);
			$value = next($item);
			$result[$key] = $value;
            
			$item = $this->fetch_result($query_result);
		}
		return $result;
	}
	/**
	 * Devuelve una fila identificada unívocamente por una clave primaria.
	 *
	 * @param $table_name string Nombre de la tabla
	 * @param $pk string Nombre del campo que hace de clave primaria
	 * @param $id string Valor de la clave primaria buscada
	 * @param $strict_only_one_row bool Throw an error if found more than one row 
	 * @return Array Array asociativo con la tupla que contiene los datos
	 */
	public function fetch_row( $table_name, $pk, $id, $strict_only_one_row = false)  {
	   $this->init_config();
		$sql = "SELECT * FROM $table_name WHERE ".$this->escape_column_name($pk)."='".$this->escape_string($id)."'";
		$result = $this->execute_query($sql);
		$row = $this->fetch_result($result);
		if ( $strict_only_one_row && $this->fetch_result($result) !== false ) {
			throw new ExceptionDatabase("More than one element returned from fetch row");
		}
		return $row;
	}
	/**
	 * Devuelve una fila identificada unï¿½vocamente por un conjunto de claves primarias.
	 *
	 * @param string $table_name Nombre de la tabla
	 * @param Array $pks Array con los nombres de los campos de clave primaria.
	 * @param Array $ids Array asociativo, con un valor de clave primaria por cada nombre de clave primaria.
	 * @return Array Array asociativo con la tupla que contiene los datos
	 */
	public function fetch_row2($table_name,$pks,$ids) {
		if (!(is_array($pks) && is_array($ids) && count($pks)>0 && count($pks)==count($ids))) {
			throw new ExceptionDatabase("Bad parameters");
		}
		$sql = "SELECT * FROM $table_name ";
		if (count($pks)>0) {
			$sql .= "WHERE ";
			foreach ($pks as $pk) {
				$sql .= "$pk='".$ids[$pk]."' AND ";
			}
			$sql = substr($sql,0,-4);
		}
		$result = $this->execute_query($sql);
		$row = $this->fetch_result($result);
		if ( $this->fetch_result($result)!== false ) {
			throw new ExceptionDatabase('More than one row fetched');
		}
		return $row;
	}
	/**
	 * 
	 * @param string $table_name
	 * @param key_set $key_set
	 * @param array $col_names
	 * @return mixed
	 */
	public function fetch_row3($table_name,key_set $key_set, $col_names_array) {
		if (count($key_set->keys_values)==0) throw new ExceptionDatabase('key_set doesn\'t have values');
		$sqlcols = '';
		foreach ($col_names_array as $col) {
			$sqlcols .= $this->escape_column_name($col).", ";
		}
		$sqlcols = substr($sqlcols,0,-2);
		$sql = "SELECT $sqlcols FROM $table_name WHERE ";
		foreach ($key_set->keys_values as $pk => $value) {
			$sql .= $this->escape_column_name($pk)."='".
				$this->escape_string($value).
				"' AND ";
		}
		$sql = substr($sql,0,-4);
		//TODO: Usar execute_query($sql,1,1)

		$result = $this->execute_query($sql);
		$row = $this->fetch_result($result);
		//TODO: Comprobar que no solo devuelve un elemento
		return $row;
	}

	//--------------------------------------------------------------
	
	// Funciones auxiliares de DB no específicas
	
	public function fetch_columns_names($query_result) {
		$result = array();
		$row = $this->fetch_result($query_result);
		if ( $row ) {
			$result = array_keys($row);
			$row = $this->fetch_result($query_result);
		}
		return $result;
	}



	// -- Funciones auxiliares ---------------------------------------------------------
	
	// Funciones auxiliares de strings y sql
	function str_sql_without_where($sql) {
		$spos = strpos($sql, "WHERE");
		if ($spos===false) {
			return $sql;
		} else {
			return substr($sql,0,$spos);
		}
	}
	function str_sql_where_clause($sql) {
		$spos = strpos($sql, "WHERE");
		if ($spos===false) {
			return false;
		} else {
			return substr($sql,$spos+5);
		}		
	}	
	function concat_sql_restrictions($orig,$aditional) {
		if ( $orig != '' ) {
			if ( $aditional != '' ) {
				if ( $aditional[0] != '(' || substr($aditional,-1,1) != ')' ) {
					$aditional = "( $aditional )";
				}
				if ( $orig[0] != '(' || substr($orig,-1,1) != ')' ) {
					$orig = "( $orig )";
				}				
				return "$orig AND $aditional";
			} else {
				return $orig;
			}
			
		}
		else return $aditional;
	}
	// Funciones auxiliares de arrays
	function capitalize_keys(&$array) {
		$t = array();
		foreach ($array as $key => $value ) {
			$t[strtoupper($key)]=$value;
		}
		$array = $t;
		return true;
	}
	function lowercase_keys(&$array) {
		$t = array();
		foreach ($array as $key => $value ) {
			$t[strtolower($key)]=$value;
		}
		$array = $t;
		return true;
	}
	function capitalize_values(&$array) {
		if ( ! $this->capitalize_values_use ) {
			return true;
		}
		if ( count( $array > 0 ) ) {
			foreach( $array as $key => $value ) {
				$array[$key] = strtoupper($value);
			}
		}
		return true;
	}
	function addslashes_array($input_array) {
		//TODO: Comprobar si MagicQuotes esta activo
		//if (!get_magic_quotes_runtime() && !get_magic_quotes_gpc()) {
		//  $value = addslashes($value);
		//}
		$data = array();
		foreach($input_array as $key=> $value ) {
			$data[$key] = addslashes($value);
		}
		return $data;		
	}
	/*
	// -- Funciones de fecha y hora ----------------------------------------------------
	function get_date($date_value,$format='d-m-Y') {
		if ($date_value == '' ) return '';
		//if ($this->db_cfg['type']=='mysql') {
			$regs=array();
			//En mysql, por defecto la fecha tiene de formato: '0000-00-00' (aï¿½o-mes-dia)
			$regs=array();
			if ( ! ereg('([0-9]{4})-([0-9]{2})-([0-9]{2})',$date_value,$regs) ) {
				return ("**Error: Formato de fecha no reconocido: $date_value");
			}
			$year  = $regs[1];
			$month = $regs[2];
			$day   = $regs[3];
			if ($year=='0000' && $month=='00' && $day == '00' ) $result = '';
			else $result = date($format, mktime(0, 0, 0, $month, $day, $year));
		//} else {
		//	$result = $date_value;
			//throw new ExceptionDeveloper("***No implementado");
		//}
		return $result;
	}
	function get_time($time_value, $format='H:i') {
		if ($time_value == '' ) return '';
			//En mysql, por defecto la hora tiene de formato: '00:00:00' (hora:minuto:segundo, formato 24 horas)
			$regs=array();
			if ( ! ereg('([0-9]{2}):([0-9]{2}):([0-9]{2})',$time_value,$regs) ) {
				return ("**Error: Formato de fecha no reconocido: $time_value");
			}
			$hour   = $regs[1];
			$minute = $regs[2];
			$second = $regs[3];
			
			// TODO: Hacer que los segundos sean opcionales 
			if ( $second=='00' && $hour=='00' && $minute == '00' ) $result = '';
			else $result = date($format, mktime( $hour, $minute, $second ));			
		return $result;
	}
	function get_datetime($datetime_value, $format='d-m-Y H:i') {
		if ($datetime_value == '' ) return '';

			//En mysql, por defecto fecha y hora tiene de formato: '0000-00-00 00:00:00' (aï¿½o-mes-dia hora:minuto:segundo, formato 24 horas)
			$regs=array();
			if ( ! ereg('([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})',$datetime_value,$regs) ) {
				return ("**Error: Formato de fecha no reconocido: $datetime_value");
			}
			$year   = $regs[1];
			$month  = $regs[2];
			$day    = $regs[3];			
			$hour   = $regs[4];
			$minute = $regs[5];
			$second = $regs[6];
			if ( $year=='0000' && $month=='00' && $day == '00' && $second=='00' && $hour=='00' && $minute == '00' ) $result = '';
			else $result = date($format, mktime( $hour, $minute, $second, $month, $day, $year ));	
		
		return $result;			
	}

	function now_date() {
		$result =  date($this->get_date_format());
		$result = strtolower($result);
		return $result;
	}
	function now_time() {
		return strtolower( date($this->get_time_format()) );
	}
	function now_datetime() {
		$result =  date($this->get_datetime_format());
		$result = strtolower($result);
		return $result;
	}
	function get_date_format() {
		$result = $this->date_format;
		return $result;
	}
	function get_time_format() {
		return $this->time_format;
	}
	function get_datetime_format() {
		return $this->date_format.' '.$this->time_format;
	}	
	function parse_datetime($human_date_and_or_time) {
		throw new ExceptionDatabase("***No implementado");
	}
	*/
	// -- Funciones para procesar orden artificial de tuplas ---------------------------
	function reorder_item($table_name,$pk,$new_position,$order_field) {
		$sql_where = '';
		foreach($pk->primary_values as $key => $value) {
			$sql_where .= $key .' = '.$value. ' and ';
		}
		$sql_where = substr($sql_where,0,-1*strlen(' and '));
		
	}
	function get_last_order($table_name,$order_field) {
		
	}
	function get_order_regen_sql($table_name,$order_field) {
		$sql='
			set @nord := 0;
			update '.$table_name.'
			set
			'.$order_field.' = ( SELECT @nord := @nord+2 )
			order by '.$order_field.' desc;';
		return $sql;
	}

	//------------------------------------------------------------------------------------------------------------------

}
?>
