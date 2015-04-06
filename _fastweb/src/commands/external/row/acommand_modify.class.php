<?php
abstract class acommand_modify extends acommand_row implements icommand_row {
	

	public function execute() {
		//recuperamos la nueva información

		$this->init_values();
		
		//Validamos que no se vulneren las restricciones
		if ( ! $this->table->columns_col->validate_restrictions() ) {
			echo "**ERROR: No puede editar un registro de ese conjunto";
			return;
		}
		
		$this->check_restrict_modify();
		
		$this->table->columns_col->validate();
		
		if ( count($this->table->control_group->get_subcontrols()) == 0 ) {
			$this->table->control_group->add($this->table->columns_col->get_keys());	
		}
		
		if (! ($this->table->control_group instanceof control_edit) ) {
			if ( ! ($this->table->control_group instanceof control_group) ) {
				throw new ExceptionDeveloper('Expected control_group');
			}			
			//Creamos un control de edición
			$control_edit = new control_edit();
			$control_edit->set_table($this->table);
			$control_edit->add($this->table->control_group);
			$control_edit->set_command_target($this->get_command_target_name());
			$control_edit->set_title($this->table->get_record_title());
            $control_edit->set_readonly($this->table->control_group->is_readonly());
			if ( ! $this->can_modify) {
                $control_edit->set_readonly();
            }            
            
			

			echo $control_edit->get_control_render();
		} else {
            $this->table->control_group->set_command_target($this->get_command_target_name());

            if ( ! $this->can_modify) {
                $this->table->control_group->set_readonly();
            }
			echo $this->table->control_group->get_control_render();
		}
		
	}
	
	protected abstract function init_values();
	
	protected abstract function get_command_target_name();
	
    //--------------
    
	protected $restrict_modify = false;
    protected $restrict_modify_column_value = array();
    protected $can_modify = true;
    
	public function set_restrict_modify($restrict_modify=true) {
		$this->restrict_modify = $restrict_modify;
	}
	public function set_restrict_modify_field_value($column_value_array) {
	   if (! is_array($column_value_array)) throw new ExceptionDeveloper('$field_value_array not array');
       $this->restrict_modify_column_value = $column_value_array;
	}
	protected function check_restrict_modify() {
		if (!$this->restrict_modify) return;
        $this->can_modify = true;
        foreach($this->restrict_modify_column_value as $field => $value) {
            if ( $this->table->columns_col->get($field)->get_value() != $value) {
                $this->can_modify = false;
                break;
            }
        }

        if ( ! $this->can_modify) {
			$this->table->columns_col->set_readonly();
		}
        
	}
	
	
	
}