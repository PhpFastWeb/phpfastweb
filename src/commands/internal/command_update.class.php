<?php
class command_update extends acommand_internal implements icommand_internal {

	
	public function set_type(int $type) {
		$this->type = $type;
	}
	public function get_key() {
		return 'update';
	}
	public function execute() {
		$cmd = new command_process_row();
		$cmd->set_table($this->table);
		$cmd->set_post_insert_function($this->post_insert_function);
		$cmd->set_type(command_process_row::type_update);
		$cmd->execute();
	}	
	
	public $post_insert_function = '';
}
?>