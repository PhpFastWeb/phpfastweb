<?php
	class table_data_select_config {
		public $table_name = '';
		public $primary_keys = array('id');
		public $columns_select = array();
		public $columns_select_options = array();
		public $order_default_column = 'id';
		public $order_default_order = 'ASC';
		public $separator = ' | ';
		
		public function __construct($table_name='',$array_columns_select=null) {
			$this->table_name = $table_name;
			if ( ! is_null($array_columns_select))
				$this->columns_select = $array_columns_select;
		}
	}
?>