<?php
class column_age extends acolumn_calculated implements icolumn {
	
	protected $column_birthdate = 'fecha_nacimiento';
	
	public function set_column_birthdate($column_name) {
		$this->column_birthdate = $column_name;
	}
	
	protected function get_birthdate() {
		$result = $this->table->columns_col->get($this->column_birthdate)->get_value();
		return $result;
	}
	protected function get_age() {
		if ($this->get_birthdate()=='') return '';
		$ba = split('-',$this->get_birthdate());
		//return $ba[1].','.$ba[2].','.$ba[0];
		
		$ageTime =   mktime(0, 0, 0, $ba[1],$ba[2],$ba[0]);
		//return $ageTime;
		//$ageTime = mktime(0, 0, 0, 9      ,9    ,1919); // Get the person's birthday timestamp
		$t = time(); // Store current time for consistency
		$age = ($ageTime < 0) ? ( $t + ($ageTime * -1) ) : $t - $ageTime;
		$year = 60 * 60 * 24 * 365;
		$ageYears = $age / $year;
		//return $ageYears;
		$result = floor($ageYears);
		return $result;
	}
	public function get_input_plain() {
		return $this->get_formatted_value();
	}
	
	public function get_value() {
		return $this->get_age();
	}
	
	public function get_formatted_value() {
		$v = $this->get_age();
		if ($v=='') return '(especifique fecha de nacimiento)';
		return $this->get_age()." años";
	}
	
	//add_validation_message
	
}