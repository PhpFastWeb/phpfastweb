<?php 
class column_select extends acolumn implements icolumn {
	
	/**
	 * @param icolumn $col
	 * @return columns_select
	 */
	public static function &cast(icolumn $col){
		if ( ! $col instanceof column_select) throw new Exception("El objeto no es de la clase adecuada");
		return $col;
	}
	
	protected $options = array();
	protected $default_option;
	/**
	 * Sets the available options for the select
	 * @param array $options
	 */
	public function &set_options($options_array) {
		if (!is_array($options_array)) throw new ExceptionDeveloper('Array expected'); 
		$this->options = $options_array;
		return $this;
	}
	
	/**
	 * Returns the option with specified index
	 * @param $index
	 * @return mixed
	 */
	public function get_option($index) {
		if ( ! isset($this->options[$index]) ) {
			//throw new ExceptionDeveloper('Option "'.$this->column_name.'" not defined with index="'.$index.'"');
            return "[ ".$index." ]";
   		}
		return $this->options[$index];
	}
	/**
	 * Returns a copy of the options array
	 * @return array
	 */
	public function get_options() {
		return $this->options;
		//TODO: Return copy of array
	}
    
    //TODO: Having set_default_value on icolumn/acolumn renders this method unnecesary
    //Maybe overwrite that method to check for key existance.
	/**
	 * Sets the default option for the select
	 * @param string $key
	 */
	public function &set_default_option($key) {
		if ( count($this->options) > 0 
			&& ! isset( $this->options[$key] )
			) {
			throw new ExceptionDeveloper("No es encontró la opción con la clave $key");
		}
		$this->default_option = $key;
		return $this;
	}
	protected function &set_value_from_restriction() {
        parent::set_value_from_restriction();
		if ($this->is_restricted() && ! $this->is_restricted_to_one_value() ) {
		    //Restricted to multiple values
            //We filter the options, so only those restricted can be selected
			$options = array();
			foreach ( $this->restricted_value as $rvalue ) {
				if ( ! isset($this->options[$rvalue] ) ) {
					//throw new ExceptionDeveloper('Restricted value not set in options');
				} else {
					$options[$rvalue] = $this->options[$rvalue];
				}
			}
			$this->options = $options;
		}
		return $this;
	}	
	public function get_formatted_value() {
		$result = "";
		if (isset($this->value)) {
			if (is_array($this->value)) throw new ExceptionDeveloper('$value is array');
			
			if (isset($this->options[$this->value])) {
				$result = $this->options[$this->value];	
			} else if ( $this->value == "" || $this->value == '0' ) {
				//TODO: comprobar antes si esta respuesta es válida
				return '';
			} else {
				return  "<i>(valor actual {$this->value} no encontrado)</i>";
			}
			
		} else {
			if ($this->value==null) {
				$result = "";
//				if (isset($this->default_value)) {
//					if ( ! array_key_exists($this->default_value,$this->options) ) {
//						throw new ExceptionDeveloper("Valor '{$this->default_value}' no encontrado en select '".$this->column_name."'");
//					}
//					$result = $this->options[$this->default_value];
//				} else if (isset($this->default_option)) {
//					if ( ! array_key_exists($this->default_option,$this->options) ) {
//						throw new ExceptionDeveloper("Valor '{$this->default_value}' no encontrado en select '".$this->column_name."'");
//					}
//					$result = $this->options[$this->default_option];
//				}
			}
		}
		return $result;
	}
	public function &set_value($value) {
		if ( ! array_key_exists($value,$this->options) && $value != "" && $value != '0' ) {			
			//throw new ExceptionDeveloper("Valor '$value' no encontrado en select '".$this->column_name."'");
		}
		$this->value = $value;
		return $this;
	}
	public function get_input_plain() {
	   
		switch($this->type) {
			case 'select':
				return $this->get_input_plain_select();
				break;
			case 'radio':
				return $this->get_input_plain_radio();
				break; 
            case 'scale':
				return $this->get_input_plain_scale();
				break; 
            default:
                throw new ExceptionDeveloper("Type $this->type not supported");            
		}
        
	}
	
