<?php
class column_linked extends acolumn implements icolumn {
	public $url_popup;
	public $table_name;
	public $columns_text = array();
	public $format_string = "%s";
	public $key_column = "id";
	
	public function __construct($column_name='',$table_name='',$columns_text_array=null,$format_string='%s') {
		if ($column_name != '')           $this->column_name = $column_name;
		if ($table_name != '')            $this->table_name = $table_name;
		if ($columns_text_array != null)  $this->columns_text = $columns_text_array;

		$this->format_string = $format_string;
		$this->set_nullify_empties(true);
        
        parent::__construct();
	}
	/**
	 * @param icolumn $col
	 * @return column_linked
	 */
	public static function &cast(icolumn $col) {
		return $col;
	}	
	public function &set_table_name($table_name) {
		$this->table_name = $table_name;
		return $this;
	}
	public function &set_url_popup($url_string) {
		$this->url_popup = $url_string;
		return $this;
	}
	public function &set_format_string($format) {
		$this->format_string = $format;
		return $this;
	}
	public function &set_key_column($id='id') {
		$this->key_column = $id;
		return $this;
	}
	protected $data = array();
	protected function get_data($id) {
		$pk = $this->key_column;
		if ($id=='') return '&nbsp;';
		if (!isset($this->data[$id])) {
			$t = website::$database->execute_get_array('SELECT * FROM '.$this->table_name.' WHERE '.$pk.'=\''.$id.'\'');
			//TODO: Optimizar
			if (isset($t[0]))
				$this->data[$id] = $this->get_row_text($t[0]);
			else return '&nbsp;';
		}
		if ($this->data[$id]=='') return '&nbsp';
		return $this->data[$id];
	}
	public function get_row_text($row) {
		$f = explode('::',$this->row_text_function);
		return call_user_func($f,$row);
//		$d = $row;
//		unset($d['id']);
//		if (count($this->columns_text)==0) {
//			$k = array_keys($d);
//			$this->columns_text[] = reset($k);
//		}
//		if (count($this->columns_text)>1 && $this->format_string =='%s') {
//			$this->format_string = '';
//			foreach($this->columns_text as $col) {
//				$this->format_string .= "%s";
//			}
//		}
//		$p = array();
//
//		foreach($this->columns_text as $c) {
//			$p[] = $row[$c];
//		}
//		$result = vsprintf($this->format_string,$p);
//		return $result;
	}
	protected $row_text_function = 'command_popselect::static_get_row_text';
	public function &set_row_text_function($function_name) {
		$this->row_text_function = $function_name;
		return $this;
	}
	public function get_formatted_value() {
		return $this->get_data($this->value);
	}
	
	/**
	 * 
	 */
	public function get_input_plain() {
		$value = $this->value;
        $result = '';
		if ($this->value == '0') $value = '';
		//$result = '<table style="border:0; border-collapse:collapse; display:inline;"><tr><td style="vertical-align:top;">';
        
        //$result = '<div>';
        
		$result .= '<input  type="text" style="margin-top:0px; width:40px; text-align:right; display: inline-block; vertical-align:top;" value="'.$value.'" ';
        $result .= "id=\"{$this->get_html_id()}\" name=\"{$this->get_html_id()}\" ";
        

		$result .= " onchange=\"javascript:linked_change(this);\"";
	
		
		$result .= " readonly=\"readonly\" ";
		$result .= " />";
        
		//$result .= '</td><td style="vertical-align:top;">';'
        $result .= '<div style="display: inline-block; vertical-align:top; width:310px; min-height:15px; margin: 0px 2px 2px 2px; padding:2px 2px 3px 3px; border:1px solid #A7A6AA;"';
		$result .= " id=\"{$this->get_html_id()}_text_\">"; 
		$result .= $this->get_data($value);
		$result .= '</div>';
        
		//$result .= '</td><td style="vertical-align:top;padding-top:3px;">';
        
		$result .= '<a href="#" class="link_page"  onclick="'.$this->get_execute_onclick().'">Cambiar</a>';
		$result .= ' <a href="#" class="link_page" onclick="'.$this->get_reset_click().'">Borrar</a> ';
        
		//$result .= '</div>';
		return $result;
	}
	public function get_execute_onclick() {
		return "openPopup('{$this->get_html_id()}','".$this->url_popup."');";
	}
	public function get_reset_click() {
		$result = "document.getElementById('{$this->get_html_id()}').value='&nbsp;';";
		$result .= "document.getElementById('{$this->get_html_id()}').onchange();";
		//$result .= "document.getElementById('{$this->get_html_id()}_text_').innerHTML='&nbsp;';";
		$result .= "return false;";
		return $result;
	}

	/**
	 * @param unknown_type $value
	 */
	static function convert_format_user_to_db($value) {
		return $this->value;
	}	
	
	public function get_db_type() {
		return 'int(32) unsigned';
	}
		
}
?>