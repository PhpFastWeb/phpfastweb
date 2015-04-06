<?php
class column_derived extends column_text implements icolumn {
	protected $derived_column = '';
	public function &set_derived_column($column_name) {
		$this->derived_column = $column_name;
		return $this;
	}
	public function get_formatted_value() {
		if ($this->value == '') {
			return $this->table->columns_col->get($this->derived_column)->get_formatted_value();
		} else {
			return $this->value;
		}
	}
}
?>