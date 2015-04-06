<?php
class command_insert_or_update extends acommand_internal implements icommand_internal {

	
	public function set_type(int $type) {
		$this->type = $type;
	}
	public function get_key() {
		return 'insert_or_update';
	}
	public function execute() {
        if ( $this->table->only_one_insert_check() ) {
            return;
        }

		$cmd = new command_process_row();
		$cmd->set_table($this->table);
		$cmd->set_post_insert_function($this->post_insert_function);
		$cmd->set_type(command_process_row::type_insert_or_update);
		$ok = $cmd->execute();
        
        if ( $ok ) $this->table->only_one_insert_sets();
        
        return $ok;
	}	
	public $post_insert_function = '';
}
