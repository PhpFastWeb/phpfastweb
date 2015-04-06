<?php
class item_prototype_basic extends aitem_prototype implements iitem_prototype {
	
	protected $mode = 'resume';
	function get_render() {
		switch($this->mode) {
			case 'resume':
				return $this->get_render_resume();
			case 'detail':
				return $this->get_render_detail();
			default;
				throw new ExceptionDeveloper('mode not defined');
		}
	}
	function get_render_resume() {
		
		$result = '';
		foreach($this->row_data as $key => $value) {
			if (in_array($key,$this->table->columns_table_view))
				$result .= $key ." : ".$value."<br />\r\n";
		}
			
		$result .= $this->get_actions();
		$result .= "<hr />";
		return $result;
	}
	protected function get_actions() {
		$result = '';
		if (count ( $this->table->commands ) > 0) {
			$result = '';
			foreach ( $this->table->commands as $cmd ) {
				if ($cmd instanceof icommand_row && ! ($cmd instanceof command_set)) {
					$cmd->set_key_set( $this->table->key_set, $this->row_data );
					if ( $cmd->get_key() == $this->table->default_click_command ) {
						$result .= '<b>'.$cmd->get_execute_link ().'</b> ';
					} else {
						$result .= $cmd->get_execute_link ().' ';
					}

				}
			}
		}
		return $result;
	}
}
?>