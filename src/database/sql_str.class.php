<?php
/**
 * Inmutable class, constructor admits variable parameters, first one a SQL string,
 * rest are strings for substitution in the first one, using current database escaping:
 *  {n} uses escape_string
 *  {#n} uses escape_column_name (table/view/database name)
 *  {\@n} doesn't use any escaping
 */
class sql_str {
	
	/* Incubation */
	const escape_do_not_escape = 0;
	const escape_as_string = 1;
	const escape_as_object_name = 2;
	const escape_as_number = 3;
	const escape_as_date = 4;
	const escape_as_like = 5;
	protected $escape_return_type = self::escape_do_not_escape;
	
	const delimiters_return_without = 0;
	const delimiters_return_with = 1;
	protected $delimiters_return_type = self::delimiters_return_without;
	
	/* To be redefined by database engine */
	protected $delimiters = array(
		self::escape_do_not_escape => '',
		self::escape_as_string => "'",
		self::escape_as_number => "'",
		self::escape_as_date => "'",
		self::escape_as_like => "'",
	);
	
	//----------------------------------------------------------
	
    public static function &get_instance() {        
        $reflection = new ReflectionClass( __CLASS__ );
        $instance = $reflection->newInstanceArgs( func_get_args() );
        return $instance;
    }
    
    
    protected $query_string = '';
    protected $parameters = array();    
    /**
     * Inmutable class, constructor admits variable parameters, first one a SQL string,
     * rest are strings for substitution in the first one, using current database escaping:
     *  {n} uses escape_string
     *  {#n} uses escape_column_name (table/view/database name)
     *  {\@n} doesn't use any escaping
     */
    public function __construct() {
        $this->parameters = func_get_args();
        if (count($this->parameters) > 0) {
            $this->query_string = $this->parameters[0];
            $this->parameters = array_slice($this->parameters, 1);
        }
        $this->process_result();
    }
    protected $result_string = '';
    protected function process_result() {
    	if ( $this->result_string != '' ) return $this->result_string;
        $i = 0;
        $this->result_string = $this->query_string;
        foreach ($this->parameters as $param) {
        	if ($param instanceof sql_str) {
        		$this->result_string = str_replace('{'  . $i . '}',$param->__toString(), $this->result_string);
            	$this->result_string = str_replace('{#' . $i . '}',$param->__toString(), $this->result_string);
            	$this->result_string = str_replace('{@' . $i . '}',$param->__toString(), $this->result_string);                
       		} else {
            	$this->result_string = str_replace('{'  . $i . '}', website::$database->escape_string($param), $this->result_string);
            	$this->result_string = str_replace('{#' . $i . '}', website::$database->escape_column_name($param), $this->result_string);
            	$this->result_string = str_replace('{@' . $i . '}', $param, $this->result_string);
         	}
            $i++;
        }
        //Desescapamos casos a no sustituir
        //TODO: desescapar esto correctamente
        //$result_string = str_replace('\\{','{',$result_string);
        //$result_string = str_replace('\\}','}',$result_string);
        //$result_string = str_replace('\\\\','\\',$result_string);

		//$this->result_string = preg_replace('/[\n]+/i', "\n", $this->result_string);

        return $this->result_string;
    }
    public function __toString() {
    	$this->process_result();
    	switch( $this->escape_return_type ) {
    		case self::escape_do_not_escape:
    			return $this->result_string;
   			case self::escape_as_string:
   				return website::$database->escape_string($this->result_string);
			case self::escape_as_object_name:
				return website::$database->escape_column_name($this->result_string);
			default:
				throw new ExceptionDeveloper('Not implemented');
   		}    	
   	}
}
