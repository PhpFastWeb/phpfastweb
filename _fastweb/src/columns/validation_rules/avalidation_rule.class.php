<?php
abstract class avalidation_rule implements ivalidation_rule {
	
	protected $messages = array();
	protected $already_checked = false;
	protected $validates = true;
	protected $enforce = true;
	public function set_enforce($enforce=false) {
		$this->enforce = $enforce;
	}
	
	protected function invalidate() {
		if ($this->enforce) $this->validates = false;
	}
	
	public function get_validation_messages() {
		return $this->messages;
	}
	public function reset() {
		$this->already_checked = false;
		$this->validates = true;
		$this->messages = array();
	}
	public function set_already_checked($already_checked=true) {
	   $this->already_checked = $already_checked;
       return $this;
    }
	/**
	 * Return true if validation must be skipped due to this conditions:
	 * 1. Validation has already been done
	 * 2. Field is not enabled
	 * If it must be skipped, the check_column() method must return what is in $validates propertie
	 */
	protected function skip_validation(icolumn $column) {
	    
		$already_checked = $this->already_checked;
		$this->already_checked = true;
		//--
		if ($already_checked) return true;
		//--
		if ( ! $column->is_binded_enabled() ) {
			$this->validates = true;
			return true;
		}
        
		if ( ! $column->get_visible() ) {
			$this->validates = true;
			return true;		
		}
        
		if ( $column->get_table() && $column->get_table()->control_group->count_subcontrols()>0 && ! $column->get_table()->control_group->has_column($column->get_column_name()) ) {
			$this->validates = true;
			return true;				
		}
		return false;
		
	}
	// Protected -------------------------------------------------
	
} 
?>