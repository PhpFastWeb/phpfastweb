<?php
class columns_collection extends ArrayObject {
	protected $table = null;
	public function set_table(table_data &$table) {
		$this->table = $table;
	}
	public function __construct(table_data &$table) {
		$this->table = $table;
	}
	/**
	 * Set values for each columns, from an array, where each key is the column name.
	 * The values must be 'human formatted'.
     * Permissive if array has other keys than columns present.
	 * @param array $array_values
	 */
	public function set_formatted_values_array($array_values) {
		foreach ( $array_values as $key => $value ) {
			if (! isset ( $this [$key] )) {
				//throw new ExceptionDeveloper ( "No se encontró la columna: " . $key );
			} else {
				$col = $this->get ( $key );
				//if ( $col->is_readonly() || $col->is_restricted_to_one_value() ) {
                if ( $col->is_restricted_to_one_value() ) {
					//TODO: No debería ser necesario establecer ningún valor
					$col->set_value ( $value );
				} else {
					$col->set_formatted_value( $value );
				}
				//Comprobamos si la columna ha cambiado
				if (isset($array_values[$key."_prev_"]) && $array_values[$key."_prev_"] == $value) {
					$col->set_changed(false);
				} else {
					$col->set_changed(true);
				}
			}
		}
	}
    /**
     * Set values for each column, from an array, where each key is the column id
     * The values must be 'database formatted'.
     * @param array $array_values
     */
	public function set_values_array($array_values) {
		if ($array_values==null) return; //Some operations give null for empty arrays
		foreach ( $array_values as $key => $value ) {
			if (! isset ( $this [$key] )) {
				//throw new ExceptionDeveloper ( "No se encontró la columna: " . $key );
			} else {
				$this->get($key)->set_value($value);
			}
		}
	}
	
    /**
     * Returns true if any column in the set is required
     */
    public function has_required_columns() {
        $found = false;
        $col = reset($this);
        while ( $col && ( ! $found ) ) {
            $found = $col->is_required();
            $col = next($this);
        }
        return $found;
    }
    
    /**
     * Takes an array of icolumns objects, indexed by its key references,
     * and adds it to the current set.
     */
	public function add_array($indexed_array_of_columns) {
		if (!is_array($indexed_array_of_columns))  { throw new ExpcetionDeveloper('Parameter passed not array'); }
		foreach ($indexed_array_of_columns as $index => $column) {
			if (! $column instanceof icolumn) { throw new ExpcetionDeveloper('Object passed in array not icolumn'); }
			$column = acolumn::cast($column);
			$column->set_table($this->table);
			$column->set_column_name($index);	
			if ( $this->table->columns_required_all ) {
				if ( ! $column instanceof column_checkbox )
					$column->set_required(true);
			}					
			$this->offsetSet($index,$column);
		
		}
   	}
    
	/**
	 * Set or clears readonly property in all columns of the collection
	 * @param bool $readonly
	 */
	public function set_readonly($readonly=true) {
		foreach ($this as $col) {
			$col->set_readonly($readonly);
		}	
	}
//	/**
//	 * @return array
//	 */
//	public function get_new_row() {
//		$row = array ();
//		foreach ( $this as $col ) {
//			if (! is_null ( $col->get_default_value () )) {
//				$row [$col->get_column_name ()] = $col->get_default_value ();
//			}
//		}
//		return $row;
//	}
	public function set_columns_new_row() {
	   $forced_new = array();
        if ( $this->table->filter_persist_use_for_new ) {
            $this->table->filters->load_filters_from_session();
        }
		foreach ( $this as $col ) {
            $new_value = '';
            
			if ( ! is_null ( $col->get_default_value () )) {
				$new_value = $col->get_default_value ();
			}
            if ( $this->table->filter_persist_use_for_new 
                && isset($this->table->filters_data['FILTER_'.$col->get_column_name()] ) ) {
				$new_value = $this->table->filters_data['FILTER_'.$col->get_column_name()];
                //TODO: Check that this new value is valid
                //TODO: Use other forms of filters
			}
            $col->set_value( $new_value );
		}
	}
	public function get_restricted_values() {
		$result = array ();
		foreach ( $this as $col ) {
			if ( ! is_null ( $col->get_restricted_value() ) && ! is_array( $col->get_restricted_value()) ) {
				$result [$col->get_column_name ()] = $col->get_restricted_value ();
			}
		}
		return $result;
	}
    /**
     * Returns an array with column names as keys, and values
     * @return Array
     */
	public function get_array() {
		$result = array ();
		foreach ( $this as $col ) {
		  $result [$col->get_column_name ()] = $col->get_value ();
		}
		return $result;
	}
	public function get_default_values() {
		$result = array ();
		foreach ( $this as $col ) {
			if (! is_null ( $col->get_default_value () )) {
				$result [$col->get_column_name ()] = $col->get_default_value ();
			}
		}
		return $result;
	}
	//-------------------------------------------------------------------------------------
	protected $errors = array ();
	public function get_validation_messages() {
		return $this->errors;
	}
	public function validate() {
		$ok = true;
        
		foreach ( $this as $key => $col ) {
			//$col = acolumn::cast( $col );			
			$ok_col = $col->validate();
			if ( ! $ok_col ) $this->errors = array_merge( $this->errors, $col->get_validation_messages() );
            $ok = $ok && $ok_col;
		}
		return $ok;
	}
	public function validate_restrictions() {
		$ok = true;
		foreach ( $this as $key => $col ) {
			$col = acolumn::cast ( $col );
			$ok_col = true;
			if ( ! $col->is_value_allowed_by_restrictions( $col->get_value() ) ) {
			     $ok_col = false;		
			}
			$ok = $ok && $ok_col;
		}
		return $ok;
	}
	
