<?php
/**
 * Wrapper around idatabase_provider to be implemented with interchangeable object,
 * and be extended on main database class
 */
class database_wrapper implements idatabase_wrapper {

	public $sql = '';

    public $num_rows = -1;
    
  	// Wrapper around db_provider ------------------------------------------
  	
	/**
     * @var idatabase_provider
     */
    public $db_provider = null;     //todo: volver private again
    
  	/** 
  	 * @return idatabase_provider
  	 */
  	public function get_db_provider() {
  		return $this->db_provider;
  	}
  	
  	/**
  	 * @param idatabase_provider $db_provider
  	 * @return 
  	 */
  	public function &set_db_provider(idatabase_provider $db_provider) {
  		$this->db_provider = $db_provider;
  		return $this;
  	}
  	
	function escape_string( $var ) { //TODO: change name to scape_data
	   $this->init_config();
		return $this->db_provider->escape_string($var);		
	}
	
	public function get_affected_rows() {
		return $this->db_provider->get_affected_rows($this->conection);	
	}
	
	public function reset($query_result) {
		return $this->db_provider->reset($query_result);
	}	
	
	function escape_column_name($column_name) { //TODO: Unify with table name to scape_name
		return $this->db_provider->escape_column_name($column_name);
	}  	
	
	public function escape_table_name($table_name) { //TODO: Unify with column name to scape_name
		return $this->escape_column_name($table_name);
	}	
	
  	public function get_last_error() {
  		return $this->db_provider->get_last_error($this->conection);
  	}
  	public function get_last_error_original() {
  		return $this->db_provider->get_last_error_original($this->conection);
  	}  	
  	public function get_last_error_num() {
  		return $this->db_provider->get_last_error_num($this->conection);
  	}

}