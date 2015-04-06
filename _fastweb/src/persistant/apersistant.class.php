<?php

class apersistant implements ipersistant {  

    /**
     * Name of table containing this class' data, if different that class name
     * @var string
     */
    protected $_table_name = '';
    
    /**
     * Name of DB field that is PK
     * @var string
     */
    protected $_id_field = 'id';   
	 
	//-------------------------------------------------------------------------------------
	
	/**
	 * Defines 1:n relations, in the following way:
	 * $_fk_1n = array('_puestos' => array('resp_puesto','id_empresa'));
	 * _puestos    : property existing in current class
	 * resp_puesto : name of apersistent class
	 * id_empresa  : name of FK on foreign class referencing PK of current class 
	 * @var array
	 */
    public $_fk_1n = array();  

    /**
     * Defines 1:1 relations, in the following way
     * $_fk_11 = array('_modelo_puesto' => array('modelo_puesto','id_modelo_puesto'));
     * _modelo_puesto   : property existing in current class
     * modelo_puesto    : name of apersistent class
	 * id_modelo_puesto : name of FK on foreign class referencing PK of current class 
     * @var array
     */
    public $_fk_11 = array();
    
    /**
     * Defines n:1 relations, in the following way
     * $_fk_n1 = array('_modelo_puesto' => array('modelo_puesto','id_modelo_puesto'));
     * _modelo_puesto   : property existing in current class
     * modelo_puesto    : name of apersistent class
	 * id_modelo_puesto : name of FK on foreign class referencing PK of current class 
     * @var array
     */
    public $_fk_n1 = array(); //Equivalente al anterior
    
    /**
     * Defines n:m relations, in the following way
     * $_fk_nm = array('_modelos_fuentes_dano' => array('modelo_fuentes_dano','rel_modelo_puestos_modelo_fuentes_de_dano'));
     * _modelos_fuentes_dano                     : property that holds collection access
     * modelo_fuentes_dano                       : name of apersistent class
     * rel_modelo_puestos_modelo_fuentes_de_dano : table with n:m relation data
     * @var array
     */	
    public $_fk_nm = array();
    
	//-------------------------------------------------------------------------------------
	
    /**
     * Specifies if object has loaded data from DB
     * @var bool
     */
	private $_loaded = false;
    /**
     * Specifies if object has been recursively loaded from DB
     * @var bool
     */
	private $_loaded_recursive = false;
	/**
	 * Specifies which arrays of derived apersistant objects are loaded
	 */
 	private $_loaded_array = array();
	
	/**
	 * Specifies if object has modifications not saved
	 * @var bool
	 */
	private $_saved = false;
	
	/**
	 * Copy of data applied to object
	 * @var array
	 */
	private $_data = array();
	
	/**
	 * Magic value to represent that a primary key has not yet any value
	 */
    protected static $_id_new = -1;

   	//-------------------------------------------------------------------------------------
   	
    /**
     * Create an instance of an object linked to database storage.
     * If a value PK is given, it is stored on given property
     * If an array of key => value is given, that data is stored on propertys
     * $param $id_value_or_array_of_propertys mixed
     */
    public function __construct($id_value_or_array_of_propertys=null) {
        $id_field = $this->_id_field;
        
        //We try to set values from parameters
        if ( ! is_null( $id_value_or_array_of_propertys ) ) {
            if ( is_array( $id_value_or_array_of_propertys ) ) {
                $this->set_data($id_value_or_array_of_propertys);
            } else {
                $this->$id_field = $id_value_or_array_of_propertys;
            }
        }
        
    	if ( $this->$id_field == '') {
	  		$this->$id_field = self::$_id_new;
	  		self::$_id_new--;
  		}
   	}
   	
