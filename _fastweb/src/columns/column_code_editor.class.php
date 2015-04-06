<?php 

//TODO: Implement external requirements

class column_code_editor extends column_textarea implements icolumn {
	
	private $limit_len;
	
	public function __construct() {
		//website::$current_page->js_files[] = website::$base_url.'/_ext/orion/orion-editor.js';
		//throw new ExceptionDeveloper("Not implemented");
		parent::__construct();
	}

	protected $cols_width = 80;
	/*
	public function get_js_onchange() {
		return '';
		
		$js = $this->get_js_binded_columns_anim();
		$js .= 'hasChanges(this);';
		//$js .= 'this.style.height=this.scrollHeight +\'px\';';
		if ( $js != '') {
			 $js = ' onchange="'.$js.'" onkeyup="checkOnchangeKeyup(this);"  onblur="checkOnchangeKeyup(this);"';
		}
		return $js;
		
	}*/
	public function get_input_plain() {
		$result = '';
		//$result .=  "<div class=\"textarea_container\">";
		$text = $this->get_formatted_value();
		$js_onchange = $this->get_js_onchange();
		$result .= "<textarea name=\"{$this->get_html_id()}\" id=\"{$this->get_html_id()}\" cols=\"$this->cols_width\" rows=\"2\" $js_onchange style=\"height:30px; width:730px;\">$text</textarea>\r\n";
		$result .= "<script type=\"text/javascript\">adaptTextarea('{$this->get_html_id()}');</script>\r\n";
		//$result .= "</div>";
		return $result;
				
	}
	
}

?>
