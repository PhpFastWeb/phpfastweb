<?php
class control_group extends acontrol implements icontrol {
	
	protected $controls = array();
	
	public function __construct(table_data &$table=null) {
		if ($table != null) {
			$this->set_table($table);
		}
	}
	/**
	 * Adds a control to the group.
	 * If an string is passed, it gets the column with that id, and adds a control_simple for that column.
	 * If an array is passed, it process each element of the array.
	 * If multiple arguments are passed, it process each of the arguments (same as array).
	 */
	public function add() {

	    for ($i = 0;$i < func_num_args();$i++) {
	      	$param = func_get_arg($i);
			if (is_array($param)) {
				foreach($param as $c) {
					$this->add($c);
				}
			} else {
				$this->add_single($param);
			}
	    }
	}
	
	protected function add_single($control) {
		if ($this->table == null ) { throw new ExceptionDeveloper('property control_group->$table not set'); }
		if (is_string($control)) {
			$column = $this->table->columns_col->get($control);
			$control = new control_simple();
			$control->set_column($column);
			$this->add($control);
		} else if ($control instanceof icontrol) {
			$control->set_table($this->table);
			$this->controls[] = $control;
		} else {
			throw new ExceptionDeveloper("Expected string or icontrol");
		}
	}
	
	public function has_column($column_name) {
		$found = false;
		$next = reset($this->controls);
		while ( ! $found && $next) {
			$found = $next->has_column($column_name);
			$next = next($this->controls);
		}
		return $found;
	}
	public function count_subcontrols() {
	   return count($this->controls);
    }
	public function remove() {
	    for ($i = 0;$i < func_num_args();$i++) {
	      	$param = func_get_arg($i);
			if (is_array($param)) {
				foreach($param as $c) {
					$this->remove($c);
				}
			} else {
				$this->remove_single($param);
			}
	    }
	}
	protected function remove_single($control_id) {
		foreach ($this->controls as $key => $control) {
			$control = acontrol::cast($control);
			if ($control->get_id()==$control_id) {
				unset($this->controls[$key]);
				return;
			}
		}
		throw new ExceptionDeveloper("Control $control_id not found to remove");
	}
//	public function add(array $col_names_array) {
//		if ($this->table == null ) { throw new ExceptionDeveloper("table debe estar definido"); }
//		foreach ($col_names_array as $col_name) {
//			if ($col_name instanceof icontrol) {
//				$this->add($col_name);
//			} else {
//				$column = $this->table->columns_col->get($col_name);
//				$control = new control_simple();
//				$control->set_column($column);
//				$this->add($control);
//			}
//		}	
//	}
//	protected function add($col_name) {
//		$column = $this->table->columns_col->get($col_name);
//		$control = new control_simple();
//		$control->set_column($column);
//		$this->add($control);
//	}

	public function get_subcontrols() {
		return $this->controls;
	}
	protected $row = array();
	
	public function set_values($row_array) {
		$this->row = $row_array;
		foreach($this->controls as $control) {
			$control->set_values($row_array);
		}
	}
	/**
	 * @return string
	 */
	public function get_control_render() {
		$result = '';
		foreach($this->controls as $control) {
			$result .= $control->get_control_render();
			//$result .= '<br />';
		}
		return $result;
	}	
}
?>