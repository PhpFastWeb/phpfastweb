<?php

abstract class acommand_row extends acommand_external implements icommand_row {
	
	/**
	 * @var key_set
	 */
	protected $key_set;
	public function set_key_set(key_set $key_set, $key_values=null) {
		if ($key_values!=null && !is_array($key_values)) throw new ExceptionDeveloper('Array or null expected'); 
		$this->key_set = $key_set;
		if ( ! is_null($key_values) ) {
			$this->key_set->set_keys_values($key_values);
		}
		
	}
	public function get_key_set() {
		return $this->key_set;
	}
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return icommand_row
	 */
	public static function &cast(icommand $cmd) {
		if ( ! ($cmd instanceof icommand_row) ) {
			throw new ExceptionDeveloper("Clase incorrecta");
		}
		return $cmd;
	}
	
	public function get_execute_url() {
		$url = $this->key_set->get_url();
		$url->set_var ( $this->get_command_label (), $this->get_key () );
		return $url;
	}

	protected function get_parameters_str() { 
		if (!isset($this->key_set) || count($this->key_set->keys_values)==0) return '';
		return $this->table->key_set->get_pks_string(); 
	
	}
	
    /**
     * Show a link to the right of the row for that command
     */
    public $show_row_link_on_table = true;

}

?>