<?php
class validation_unique extends avalidation_rule implements ivalidation_rule {

    protected $invalid_message_text = '';
    public function __construct($invalid_message_text='') {
        $this->invalid_message_text = $invalid_message_text;
    }
	function check_column(icolumn $column) {	
		if ($this->skip_validation($column)) return $this->validates;
		
		if (!$column->has_changed()) return true;
		
		$val = $column->get_value();
		
		//Comprobamos si existe en la base de datos
		$e = website::$database->exist_row2($column->get_table()->table_name,$column->get_column_name(),$val);
		
		if ($e) {
			$this->invalidate();
            if ($this->invalid_message_text == '') {
		      $this->messages[] = "El campo '".$column->get_title()."' debe tener un valor único, pero ya exite otro registro con el valor '".$val."'.";
            } else {
                $this->messages[] = $this->invalid_message_text;
            }
		}
		return $this->validates;
	}
}
?>