    /**
     * Static call to act as a factory and create an object given the pk value or array of propertys => values
     * $param $id_value_or_array_of_propertys mixed
     */
    public static function create($id_value_or_array_of_properties) {
        if ( $id_value_or_array_of_properties==null) throw new ExceptionDeveloper('No pk value given');
        //Is not assert because can fail on live server
        
        $class = get_called_class();
        $result = new $class($id_value_or_array_of_properties);
        return $result;
    }
    
   	/**
   	 * Returns if current object or property is already loaded.
   	 * @return bool
   	 */
   	public function is_loaded($property='') {
   		if ($property=='')
   			return $this->_loaded;
		
		if ( isset( $this->$property ) ) {
			if ( $this->$property instanceof apersistant ) {
				return $this->$property->is_loaded();
			} else {
				throw new ExceptionDeveloper("Querying is_loaded on non persistant object");
			}
		} else {
			if ( in_array( $property, $this->_loaded_array ) ) {
				return true;
			} else {
				return false;
			}
		}
	}
   	/**
   	 * If not previously loaded, loads all inmediate properties.
   	 * Returns self reference.
   	 * @param $fk_key string Value of PK if not stored in object instance
   	 * @param $index int For collections, index of the item to be loaded
   	 * @return apersistant
   	 */
	public function &load($fk_key='',$index = -1) {	
  		if ($fk_key=='') {
  		    
  			if ($this->_loaded) return $this;
    		$id_field = $this->_id_field;
            if ($this->$id_field == '') throw new ExceptionDeveloper('id_field value not set');
    		$sql = $this->get_sql($this->get_table_name(),$this->_id_field,$this->$id_field);
    		$this->set_data(website::$database->execute_get_row_array($sql));
            $this->_loaded = true;
        } else {
        	if (key_exists($fk_key,$this->_fk_1n)) {
        		if ( ! in_array($fk_key, $this->_loaded_array ) ) {
            		$this->load_fk_1n($fk_key, $index);
					$this->_loaded_array[] = $fk_key;  
				}				          	
			} else if (key_exists($fk_key, $this->_fk_11) ) {
				if ($index != -1) throw new ExceptionDeveloper('Trying to lazy load an index on non collection');
				if ( ! in_array($fk_key, $this->_loaded_array ) ) {
					$this->load_fk_11($fk_key);
					$this->_loaded_array[] = $fk_key;			
				}	
			} else if (key_exists($fk_key, $this->_fk_n1) ) {				
				if ($index != -1) throw new ExceptionDeveloper('Trying to lazy load an index on non collection');
        		if ( ! in_array($fk_key, $this->_loaded_array ) ) {
					$this->load_fk_n1($fk_key);					
					$this->_loaded_array[] = $fk_key;		
				}            	
        	} else if (key_exists($fk_key, $this->_fk_nm)) {
				if ( ! in_array($fk_key, $this->_loaded_array ) ) {        		
        			$this->load_fk_nm($fk_key, $index);				
					$this->_loaded_array[] = $fk_key;        	
				}	
       		} else {
       			throw new ExceptionDeveloper('FK identifier not defined: '.$fk_key);
   			}
        }
		return $this; 
	}
	
