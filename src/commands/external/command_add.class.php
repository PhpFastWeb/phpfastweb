<?php
class command_add extends acommand_external implements icommand {
	protected $row_text_function = 'command_popselect::static_get_row_text';
	public function set_row_text_function($function_name) {
		$this->row_text_function = $function_name;
	}

	protected $column_name = '';
	public function set_column_name($column_name) {
		$this->column_name = $column_name;
	}
	public function get_column_name() {
		return $this->column_name;
	} 
    
    public function __construct($column_name="") {
        $this->set_column_name($column_name);
    }
	public function get_execute_link() {
		assert::is_not_empty($this->column_name);
		$col = $this->table->columns_col->get($this->column_name);
		$col = column_linked::cast($col);
		$cn = $col->get_column_name();
		$onc = $col->get_execute_onclick();
		
		$result = '<form method="post" target="_self" action="'.html_template::get_php_self().'" style="margin:0 0;display:none;" name="form_add" id="form_add">';
		$result .= '<script type="text/javascript">checkChanges=false;</script>'."\r\n";
		$result .= '<input type="hidden" name="'.acommand::get_command_label().'" id="'.acommand::get_command_label().'" value="insert" />';
		$result .= '<input type="hidden" name="'.$cn.'" id="'.$cn.'" value="" onchange="javascript:alert(\'envio\');" />';
		$result .= '<input type="hidden" name="'.$cn.'_prev_" id="'.$cn.'_prev_" value="" />';
		$result .= '<input type="hidden" name="'.$cn.'_text_" id="'.$cn.'_text_" />';
		foreach ($this->table->columns_restriction as $col_r => $value) {
			$val =  $value;
			$result .= '<input type="hidden" name="'.$col_r.'" id="'.$col_r.'" value="'.$val.'" />';
			$result .= '<input type="hidden" name="'.$col_r.'_prev_" id="'.$col_r.'_prev_" value="'.$val.'" />';
		}
		// type="hidden"     display:none;
		//$result .= '<input type="submit" />';
		$result .= '</form>';
		$result .= "<a href=\"#\" onclick=\"{$onc}\" >". $this->get_name () ."</a>";
		return $result;
	}


	public $table = null;
	
	public function execute() {
//		//Inicializamos la nueva información
//		$this->table->columns_col->set_columns_new_row();				
//		
//		if ( count($this->table->control_group->get_subcontrols()) == 0 ) {
//			$this->table->control_group->add($this->table->columns);	
//		} 
//		
//		//Creamos un control de edición
//		$control_edit = new control_edit();
//		$control_edit->set_table($this->table);
//		$control_edit->add($this->table->control_group);
//		$control_edit->set_command_target('insert');
//		$control_edit->set_title($this->table->table_title.': Nuevo');
//		
//		echo $control_edit->get_control_render();
	}

	
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return command_add
	 */
	public static function &cast(icommand $cmd) {
		if (! ($cmd instanceof command_new)) {
			throw new ExceptionDeveloper ( "Clase incorrecta" );
		}
		return $cmd;
	}
	
	protected $name = "Añadir elemento";
	
	public function get_key() {
		return "add";
	}


}

?>