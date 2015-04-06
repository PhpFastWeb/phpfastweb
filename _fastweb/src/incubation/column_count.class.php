<?php
class column_count extends column_number {
	public $is_readonly = true;
	
	public function create_db_column() {
			return true;	
	}
	
	public function get_composite_column_name() {
		//COUNT(`idit_il_matriculas`.`id`)	 
	}
	
	public function get_db_columns_str() {
		$a = $this->get_original_column_name();
		if ($a == '') {
            $sql = new sql_str('`{#0}`.`{#1}`',
                $this->get_table_name(),
                $this->get_column_name());
            $result = $sql->__toString();
		} else {
            $sql = new sql_str('COUNT (`{#0}`.`{#1}`) AS `{#2}`',
                $this->get_original_table_name(),
                $this->get_original_column_name(),
                $this->get_column_name());
            $result = $sql->__toString();
		}
		return $result;
	}
}