	/**
	 * @param $fk_collection array
	 * @return apersistant
	 */
    public function &load_recursive($fk_collection=null) {
    	if ( $this->_loaded_recursive ) return $this;
        if ( ! isset($fk_collection) || is_null ( $fk_collection ) ) {
            $this->load();
            $this->load_recursive($this->_fk_11);
            $this->load_recursive($this->_fk_n1);
            $this->load_recursive($this->_fk_1n);
            $this->load_recursive($this->_fk_nm);
            $this->_loaded_recursive = true;
            $this->_loaded_derived = true;
            return $this;
 		}
 		if ( ! is_array($fk_collection) ) {
 			throw new ExceptionDeveloper('Relation property to load must be specified with an array');
		}
	    foreach($fk_collection as $col_name => $fk) {
	    	
	    	//_fk_n1 = Array ( [_modelo_puesto] => Array ( [0] => modelo_puesto [1] => id_modelo_puesto ) ) 
	    	//echo "$col_name => ".print_r($fk,true)."<br />";
	    	
	        $this->load($col_name);
     
            if ( ! isset($this->$col_name) ) throw new ExceptionDeveloper("Loaded property should be set");
            
            
            /*if ($col_name == "_modelo_puesto" ) {
            	echo "$col_name<br />";
            	echo "_fk_11: ".print_r($this->_fk_11,true)."<br />";
            	echo "_fk_1n: ".print_r($this->_fk_1n,true)."<br />";
            	echo "_fk_n1: ".print_r($this->_fk_n1,true)."<br />";
            	echo "_fk_nm: ".print_r($this->_fk_nm,true)."<br />";
            	echo "merge: ".print_r( array_keys( array_merge($this->_fk_n1, $this->_fk_11 ) ),true)."<br />";
            	if ( in_array($fk_collection,  array_keys( array_merge( $this->_fk_n1, $this->_fk_11 ) ) ) ) {
            		echo "in_array<br />";
           		}
           	}*/
            //We load inmediate objects
            if ( in_array($col_name, array_keys( array_merge( $this->_fk_n1, $this->_fk_11 ) ) ) ) {
            	if ( ! $this->$col_name instanceof apersistant ) throw new ExceptionDeveloper("Loaded property should be apersistant");
				//echo "$col_name recursive<br />";
				
				$this->$col_name->load_recursive();	
           	}
            
            //We load array objects
			if ( ! is_array( $this->$col_name ) && in_array ( $col_name, array_keys( array_merge($this->_fk_1n, $this->_fk_nm) ) ) )
				throw new ExceptionDeveloper("Loaded property should be array");
	        if ( in_array ( $col_name, array_keys( array_merge($this->_fk_1n, $this->_fk_nm) ) ) ) {
	        	foreach($this->$col_name as $object) {
	        		if ( ! $object instanceof apersistant ) throw new ExceptionDeveloper("Loaded object should be persistent");
	            	else $object->load_recursive();
	        	}
        	}
	    }
        return $this;       
    }
    public function get_depth_tree_ui() {
    	return '<ul style="font-family: sans-serif;"><li>'.$this->get_depth_tree().'</li></ul>';
   	}
    private function get_depth_tree($fk_collection=null) {
    	$result = '';
        if ( ! isset($fk_collection) || is_null ( $fk_collection ) ) {
        	$idf = $this->_id_field;
        	$id_refs = "defs_apersistant_".get_class($this)."_".$this->$idf."_".rand(0,99999)."_";
            $result .= "";
            $result .= "<i>".get_class($this)."</i> <span style=\"color:#888; font-size:10px;\">[id = ".$this->id."]";
            $result .= " <a href=\"#\" onclick=\"javascript:document.getElementById('$id_refs').style.display = ( document.getElementById('$id_refs').style.display=='block' ) ? 'none' : 'block'; return false;\">defs</a></span>";
            $result .= '<div id="'.$id_refs.'" style="display:none;">';
            $result .= '_loaded = '.( ($this->_loaded ) ? 'true':'false' ).'<br />';
            $result .= '_loaded_recursive = '.( ($this->_loaded_recursive ) ? 'true':'false' ).'<br />';
            $result .= '_fk_11 = '.print_r($this->_fk_11,true).'<br />';
            $result .= '_fk_1n = '.print_r($this->_fk_1n,true).'<br />';
            $result .= '_fk_n1 = '.print_r($this->_fk_n1,true).'<br />';
            $result .= '_fk_nm = '.print_r($this->_fk_nm,true).'<br />';						            
            $result .= '</div>';
            $result1 = $this->get_depth_tree($this->_fk_11);
            $result1 .= $this->get_depth_tree($this->_fk_n1);
            $result1 .= $this->get_depth_tree($this->_fk_1n);
            $result1 .= $this->get_depth_tree($this->_fk_nm);
            if ( $result1 != '' ) $result1 = '<ul style="list-style-type:none; padding: 5px 30px;">'.$result1.'</ul>';
            $result .= $result1;
            return $result;
 		}
 		if ( ! is_array($fk_collection) ) {
 			throw new ExceptionDeveloper('Relation property to load must be specified with an array');
		}
	    foreach($fk_collection as $col_name => $fk) {
	    	if ( ! isset( $this->$col_name ) ) return "<li><b>$col_name</b> = not set or null</li>";
	    	
	    	if ( ! is_array($this->$col_name) ) {
	    		if ( $this->$col_name instanceof apersistant ) {
	    			$result .= "<li><span style=\"color:#aaa; font-family: Wingdings, 'Zapf Dingbats'\">3</span>";
					$result .= " <b>$col_name</b> : ".$this->$col_name->get_depth_tree()."</li>";
				} else {
					$result .= "<li><span style=\"color:#aaa; font-family: Wingdings, 'Zapf Dingbats'\">3</span>";
					$result .= " [$col_name = ".$this->$col_name."]</li>";
				}
					
    		} else {
        		$idf = $this->_id_field;
        		$id_refs = "arr_apersistant_".get_class($this)."_".$this->$idf."_".$col_name."_".rand(0,99999)."_";    			
    			$result .= "<li>";
    			if ( count($this->$col_name) > 0 ) {
					$result .= "<a href=\"#\" onclick=\"javascript:";
					$result .= "if ( getElementById('$id_refs').style.display == 'block' ) { document.getElementById('$id_refs').style.display='none'; this.innerHTML = '0'; } else { document.getElementById('$id_refs').style.display='block'; this.innerHTML = '1'; } ; return false;\" ";
					$result .= "style=\"text-decoration:none; font-family: Wingdings, 'Zapf Dingbats'\">0</a>";
				} else {
					$result .= "<span style=\"color:#aaa; font-family: Wingdings, 'Zapf Dingbats'\">0</span>";
				}
				$result .= " <b>$col_name</b> <span style=\"color:#888; font-size:10px;\">(".count($this->$col_name)." elements)</span>";
    			$result1 = '';
    			foreach($this->$col_name as $k => $o) {
		    		if ( $o instanceof apersistant ) {
		    			$result1 .= "<li><span style=\"color:#aaa; font-family: Wingdings, 'Zapf Dingbats'\">3</span>";
						$result1 .= " <b>$k</b> : ".$o->get_depth_tree()."</li>";
					} else {
						$result1 .= "<li><span style=\"color:#aaa; font-family: Wingdings, 'Zapf Dingbats'\">3</span>";
						$result1 .= " <b>$k</b> : $o</li>";
					}
   				}
   				
	   			if ( count($this->$col_name) > 0 ) {
			   		$result1 = '<div id="'.$id_refs.'" style="display:none;"><ul style="list-style-type:none; padding: 5px 30px;">'.$result1.'</ul></div>';
		   		}
   				$result .= $result1. "</li>";
   			}
	    }
        return $result;       
    }    
    /*
	protected $_loaded_derived = false;
	public function &load_derived($fk_collection=null) {
   		if ( $this->_loaded_derived ) return $this;
        if ( ! isset($fk_collection) || is_null ( $fk_collection ) ) {
            $this->load();
            $this->load_derived($this->_fk_11);
            $this->load_derived($this->_fk_n1);
            $this->load_derived($this->_fk_1n);
            $this->load_derived($this->_fk_nm);
            $this->load_derived = true;
            return $this;
 		}
 		if ( ! is_array($fk_collection) ) {
 			throw new ExceptionDeveloper('Relation property to load must be specified with an array');
		}
	    foreach($fk_collection as $col_name => $fk) {
	        $this->load($col_name);	        
            if ( ! isset($this->$col_name) ) throw new ExceptionDeveloper("Loaded property should be set");
	    }
        return $this;    		
	}
	*/
    public function load_fk_1n($fk_key, $index = -1) {
    	if ( ! isset( $this->_fk_1n[$fk_key] ) ) 
   			throw new ExceptionDeveloper("not found this->_fk_1n[$fk_key]");
    	if ( ! is_null( $this->$fk_key ) && ! is_array( $this->$fk_key ) )  
   			throw new ExceptionDeveloper("Property $fk_key contains non array value");
        assert::is_in_array($this->_fk_1n,$fk_key);
        assert::is_in_array($this->_fk_1n[$fk_key],0);	
		$class = $this->_fk_1n[$fk_key][0];
        if ( $class=='' ) throw new ExceptionDeveloper('fk_1n class name empty');	
		$parent_id_field  = $this->_id_field; //Current PK field	
        $sql = self::get_sql(
			$this->get_foreign_table_name($class), //foreign table name
			$this->_fk_1n[$fk_key][1], //foreign_id_field
			$this->$parent_id_field);       //parent_id_field, current PK field		
        $rows_data = website::$database->execute_get_array($sql);       
        if ( $rows_data===false ) throw new ExceptionDeveloper(website::$database->get_last_error());
		
		$this->$fk_key = $this->array_merge_overwrite( $rows_data, $class, $this->$fk_key );
    }

