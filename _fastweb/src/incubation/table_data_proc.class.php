<?php

class table_data_proc extends table_data_row {

	function configure_actions_procedures() {
		//Esta función configura las acciones por defecto
		
		//INSERT
		$proc_name = $this->get_default_proc_name($this->table_name,"_I");
		$params = array();
		foreach ($this->columns as $col_name) {
			$params['P'.$col_name] = $col_name;
		}
		$this->actions_procedures['INSERT'] = array($proc_name => $params );
		
		//DELETE
		$proc_name = $this->get_default_proc_name($this->table_name,"_D");
		$primary_keys = $this->key_set->primary_keys;
		$params = array();
		foreach ($primary_keys as $col_name) {
			$params['P'.$col_name] = $col_name;
		}
		$this->actions_procedures['DELETE'] = array($proc_name => $params );
				
		//UPDATE
		$proc_name = $this->get_default_proc_name($this->table_name,"_U");
		$params = array();
		foreach ($this->columns as $col_name) {
			$params['P'.$col_name] = $col_name;
		}
		$this->actions_procedures['UPDATE'] = array($proc_name => $params );		
		
		
	}

	function get_default_proc_name($table_name,$sufix) {
		$proc_name = $table_name;
		$max_long = 30;
		if ( ( strlen($proc_name) + strlen($sufix) ) > $max_long ) {
			$proc_name = substr($proc_name,0,$max_long - strlen($sufix) );
		}
		$proc_name .= $sufix;
		return $proc_name;
	}
	
	function action_procedures_execute($action,$row) {
		$proc_name = ''; $params = array();
		$procedure = $this->actions_procedures[$action];
		foreach ($procedure as $key => $value) {
			$proc_name=$key;
			$params = $value;
		}
		foreach ($params as $proc_key => $col_name ) {
			$params[$proc_key]=$row[$col_name];
		}
		//añadimos parámetro de resultado:
		//$params['RESULT']='';
		return website::$database->db_procedure('',$proc_name,$params);		
	}
	
	//----------------------------------------------------------------------------------------


}


?>
