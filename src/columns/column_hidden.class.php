<?php 
class column_hidden extends column_text implements icolumn {

	public $visible = false;
/*
	public function validate() {
		
		if ( $this->value != "" && ! $this->is_primary_key) {
			$this->validates = false;
			$this->validation_messages[] = "Se intentó definir el campo oculto ".$this->column_name;
		}
		
		return parent::validate();
	}
	
	public function get_formatted_value() {
		return '';
	}

	public function get_input_plain() {
		$result = "";
		return $result;
	}
*/
}

?>
