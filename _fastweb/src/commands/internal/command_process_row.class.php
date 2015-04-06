<?php
class command_process_row extends acommand_internal implements icommand_internal {
	const type_insert = 1;
	const type_update = 2;
	const type_insert_or_update = 3;
	protected $type = 0;
	
	
	public function set_type($type) {
		$this->type = $type;
	}
	public function get_key() {
		return 'process_row';
	}
    
    /**
     * Tries to insert or update record and attached files. Returns false on error
     * @return false on error
     */
	public function execute() {	
	   
		if ($this->type == 0 ) throw new ExceptionDeveloper("Record process type not well defined");
		return $this->print_process_row();
	}	
	
	protected $errors = array();
	
	protected $col_exists = false;

	protected $get_previous_row = false;
	protected $previous_row = array();

	
    /**
     * Process row and sends result to browsers. Returns false on error
     * @return bool false if errors
     */
	function print_process_row() {
	   
		$ok = $this->validate();

		if ( ! $ok ) {
            $this->print_error();
            $this->reedit();
            return false;		  
        }
		$ok = $this->print_insert_db();
		if ( ! $ok ) {
            $this->print_error();
            $this->reedit();
            return false;		  
        }
		$ok = $this->insert_files();
		if ( ! $ok ) {
            $this->print_error();
            $this->reedit();
            return false;		  
        }    
        
        $this->print_post_insert();
		return true;
	}
	protected function print_error() {
			echo "<center><div class=\"record_invalid\">";
			echo "<b>Error al guardar la información</b><br /><br />\r\n";
			foreach ( $this->errors as $error )	{
				echo "<img src=\"" . website::$theme->get_img_dir () . "/icon_error.gif\" align=\"top\" border=\"0\" alt=\"\" /> ";
				echo $error . '<br />';
			}
			echo "<br />Los datos <b><u>no han sido guardados</u></b>, corrija los errores mencionados y vuelva a intentarlo de nuevo, por favor.";
			echo "</div></center><br />";
			return;
	}
	protected function reedit() {
		switch($this->type) {
			case command_process_row::type_insert:
				$cmd = new command_new();
				$cmd->set_table($this->table);
				$cmd->is_reedit = true;
				$cmd->execute();
				break;
			case command_process_row::type_insert_or_update:
				if($this->col_exists) {
                    $cmd = new command_set();
                    $cmd->is_reedit = true;
					$cmd->set_table($this->table);
					$cmd->execute();				
				} else {
					$cmd = new command_set();
					$cmd->set_table($this->table);
					$cmd->execute();
				}
				break;
			case command_process_row::type_update:
				if ($this->table->control_group instanceof control_edit) {
					$control_edit = $this->table->control_group;
					
    			} else {
					$control_edit = new control_edit();
					$control_edit->set_table($this->table);
					if ( count($this->table->control_group->get_subcontrols()) == 0 ) {
						$this->table->control_group->add($this->table->columns);	
					}
					$control_edit->add($this->table->control_group);
				}
				$control_edit->set_command_target('update');
				$control_edit->set_title('Edición');
				//echo $control_edit->command_target;
				echo $control_edit->get_control_render();
				break;
		}

	}
	
	protected $post_insert_function = null;
	public function set_post_insert_function($function) {
		$this->post_insert_function = $function;
	}
	protected function print_post_insert() {
		// We execute possible post insert function
		// (closures require PHP 5.3)
	
	 	
		if ($this->post_insert_function != null) {
			$f = $this->post_insert_function;
			$f($this);
		}
		
        $this->table->after_insert_or_update();
        
		// We establish target
		if ( $this->table->send_email_after_save )
			$this->send_email( $this->table->table_data );
		
		if ($this->table->save_continue_url != '') {
			$target = $this->table->save_continue_url;
		} else {
			$target = html_template::get_php_self();
		}
		if ( $this->table->save_continue_append_id ) {
			$id = $this->table->key_set->keys_values;
			foreach ($this->table->key_set->primary_keys as $k) {
				if (isset($id[$k])) {
					$target .= '/'.$id[$k];
				} else {
					throw new ExceptionDeveloper('Not all primary keys values defined');
				}
			}
			$target .= '/';
			//@deprecated: Remove the following for new version
			eval ("\$target = \"$target\";"); //TODO: Assest posible hack threath	
		}
		
		//We handle post insert message		
		if ( ! $this->table->save_continue_immediately ) {
			echo "<div style=\"text-align:center;\"><div style=\"font-weight: bold; margin:0 auto; padding: 10px 20px;text-align:center; margin: 10px 10px; display: inline-block; background-color:#DEDEDE; border:3px solid green;\" id=\"ok_message\">\r\n";
			echo "<img src=\"" . website::$theme->get_img_dir () . "/icon_ok.gif\" align=\"top\" border=\"0\" alt=\"\" /> ";
			echo "{$this->table->save_message}\r\n";
			echo "</div></div>\r\n";
			if ($this->table->show_row_after_save) {
				$this->print_row_table ( $this->data );
			}
			echo "<br />\r\n";
			echo "<br />\r\n";
			echo "<form name=\"form_continue\" id=\"form_continue\" action=\"$target\" style=\"margin:0 0; text-align:center;\">";
			echo "<input type=\"submit\" value=\"Continuar\" onclick=\"this.disabled=true;this.form.submit();return false;\"/></form>\r\n";
            echo "<script type=\"text/javascript\">document.form_continue.elements[0].focus();</script>\r\n";						
		} else {
			message_float::add_message($this->table->save_message);
			html_template::redirect( $target );
		}
	}

