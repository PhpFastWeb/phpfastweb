<?php
class validation_match extends avalidation_rule implements ivalidation_rule {

    protected $ref_column = null;
    function __construct(icolumn $ref_column) {
        $this->ref_column = $ref_column;
        
    }
	function check_column(icolumn $column) {	
    
		if ( $this->skip_validation($column) ) return $this->validates;
        
        if ( $this->ref_column->get_value() != $column->get_value() ) {
            $this->messages[] = "Los campos no coinciden";
            $this->invalidate();
        }
		return $this->validates;
	}
}
?>