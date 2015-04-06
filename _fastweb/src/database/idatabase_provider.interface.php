<?php

interface idatabase_provider {
	/**
	 * Opens a conection to a database
	 * @param string $ip
	 * @param string $db_name
	 * @param string $user
	 * @param string $password
	 * @param string $charset
	 * @return resource
	 */
	public function connect($ip,$db_name,$user,$password,$charset='');
	/**
	 * Opens a persistant connection to a database
	 * @param string $ip
	 * @param string $db_name
	 * @param string $user
	 * @param string $password
	 * @param string $charset
	 * @return resource
	 */
	public function pconnect($ip,$db_name,$user,$password,$charset='');
	/**
	 * Closes a connection
	 * @param resource $conection
	 * @return boolean
	 */
	public function close($conection);
	/**
	 * 
	 * @param resource $query_result
	 * @return string
	 */
	public function errmsg($query_result);
	/**
	 * 
	 * @param resource $query_result
	 * @return array
	 */
	public function fetch_assoc($query_result);
	/**
	 * 
	 * @param resource $query_result
	 * @return int
	 */
	public function num_rows($query_result);
	/**
	 * 
	 * @param resource $query_result
	 * @return int
	 */	
	public function affected_rows($conection);
	
	/**
	 * Executes SQL that returns a number of rows, or 0 rows.
	 * If it returns false, an exception is thrown.
	 * @param resource $conection
	 * @param string $sql
	 * @return resource
	 */
	public function execute_query($conection,$sql);
	/**
	 * Executes SQL that doesn't return any rows.
	 * It can return false from database with some valid operations.
	 * @param resource $conection
	 * @param string $sql
	 * @return resource
	 */
	public function execute_nonquery($conection,$sql);	
	/**
	 * 
	 * @param resourece $conection
	 * @param string $sql
	 * @param int $top_limit
	 * @param int $bottom_limit
	 * @param string $sql_order
	 * @return resource
	 */
	public function execute_query_ranged($conection,$sql,$top_limit=-1, $bottom_limit=-1, $sql_order='');
	
	/**
	 * 
	 * @param resource $conection
	 * @return string
	 */
	public function get_last_insert_id($conection);
	/**
	 * 
	 * @param resource $conection
	 * @return int
	 */
	public function get_affected_rows($conection);
	
	/**
	 * 
	 * @param resource $query_result
	 * @return bool
	 */
	public function reset($query_result);	
	/**
	 * 
	 * @param string $col_name
	 * @return string
	 */
	public function escape_column_name($col_name);
	
	/**
	 * 
	 * @param string $var
	 * @return string
	 */
	function escape_string($var);
	
	/**
	 * @param resource $conection
	 * @return string
	 */
	function get_last_error($conection);
	/**
	 * @param resource $conection
	 * @return string
	 */
	function get_last_error_num($conection);
	
}

?>