<?php
class column_email extends column_text implements icolumn {
	public function __construct() {
		$this->validation_rules[] = new validation_email_simple();
        parent::__construct();
	}
	public function get_input_plain() {
		$result = parent::get_input_plain ();
		$e = $this->get_value();
		$i = website::$theme->get_img_dir() . '/icon_email.gif';
		$result .= " <a href=\"mailto:$e\" title=\"Enviar email\"><img src=\"$i\" alt=\"Enviar email\" title=\"Enviar email\" style=\"vertical-align:bottom; margin-bottom:3px;\" /></a>";
		return $result;
	}

    
    public function get_control_render() {
	    $fv = parent::get_formatted_value();
		if ($fv == '' ) return '';
		return '<a href="mailto:'.$fv.'">'.$fv.'</a>';
    }
}
?>