<?php
class control_literal extends acontrol implements icontrol {
	/**
	 * @param array $row
	 */
	public function set_values($row_array) {
		return;
	}

	public $literal = '';
	public function set_literal($literal) {
		$this->literal = $literal;
	}
	public function get_literal() {
		return $this->literal;
	}
	public function get_control_render() {
		$result = $this->literal;	
	
		if ( ! is_null($this->id) && $this->id ) {
			$result = '<span id="'.$this->id.'">'.$result.'</span>';
		}	
		return $result;
	}
	public function __construct($literal='',$id=null) {
		$this->literal = $literal;
		if (! is_null($id) ) {
			$this->id = $id;
		}
	}
	/**
	 * @param icontrol $control
	 * @return control_literal
	 */
	public static function &cast(icontrol $control) {
		return $control;
	}





}
?>