	protected function check_need_get_previous_row() {
		foreach ( $this->table->columns_col as $col_name => $col ) {
			if ($col instanceof column_file) {
				if ( isset($_FILES) && isset($_FILES[$col_name]) && isset($_FILES[$col_name]['name']) && !empty($_FILES[$col_name]['name'])) {
					$this->get_previous_row = true;
				}
			} 
		}	
		return $this->get_previous_row;		
	}
    
	protected function validate_pk_insert() {
		//Queremos insertar una nueva fila que no exista
		if ( ! $this->table->edit_pk) {
			//La clave es automática o se especifica con restricciones
			if ( $this->table->key_set->validate() ) {
				$ok = true;
				foreach ($this->table->key_set->primary_keys as $pk) {
					if ( ! $this->table->columns_col->get($pk)->is_restricted() ) {
						$ok = false;
					}
				} 
				if ($ok == false) {
				//La clave es automática pero se ha especificado					
					$this->errors[] = "Se intentó especificar clave primaria para nuevo registro automático";
				}
				return $ok;
			}
		} else {
			//La clave es manual	
			if ( ! $this->table->key_set->validate() ) {
				$this->errors[] = "No se especificaron todos los campos de clave primaria necesarios";
				return false;
			} 
			//Comprobamos si existe ya el registro
			$key_set2 = clone($this->table->key_set);
			$key_set2->set_keys_values_col($this->table->columns_col);
			if (website::$database->exist_row($this->table->table_name,$key_set2)) {
				$this->errors[] = "Ya existe un registro con ese identificador";
				return false;
			} 
		}
		return true; //En caso de $ok=false esto no se usará más adelante
	}
	protected function validate_pk_update() {
		global $_POST;
		$ok = true;
		if ( ! $this->table->edit_pk ) {

		} else {
			//Clave primaria editable, tomamos la previa para el key_set de tabla
		
			//Si las claves son diferentes, comprobamos que la nueva no exista el nuevo conjunto de claves
			$ksp = $this->table->key_set->get_clean_array_values($_POST,'_prev_');
			$ksa = $this->table->key_set->get_clean_array_values($_POST);
			//TODO: Tener en cuenta restricciones, o prohibir cambios
			$dif = array_diff_assoc($ksp,$ksa);
			if (count($dif)>0) {
				//Las claves primarias son diferentes, y es necesario comprobar si existe el destino la nueva
				$key_set_nueva = clone($this->table->key_set);
				$key_set_nueva->set_keys_values($ksa);
				if (website::$database->exist_row($this->table->table_name,$key_set_nueva)) {
					foreach($this->table->key_set->primary_keys as $col_name) {
						$this->table->columns_col->get($col_name)->invalidate();
						$this->table->columns_col->get($col_name)->add_validation_message('Ya existe otro registro con el identificador especificado');
					}
					$ok = false;
				}				
			}
		}
		return $ok; 
	}

	protected function validate_pk_insert_or_update() {

		//Hay que determinar si se trata de inserción o actualización
		if ( ! $this->table->key_set->validate() ) {
			//No contiene clave, solo puede ser inserción
			$this->col_exists = false;
			return $this->validate_pk_insert();
		}
		
		//Contiene clave
		
		//Comprobamos si existe
		if (website::$database->exist_row($this->table->table_name,$this->table->key_set)) {
			$this->col_exists = true;
			return $this->validate_pk_update();	
		} else {
			$this->col_exists = false;
			return $this->validate_pk_insert();
		}
		//TODO: Validar si se trata de una edición de clave primaria

	}

