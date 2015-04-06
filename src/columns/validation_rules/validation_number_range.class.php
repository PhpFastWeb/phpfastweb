<?php
class validation_number_range extends avalidation_rule {

	protected $min;
	protected $max;
	function __construct($min=null,$max=null) {
		$this->min = $min;
		$this->max = $max;
	}
	/**
	 * @param icolumn $column
	 */
	function check_column(icolumn $column) {
		if ($this->skip_validation($column)) return $this->validates;
		//--------------------------------
		$ok = true;
		$col_num = column_number::cast($column);
		$value = $col_num->get_value();
		if ($value=='') return true;

		//if ( ! is_null($this->max) && ( ! is_numeric($value) || ( $value > $this->max ) ) ) {
		if ( ! is_null($this->max) && ( $value > $this->max ) ) {			
			$ok = false;
			if ( ! is_null($this->min) ) {
				$this->messages[] = 'El valor debe estar entre '."{$this->min}".' y '."{$this->max}".'.';
			} else {
				$this->messages[] = 'El valor debe ser menor o igual a '."{$this->max}".'.';
			}
		}
		if ( ! is_null($this->min) && ( $value < $this->min ) ) {
			$ok = false;
			if ( ! is_null($this->max) ) {
				$this->messages[] = 'El valor debe estar entre '."{$this->min}".' y '."{$this->max}".'.';
			} else {
				$this->messages[] = 'El valor debe ser mayor o igual a '."{$this->min}".'.';
			}
		}
		if ( ! $ok ) $this->invalidate();
		return $this->validates;
	}
}