	public function get_input_hidden_prev_value() {
		$prev_value = $this->get_value();
		
		if (empty($prev_value)) {
			$os = $this->get_options();
			$ks = array_keys($os);
			$prev_value = reset($ks);
			
		}
		$result = "<input type=\"hidden\" name=\"{$this->get_html_id()}_prev_\" id=\"{$this->get_html_id()}_prev_\" value=\"".htmlentities($prev_value)."\" />\r\n";
		return $result;
	}
	public function get_input_plain_select() {
	   
		$js = $this->get_js_onchange();
		$result = "<select id=\"{$this->get_html_id()}\" name=\"{$this->get_html_id()}\" $js>\r\n";
        $found = false;
		foreach($this->options as $key => $value ) {
			$result .= "<option value=\"$key\"";
			if ( "$key" === "{$this->value}" ) {
				$result .= " selected=\"selected\" ";
                $found = true;
			}
			if ($value=="") {
				$result .= ">&nbsp;</option>\r\n";
			} else {
				$result .= ">$value</option>\r\n";
			}
		}

		$result .= "</select>\r\n";
        if ( ! $found ) {
            if ( $this->value == "" || $this->value == '0' ) {
				//TODO: comprobar antes si esta respuesta es válida
			} else {
				$result .= "<br /><i>(valor actual {$this->value} no encontrado)</i><br />\r\n";
			}
        }        
		return $result;
	}
	public function get_input_plain_radio() {	
		$js = $this->get_js_onchange();
		$binds = $this->get_column_binds();
		$result = '<div style="padding:4px 0 5px 15px;" >';
	    $found = false;
		foreach($this->options as $key => $value ) {
			
			$result .= "<input type=\"radio\" name=\"{$this->get_html_id()}\" value=\"$key\" id=\"{$this->get_html_id()}_{$key}_\"";
			if ( "$key" === "{$this->value}" ) {
				$result .= " checked=\"checked\" ";
                $found = true;
			}
			$result .= $js;
			
			$link = '';
			if (count($binds)>0) {
				foreach ($binds as $bind) {
					$bind = columns_bind::cast($bind);
					if ($bind->is_value_to_enable($key) && !$this->is_readonly()) {
						if ( $this->link_html != '' ) {
							//TODO: Generalizar esto
							$link .= '<a href="#" onmouseover="return overlib(\'Al seleccionar este valor se muestran otros controles\', AUTOSTATUS, WRAP, FGCOLOR,\'#FFFFE1\');" onmouseout="nd();" style="cursor:default; text-decoration:none;" onclick="return false;" tabindex="999">';
							$link .= '<img src="'.website::$theme->get_img_dir().'/link.png" alt="" style="vertical-align:middle; margin: 0 0 2px 5px;" />';
							$link .= '</a>';			
						}		
					}
				}
			}
            $value_text = ( $value != '' ) ? $value : '(en blanco)';
            $style_label = ( $this->type != 'scale' ) ? ' style="display: inline;" ' : '';

            $result .= " /><label for=\"{$this->get_html_id()}_{$key}_\" class=\"radio_option\" $style_label >$value_text</label>$link\r\n";

            if ( $this->type != 'scale' ) {
                $result .= "<br />";
            }
		}
        if ( ! $found ) {
            if ( $this->value == "" || $this->value == '0' ) {
				//TODO: comprobar antes si esta respuesta es válida
			} else {
				$result .= "<br /><i>(valor actual {$this->value} no encontrado)</i><br />\r\n";
			}
        }              
        $result .= "</div>";
		return $result;
	}
    
    public function get_input_plain_scale() {	
		$js = $this->get_js_onchange();
		$binds = $this->get_column_binds();
		$result = '<div class="radio_option_scale_wrapper">';
	    $found = false;
		foreach($this->options as $key => $value ) {
		  
			$result .= "<label for=\"{$this->get_html_id()}_{$key}_\" class=\"radio_option_scale\"><span class=\"radio_option_scale_span_pre\"></span>";
			$result .= "<input type=\"radio\" name=\"{$this->get_html_id()}\" value=\"$key\" id=\"{$this->get_html_id()}_{$key}_\"";
            
			if ( "$key" === "{$this->value}" ) {
				$result .= " checked=\"checked\" ";
                $found = true;
			}
			$result .= $js;
            
            $result .= ' /><span class="radio_option_scale_value_text">';				
			$result .= ($value=="") ? "(en blanco)" : $value;			
            $result .= '</span><span class="radio_option_scale_span_post"></span></label>';

            
		}
        if ( ! $found ) {
            if ( $this->value == "" || $this->value == '0' ) {
				//TODO: comprobar antes si esta respuesta es válida
			} else {
				$result .= "<br /><i>(valor actual {$this->value} no encontrado)</i><br />\r\n";
			}
        }              
        $result .= "</div>";
		return $result;
	}
    
    
	public function get_js_onchange() {
        $js = parent::get_js_onchange();
        if ( $js != '' && ( $this->type == 'radio' || $this->type == 'scale' ) ) {
            $js .= ' onclick="this.blur(); this.focus();" ';            
        }
        return $js;
    }
    
	public function get_db_type() {
		return 'int(32) unsigned';
	}
	
	protected $type = 'select';
	public function &set_type($type) {
		switch($type) {
			case 'select':
				$this->type = 'select';
				break;
			case 'radio':
				$this->type = 'radio';
				break;
			case 'scale':
				$this->type = 'scale';
				break;                
			default:
				throw new ExceptionDeveloper('Type '.$type.' not supported');
		}
        return $this;
	}
	public function get_type() {
		return $this->type;
	}
	//------------------------------------------------------------------------------------------------------
	
	public function get_control_render_radio() {
		
		$result = "";
		$result .= $this->get_title()."<br />\r\n";
		//TODO: Insert here icons
		$result .= $this->get_input();			
		$result .= $this->get_input_hidden_prev_value();
			
		return $result;
	}
	public function get_db_select_str() {
		if (count($this->options)==0) {
			return website::$database->escape_string($key).' as '.$this->get_column_name();
		}
		$result = 'CASE '.website::$database->escape_column_name($this->get_column_name()).' ';
		foreach ( $this->options as $key => $value ) {
			$result .= "WHEN '".website::$database->escape_string($key)."' THEN '".website::$database->escape_string($value)."' "; 
		}
		$result .= "END ";
		//$result .= website::$database->escape_string($key).' as '.$this->get_column_name();
		return $result;
	}
}
?>