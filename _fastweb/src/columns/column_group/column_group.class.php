<?php
class column_group extends acolumn implements icolumn {
	
	

	/**
	 * 
	 * @var array
	 */
	public $columns_col = array();
	
	public function validate() {
		$this->validates = true;
		$this->validation_messages = array();
		foreach ($this->columns_col as $col) {
			$col = acolumn::cast($col);
			$this->validates = ( $col->validate() && $this->validates );
			$this->validation_messages = array_merge($this->validation_messages,$col->get_validation_messages);
			
		}
		
		if ( $this->limit_len > -1 && strlen($this->value) > $this->limit_len ) {
			$this->validates = false;
			$this->validation_messages[] = "El texto no debe ser más largo de ".$this->limit_len." caracteres.";
		}
		//return parent::validate();
	}
	public function get_formatted_value() {
		$result = $this->value;
		return $result;
	}

	public function get_input_plain() {
		$result = "<input type=\"text\" name=\"{$this->get_html_id()}\" id=\"{$this->get_html_id()}\" value=\"$this->value\" />";
		return $result;
	}

}

?>