    public function load_fk_n1($fk_key) {
        return $this->load_fk_n1_or_11($fk_key,$this->_fk_n1);
    }
    
    public function load_fk_11($fk_key) {
        return $this->load_fk_n1_or_11($fk_key,$this->_fk_11);
    }
    
    public function load_fk_nm( $fk_key, $index = -1 ) {
    	if ( ! isset( $this->_fk_nm[$fk_key] ) )
   			throw new ExceptionDeveloper("not found this->_fk_nm[$fk_key]");
    	if ( ! is_null( $this->$fk_key ) && ! is_array( $this->$fk_key ) )  
   			throw new ExceptionDeveloper("Property $fk_key contains non array value");	

    	$parent_id_field  = $this->_id_field; //Current PK field
    	$parent_id_value = $this->$parent_id_field;
    	$parent_table_name = $this->get_table_name();
    	$parent_class = get_class($this);
    	$parent_rel_id_field = $parent_id_field."_".$parent_class; //TODO: flexibilize this
    	
		$foreign_class 	  = $this->_fk_nm[$fk_key][0];
		$foreign_id_field = $this->get_class_default_property($foreign_class,"_id_field");
		$foreign_tablen_name = $this->get_foreign_table_name($foreign_class);
		$foreign_rel_id_field = $foreign_id_field."_".$foreign_class; //TODO: flexibilize this
		
    	$rel_table_name   = $this->_fk_nm[$fk_key][1];
    	
		$sql = "SELECT
				    `$foreign_tablen_name`.*
				FROM
				    `$rel_table_name`
				    INNER JOIN `$parent_table_name` 
				        ON (`$rel_table_name`.`$parent_rel_id_field` = `$parent_table_name`.`$parent_id_field`)
				    INNER JOIN `$foreign_tablen_name` 
				        ON (`$rel_table_name`.`$foreign_rel_id_field` = `$foreign_tablen_name`.`$foreign_id_field`)
				WHERE (`$parent_table_name`.`$parent_id_field` = $parent_id_value);";
		
  		$rows_data = website::$database->execute_get_array($sql);
  		if ($rows_data===false) throw new ExceptionDeveloper( website::$database->get_last_error() );		
		$this->$fk_key = $this->array_merge_overwrite( $rows_data, $foreign_class, $this->$fk_key );
       	return $this->$fk_key;
   	}
   	
