<?php 
class column_time extends column_text implements icolumn {
	
	public function get_formatted_value() {
		if ($this->formatted_value != '' ) return $this->formatted_value;
		
		if ($this->value == "" ||
		 	$this->value == '00:00' ||
		 	$this->value == '00:00:00'
		) return '';
		//Intentamos la conversión
		if ( ! preg_match('/\d\d\:\d\d(:\d\d)?/', $this->value)) {
			//No podemos convertir
			return $this->value;
		}
		return self::convert_format_db_to_user($this->value);
	}
	public function &set_formatted_value($value) {
		$this->formatted_value = $value;
		return $this;

	}
	public static function convert_format_user_to_db($value) {
		if ($value == null) return null;
		if ($value == "" ||
		 	$value == '00:00' ||
		 	$value != '00:00:00'
		) return '';
		if ( ! preg_match('/\d\d:\d\d(:\d\d)?/',$value)) {
			throw new ExceptionDeveloper("Se esperaba hora en formato HH:MM:SS o HH:MM");
		}
		$result = $value;
		return $result;
	}
	public static function convert_format_db_to_user($value) {
		if ($value == null) return null;
		if ($value == "" ||
		 	$value == '00:00' ||
		 	$value == '00:00:00'
		) return '';
		if ( ! preg_match('/\d\d:\d\d(:\d\d)?/', $value) ) {
			throw new ExceptionDeveloper("Se esperaba hora en formato HH:MM:SS o HH:MM");
		}
		$result = $value;
		return $result;
	}	
	public function validate() {
		if (count($this->validation_rules)==0) {
			$this->validation_rules[] = new validation_time();
		}
		return parent::validate();
	}

	public function get_input_plain() {
		//$result = parent::get_input_plain();
		$text = $this->get_formatted_value();
		$js_onchange = $this->get_js_onchange();
        $autocomplete = $this->get_table()->autocomplete ? '' : 'autocomplete="off"';
		$result = "<input type=\"text\" name=\"".$this->get_column_name()."\" id=\"".$this->get_column_name()."\" class=\"form_edit_row_time\"";					
		$result .= " value=\"".$text."\"";
		$result .=" $autocomplete $js_onchange />\r\n";
		
		$result .= " Formato: HH:MM:SS, HH:MM, MM\r\n";
		return $result;
	}

	public static function get_input_plain_static($control_id,$value) {
		if ( $value != '00:00' && $value != '00:00:00' ) {
			$text = $value;
			$text = stripslashes($text);
			$text = htmlentities($text);
		} else {
			$text = '';
		}
        $autocomplete = $this->get_table()->autocomplete ? '' : 'autocomplete="off"';
		$result = "<input type=\"text\" name=\"".$control_id."\" id=\"".$control_id."\" class=\"form_edit_row_time\"";					
		$result .= " value=\"".$text."\"";
		$result .=" $autocomplete />\r\n";
		return $result;
	}
}

?>
