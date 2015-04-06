<?php
abstract class aitem_prototype implements iitem_prototype {
	protected $table;
	public function set_table(table_data &$table) {
		$this->table = $table;
	}
	protected $row_data = array();
	protected $odd = true;
	public function set_row($row, $odd=null) {
		$this->row_data = $row;
		if (!isset($odd)) {
			$this->odd = ( ! $this->odd );
		} else {
			$this->odd = $odd;
		}
	}
}
?>