<?php 
class column_text extends acolumn_text_related implements icolumn {

	public $auto_trim = true;
	protected $width = '';
	public function &set_width($css_width) {
		$this->width = $css_width;
		return $this; 
	}
	public function get_width() {
		return $this->width;
	}
	
    public function &set_auto_trim($auto_trim=true) {
        $this->auto_trim = $auto_trim;
        return $this;
    }
    public function &set_value($value) {
        if ( is_array($value) ) throw new ExceptionDeveloper("Excepted single value in set_value, array recieved");
        if ($this->auto_trim) $this->value = trim($value);
        else $this->value = $value;
        return $this;
    }

	public function get_formatted_value() {
		$result = stripslashes($this->value);
		return $result;
	}
	
	public function get_input_plain() {
		$v = $this->get_formatted_value();
		$js_onchange = $this->get_js_onchange();
		$ss = "";
		$id = $this->get_html_id();
		if ($this->width != '') {
			$ss = "style=\"width:$this->width;\"";
		}
        $autocomplete = $this->get_table()->autocomplete ? '' : 'autocomplete="off"';
		$result = "<input type=\"text\" class=\"form_edit_row_inputtext\" name=\"$id\" id=\"$id\" value=\"$v\" $ss $autocomplete $js_onchange />";
		return $result;
	}
	public function get_db_type() {
		return 'varchar(255)';
	}	
}

?>
