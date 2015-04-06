<?php
/**
 * Wraps arround idatabase_provider for a single conection
 * @author Vicente Herrera García
 */
interface idatabase_wrapper {    
  	/** 
  	 * @return idatabase_provider
  	 */
  	public function get_db_provider();
  	
  	/**
  	 * @param idatabase_provider $db_provider
  	 * @return 
  	 */
  	public function &set_db_provider(idatabase_provider $db_provider);
  	
	function escape_string( $var );
	
	public function get_affected_rows();
	
	public function reset($query_result);
	
	function escape_column_name($column_name);
	
	public function escape_table_name($table_name);
	
  	public function get_last_error();
    
    public function get_last_error_original();
  	
  	public function get_last_error_num();

}