   	//--------------------------------------------------------------------------
	    
    private function load_fk_n1_or_11( $fk_key, $_fk_info ) {
    	assert::is_set($fk_key);
    	assert::is_set($_fk_info);
    	assert::is_array($_fk_info);
    	if ( ! isset($_fk_info[$fk_key]) ) {
    		throw new ExceptionDeveloper("$fk_key not found in array:".print_r($_fk_info,true));
   		}
    	//We test if object exist, in that case we delegate its loading to itself
    	if ( isset($this->$fk_key ) && $this->$fk_key instanceof apersistant ) {
    		return $this->$fk_key->load();
   		} else {
	   		$class 			    = $_fk_info[$fk_key][0];
	        $parent_id_field    = $_fk_info[$fk_key][1]; //FK field in parent class
			$foreign_id_field   = $this->get_class_default_property($class,"_id_field");
   			$p = new $class();
   			$p->$foreign_id_field = $this->$parent_id_field;
   			$p->load();
   			$this->$fk_key = $p;		
   			/*
			//TODO: Specify which one of the two classes has the other one's pk in their row data
	   		$class 			    = $_fk_info[$fk_key][0];
	        $parent_id_field    = $_fk_info[$fk_key][1]; //FK field in parent class
			$foreign_id_field   = $this->get_class_default_property($class,"_id_field"); 		
			$foreing_table_name = $this->get_foreign_table_name($class);
	        if (!isset($this->$parent_id_field)) throw new ExceptionDeveloper("Field '$parent_id_field' not defined in class '".get_class($this)."'");
	        $sql = self::get_sql($foreing_table_name, $foreign_id_field,$this->$parent_id_field);
	        $rows_data = website::$database->execute_get_array($sql);
	  		if ($rows_data===false) throw new ExceptionDeveloper(website::$database->get_last_error());        
	        if ( count($rows_data) > 1 ) throw new ExceptionDeveloper('More that one result in 1:1 or n:1 relation');
	       	if ( count($rows_data) == 1 ) {
	       		$data = reset($rows_data);
	            $p = new $class();
	            if ( ! $p instanceof apersistant ) { throw new ExceptionDeveloper("Class $class not persistent"); }
	            $p->set_data($data);
	            $this->$fk_key = $p;			
			}
			*/
		}
       	return $this->$fk_key ;
    }
   	
