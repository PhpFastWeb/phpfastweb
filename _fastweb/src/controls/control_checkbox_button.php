<?php
class control_checkbox_button extends acontrol_column  implements icontrol {
	/**
	 * @param icontrol_simple $control
	 * @return control_checkbox_button
	 */
	public static function &cast(icontrol $control) {
		if ( ! $control instanceof control_simple ) throw new ExceptionDeveloper('Cast incorrecto');
		return $control;
	}
	public $button_states = array('Cambiar a SI','Cambiar a NO');
	public function get_control_render() {

		$result = $this->column->get_input_hidden();
		
		$result .= "\r\n";
		$result .= $this->column->get_title()." : ".$this->column->get_value();
		$t = $this->button_states[$this->column->get_formatted_value()];
		$result .= "<input type=\"button\" value=\"$t\" onclick=\"javascript:prompt('¿Esta seguro?');\" />";
	}

}
?>