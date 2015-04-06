<?php


class commands_factory {
	/**
	 * 
	 * @param string $command_key
	 * @return icommand_external
	 */
	public static function create($command_key) {
		switch ($command_key) {
			case 'delete':
				return new command_delete();
			case 'edit':
				return new command_edit();
			case 'new':
				return new command_new();
			case 'print':
				return new command_print();
			case 'insert':
				return new command_insert();
			case 'remove_file':
				return new command_remove_file();
			case 'update':
				return new command_update();
			case 'table':
				return new command_table();
			case 'set':
				return new command_set();
			case 'insert_or_update':
				return new command_insert_or_update();
			default:
				return false;
		}
	}
	protected static function get_all_commands_array() {
		$result = array();
		$result[] = self::create('table');
		
		
		$result[] = self::create('new');
		$result[] = self::create('edit');
		$result[] = self::create('print');
		
		$result[] = self::create('delete');
		
		$result[] = self::create('insert');
		$result[] = self::create('set');
		$result[] = self::create('insert_or_update');
		$result[] = self::create('update');
		$result[] = self::create('remove_file');
		return $result;
	}
	public static function add_all_commands_to(table_data &$table) {
		$cs = self::get_all_commands_array();
		foreach ($cs as $c) {
			$table->add_command($c);
		}
	}
	public static function get_default_click_command_name() {
		return 'edit';
	}
	
}

?>