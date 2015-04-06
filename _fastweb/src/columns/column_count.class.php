<?php 
class column_count extends column_number implements icolumn {
	
	public function get_input_plain() {
        return '';
	}
    public function is_readonly() {
        return true;
    }
    protected $alias = '';
    public function &set_alias($alias) {
        $this->alias = $alias;
        return $this;
    }
    public function get_alias($alias) {
        if ($this->alias != '') {
            return $this->alias;
        } else {
            return $this->column_name;
        }
    }
    //--------------------------------------------------------------------
	public function get_db_type() {
		return '';
	}
	public function get_db_columns_str() {
        $result = new sql_str("COUNT({#0}.{#1}) AS {#2}",
                $this->get_table_name(),
                $this->get_original_column_name(),
				$this->get_column_name());		
		return $result->__toString();
	}
    /*
	public function get_db_where_str() {
		$v = $this->get_db_values_str();
		if ($v=='NULL') {
			return 'ISNULL('.$this->get_db_columns_str().')';
		}
		$result = $this->get_db_columns_str() . ' = '.$v;
		return $result;
	}*/
	
}

?>