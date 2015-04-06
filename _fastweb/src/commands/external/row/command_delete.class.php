<?php

class command_delete extends acommand_row implements icommand_row {
	
	protected $row;
    
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return command_delete
     */
	public static function &cast(icommand $cmd) {
		if ( ! ($cmd instanceof command_delete) ) {
			throw new ExceptionDeveloper("Clase incorrecta");
		}
		return $cmd;
	}
 	public function execute() {
		echo $this->get_execute_delete ();
	}
    public $name = 'Borrar';
	public function get_name() {
		return $this->name;
	}
    public function set_name($name) {
        $this->name = $name;
        return $this;
    }
	public function get_key() {
		return "delete";
	}    
    
    public $show_record_before_delete = true;
    
    /**
     * Sets the use of soft delete, allways, auto or never.
     * Use consts use_soft_delete_allways/auto/never
     * @param $use_soft_delete integer
     * @return command_delete reference to self
     */
    public function &set_soft_delete($use_soft_delete) {
        $this->use_soft_delete = $use_soft_delete;
        return $this;
    }
    protected $use_soft_delete = 0;
    const use_soft_delete_allways = 2;
    const use_soft_delete_auto = 1;
    const use_soft_delete_never = 0;
    
    /**
     * Sets column name that enables and thisables row
     * @param $column_name column that enables row
     * @return command_delete reference to self
     */ 
    public function &set_soft_delete_column_name($column_name) {
        $this->soft_delete_column_name = $column_name;
        return $this;
    }
    public $soft_delete_column_name = 'enabled';
    

    protected function get_execute_delete() {
        $result = '';
		if (isset ( $_POST ['command_param_delete_confirmed_'] ) && $_POST ['command_param_delete_confirmed_'] = 1) {
			$this->table->key_set->set_keys_values( $_POST );
			$this->row = $this->table->fetch_row( $this->table->key_set );
            $this->table->columns_col->set_values_array ( $this->row );
			$result = $this->get_execute_delete_confirmed();
		} else if (isset ( $_POST ['command_param_soft_delete_confirmed_'] ) && $_POST ['command_param_soft_delete_confirmed_'] = 1) {
			$this->table->key_set->set_keys_values( $_POST );
			$this->row = $this->table->fetch_row( $this->table->key_set );
            $this->table->columns_col->set_values_array ( $this->row );            
			$result = $this->get_execute_soft_delete_confirmed();            
        } else {
			$this->table->key_set->set_keys_values( $_GET );
			$this->row = $this->table->fetch_row( $this->table->key_set );
            $this->table->columns_col->set_values_array ( $this->row );            
			$result = $this->get_execute_delete_unconfirmed();
		}
        
        return $result;
	}
    protected function build_sql_delete_string() {
		$sql = "DELETE FROM ". website::$database->escape_table_name($this->table->table_name) 
            ." WHERE ".$this->key_set->get_sql_where($this->table->columns_col);
        return $sql;
    }
    
    protected function get_execute_soft_delete_confirmed() {
        if ( count( $this->get_table()->relations ) > 0 ) throw new ExceptionDeveloper("Soft delete no implementado para tablas enlazadas");        
        assert::is_in_array($this->row,$this->soft_delete_column_name);
        if ( $this->use_soft_delete == self::use_soft_delete_never ) throw new ExceptionDeveloper("Se intentó un soft delete sin que estuviera establecido su uso");

        $this->row[$this->soft_delete_column_name] = 0;
        
        try {
			$ok = website::$database->update3(
                $this->table->table_name, 
                $this->table->columns_col->get_columns_names(),
				$this->row, $this->table->key_set);
			//TODO: Permitir más de una primary key
		} catch(Exception $e) { $ok = false; }
        
        $result = "<table align=\"center\"><tr><td>\r\n";
		if ( $ok ) {         
			$result .= "<img src=\"" . website::$theme->get_img_dir () . "/icon_ok.gif\" align=\"top\" border=\"0\" alt=\"\" /> ";
			$result .= "<b>Registro deshabilitado</b><br /><br />\r\n";
		} else {
			$result .= "<img src=\"" . website::$theme->get_img_dir () . "/icon_error.gif\" align=\"top\" border=\"0\" alt=\"\" /> ";
			$result .= "<b>Ocurrio un problema al intentar deshabilitar el registro</b><br /><br />\r\n";
			$result .= website::$database->get_last_error()."<br /><br />\r\n";            
		}
        
		$result .= "<form action=\"".html_template::get_php_self()."\"  method=\"get\" style=\"margin:0 0; text-align:center;\"><input type=\"submit\" value=\"Continuar\" onclick=\"this.disabled=true; document.location=this.form.action;\"/></form>";		
		$result .= "</td></tr></table>\r\n";
		$result .= "<br />";    
        
        return $result;
    }
    