	//----------------------------------------------------------------------------------------------------
	protected function validate() {
		$ok = true;	
		
		//We set values to the columns
		$this->table->columns_col->set_formatted_values_array($_POST);
        
		//If primary key is editable, we set its new value
		if ( ! $this->table->edit_pk ) {
			$this->table->key_set->set_key_columns($this->table->columns_col);
			//This assures us that restrictions are met
		} else {
			//When primary key editable, maybe previous primary key is set
			if ( $this->table->key_set->is_in_array($_POST,'_prev_') ) {
				$this->table->key_set->set_keys_values($_POST,'_prev_');
			} else {
				//Else, previous validation of key_set will check it's well defined
				$this->table->key_set->set_key_columns($this->table->columns_col);				
			}
		}
		
		//We validate all rules
		switch($this->type) {
			case command_process_row::type_insert:
				$ok = $this->validate_pk_insert();
			break;
			case command_process_row::type_update:
				$ok = $this->validate_pk_update();
			break;
			case command_process_row::type_insert_or_update:
				$ok = $this->validate_pk_insert_or_update();
			break;
			default:
				throw new ExceptionDeveloper("Tipo de comando de procesamiento no definido");
			break;
		}
		
		//Ejecutamos los validadores de todas las columnas
		$ok_col = $this->table->columns_col->validate();
		$ok = $ok && $ok_col;
		$this->errors = array_merge($this->errors,$this->table->columns_col->get_validation_messages());
		
		//TODO: Detectar intentos de modificar campos restringidos en lugar de forzar 
		
		return $ok;
	}
	/**
	 * Inserta en BD los datos especificados.
	 * Si no se pasa parámetro, se toman los datos de _POST según especificaciones de table
	 * @param $data Datos a insertar en BD
	 */
	function print_insert_db() {
		//
        $this->table->after_init_values();
        
		//Realizamos la inserción o actualización de los datos
		//La siguiente comprobación también valida restricciones
		$result = false;
		
		//$e = new ExceptionDatabaseQuery();
		try {
			switch($this->type) {
				case command_process_row::type_insert:
					$result = website::$database->insert_table_data( $this->table );                   
				break;
				case command_process_row::type_update:
					if ($this->check_need_get_previous_row()) {
						$this->previous_row = $this->table->fetch_row ( $this->table->key_set );
					}
					$result = website::$database->update_table_data( $this->table );
				break;
				case command_process_row::type_insert_or_update:
					if ($this->col_exists) {
						if ($this->check_need_get_previous_row()) {
							$this->previous_row = $this->table->fetch_row( $this->table->key_set );
						}			
						$result = website::$database->update_table_data( $this->table );
						//if ($result != 1 ) $result = false; //if same data is in update, mysql returns 0 rows affected
					} else {
						$result = website::$database->insert_table_data( $this->table );
					}
				break;
			}
		} catch(Exception $e) {
	       if ( ! $e instanceof ExceptionDatabase ) {
				throw $e;
            }
		}
		
		$ok = true;	
		if ($result === false) {
			//Ocurrió un error
			$ok = false;
			$error = "Error al procesar la información en la base de datos:<br />";
			$error .= "<br />".website::$database->get_last_error();
            
			if ( website::in_developer_mode() ) {
                $error .= ' <a href="#" onclick="showhide(\'phpfw_db_error\');">Show debug info</a>';
                $error .= "<div id=\"phpfw_db_error\" style=\"display:none;\"><br /><i>Error:</i><br />".website::$database->get_last_error_original();
                if ( isset($e) && $e instanceof ExceptionDatabaseQuery ) {                    
				    $error .= "<br /><br /><i>SQL:</i><br />".$e->get_formatted_sql_error()."<br />";
                } else {
                    $error .= "<br /><br /><i>SQL:</i><br />".website::$database->sql;
                }
                $error .= '</div>';
                
			}
			$this->errors[] = $error;
				
		} else if (is_array($result) && $this->table->key_set->is_in_array($result)) {
			//Se han generado claves automáticas 
			$this->table->key_set->set_keys_values($result);
		}
		return $ok;

	}
	protected function insert_files() {
		//Si algún campo es fichero, lo procesamos
		$data = $_POST;
		$has_files = false;
		$cols_files = array();
		$ok = true;
		foreach ( $this->table->columns_col as $col_name => $col ) {
			if ($col instanceof column_file) {
				if ( isset($_FILES) && isset($_FILES[$col_name]) && isset($_FILES[$col_name]['name']) && !empty($_FILES[$col_name]['name'])) {
					$result = $this->process_upload ( $col, $data );
					if ( $result !== false ) {
						$has_files = true;
						$cols_files[$col_name] = $result;
					} else {
						$ok = false;
						$this->errors [] = 'Error al procesar el fichero del campo: "' . $col->get_title().'"';
					}
				}
			}
		}
		//Si se han subido ficheros, actualizamos los campos correspondientes de la base de datos
		
		//Como se trata de un insert, en 
		if ($has_files) {
			//Actualizamos la fila con los datos de los ficheros
			$columns = array_keys($cols_files);
			website::$database->update3( $this->table->table_name, $columns, $cols_files, $this->table->key_set );
		}
		//Recuperamos los datos insertados
		//$row = $this->fetch_row($_POST[$this->primary_key]);
		
		return $ok;
	}
	

	
/**
 * Procesa la subida de un fichero para una columna.
 * Debe comprobarse antes de llamar a esta función que realmente hay algún fichero que procesar, o devolverá error.
 * Debe pasarse en el parámetro $row el valor actual de la tupla, para validar que no se sobreescriben archivos para ésta.
 * En this->table debe estar correctamente populado key_set
 * @param string $col_name
 * @param array $row
 * @return string Nombre final dado al archivo tras almacenarlo, o false si ocurrió un error.
 */
function process_upload($col,$row) {
		$col_name = $col->get_column_name();
		if ( !isset($_FILES) || !isset($_FILES[$col_name]) || $_FILES[$col_name]['name']=='' || $_FILES[$col_name]['tmp_name']=='') {
			
			$this->errors[] = "Fichero no encontrado en columna: '".$col_name."'";
			return false;
		}
		$dir = $col->get_dir_upload();
				
		//Especificamos el nuevo nombre de fichero
		
		// Comprobamos que la configuración de directorio de subida es correcta
		if ( ! is_dir( $dir ) || $dir=='' ) {
            if (website::in_developer_mode()) {
                $this->errors[] = "Directorio de subida de ficheros no es correcto :".$dir;
            } else {
			     $this->errors[] = "Directorio de subida de ficheros no es correcto";
            }
			return false;
		}
		
		// Procesamos datos del fichero subido
		//echo "Fichero subido: <b>".$_FILES[$col_name]['name']."</b><br />";
		if (!is_uploaded_file($_FILES[$col_name]['tmp_name'])) {
			$this->errors[] = "No se encontró el fichero subido";
			return false;
		} 
		
		$source = $_FILES[$col_name]['tmp_name'];
		
		//Comprobamos que el fichero no excede las dimensiones de tamaño
		if ( $this->table->upload_size_limit > 0 && filesize($source) > $this->table->upload_size_limit ) {
			$this->errors[]="Tamaño del fichero '".$_FILES[$col_name]['name']."' :".(round(filesize($source)/1024)+1). " Kb.<br />".
				"El fichero sobrepasó el tamañó máximo permitido de ".(round(filesize($this->upload_size_limit)/1024)+1)." Kb.<br />";
			unlink(ini_get('upload_tmp_dir').$_FILES[$col_name]['tmp_name']);
			return false;
		}
			
		//Comprobamos que la imagen es un jpg
		if ($this->table->columns_col->get($col_name) instanceof column_image && 
			strtolower(substr($_FILES[$col_name]['name'],-4)) != '.jpg') 
		{
			$this->errors[] = "Solo se permiten imágenes en formato '.jpg'.";	
			unlink(ini_get('upload_tmp_dir').$_FILES[$col_name]['tmp_name']);		
			return false;				
		}

		//Comprobamos que el fichero no excede el máximo permitido para el directorio
		$dir_size = dirsize($dir);
		if ($this->table->upload_dir_size_limit > 0 && ( $dir_size + filesize($source) ) > $this->upload_dir_size_limit )  {
			$this->errors[]="Tamaño del fichero '".$_FILES[$col_name]['name']."' :".(round(filesize($source)/1024)+1). " Kb.<br />".									
				 "El directorio no tiene capacidad suficiente para almacenar el fichero.<br />".
				 "Capacidad del directorio: ".(round(filesize($dir_size)/1024)+1)."de ".(round(filesize($this->upload_dir_size_limit)/1024)+1)." Kb.<br />";
			unlink(ini_get('upload_tmp_dir').$_FILES[$col_name]['tmp_name']);			
			return false;
		}
		
		$newfilename = $this->table->key_set->get_pks_string("_")."_".$_FILES[$col_name]['name'];
		$destination = $dir.$newfilename;
		
		if (strlen($newfilename)>253) {
			$this->errors[]="El nombre del archivo adjunto es demasiado largo. Acortelo e inténtelo de nuevo.";
			return false;
		}
		
		//Comprobamos si existia otro fichero en esa tupla y no se tiene permiso para cambiarlo
		if (isset($this->previous_row[$col_name]) && $this->previous_row[$col_name] != '' && ! $this->table->upload_allow_overwrite ) {
			$this->errors[] = "No tiene permiso para eliminar el fichero existente :'".$row[$col_name]."'";
			unlink(ini_get('upload_tmp_dir').$_FILES[$col_name]['tmp_name']);
			return false;
		}
			
		//Comprobamos si el mover el fichero sobreescribe otro del directorio y no tiene permiso para eso
		if ( is_file($destination) && ( ! isset($this->previous_row[$col_name]) || $this->previous_row[$col_name]!=$newfilename)) {			
			$this->errors[] = "Ya existe un fichero con ese nombre en el directorio destino.";
			//echo "Renombre su fichero antes de subirlo.<br /><br />";
			@unlink(ini_get('upload_tmp_dir').$_FILES[$col_name]['tmp_name']);
			return false;
		}

		//Si ya existía uno en la tupla, borramos en la anterior
		if (isset($this->previous_row[$col_name]) && $this->previous_row[$col_name] != '' ) {
			//Borramos el archivo sustituido
			//TODO: Utilizar upload en lugar de Image
			$img_sust = new image($this->previous_row[$col_name],$dir);
			if ( ! $img_sust->delete() ) {
				$this->errors[] = "No se pudo borrar el fichero existente previamente en: <br />".
					$dir.$row[$col_name]."<br />\r\n";
				unlink(ini_get('upload_tmp_dir').$_FILES[$col_name]['tmp_name']);
				return false;
			} else {								
				//echo "Borrado el fichero existente previamente ".$row_previous[$col_name]."<br />\r\n";
			}
		}
		
		//Intentamos mover el fichero
		if ( ! @move_uploaded_file($source,$destination ) ) {
			$this->errors[] = "No se ha podido procesar el fichero subido";
			@unlink(ini_get('upload_tmp_dir').$_FILES[$col_name]['tmp_name']);
			return false;
		}
		
		//echo "Fichero preparado para añadir a la base de datos.<br />";
							
		return $newfilename;
	}
	function send_email($row_data) {
		if (website::in_developer_mode()) return;
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Additional headers
		$headers .= 'To: '.$this->table->send_email_after_save_destination . "\r\n";
		$headers .= 'From: '.$this->table->send_email_after_save_origin. "\r\n";

		@mail(
			$this->table->send_email_after_save_destination,
			'Nuevos datos en '.$this->table->table_title,
			$this->get_row_table($row_data),
			$headers
			);
	}
	function get_row_table($row,$title='') {
		  $result = "";
		  $pks = $this->table->key_set->primary_keys;

		  $result .= "<table class=\"data_table\" summary=\"$title\" style=\"width:580px;\">\r\n";
		  $result .= "<thead><tr><td class=\"table_title\" colspan=\"2\">\r\n";
		  $result .= $title."\r\n";
		  $result .= "</td></tr></thead>\r\n";
	
		  $result .= "<tbody>\r\n";
	
		  $par = true;
		  $class[true] = "class=\"odd\"";
		  $class[false] = "class=\"even\"";
	
		  foreach($this->table->columns_col as $col) {
		  	if ($col->get_visible() && $this->table->control_group->has_column($col->get_column_name())) {
		  		$result .= "<tr><td>".$col->get_short_title()."</td><td>".$col->get_formatted_value()."</td></tr>";
		  	}
		  }
		  $result .= "</tbody>\r\n";
		  $result .= "</table>\r\n";
		  return $result;
	}
}

?>