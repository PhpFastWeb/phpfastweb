<?php

class validation_max_length extends avalidation_rule {

	protected $max_length;
	function __construct($max_length) {
		$this->max_length = $max_length;
	}
	/**
	 * @param icolumn $column
	 */
	function check_column(icolumn $column) {
		if ($this->skip_validation($column)) return $this->validates;
		//--------------------------------
		
        if ( strlen($column->get_value()) > $this->max_length) {
            $this->messages[] = 'La longitud de este campo debe ser de al menos '.$this->max_length.' caracteres.';
            $this->invalidate();
        }
		return $this->validates;
	}
}