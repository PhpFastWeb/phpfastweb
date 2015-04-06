<?php
class control_simple extends acontrol_column implements icontrol {
	/**
	 * @param icontrol_simple $control
	 * @return icontrol_simple
	 */
	public static function &cast(icontrol $control) {
		if ( ! $control instanceof control_simple ) throw new ExceptionDeveloper('Cast incorrecto');
		return $control;
	}
    public function count_subcontrols() { return 0; }
	/**
	 * @return string
	 */
	public function get_control_render() {
		
		//TODO: propagate this code into each subclass
		$result = '';
		if ($this->column instanceof column_hidden || ( ! $this->column->get_visible() ) ) return $this->column->get_input();
		
		$val_class = '';
		$val_msg = '';
		$val_msg_array = $this->column->get_validation_messages();
		if (count($val_msg_array)>0) {
			$val_msg_txt = '';
			foreach($val_msg_array as $m) {
				$val_msg_txt .= $m.'<br />';
			}
			$val_msg_txt = substr($val_msg_txt,0,-6);
			
			$val_msg = '<div class="control_validation_messages">';
			$val_msg .= '<img src="'.website::$theme->get_img_dir().'/tip_validation.png" alt="" class="tip_validation" />';
			$val_msg .= $val_msg_txt;
			$val_msg .= '</div>';
		}
        $class = $this->column->get_control_render_class();
        $class = ($class != '') ? 'fw_control_div_ '.$class : 'fw_control_div_';
		$result .="<div class=\"$class\" id=\"".$this->column->get_column_name()."_control_\" $val_class>";
		if ($this->column->is_readonly() || $this->column->is_restricted()) {
			$result .= "<label for=\"".$this->column->get_column_name()."\" class=\"label_readonly\">";
			$result .= $this->column->get_title();
			$result .= "</label>: \r\n";
			$result .= '<b>'.$this->column->get_input().'</b>';
			$result .= $this->column->get_input_hidden_prev_value();
			$result .= $val_msg;
		} else {	
			if ($this->column instanceof column_linked) {
				//$result .="<div style=\"margin:4px 0;\" id=\"".$this->column->get_column_name()."_control_\" $val_class>";
				$result .= "<label for=\"".$this->column->get_column_name()."\">";
				$result .= $this->column->get_title();
				$result .= "</label>\r\n";
				$result .= $this->column->get_input();
				$result .= $this->column->get_input_hidden_prev_value();
				$result .= $val_msg;
			} else if ($this->column instanceof column_checkbox) {
				
				$result .= "<label for=\"".$this->column->get_column_name()."\" class=\"radio_option\">";
                $result .= $this->column->get_input();
                $result .= " ";
				$result .= $this->column->get_title();
				$result .= "</label>\r\n";
				$result .= $this->column->get_input_hidden_prev_value();
				$result .= $val_msg;
			} else if ($this->column instanceof column_select && $this->column->get_type()=='radio') {
				$result .= $this->column->get_control_render_radio();
				$result .= $val_msg;
				$result .="</div>\r\n";
				return $result;
			} else if ($this->column instanceof column_select && $this->column->get_type()=='scale') {
				$result .= "<label>";
				$result .= $this->column->get_title();
				$result .= "</label>\r\n";
				$result .= $this->column->get_input();
				$result .= $this->column->get_input_hidden_prev_value();
				$result .= $val_msg;
			} else {
				$result .= "<label for=\"".$this->column->get_column_name()."\">";
				$result .= $this->column->get_title();
				$result .= "</label>\r\n";
				$result .= $this->column->get_input();
				$result .= $this->column->get_input_hidden_prev_value();
				$result .= $val_msg;
			
			}

		}	
		$result .="</div>\r\n";
		
		return $result;
	}

}
?>