<?php

class command_remove_file extends acommand_internal implements icommand_internal {
	protected $row;
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return command_remove_file
	 */
	public static function &cast(icommand $cmd) {
		if ( ! ($cmd instanceof command_remove_file) ) {
			throw new ExceptionDeveloper("Clase incorrecta");
		}
		return $cmd;
	}
	public function get_name() {
		return "Borrar fichero";
	}
	
	public function get_key() {
		return 'remove_file';
	}
	public function execute() {
		if (!isset($_GET['column_filename'])) throw new Exception('column not defined');
		$this->table->key_set->set_keys_values ( $_GET );
		$this->row = $this->table->fetch_row ( $this->table->key_set );
        $this->table->columns_col->set_values_array ( $this->row );  
		$this->print_remove_file($this->table->key_set,$this->table->columns_col[$_GET['column_filename']]);
	}
    public function print_remove_file(key_set $key_set, column_file $col, $remove_from_row = true) {
        echo "<center>";
        echo $this->get_execute_remove_file($key_set, $col, $remove_from_row);        
		echo "<br />[ <a href=\"".html_template::get_php_self()."\">Continuar</a> ]";
		echo "</center>";
        
    }
	public function get_execute_remove_file(key_set $key_set, column_file $col, $remove_from_row=true) {
		
		$dir = $col->get_dir_upload();
        $filename = $col->get_value();
		$result = '';
		
		if ( ! $col->get_upload_dir_size_limit() ) {
			$result .= ( "** Error: No tiene permiso para eliminar o sobreescribir el fichero." );
            die ($result);
            //return $result;
        }
			
		//Intentamos borrar el fichero
		if ($col instanceof column_image) {
			$img_sust = new image ( $filename, $dir );
			if ( ! $img_sust->delete() ) {
				$result .= "<img src=\"" . website::$theme->get_img_dir () . "/icon_error.gif\" align=\"top\" border=\"0\" alt=\"".$dir.$filename."\" title=\"".$dir.$filename."\" /> ";
				$result .= "<b>***Error: No se pudo borrar el fichero existente previamente: " .  $filename . "<br />\r\n";
				$result .= "Por favor, informe al administrador del sitio que dicho fichero debe ser eliminado de forma manual.</b><br /><br />\r\n";
				$remove_from_row = false;
			}
		} else {
			if ( ! file_exists ( $dir . $filename ) ) {
				$result .= "<img src=\"" . website::$theme->get_img_dir () . "/icon_warning.gif\" align=\"top\" border=\"0\" alt=\"".$dir.$filename."\" title=\"".$dir.$filename."\" /> ";
				$result .= "Advertencia: No existía el fichero en disco: " .  $filename . "<br /><br />";
				//$remove_from_row = true;
			} elseif ( ! @unlink ( $dir . $filename )) {
				$result .= "<img src=\"" . website::$theme->get_img_dir () . "/icon_warning.gif\" align=\"top\" border=\"0\" alt=\"".$dir.$filename."\" title=\"".$dir.$filename."\" /> ";
				$result .= "Advertencia: No se pudo eliminar de disco el fichero: " .   $filename . "<br /><br />";
				$remove_from_row = false;
			} else {
				//echo "<img src=\"" . website::$theme->get_img_dir () . "/icon_ok.gif\" align=\"top\" border=\"0\" alt=\"\" /> ";
				//echo "Fichero <b>$filename</b> eliminado de disco.<br />";
			}
		}
		
		//Comprobamos si hace falta actualizar la base de datos.
		if  ( $remove_from_row ) {
            assert::is_array($this->row);
            assert::is_same_value_and_type( $this->row[$col->get_column_name()],$col->get_value() );
			//Eliminamos la entrada en la tupla
			$this->row[$col->get_column_name()] = '';
			//Realizamos la modificación de los datos en la BD
			
			$this->table->key_set->set_keys_values($this->row);
            
            try {
    			$ok = website::$database->update3(
                    $this->table->table_name, 
                    $this->table->columns_col->get_columns_names(),
    				$this->row, $this->table->key_set);
    			//TODO: Permitir más de una primary key
			} catch(Exception $e) { $ok = false; }
            
			if ($ok) {
				$result .= "<img src=\"".website::$theme->get_img_dir()."/icon_ok.gif\" align=\"top\" border=\"0\" alt=\"\" /> ";
				$result .= "Fichero <b>".$col->get_value()."</b> eliminado de la base de datos.<br />";
			} else {
				$result .= "<img src=\"".website::$theme->get_img_dir()."/icon_error.gif\" align=\"top\" border=\"0\" alt=\"\" /> ";
				$result .= "Error, no se pudo eliminar el fichero <b>".$col->get_value()."</b> de la base de datos.<br />";
			}
			
		}
		
        return $result;
		//if ( $remove_from_row && $ok ) return true;
	}
}

?>