   	//--------------------------------------------------------------------------
   	
   	/**
   	 * Add to array $arr objects of $class with data set from $rows_data, using index of
 	 * $class PK value. If index exists, it is overwritten.
 	 * Returns resulting array.
   	 * @return array
   	 */
   	private function array_merge_overwrite( $rows_data, $class, $arr=null) {
   		assert::is_array($rows_data);
	    assert::is_subclass_of($class,'apersistant');
	 	if ( is_null( $arr ) ) $arr = array();
	 	
		foreach($rows_data as $data) {
			$p = new $class();
			if ( isset( $arr[$p->_id_field] ) ) {
				//Object exists
				assert::is_instance_of($arr[$p->_id_field],'apersistant');				
				if ( ! $arr[$p->_id_field]->is_loaded() ) {
					//Object is not loaded
            		$p->set_data($data);
            		$arr[$data[$p->_id_field]] = $p;
  				}
  			} else {
  				//Object doesn't exists
           		$p->set_data($data);
           		$arr[$data[$p->_id_field]] = $p;  				
			}
        }
		return $arr;
	}

	/**
	 * @return apersistant
	 */
    public function &set_data($data_array) {
        $this->_data = $data_array;
        if ($this->_data) {
            //Intentamos establecer cada una de las propiedades del objeto
            $props = $this->get_public_default_properties();
            foreach($props as $key => $def_value) {
                if (substr($key,0,1) != '_') { //Ignoramos todo lo que empiece por '_'
                    if (isset($this->_data[$key])) {
                        $this->$key = $this->_data[$key];
                        //TODO: Set by a setter method
                    } else {
                        //$this->$key = $def_value; //This should reset to default class value
                    }
                }
            }
        }
        //We asume that object is loaded doing this
        /*
        if ( $this->id==64 && $this instanceof resp_puesto ) {
        	var_dump($data_array);
        	echo $this->get_depth_tree_ui();
        	die;
       	}
       	*/
        
        //$this->_loaded = true; //We may do a partial set_data, not all object loaded
        
		return $this;
    }
    
