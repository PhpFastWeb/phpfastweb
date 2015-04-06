<?php
class command_set extends acommand_modify implements icommand_row {
	
	public function get_name() {
		return "Establecer";
	}
	public function get_key() {
		return "set";
	}
	public $is_reedit = false;
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return command_set
	 */
	public static function &cast(icommand $cmd) {
		if (! ($cmd instanceof command_new)) {
			throw new ExceptionDeveloper ( "Clase incorrecta" );
		}
		return $cmd;
	}
	protected function get_command_target_name() {
		return 'insert_or_update';
	}
	protected function init_values() {
	   	if ( $this->is_reedit ) {
            return;				
		} 
		$row = false;
		
		$r = $this->table->columns_col->get_restricted_values();
		if ( ! $r ) {
			throw new ExceptionDeveloper('Columns restriction for getting single row to set not defined');
		}
		$row = website::$database->fetch_row2 ( $this->table->table_name, array_keys ( $r ), $r );
		if ( ! is_array ( $row )) {
			$this->table->columns_col->set_columns_new_row ();
		} else {
			$this->table->columns_col->set_values_array ( $row );
			$this->table->key_set->set_keys_values($row);
		}
	}

	public function execute() {
		//$this->table->control_group->set_command_target('insert_or_update');
        parent::execute();
	}
}

?>
