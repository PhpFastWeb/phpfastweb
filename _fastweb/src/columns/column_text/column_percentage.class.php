<?php
class column_percentage extends column_number {
	public function __construct($column_name='') {
		$this->column_name = $column_name;
		$this->set_unit('%');
		$this->add_validation_rule(new validation_number_range(0,100));
		$this->type = column_number::type_integer;
        parent::__construct();
	}
}
?>