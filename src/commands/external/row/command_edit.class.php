<?php
class command_edit extends acommand_modify implements icommand_row {
	
	public function get_name() {
		return "Editar";
	}
	public function get_key() {
		return "edit";
	}
	
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return command_edit
	 */
	public static function &cast(icommand $cmd) {
		if ( ! ($cmd instanceof command_edit ) ) {
			throw new ExceptionDeveloper ( "Clase incorrecta" );
		}
		return $cmd;
	}
	protected function get_command_target_name() {
		return 'update';
	}
	protected function init_values() {
		$row = $this->table->fetch_row( $this->key_set );
		$this->table->columns_col->set_values_array ( $row );
		$this->table->after_init_values();
	}

}

?>