	public function persist() {
		$data = $this->get_public_default_properties();
		foreach($data as $key => $value) {
			if (strpos($key,'_')===0) unset($data[$key]); else
			
			if (is_array($value)) unset($data[$key]);
			//TODO: if is object and apersistant, persist also
			//TODO: if is array, try to persist each element
		}
		
		return $result = website::$database->insert_or_update($this->get_table_name(),array($this->_id_field),$data);
		//echo "<br />result: ";print_r($result); echo "<br />";
		//if ( ! $result ) throw new ExceptionDeveloper('Error persisting');
	}

    private static $instances = array();
    public static function &get_instance($index_value, $class_name) {
        if ( isset( self::$instances[$index_value] ) ) {
            return self::$instances[$index_value];
        }
        $n = new $class_name;
        
        assert::is_instance_of($n, 'apersistant');       
        $n->set_data(array($n->_id_field => $index_value ) );
        $n->load();    
        self::$instances[$index_value] = $n;
        return $n;
    }
 	//----------------------------------------------------------------------------
 	/**
 	 * Gets the value of a named property. The property should be defined explicitly on class
     * or ExceptionDeveloper is raised. Aditional notes:
 	 * a) If index is defined, property must be an array, or derived array to be loaded.
 	 * b) If property is on derived data definition, it's loaded and then returned.
 	 * c) If property is a simple value, it is wraped inside a value_formatted object, then returned.
 	 * d) If property is an object or array, it is directly returned.
 	 * @param $property_name Class property's name
 	 * @return mixed Value of that property
 	 */
 	public function &get($property_name,$index = -1) {
 		if ( $index != -1 && key_exists( $property_name, array_merge( $this->_fk_11, $this->_fk_n1 ) ) )
 			throw new ExceptionDeveloper("Trying to get indexded result not from an array");
 		if ( $index != -1 ) {
 			//It comes from array (already loaded or derived)
	 		if ( key_exists( $property_name, array_merge( $this->_fk_1n, $this->_fk_nm ) ) ) {
 				$result = $this->get_derived_value_from_array($property_name, $index);
			} else {
				$result = $this->get_inmediate_value_from_array($property_name, $index);
			}
		} else {
	 		if ( key_exists( $property_name, array_merge( $this->_fk_11, $this->_fk_n1 ) ) ) {
 				//Derived: was or has to be loaded
				$this->load($property_name);
				$result = $this->$name;
			} else {
				//Should be inmediate
                $this->load();
				if ( ! property_exists($this, $property_name) ) 
					throw new ExceptionDeveloper("Property $property_name not found in class");
				$result = $this->$property_name; 
				if ( ! is_object( $this->$property_name ) && ! is_array( $this->$property_name ) ) {
					$result = value_formatted::create_from_object($this, $property_name);
				}
			}
		}
		return $result;
	}
	
	/**
	 * Returns object, array, or simple value wrapped inside an value_formatted object
	 * @return mixed
	 */
	private function &get_inmediate_value_from_array($property_name, $index) {
		if ( ! property_exists($this, $property_name) ) 
			throw new ExceptionDeveloper("Property $property_name not found in class");
		if ( ! is_array( $this->$property_name ) )
			throw new ExceptionDeveloper("Property $property_name is not an array");
        $prop_arr = $this->$property_name;
		if ( ! isset( $prop_arr[$index] ) )
			throw new ExceptionDeveloper("Array $property_name doesn't have index $index");
		//Tenememos
		$result = $prop_arr[$index];
		if ( ! is_object( $result ) && ! is_array( $result ) ) {
			$result = new value_formatted();
			$result->set_value($prop_arr[$index]);
		}
		return $result;
	}
	
