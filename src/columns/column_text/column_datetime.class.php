<?php 

class column_datetime extends column_text implements icolumn {
    
    public static $value_to_db_when_empty = '';
    
    public function &set_value($value) {
        if ( strpos($value,'/') ) {
            //throw new ExceptionDatabase('/');
        }
        parent::set_value($value );
        return $this;
    }
    public function get_value() {
        $value = parent::get_value();
        if ( $value == "" ) {
            $value = column_date::$value_to_db_when_empty;
        }
        return $value;
    }
	public function get_formatted_value() {
		if ($this->formatted_value != '' ) {
            return $this->formatted_value;
        }
		
		if ($this->value == "" ||
		 	$this->value == '0000-00-00' ||
		 	$this->value == '0000-00-00 00:00:00' ||
		 	$this->value == '0000-00-00 00:00'
		) return '';
		//Intentamos la conversión
		if ( ! preg_match('@\d\d\d\d-\d\d-\d\d (\d)?\d:\d\d(:\d\d)?@', $this->value)) {
			//No podemos convertir
            return $this->value;
            
		}
		return self::convert_format_db_to_user($this->value, $this->use_seconds);
	}
	public function &set_formatted_value($value) {
		$this->formatted_value = $value;
		$v = '';
		try {
			$v = self::convert_format_user_to_db($value, $this->use_seconds);
		} catch (Exception $e) { }
		$this->set_value($v);
		return $this;
	}    
	public static function convert_format_user_to_db($value, $use_seconds=true) {
		if ($value == null) return null;
		if ($value == "" ||
		 	$value == '0000/00/00 00:00:00' ||
            $value == '0000/00/00 00:00' ||
            $value == '0000-00-00 00:00:00' ||
            $value == '0000-00-00 00:00' 
		) return '';
		if ( ! preg_match('@\d\d/\d\d/\d\d\d\d (\d)?\d:\d\d(:\d\d)?@',$value)) {
			throw new ExceptionDeveloper("Se esperaba fecha en formato DD/MM/AAAA HH:MM:SS");
		}
		$value = str_replace(" ","/",$value);
		$value = str_replace(":","/",$value);
		$tokens = explode("/",$value);
		$result = $tokens[2]."-".$tokens[1]."-".$tokens[0]." ".$tokens[3].":".$tokens[4];
        if ( $use_seconds && ($tokens[5]) && $tokens[5] != '' ) $result .= ":".$tokens[5];
		return $result;
	}
	public static function convert_format_db_to_user($value, $use_seconds=true) {
		if ($value == null) return null;
		if ($value == "" ||
		 	$value == '0000-00-00 00:00:00' ||
		 	$value == '0000-00-00 00:00'
		) return '';
		if ( ! preg_match('@\d\d\d\d-\d\d-\d\d (\d)?\d:\d\d(:\d\d)?@', $value) ) {
			throw new ExceptionDeveloper("Se esperaba fecha en formato AAAA-MM-DD HH:MM:SS");
		}
		$value = str_replace(" ","-",$value);
		$value = str_replace(":","-",$value);
		$tokens = explode("-",$value);
		$result = $tokens[2]."/".$tokens[1]."/".$tokens[0]." ".$tokens[3].":".$tokens[4];
        if ( $use_seconds && isset($tokens[5]) && $tokens[5] != '' ) $result .= ":".$tokens[5];
		return $result;
	}	
	public function validate() {
		if (count($this->validation_rules)==0) {
			$this->validation_rules[] = new validation_datetime();
		}
		return parent::validate();
	}

