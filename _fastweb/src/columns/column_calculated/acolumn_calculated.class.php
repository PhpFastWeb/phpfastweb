<?php
abstract class acolumn_calculated extends acolumn implements icolumn {

	/**
	 * 
	 */
//	public function get_input_plain() {
//		
//	}
//	public function get_value() {
//		
//	}
//	public function get_formatted_value() {
//		
//	}
	protected $readonly = true;
	public function get_db_columns_str() {
		return '';
	}

	public function get_db_values_str() {
		return '';
	}
	public function get_db_equal_str() {
		return '';		
	}
	public function get_db_where_str() {
		return '';
	}

	
}
?>