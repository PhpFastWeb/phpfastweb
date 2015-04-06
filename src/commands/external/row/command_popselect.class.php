<?php
class command_popselect extends acommand_row {
	protected $row_text_function = 'command_popselect::static_get_row_text';
	public function set_row_text_function($function_name) {
		$this->row_text_function = $function_name;
	}
	protected $array_text_select = array();
	public function set_array_text_select($columns) {
		if (!is_array($columns)) {
			$this->array_text_select = array( $columns );
		} else {
			$this->array_text_select = $columns;
		}
	}
	protected $js_return_function = "window.opener.selectPopup";
	public function set_js_return_function($function_name) {
		$this->js_return_function = $function_name;
	}
	public function get_execute_link() {
		$result = "<a href=\"#\"";
		$result .= " onclick=\"{$this->get_execute_onclick()}\" ";
		$result .= ">". $this->get_name () ."</a>";
		return $result;
	}
	protected function get_pk() {
		if (count($this->key_set->primary_keys)!=1) {
			throw new ExceptionDeveloper('Expected one primary key');
		}
		$pk = reset($this->key_set->primary_keys);
		return $pk;
	}
	public function get_execute_onclick() {
		$pk = $this->get_pk();
		$id = $this->key_set->keys_values[$pk];
		$text = $this->get_row_text($this->table->row,$this->array_text_select);

		$text = str_replace(
			array( "'", "\n" ),
			array( "\\'", "\\n'\n\t+'" ),
			$text);
		return $this->js_return_function."('".$id."','".$text."');";
	}
	public static function static_get_row_text($row,$cols=null) {
		$d = $row; //params[0];
		//$cols = $params[1];
		//$d = $row;
//		$pk = $this->get_pk();
//		if (isset($d[$pk])) {
//			unset($d[$pk]);
//		}
		if (is_null($cols)) {
			reset($d);
			$result = end($d);
		} else {
			$result = '';
			foreach ($cols as $col) {
				$result .= $d[$col]." | ";
			}
			$result = substr($result,0,-3);
		}
		return $result;
	}
	public function get_row_text($row) {
		$f = explode('::',$this->row_text_function);
		return call_user_func($f,$row,$this->array_text_select);
	}
	/**
	 * 
	 */
	public function get_name() {
		return "Seleccionar";
	}

	/**
	 * 
	 */
	public function execute() {
	}

	/**
	 * 
	 */
	public function get_key() {
		return "popselect";
	}

		
}
?>