<?php
interface ivalidation_rule {
	
	/**
	 * Checks if column pass current validation rule
	 * @param $column
	 * @return bool
	 */
	function check_column(icolumn $column);
	/**
	 * Returns an array of validation messages of previous check
	 * return array
	 */
	function get_validation_messages();
	function set_enforce($enforce=false);

}
?>