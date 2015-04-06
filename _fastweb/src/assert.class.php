<?php

class assert {
	public static function set_not_throw_exceptions($throw=false) {
		$this->throw_exceptions = $throw;
	}
	protected static $throw_exceptions = true;
	//--
	public static function is_array($param=null) {
		if ( ! is_array($param) ) {
			if (self::$throw_exceptions) throw new ExceptionDeveloper('Parameter not array');
			return false;
		}
		return true;
	}
	public static function is_non_empty_array($param=null) {
		self::is_array($param);
		if ( count($param) == 0 ) {
			if (self::$throw_exceptions) throw new ExceptionDeveloper('Array must not be empty');
			return false;
		}
		return true;
	}
	public static function is_set($param=null) {
		if ( ! isset($param) || is_null($param) ) {
			if (self::$throw_exceptions) throw new ExceptionDeveloper('Parameter must be set');
			return false;
		}
		return true;
	}
	public static function is_not_empty($param=null) {
		self::is_set($param);
        if ( empty($param) ) {
			if (self::$throw_exceptions) throw new ExceptionDeveloper('Parameter must not be empty');
			return false;
		}
		return true;
	}
	public static function is_instance_of($var, $class_name) {
		if ( ! $var instanceof $class_name ) {
			if (self::$throw_exceptions) throw new ExceptionDeveloper('$var must be instance of '.$class_name.'<br />'.print_r($var,true));
			return false;
		}
		return true;
	}
    public static function is_in_array($array_var,$index) {
        self::is_array($array_var);
        if ( ! isset( $array_var[$index] ) ) throw new ExceptionDeveloper("index $index must be defined in array<br /><pre>".print_r($array_var,true).'</pre>');
    }
	public static function is_subclass_of($child_class, $parent_class) {
		$c = new $child_class();
		if ( ! $c instanceof $parent_class ) {
			if (self::$throw_exceptions) throw new ExceptionDeveloper("Class $child_class must be a subclass of $parent_class");
			return false;
		}
		return true;
	}
    public static function is_same_value_and_type($value1,$value2) {
        if ( $value1 !== $value2 ) {
            if (self::$throw_exceptions) throw new ExceptionDeveloper("Values must be same content and type: $value1 $value2");
            return false;
        }
        return true;
    }
}
?>