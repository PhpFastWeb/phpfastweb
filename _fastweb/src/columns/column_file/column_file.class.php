<?php
class column_file extends acolumn implements icolumn {
	
	private $limit_len;
	public function set_limit_len($max_len) {
		$this->limit_len = $max_len;
	}

	
	protected $dir_upload = '';
	public function set_dir_upload($dir) {
		$this->dir_upload = $dir;
	}
	public function get_dir_upload() {
	   if ($this->dir_upload == '') return website::$base_dir.'/__upload/'.$this->table->table_name.'/';
		return $this->dir_upload;
	}
    protected $generator_url = '';
	public function &set_generator_url($url_string) {
		$this->generator_url = $url_string;
		return $this;
	}
    public function get_generator_url() {
        if ($this->generator_url=='') return website::$base_url.'/__upload/'.$this->table->table_name.'/';
        return $this->generator_url;
    }
    
    public function get_upload_allow_overwrite() {
    	return $this->upload_allow_overwrite;
   	}
	protected $upload_allow_overwrite=true;
	
	protected $upload_dir_size_limit = -1;
	public function get_upload_dir_size_limit() {
		return $this->upload_dir_size_limit;
	}
	
	public function get_formatted_value() {
		$result = '';
		if ($this->value != '') {
			$destination = $this->get_dir_upload().$this->value;
			if (is_file ( $destination )) {
				$result .= $this->get_representation ( $this->get_dir_upload(), $this->value );
				//$result .= " &nbsp;&nbsp;&nbsp;\r\n";
			
			} else {
				$result = "<i>" .$this->value. "</i> &nbsp;&nbsp;&nbsp;\r\n";
			}
		}
		return $result;		
	}
	public function get_input_plain() {
		$result = '';
		if ($this->value != '') {
			$destination = $this->table->upload_dir.$this->value;
			if (is_file ( $destination )) {
			 //$result .= " &nbsp;&nbsp&nbsp;";
				$result .= $this->get_representation ( $this->get_dir_upload(), $this->value );
                //$result .= " &nbsp;&nbsp;&nbsp;\r\n";
				
			
			} else {
				$result = " &nbsp;&nbsp;<i>" .$this->value. "</i> &nbsp;&nbsp;&nbsp;\r\n";
			}
		}
		$result .= $this->get_input_upload ( );
		return $result;
	}
	
