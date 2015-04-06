<?php 
class column_textarea extends acolumn_text_related implements icolumn {
	
	
	
	protected $cols_width = 48;
	
	
	public function get_formatted_value() {
		$result = nl2br(stripslashes($this->value));
		return $result;
	}
	public function &set_formatted_value($value) {
		$this->value = trim ( $value );
		return $this;
	}
	public function get_js_onchange() {
		$js = $this->get_js_binded_columns_anim();
		$js .= 'hasChanges(this);';
		//$js .= 'this.style.height=this.scrollHeight +\'px\';';
		if ( $js != '') {
			 $js = ' onchange="'.$js.'" onkeyup="checkOnchangeKeyup(this);"  onblur="checkOnchangeKeyup(this);"';
		}
		return $js;
	}
	public function get_input_plain() {
		$result = '';
		//$result .=  "<div class=\"textarea_container\">";
		$text = stripslashes($this->get_value());
		$js_onchange = $this->get_js_onchange();
		$result .= "<textarea name=\"{$this->get_html_id()}\" id=\"{$this->get_html_id()}\" cols=\"$this->cols_width\" rows=\"2\" $js_onchange style=\"height:30px;\">$text</textarea>\r\n";
		$result .= "<script type=\"text/javascript\">adaptTextarea('{$this->get_html_id()}');</script>\r\n";
		//$result .= "</div>";
		return $result;
		
//		if ( $this->columns_max_len[$col_name] > 0 ) {
//			echo "maxlength=\"".$this->columns_max_len[$col_name]."\""; 
//		}
				
	}

}

?>
