<?php
/**
 * Characterize a n:m relation between to database tables using an auxiliary relation table
 * [Table_a]   [Table_rel]   [Table_b]
 * [id     ] - [id_a id_b] - [id     ]
 */
class table_relation {
    /**
     * Name in database of one of the tables in the relation
     */
    protected $table_name_a;
    /**
     * Name in database of one of the tables in the relation
     */
    protected $table_name_b;
    /**
     * @var string
     */
    protected $id_a = 'id';
    /**
     * Name of primary key for table B
     * @var string
     */
    protected $id_b = 'id';
    /**
     * Name in database of table containing the n:m relation
     * @var string
     */
    protected $table_name_rel = '';
    /**
     * @var string
     */
    protected $id_rel_a = '';
    /**
     * @var string
     */
    protected $id_rel_b = '';
    //-----------------------------------------------------------------


    /**
     * @return string
     */
    public function get_table_name_a() {
        return $this->table_name_a;
    }
    /**
     * @param table_name_a 
     * @return table_relation
     */
    public function &set_table_name_a($table_name_a) {
        $this->table_name_a = $table_name_a;
        return $this;
    }
    //--------------------------------------------------------------

    /**
     * @return 
     */
    public function get_table_name_b() {
        return $this->table_name_b;
    }
    /**
     * @param table_name_b 
     * @return 
     */
    public function &set_table_name_b($table_name_b) {
        $this->table_name_b = $table_name_b;
        return $this;
    }
    //--------------------------------------------------------------
    protected $key_set_a;

    /**
     * @return 
     */
    public function get_key_set_a() {
        return $this->key_set_a;
    }

    /**
     * @param key_set_a 
     * @return 
     */
    public function &set_key_set_a($key_set_a) {
        $this->key_set_a = $key_set_a;
        return $this;
    }
    //--------------------------------------------------------------
    protected $key_set_b;
    /**
     * @return 
     */
    public function get_key_set_b() {
        return $this->key_set_b;
    }
    /**
     * @param key_set_b 
     * @return 
     */
    public function &set_key_set_b($key_set_b) {
        $this->key_set_b = $key_set_b;
        return $this;
    }
    //--------------------------------------------------------------

    /**
     * @return string
     */
    public function get_id_a() {
        return $this->id_a;
    }
    /**
     * @param id_a 
     * @return table_relation
     */
    public function &set_id_a($id_a) {
        $this->id_a = $id_a;
        return $this;
    }
    //--------------------------------------------------------------

    /**
     * @return string
     */
    public function get_id_b() {
        return $this->id_b;
    }
    /**
     * @param id_b 
     * @return table_relation
     */
    public function &set_id_b($id_b) {
        $this->id_b = $id_b;
        return $this;
    }
    //--------------------------------------------------------------
    /**
     * @return string
     */
    public function get_table_name_rel() {
        if ($this->table_name_rel != '')
            return $this->table_name_rel;
        else
            return 'rel_' . $this->get_table_name_a() . "_" . $this->get_table_name_b();
    }
    /**
     * @param table_name_rel 
     * @return table_relation
     */
    public function &set_table_name_rel($table_name_rel) {
        $this->table_name_rel = $table_name_rel;
        return $this;
    }
    //--------------------------------------------------------------

    /**
     * @return string
     */
    public function get_id_rel_a() {
        if ($this->id_rel_a != '')
            return $this->id_rel_a;
        else
            return 'id_' . $this->get_table_name_a();
    }
    /**
     * @param id_rel_a 
     * @return table_relation
     */
    public function &set_id_rel_a($id_rel_a) {
        $this->id_rel_a = $id_rel_a;
        return $this;
    }
    //--------------------------------------------------------------

    /**
     * @return string
     */
    public function get_id_rel_b() {
        if ($this->id_rel_b != '')
            return $this->id_rel_b;
        else
            return 'id_' . $this->get_table_name_b();
    }
    /**
     * @param id_rel_b 
     * @return table_relation
     */
    public function &set_id_rel_b($id_rel_b) {
        $this->id_rel_b = $id_rel_b;
        return $this;
    }
    //--------------------------------------------------------------
	public function get_sql_from($join_mode='INNER') {
		if ( ! isset( website::$database ) ) {
			throw new ExceptionDeveloper("Database must be defined to correctly escape strings");
		}		
	
		//We escape all variables
		$ta = website::$database->escape_table_name($this->get_table_name_a());
		$tb = website::$database->escape_table_name($this->get_table_name_b());
		$tr = website::$database->escape_table_name($this->get_table_name_rel());
		$ida = website::$database->escape_column_name($this->get_id_a());
		$idb = website::$database->escape_column_name($this->get_id_b());
		$idrela = website::$database->escape_column_name($this->get_id_rel_a());
		$idrelb = website::$database->escape_column_name($this->get_id_rel_b());
		
				
   		//Build SQL
		$sql = "
			$tb
		    $join_mode JOIN $tr 
		        ON ($tb.$idb = $tr.$idrelb)
		    $join_mode JOIN $ta 
		        ON ($tr.$idrela = $ta.$ida) \r\n";
      	return $sql;
	}
	protected static function get_sql_from_static_resolve($class_name,$join_mode='INNER') {
		$t = new $class_name();
		return $t->get_sql_from($join_mode);
	}
}

?>