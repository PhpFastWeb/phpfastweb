<?php
class columns_bind {
	public $input_id_source;
	public $input_id_target;
	public $column_name_source;
	public $values_to_enable = array();
	public $control_id;
	public function __construct($column_name_source,$input_id_source,$input_id_target,$value_to_enable_or_array,$control_id) {
		$this->column_name_source = $column_name_source;
		$this->input_id_source = $input_id_source;
		$this->input_id_target = $input_id_target;
		if (is_array($value_to_enable_or_array)) {
			$this->values_to_enable = $value_to_enable_or_array;
		} else {
			$this->values_to_enable = array($value_to_enable_or_array);
		}
		$this->control_id = $control_id;
	}
	public function is_value_to_enable($value) {
		if (in_array($value,$this->values_to_enable)) {
			return true;
		}
		return false;
	}
	public function get_values_enable_array_js() {
		$result = '';
		foreach($this->values_to_enable as $v) {
			$result .= "'".str_replace("'","\\'",$v)."',";
		}
		$result = substr($result,0,-1);
		$result = "[".$result."]";
		return $result;
	}
	public function get_js() {
		$vs = $this->get_values_enable_array_js();
		$js = "bind_input('".$this->input_id_source."',".$vs.",'".$this->input_id_target."','".$this->control_id."');";
		return $js;
	}
	public function get_js_anim() {
		$vs = $this->get_values_enable_array_js();
		$js = "bind_input_anim('".$this->input_id_source."',".$vs.",'".$this->input_id_target."','".$this->control_id."');";
		return $js;
	}
	/**
	 * @param columns_bind $bind
	 * @return columns_bind
	 */
	public static function &cast(columns_bind $bind) {
		return $bind;
	}
}
?>