<?php
class value_formatted {

    protected $value;    
    /**
     * @return string
     */
    public function get_value() {
        return $this->value;
    }
    public function __toString() {
        return $this->get_value();
    }
    /**
     * @param $value string
     * @return formatted_value
     */
    public function &set_value( $value ) {
        $this->value = $value;
        $this->formatted_value = null; //We invalidate possible stored formatted value
        return $this;
    }
    
    protected $formatted_value = null;    
    /**
     * @return string
     */
    public function get_formatted_value() {
		if ( $this->formatted_value == null ) {
			$this->formatted_value = $this->raw_to_formatted($this->value);
		}
        return $this->formatted_value;
    }
    /**
     * @param $value string
     * @return formatted_value
     */
    public function &set_formatted_value( $formatted_value ) {
        $this->formatted_value = $formatted_value;
        $this->value = $this->formatted_to_raw($formatted_value);
        return $this;
    }    
    
    protected function formatted_to_raw($value) {
    	return $value;
   	}
   	protected function raw_to_formatted($value) {
   		return $value;
	}
    //--------------------------------------------------------------

    public function __construct( $value ) {
        assert::is_set( $value );
        $this->value = $value;
    }

	//--------------------------------------------------------------
	public static function create_from_object(apersistant $object, $property_name) {
        assert::is_set($object);
        assert::is_set($object->$property_name);
        //TODO: check what kind of value_formatted must be returned by $object definition 
		return new value_formatted($object->$property_name);
	}

}

?>