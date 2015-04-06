<?php
class column_url extends column_text implements icolumn {
//	function get_formatted_value() {
//		return "<a href=\"" . $this->value . "\">" . $this->value . "</a>";
//	}
	public function get_input_plain() {
		$result = parent::get_input_plain ();
		$e = $this->get_value ();
		//$i = website::$theme->get_img_dir () . '/icon_link.gif';
		
		//$idjv = "document.getElementById('".$this->get_column_name()."').Value";
		//$js = "if($idjv!=''){document.location=$idjv;}return false;";

        if ( $e != '' )
		  $result .= " <a href=\"$e\" target=\"_blank\">abrir</a>";
		return $result;
	}
	public function get_db_type() {
		return 'text';
	}
}
?>