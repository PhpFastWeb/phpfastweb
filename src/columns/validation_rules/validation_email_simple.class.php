<?php
class validation_email_simple extends avalidation_rule {
	/**
	 * @param icolumn $column
	 */
	function check_column(icolumn $column) {
		if ($this->skip_validation($column)) return $this->validates;
		
		//--------------------------------
		$v = $column->get_formatted_value();
		$v = trim($v);
		$column->set_value($v);
		//Validamos cosas sencillas
		if ($v=='') return true;
		//1. Una @
		$ok = (substr_count($v,'@') == 1);
		//algo a la izquierda de la @
		$ok = $ok && ( (strpos($v,'@') != 0 && strpos($v,'@') != strlen($v)-1));
		if ( !$ok ) {
			$ok = false;
			$this->messages[] = 'Debe indicarse un email de la forma \'usuario@dominio.ext\'.';
		}
		if ( ! $ok ) $this->invalidate();
		return $this->validates;
	}
}
