<?php
abstract class acontrol_column extends acontrol implements icontrol {
	/*
	 * @var icolumn
	 */
	protected $column;
	
	//TODO: Aplanar esta clase directamente con acolumn/icolumn
	
	public function set_column(icolumn $column) {
		$this->column = $column;
	}
	public function get_column() {
		return $this->column;
	}
	public function set_values($row_array) {
		if ($this->column == null) {
			throw new ExceptionDeveloper("Columna no establecida");
		}
		$this->column->set_value_from_array($row_array);
	}
	public function has_column($column_name) {
		if ($this->column && $this->column->get_column_name() === $column_name) {
			return true;
		}
		return false; 
	}	
}
?>