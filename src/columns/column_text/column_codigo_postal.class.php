<?php

class column_codigo_postal extends column_number {
	private $retrieve_data = true;
	private $cp_data_table_name = 'cp_localidades_calles';
	
	public function __construct() {
		parent::__construct();
		
		$this->add_validation_rule(new validation_number_range(1000,99999));
	}
	
	public function &set_retrieve_data($do_retrieve=true) {
		$this->retrieve_data = $do_retrieve;
		return $this;
	}
	
	public function &set_value($value) {
		$value = trim(strtoupper(str_replace('-','',$value)));
		$value = trim(strtoupper(str_replace(' ','',$value)));
		$value = trim(strtoupper(str_replace('.','',$value)));		
		$this->value = $value;
        return $this;
	}
	
	public function get_input_plain() {
		
		$result = $this->get_input_plain_orig();
		
		$value = $this->get_formatted_value();
		
		if ( $value && $this->retrieve_data && is_numeric($value)) {
			try {
				$tn = website::$database->escape_table_name($this->cp_data_table_name);
				$v =  website::$database->escape_string($value);
				$sql = "SELECT * FROM $tn WHERE codigo_postal='$v'";
				$res = website::$database->execute_query($sql,1);
				$data = website::$database->fetch_result($res);
				if (isset($data['localidad']) && $data['localidad'] != '' ) {
					$result .= ' &nbsp;Localidad: <b>'.$data['localidad'].'</b>';	
				}	
			} catch( Exception $e ) {
			}
		}
		return $result;
	}
	
	public function get_input_plain_orig() {
		
		$v = $this->get_formatted_value();
		
		$js_onchange = $this->get_js_onchange();
		$autocomplete = $this->get_table()->autocomplete ? '' : 'autocomplete="off"';
		$result = "<input type=\"text\" class=\"form_edit_row_inputtext\" name=\"{$this->get_html_id()}\" id=\"{$this->get_html_id()}\" value=\"$v\" $autocomplete $js_onchange style=\"text-align:left; width:154px;\" />";
		
		return $result;
		
	}
	
}