	public function reset_validation() {
		foreach ( $this as $key => $col ) {
			$col = acolumn::cast ( $col );
			$ok_col = $col->reset_validation ();
		}
	}
	//------------------------------------------------------------------------------------
	/**
	 * @param $index
	 * @return icolumn
	 */
	public function get($index) {
		if ($this->offsetExists($index)) {
			return $this->offsetGet($index);
		} else {
			throw new ExceptionDeveloper('No encontrado índice: '.$index);
		}
		//return $this [$index];
	}
	
	/**
	 * Unsets registered column
	 * @param string $index
	 */
	public function offsetUnset($index) {
		parent::offsetUnset($index);
	}
	
	/**
	 * @return array
	 */
	public function get_columns_names() {
		$result = array ();
		$a = $this->getArrayCopy();
		foreach ( $a as $col ) {
			$result [] = $col->get_column_name ();
		}
		return $result;
	}
	// ArrayObject overrides, to allow type checking
	/**
	 * @param string $index
	 * @return icolumn
	 */
	public function get_keys() {
		return $this->get_columns_names();
	}
	function offsetGet($index) {
		$val = parent::offsetGet ( $index );
		if (! $val instanceof icolumn) {
			throw new ExceptionDeveloper ( "Tipo de objeto almacenado debe ser icolumn" );
		}
		return $val;
	}
	/**
	 * @param string $index
	 * @param icolumn $newval
	 */
	public function offsetSet($index, $newval) {
		if (! $newval instanceof icolumn) {
			throw new ExceptionDeveloper ( "Tipo de objeto almacenado debe ser icolumn" );
		}
		$newval = acolumn::cast($newval);
		if (is_null ( $index )) {
			$index = $newval->get_column_name ();
		} else {
			if ($index != $newval->get_column_name ()) {
				if ( $newval->get_column_name()=='' ) {
					$newval->set_column_name ( $index );
				} else {
					throw new ExceptionDeveloper ( "Can't add a column unless index matches column name" );
				}
				$newval->set_table($this->table);
			}
		}
		$newval->set_table($this->table);
		parent::offsetSet ( $index, $newval );
	}
	/**
	 * @param icolumn $value
	 */
	public function append($value) {
		if (! $value instanceof icolumn) {
			throw new ExceptionDeveloper ( "Expected icolumn" );
		}
		$this->offsetSet ( $value->get_column_name (), $value );
		//parent::append($value);
	}
	public function append_after($key_before, $value_after) {
		//$this->append($value_after);
		$c = new columns_collection($this->table);
		
		foreach ($this as $key => $value) {
			$c->append($value);
			if ( $key == $key_before ) {
				$c->append($value_after);
			}
		}
		return $c;
	}
	
//	//Property and methods for implementing Iterator
//
//	
//	/**
//	 * @return icolumn
//	 */
//	public function current() {
//		return parent::current();
//	}
//	/**
//	 * @return icolumn
//	 */
//	public function next() {
//		return parent::next();
//	}
//	/**
//	 * @return string
//	 */
//	public function key() {
//		return parent::key();
//	}
//	/**
//	 * @return bool
//	 */
//	public function valid() {
//		return parent::valid();
//	}
//	
//	/**
//	 * @return icolumn
//	 */
//	public function rewind() {
//		return parent::rewind();
//	}


}
?>