	function get_representation($file, $dir) {
		$src = $this->get_src();
		$t = $this->get_shortened_filename();
		$result = '<a href="'.$src.'" target="_blank" ';
        $result .= 'onclick="if (event.stopPropagation){event.stopPropagation();}else if(window.event){window.event.cancelBubble=true;} return true;"';
        //TODO: make this a call to library function
        $result .= '>';
		$result .= '<img src="'. website::$theme->get_img_dir() . '/icon_file.gif" style="vertical-align:bottom;" />';
		$result .= $t.'</a>';
		return $result;
	}
	public function get_input_hidden_prev_value() {
		//TODO: Comprobar que esto es necesario
		$result = "<input type=\"hidden\" name=\"{$this->get_html_id()}_prev_\" id=\"{$this->get_html_id()}_prev_\" value=\"".htmlentities($this->get_value())."\" />\r\n";
		return $result;
	}
	function get_input_upload() {

		$result = '';
		if ( $this->value != '') {
			//echo "<br />";
			$destination = $this->table->upload_dir.$this->value;
			if ( is_file ( $destination) ) {
//				$result .= "<div style=\"font-size:9px;width:190px; float:right;margin-top:-7px;\">";
//				$result .=  "Tamaño: " . (round ( filesize ( $destination ) / 1024 ) + 1) . " Kb<br />";
//				$result .=  "Permisos de fichero: " . fileperms ( $destination ) . "<br />\r\n";
//				$result .=  "Última modificación fichero: " . date ( "d-m-Y", filemtime ( $destination ) ) . "<br />\r\n";
//				$result .=  "MD5: " . md5_file ( $destination ) . "<br />";
//				$result .=  "</div>";
				
			} else {
				$result .=  basename($this->value)."<br /><b>Fichero no encontrado</b>.<br /><br />";
			}
			if ($this->upload_allow_overwrite) {
				//echo "<font style=\"font-size:9px;\">";	
				//$url = new url();
				$url = $this->table->key_set->get_url();
				$url->set_var(acommand::get_command_label(),'remove_file');
				$url->set_var("column_filename",$this->get_column_name());
				$url->set_var("column_filename",$this->get_column_name());
				
				$id_up = $this->get_column_name()."_input_file_";
				
				$result .=  "<br />[ ";
				$result .=  "<a href=\"{$url}\" ";				
				$result .=  "onclick=\"javascript: if(confirm('&iquest;Borrar fichero permanentemente?')){document.location=this.href;}else{return false;}\" ";
				$result .=  ">";
				$result .=  "Eliminar</a>";
				$result .=  " | ";
				$result .=  "<a href=\"#\" onclick=\"javascript: showhide('$id_up');return false;\">Sobreescribir</a> ]";
			
				$result .=  "<div id=\"".$id_up."\" style=\"display:none; font-size:0.9em; color:#696969; border:1px solid #A7A6AA; width:356px; margin:2px -40px 0 0; padding:4px 5px; overflow:hidden;\">Seleccione fichero que reemplazará a éste:<br />";
				$result .=  "<input type=\"file\" name=\"{$this->column_name}\" id=\"{$this->column_name}\" class=\"form_edit_row_upload\" /></div>\r\n";
				$result .=  "<br style=\"clear:both;\" />";
				//$result .=  "<br style=\"clear:both;\" />";
			}
			

		} else {
			//Averiguamos el tamaño total permitido para el directorio
	//		if ($this->upload_dir_size_limit > 0) {
	//			$total_size = dirsize ( $dir );
	//			$total_size = round ( $total_size / 1024 ) + 1;
	//			$max_dir_size = round ( $this->upload_dir_size_limit / 1024 ) + 1;
	//			$perc = round ( 100 * $total_size / $max_dir_size );
	//		}
			
			//Condiciones en las que se puede subir un fichero:
			if ($this->upload_allow_overwrite || //Se puede sobreescribir
				$this->value == "") { //No se debe sobreescribir, pero no existe fichero
				if ($this->get_upload_dir_size_limit() <= 0 || //No hay límite de subida en el directorio
				true) {
					$result .= "<div style=\"font-size:0.9em; color:#696969; border:1px solid #A7A6AA; width:392px; margin-top:2px; padding:5px 5px;overflow:hidden;\">Seleccione el archivo a incorporar:<br />";
				//($max_dir_size - $total_size) > 1) { //Hay limite, pero aun queda al menos 1 Kb en el directorio
					$result .=  "<input type=\"file\" name=\"{$this->column_name}\" id=\"{$this->column_name}\" class=\"form_edit_row_upload\" />\r\n";
					//$result .=  "<br style=\"clear:both;\" />";
					$result .=  "</div><br style=\"clear:both;\" />";						
				}
	//			if ($this->upload_dir_size_limit > 0 && ($max_dir_size - $total_size) <= 1) {
	//				echo "<i>(Se sobrepasó el tamaño límite para el directorio de $max_dir_size Kb)</i><br />";
	//			}
			}
		}
		return $result;
	}

	public function get_src() {
        if ($this->value == '') return '';
		return $this->get_generator_url().rawurlencode($this->value);
	}
	public function get_original_filename() {
		$newfilename = $this->table->key_set->get_pks_string("_")."_";
		return substr($this->value,strlen($newfilename));
	}
	public function get_shortened_filename() {
		$max_name_length = 45;
		
		$t = $this->get_original_filename();
		$path_info = pathinfo($t);
		$name = $path_info['filename'];
		$ext = $path_info['extension'];
		if (strlen($name)>$max_name_length) {
			$name = substr($name,0,$max_name_length-6).'[...]';
		}
		$t = $name.'.'.$ext;
		return $t;
	}

}

?>