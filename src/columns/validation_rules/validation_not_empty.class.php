<?php
class validation_not_empty extends avalidation_rule implements ivalidation_rule {

	function check_column(icolumn $column) {	
    
		if ( $this->skip_validation($column) ) return $this->validates;

		//if (!$column->has_changed()) return true;
		
		$val = $column->get_value();
		
		if ( $column->is_primary_key() && ! $column->is_primary_key_editable() ) {
			return $this->validates;
		}
		
		$empty = false;
		if ( $column instanceof column_select ) {
			$v = $column->get_option($column->get_value());
			
			if ( trim($v) === '' || trim($v) === '-' ) {
				$empty = true;
				//Se trata de un select en el que se ha seleccionado una opción
				//en blanco o un serparador con el texto '-'
			}
			
			if ( ! in_array($column->get_value(),$column->get_options()) && 
				$column->get_value() === '') {
				$empty = true;
				//Se trata de un radio button en el que no se ha pulsado ninguna opción.					
			}
			
		} else if  ( $column->get_value() === '' ) {
			$empty = true;
		}
		
		if ( isset($val) && $empty ) {
			$this->invalidate();
			if ( ! $column instanceof column_checkbox ) {
				$this->messages[] = "Debe especificar un valor para el campo '".$this->shorten($column->get_title(),50)."'";
			} else {
				$this->messages[] = "Debe marcar el campo '".$this->shorten($column->get_title(),50)."'";
			}
		}
		return $this->validates;
	}
	
	protected function shorten($title,$length) {
		if (strlen($title)<$length) return $title;
		return (substr($title,0,$length)."...");
		
	}
}
?>