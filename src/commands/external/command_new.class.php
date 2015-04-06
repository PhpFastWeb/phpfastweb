<?php

class command_new extends acommand_external implements icommand_external {
	
	public $table = null;
	
	public $is_reedit = false;
    
    protected $access_key = 'n';
    
	public function get_execute_link() {
		$url = $this->get_execute_url();
        if ( ! $this->table->inline_new_record ) {
            return $url->get_a_link( $this->get_name () );
        } else {
            $result = '<a href="'.$url->__toString().'" onclick="javascript: showhide(\'inline_new_record_\'); document.location=\'#inline_new_record_anchor_\'; return false;">'.
                $this->get_name()."</a>";
            return $result;
        }
	}        
    
	public function execute() {
	   
        //We check if you can only do one insert per computer
        if ( $this->table->only_one_insert_check() ) {
            return;
        }
        
		//If we didn't came here from a validation error, we initializate the form with new default data
		if ( ! $this->is_reedit ) {
			$this->table->columns_col->set_columns_new_row();				
		} 
		
		if ( count($this->table->control_group->get_subcontrols()) == 0 ) {
			$this->table->control_group->add($this->table->columns_col->get_keys());	
		} 
		
		if ( ! ($this->table->control_group instanceof control_edit) ) {
			if ( ! ($this->table->control_group instanceof control_group) ) {
				throw new ExceptionDeveloper('Expected control_group');
			}
			//We create a edit control
			$control_edit = new control_edit();
			$control_edit->set_table($this->table);
			
			$control_edit->set_readonly($this->table->control_group->is_readonly());
			$control_edit->add($this->table->control_group);			
			$control_edit->set_command_target('insert');
			if ($this->table->record_title != '') {
				$control_edit->set_title('Nuevo: '.strtolower($this->table->record_title));
			} else if ($this->table->table_title != '') {
				$control_edit->set_title($this->table->table_title.': Nuevo');
			} else {
				$control_edit->set_title('Nuevo');
			}
			
			//Asumimos que $this->control_group contiene una instancia de clase control_group
			$control_edit->set_readonly($this->table->control_group->is_readonly());
		
			echo $control_edit->get_control_render();
		} else {
		    $this->table->control_group->set_command_target('insert');

			if ($this->table->record_title != '') {
				$this->table->control_group->set_title('Nuevo: '.strtolower($this->table->record_title));
			} else if ($this->table->table_title != '') {
				$this->table->control_group->set_title($this->table->table_title.': Nuevo');
			} else {
				$this->table->control_group->set_title('Nuevo');
			}
                        
			echo $this->table->control_group->get_control_render();
		}

	}

	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return command_new
	 */
	public static function &cast(icommand $cmd) {
		if (! ($cmd instanceof command_new)) {
			throw new ExceptionDeveloper ( "Clase incorrecta" );
		}
		return $cmd;
	}
	
	public $name = "Nuevo";
	
	public function get_key() {
		return "new";
	}


}

?>