	protected function get_execute_delete_confirmed() {
        $result = '';
        
        //Comprobamos si hay relaciones
        $chained = false;
        if ( count( $this->get_table()->relations ) > 0 ) {
            foreach( $this->get_table()->relations as $rel ) {
                if ($rel->get_table_name_b() == $this->get_table()->get_table_name() ) {
                    $chained = true;
                    if ( $this->get_table()->columns_col->get( $rel->get_id_b() )->get_value() != '' ) {
                        //It hasn't been deleted in between confirmation
                        $sql = new sql_str("DELETE FROM {#0} WHERE {#1}='{2}' AND {#3}='{4}'",
                            $rel->get_table_name_rel(),
                            $rel->get_id_rel_a(),
                            $this->get_table()->columns_col->get( $rel->get_id_rel_a() )->get_value(),
                            $rel->get_id_rel_b(),
                            $this->get_table()->columns_col->get( $rel->get_id_b() )->get_value()
                        );
                    }
                    break;
                }
            }
        }
		
        if ( ! $chained ) {
			$sql = $this->build_sql_delete_string();
		}
        
		//Ejecutar SQL de borrado
		$ok = true;
		try {
			$ok = website::$database->execute_nonquery ( $sql );
		} catch(Exception $e) {
			$ok = false;
		}
		
		//Completamos resultado
		$result = "<table align=\"center\"><tr><td>\r\n";
		if ( $ok ) {
		  
  		    //Intentamos borrar los ficheros
            foreach ( $this->table->columns_col as $key => $col ) {
                if ($col instanceof column_file || $col instanceof column_image) {
				    if ($col->get_value()!='') {
					   $cmd_rmv_file = new command_remove_file ();
					   $result .= $cmd_rmv_file->get_execute_remove_file($this->table->key_set, $col, false );
				    }
			    }
            }
          
			$result .= "<img src=\"" . website::$theme->get_img_dir () . "/icon_ok.gif\" align=\"top\" border=\"0\" alt=\"\" style=\"width:16px; height:16px;\" /> ";
			$result .= "<b>Registro borrado correctamente</b><br /><br />\r\n";
		} else {
            if ( website::$database->get_last_error_num() == 1451 ) {
                //Unable to delete a record with a foreign key (cascade delete not enabled)
                if ( $this->use_soft_delete == self::use_soft_delete_auto ) { 
                    //Offer soft delete
                    $result .= $this->get_soft_delete_unconfirmed_form();
                    $result .= "</td></tr></table>\r\n";
    		        $result .= "<br />";                
                    return $result;
                } else {
                    //Show generic or customized message
                    $msg = website::$database->get_last_error();
                    if ( $this->table->error_unable_delete_cascade_msg != '' ) {
                        $msg = $this->table->error_unable_delete_cascade_msg;
                    }
                    $header = "No se puede borrar el registro";
                    if ( $this->table->error_unable_delete_cascade_heading != '' ) {
                        $header = $this->table->error_unable_delete_cascade_heading;
                    } 
                    
                 $result .= "<img src=\"" . website::$theme->get_img_dir () . "/icon_error.gif\" align=\"top\" border=\"0\" alt=\"\" /> ";
			     $result .= "<b>$header</b><br /><br />\r\n";
			     $result .= $msg."<br /><br />\r\n";
                }
            } else {
			     $result .= "<img src=\"" . website::$theme->get_img_dir () . "/icon_error.gif\" align=\"top\" border=\"0\" alt=\"\" /> ";
			     $result .= "<b>Ocurrio un problema al intentar borrar el registro</b><br /><br />\r\n";
			     $result .= website::$database->get_last_error()."<br /><br />\r\n";
            }
            
		}

		$result .= "<form action=\"".html_template::get_php_self()."\"  method=\"get\" style=\"margin:0 0; text-align:center;\"><input type=\"submit\" value=\"Continuar\" onclick=\"this.disabled=true; document.location=this.form.action;\"/></form>";		
		$result .= "</td></tr></table>\r\n";
		$result .= "<br />";

        
        return $result;
	}
	function get_execute_delete_unconfirmed() {
		$result = "<table align=\"center\"><tr><td>\r\n";
		//echo "<b>Borrar ";
		//echo "</b><br /><br />\r\n";
        
        if ($this->show_record_before_delete ) {
		  $cmd_print = new command_print ();
		  $cmd_print->set_table($this->table);
		  $result .= $cmd_print->get_row_table ( $this->row, 'Borrar' );
        }
		$result .= "<br/>";
		//echo "<form action=\"".html_template::get_php_self()."\" method=\"post\" enctype=\"multipart/form-data\" onsubmit=\"javascript:block_form_for_send(this); return true;\">\r\n";
		$result .= "<form action=\"".html_template::get_php_self()."\" method=\"post\" enctype=\"multipart/form-data\" >\r\n";
		$result .= "<input type=\"hidden\" name=\"" . self::get_command_label () . "\" value=\"delete\" />";
		$result .= "<input type=\"hidden\" name=\"command_param_delete_confirmed_\" value=\"1\" />";
		$result .= $this->table->key_set->get_input_hiddens_from_array ( $this->row );
		//echo "<input type=\"hidden\" name=\"$this->primary_key\" value=\"$id\" />";
		$result .= "<br /><center>¿Est&aacute; seguro de que desea borrar este registro?<br /><br />\r\n";
		$result .= "<input type=\"submit\" name=\"enviar\" value=\"  Si  \" onclick=\"this.disabled=true;this.form.submit();\" />";
		$result .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		$result .= "<input type=\"button\" name=\"back\" value=\"  No  \" onclick=\"javascript:history.go(-1);return false;\" />";
		
		$result .= "</center><br />\r\n";
		$result .= "</form>\r\n";
		
		$result .= "</td></tr></table>\r\n";
		$result .= "<br />";
		$result .= "<br />";
        return $result;
	}
    function get_soft_delete_unconfirmed_form() {
        $result = '';
		$cmd_print = new command_print ();
		$cmd_print->set_table($this->table);
		$result .= $cmd_print->get_row_table( $this->row, 'Borrar' );

		$result .= "<br /><br />";
                
        $result .= "<center><b>El registro tiene otros datos asociados y no puede ser borrado</b><br /><br />";
        $result .= "¿Quiere en su lugar deshabilitarlo para que no sea visible?<br /><br /></center>";
                        
		$result .= "<form action=\"".html_template::get_php_self()."\" method=\"post\" enctype=\"multipart/form-data\" >\r\n";
        $result .= '<center>';
		$result .= "<input type=\"hidden\" name=\"" . self::get_command_label () . "\" value=\"delete\" />";
		$result .= "<input type=\"hidden\" name=\"command_param_soft_delete_confirmed_\" value=\"1\" />";
		$result .= $this->table->key_set->get_input_hiddens_from_array ( $this->row );
		$result .= "<input type=\"submit\" name=\"enviar\" value=\"  Si  \" />";
		$result .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		$result .= "<input type=\"button\" name=\"back\" value=\"  No  \" onclick=\"javascript:history.go(-1);return false;\" />";		
		$result .= "<center></form>\r\n";
        return $result;        
    }
}

?>