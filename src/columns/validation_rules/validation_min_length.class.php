<?php
class validation_min_length extends avalidation_rule {

	protected $min_length;
	function __construct($min_length) {
		$this->min_length = $min_length;
	}
	/**
	 * @param icolumn $column
	 */
	function check_column(icolumn $column) {
		if ($this->skip_validation($column)) return $this->validates;
		//--------------------------------
		
        if ( strlen($column->get_value()) < $this->min_length) {
            $this->messages[] = 'La longitud de este campo debe ser de al menos '.$this->min_length.' caracteres.';
            $this->invalidate();
        }
		return $this->validates;
	}
}