	public function get_input_plain() {
		//$result = parent::get_input_plain();
		$text = $this->get_formatted_value();
		$js_onchange = $this->get_js_onchange();
        $autocomplete = $this->get_table()->autocomplete ? '' : 'autocomplete="off"';
		$result = "<input type=\"text\" name=\"".$this->get_html_id()."\" id=\"".$this->get_html_id()."\" class=\"form_edit_row_date\"";					
		$result .= " value=\"".$text."\"";
		$result .=" $autocomplete $js_onchange />\r\n";
		$result .= self::get_date_control($this->get_html_id());
		return $result;
	}
	public static function get_input_plain_static($control_id,$value) {
		if ( $value != '0000-00-00' && 
                $value != '0000-00-00 00:00:00' &&  
                $value != '0000-00-00 00:00') {
			$text = $value;
			$text = stripslashes($text);
			$text = htmlentities($text);
		} else {
			$text = '';
		}
        $autocomplete = $this->get_table()->autocomplete ? '' : 'autocomplete="off"';
		$result = "<input type=\"text\" name=\"".$control_id."\" id=\"".$control_id."\" class=\"form_edit_row_date\"";					
		$result .= " value=\"".$text."\"";
		$result .=" $autocomplete />\r\n";
		$result .= self::get_date_control($control_id);
		return $result;
	}
	protected static function get_date_control($control_id) {
		$result = '';
		$img_dir = website::$theme->get_img_dir();
		$input_name = $control_id;
		$date_format='d/m/Y';
		$js_date_format['d/m/y']='%d/%m/%y %H:%M';
		$js_date_format['d/m/Y']='%d/%m/%Y %H:%M';
		$js_date_format['d-m-Y']='%d-%m-%Y %H:%M';
		$js_date_format['Y-m-d']='%Y-%m-%d %H:%M';
		$js_date_format['Y/m/d']='%Y/%m/%d %H:%M';
		$result .= "<img src=\"".$img_dir."/calendario.gif\" border=\"0\" align=\"top\" alt=\"ver calendario\" ";
		$result .= 'style="cursor:pointer; " ';
		$result .= ' onmouseover="this.style.background=\'#335EA8\'" ';
		$result .= ' onmouseout="this.style.background=\'\'" ';		
		$result .= "title=\"elegir fecha\" id=\"TRIGGER_$input_name\" />\r\n";
		
		$result .= '<script type="text/javascript">'."\r\n";
    	$result .= "Calendar.setup({\r\n";
        $result .= "\t inputField     :    \"$input_name\",\r\n";     		 // id of the input field
        //$result .= "\t //".$date_format." \r\n";
        $result .= "\t ifFormat       :    \"".$js_date_format[$date_format]."\",\r\n";        // format of the input field
        $result .= "\t showsTime      :    true,\r\n";            			 // will display a time selector
        $result .= "\t button         :    \"TRIGGER_$input_name\",\r\n";      // trigger for the calendar (button ID)
        $result .= "\t singleClick    :    true,\r\n";           
        $result .= "\t step           :    1\r\n";                			 // show all years in drop-down boxes (instead of every other year as default)
    	$result .= "});\r\n";
		$result .= "</script>\r\n";
		return $result;
	}
	/**
	 * @return column_date
	 */
	public function &set_default_value_now() {
		$this->set_default_value(Date('Y-m-d H:i:s'));
		return $this;	
	}	
	public function get_db_type() {
		return 'datetime';
	}
    
	private $use_seconds = true;
    /**
     * Sets the use of seconds, in adition to hour and minutes
     * @return column_datetime reference to self
     */
    public function set_use_seconds($use_seconds=true) {
        $this->use_seconds = $use_seconds;
        return $this;
    }
	protected function get_input_hidden() {
		$result = "<input type=\"hidden\" name=\"{$this->get_html_id()}\" id=\"{$this->get_html_id()}\" value=\"".$this->get_formatted_value()."\" />\r\n";
        return $result;
	}
	public function get_input_hidden_prev_value() {
        $result = "<input type=\"hidden\" name=\"{$this->get_html_id()}_prev_\" id=\"{$this->get_html_id()}_prev_\" value=\"".$this->get_formatted_value()."\" />\r\n";
		//$result = "<input type=\"hidden\" name=\"{$this->get_html_id()}_prev_\" id=\"{$this->get_html_id()}_prev_\" value=\"".htmlentities($this->get_formatted_value())."\" />\r\n";
		return $result;
	}
}
?>