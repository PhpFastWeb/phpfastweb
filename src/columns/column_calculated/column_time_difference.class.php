<?php
class column_time_difference extends acolumn_calculated implements icolumn {
	
	protected $column_start = 'fecha_inicio';
	protected $column_end = 'fecha_fin';
	
	public function &set_column_start($column_name) {
		$this->column_start = $column_name;
		return $this;
	}
	
	public function &set_column_end($column_name) {
		$this->column_end = $column_name;
		return $this;
	}
	protected function get_start_date() {
		$result = $this->table->columns_col->get($this->column_start)->get_value();
		if ($result == '0000-00-00' || $result == '0000-00-00 00:00:00') return '';
		return $result;
	}
	protected function get_end_date() {
		$result = $this->table->columns_col->get($this->column_end)->get_value();
		if ($result == '0000-00-00' || $result == '0000-00-00 00:00:00') return '';
		return $result;
	}
	protected function get_td() {
		if ($this->get_start_date()=='' || $this->get_end_date()=='') return '';
		
		$st = split('-',$this->get_start_date());		
		$startTime =   mktime(0, 0, 0, $st[1],$st[2],$st[0]);
		
		$et = split('-',$this->get_end_date());		
		$endTime =   mktime(0, 0, 0, $et[1],$et[2],$et[0]);
		
		$difTime = $endTime - $startTime;	

		$result = floor($difTime / (60*60*24));
		return $result;
	}
	public function get_input_plain() {
		return $this->get_formatted_value();
	}
	
	public function get_value() {
		return $this->get_td();
	}
	
	public function get_formatted_value() {
		$v = $this->get_td();
		if ($v==='') return '(especifique fechas de inicio y fin)';
		if ($v==1) {
			return $v." día ";
		} else {
			return $v." días";
		}
		
	}
	
	//add_validation_message
	
}