	/**
	 * @return apersistant
	 */	
	private function &get_derived_value_from_array($property_name, $index) {
		if ( ! isset( $index ) ) 
			throw new ExceptionDeveloper('index not defined');
		if ( ! property_exists($this, $property_name) ) 
			throw new ExceptionDeveloper("Property $property_name not found in class");
 		if ( ! key_exists( $property_name, array_merge( $this->_fk_1n, $this->_fk_nm ) ) ) 
			throw new ExceptionDeveloper("Property $property_name not found on derived 1:1 or n:1 definitions");			
		if ( is_null ( $this->$property_name ) ) {
			$this->$property_name = array();
		} else if ( ! is_array( $this->$property_name ) ) {
			throw new ExceptionDeveloper("Property $property_name content is not array");
		}
		//If found on array, we return it
        $prop_arr = $this->$property_name;
		if ( isset( $prop_arr[$index] ) ) {
			//TODO: Assert that index in array equals value for primary key
			return $prop_arr[$index];
		}
		//We have to load it;
		if ( ! key_exists( $property_name, array_merge( $this->_fk_nm ) ) ) {
			$this->load_fk_1n($property_name,$index);
		} else {
			$this->load_fk_nm($property_name,$index);
		}
        $prop_arr = $this->$property_name;
		if ( ! isset( $prop_arr[$index] ) ) {

			throw new ExceptionDeveloper("Error loading index $index of $property_name");
        }
		return $prop_arr[$index];	
	}
	/*
	public function set($name, $value) {
		if ( ! property_exists($this, $name) ) throw new ExceptionDeveloper("Property $name not defined");
		if ( ! $this->$name instanceof value_formatted ) {
			$this->$name = new value_formatted();
		}
		$this->$name->set_value($value);
	}*/	
	
 	//----------------------------------------------------------------------------
 	
    private static function get_sql( $table_name, $field='' , $value='' ) {
		$sql1 = new sql_str("SELECT * FROM {#0}",$table_name);
		$sql = $sql1->__toString();
        if ( isset( $field ) && isset( $value ) && $field != '' && $value != '' ) {
        	$sql1 = new sql_str(" WHERE {#0}='{1}'",$field,$value);
        	$sql .= $sql1->__toString();
        }
        return $sql;
    }
    
    private function get_foreign_table_name($class_name) {
        if ( $class_name=='' ) throw new ExceptionDeveloper("Class name empty");
    	$tn = $this->get_class_default_property($class_name,"_table_name");
    	if ( $tn == '' ) return $class_name;
		return $tn; 
   	}
   	
    private function get_public_default_properties() {
        $ref = new ReflectionObject($this);
        $pros = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
        $result = array();
        foreach ($pros as $pro) {
            false && $pro = new ReflectionProperty();
            $result[$pro->getName()] = $pro->getValue($this);
        }
        return $result;
    }
    
    private function get_class_default_property($class_name, $propertie_name) {
    	if ( ! class_exists($class_name) ) throw new ExceptionDeveloper("Class doesn't exist: '$class_name'");
    	$t = null;
    	//$eval = '$t = new '.$class_name.'();';
    	//eval($eval);
		$t = new $class_name;
    	return $t->$propertie_name;
   	}
   	
	private function get_table_name() {
   		return ( ( $this->_table_name  == '' ) ? get_class($this) : $this->_table_name );
	}
	
	//-----------------------------------------------------------------------------	
	
	public static function static_get_public_property_names($exclude_underscored=true,$class=__CLASS__) {
		$ref = new ReflectionClass($class);
        $pros = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
        $result = array();
        foreach ($pros as $pro) {
        	if ( isset( $pro->name ) && ( ! $exclude_underscored || substr($pro->name,0,1) != '_' ) ) {
            	$result[] = $pro->name;
        	}
        }
        return $result;		
	}   
		
   	//-----------------------------------------------------------------------------
}
