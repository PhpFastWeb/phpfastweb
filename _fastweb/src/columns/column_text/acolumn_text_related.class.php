<?php
abstract class acolumn_text_related extends acolumn implements icolumn {
	
	protected $limit_len = -1;
    
    public function &set_limit_len($max_len) {
		$this->limit_len = $max_len;
		return $this;
	}
    public function get_limit_len() {
        return $this->limit_len;
    }
    public function get_js_after_control() {
        return 
            parent::get_js_after_control().
            $this->get_js_limits();
    }
    protected function get_js_limits() {
        $limit = $this->get_limit_len();
        if ( $limit < 0 ) {
            return '';
        }
        return "jQuery('#".$this->get_column_name()."').wChar({message: 'restantes', min: 0, max: $limit});\r\n";
    }
   	public function validate() {
		if (count($this->validation_rules)==0 && $this->limit_len >= 0) {
			$this->validation_rules[] = new validation_max_length($this->limit_len);
		}
		return parent::validate();
	}    
 }