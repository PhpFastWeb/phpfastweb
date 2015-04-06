<?php

interface ipersistant {
	
	/**
	 * Loads all object inmediate data, or specified derived one.
     * If no parameters specified, it uses self defined pk value.
	 * Returns self instance.
	 * @return ipersistant
	 */
	function &load($fk_key='',$index = -1);
	/**
	 * Loads all object inmediate an derived data recursively.
	 * Returns self instance.
	 * @return ipersistant
	 */	
    function &load_recursive($fk_collection=null);
    
    
    function load_fk_1n($fk_key, $index = -1);
    function load_fk_n1($fk_key);
    function load_fk_11($fk_key);
    function load_fk_nm( $fk_key, $index = -1 );
    
    /**
     * Sets the data of the object from an array, and sets its state to 'loaded'.
     * Returns an instance to itself.
     * @return ipersistant
     */
    function &set_data($data_array);
    
	function persist();
	
 	function &get($propertie_name,$index = -1);

}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
