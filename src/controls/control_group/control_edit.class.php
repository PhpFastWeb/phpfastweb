<?php
class control_edit extends control_group implements icontrol {
	
	protected $use_table = true;
	public function set_use_table($use_table=true) {
		$this->use_table = $use_table;
	}

	public $command_target=''; //'INSERT','UPDATE'
	public function set_command_target($command_target) {
		$this->command_target = $command_target;
	}
	
	protected $title='';
	public function set_title($title) {
		$this->title = $title;
	}
	public function get_title() {
	   if ($this->title != '') return $this->title;
       else if ( $this->table && $this->table->record_title ) {
            return ucfirst($this->table->record_title);
       }
    }
	/**
	 * Contains an array of special formats found, for example:
	 * images, files
	 * This allows to store in one iterative search if there is the
	 * need of a special footer for the parent control.
	 */

	public function get_control_render() {
		//echo $this->command_target."<br />";
		//echo ($this->command_target=='');
		if ($this->command_target=='') {
			//Esto pasa si se llama circularmente dos veces a get_control_render
			throw new ExceptionDeveloper('command_target no definido');
		}
		$has_images = false;
		$result = '';
		
		$result .= $this->get_opening_form();
		$result .= $this->get_primary_key_inputs();
		$result .= $this->get_validation_messages();
		$result .= $this->get_info_header();
		foreach($this->controls as $control) {
			$result .= $control->get_control_render();
		}
		$result .= $this->get_controls_js();
		$result .= $this->get_info_footer($has_images);
		$result .= $this->get_closing_form();
		return $result;
	}
	protected function get_control_render_recursive(control $control) {
		if ($control instanceof acontrol_column &&
			$coltrol->get_column() instanceof column_image) {
				$this->has_images = true;
		}		
		$result = '';
		$result = $control->get_control_render();
		return $result;
	}
	
	/**
	 * 
	 * @var table_data
	 */
	protected $table;
	protected function get_opening_form() {
		$result = '';
		
		if ($this->use_table) {
			$result .= "<table align=\"center\" class=\"data_record\" summary=\"{$this->title}\" >\r\n";
			$result .= "<thead><tr><td class=\"table_title\" colspan=\"2\">\r\n";
			$result .= $this->get_title()  . "\r\n";
			$result .= "</td></tr></thead>\r\n";
			$result .= "<tbody><tr><td>\r\n";
		} else {
            if ($this->title != '') {
                $result .= "<div style=\"font-size:14px; padding-left:3px; font-weight:bold; border-bottom:1px solid #bbb;border-left:1px solid #bbb;border-right:1px solid #bbb;\">\r\n";
                $result .= $this->get_title() . "\r\n";
                $result .= "</div>\r\n";
            }
		}
        //name=\"form_edit\" id=\"form_edit\"
		$result .= "<form  action=\"".html_template::get_php_self()."\" method=\"post\" class=\"form_edit_row\" enctype=\"multipart/form-data\""; 
		if ( ! $this->table->autocomplete ) {
            $result .= " autocomplete=\"off\" >";
            $result .= '<!-- prevent autocomplete -->';
            $result .= '<input type="text" style="display: none" autocomplete="on" /><input type="password" style="display: none" autocomplete="on" />';
        } else {
            $result .= ">\r\n";
        }
        $result .= "<input type=\"hidden\" name=\"" . acommand::get_command_label() . "\" id=\"" . acommand::get_command_label() . "\" value=\"{$this->command_target}\" />\r\n";
        
        
		return $result;
	}
	protected function get_closing_form() {
		$result = '';
		$result .= "<center class=\"no_print\">\r\n";
		if ( $this->table->default_command != $this->table->command_name 
            && $this->table->default_command != 'set'
        ) {
            if ( $this->table->discard_changes_show_button ) {
                $onclick="history.go(-1);";
                $onclick= "document.location='".$_SERVER['PHP_SELF']."';";
                $result .= "<input type=\"button\" name=\"back\" value=\"".$this->table->discard_changes_message."\" onclick=\"$onclick\" /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            }
		}
		if ( ! $this->is_readonly()) {
			$result .= "<input type=\"submit\" id=\"form_submit\" name=\"enviar\" accesskey=\"g\" value=\"";
			$result .=  $this->table->send_button_mesage . "\" onclick=\"formSending=true;this.disabled=true;this.form.submit();return false;\"/><br />\r\n";
		}
		//Cerramos el formulario
		$result .= "</center></form>\r\n";
		$result .= "<script type=\"text/javascript\">\r\n";
        $result .= "focusFormEdit();\r\n";
        $result .= "setMaxLength();\r\n";
        $result .= "</script>\r\n";
		
		if ($this->use_table) {
			$result .= "</td></tr>";
			$result .= "</tbody></table><br />\r\n";
		}
		//$result .= "<br /><br />\r\n";	
		return $result;
	}
	protected function get_primary_key_inputs() {
		$result = '';
		$result = $this->table->key_set->get_input_hiddens();
		//Imprimimos ocultos las primary keys
		//$result = $this->table->key_set->get_input_hiddens_from_array ( $row );
		
		return $result;
	}
	protected function get_validation_messages() {
		$result = '';
		return $result;
	}
	protected function get_info_header() {
		$info = '';
		if ( $this->table->columns_required_all) {
			$info .= '<center>Todos los campos son obligatorios, no deje ninguno sin rellenar</center>';
		}		
		if ($info != "") {
			$info = "<center><div class=\"s5_head_info\">".$info."</div></center>";
		}
        
        return $info;		
	}
	protected function get_info_footer() {
	   if ( $this->is_readonly()) return '';
       
		$foot_info = "";
		
		$have_img = false;
		$control = reset($this->controls);
		$has_images = false;
		foreach ($this->table->columns_col as $col) {
			if ( $col instanceof column_image && $col->is_visible() ) {
				$has_images = true;
				break;
			}
		}
		if ( $has_images ) {
			$foot_info .= "Restricciones para im&aacute;genes: ";
			$dir = $this->table->image_dir_upload;
			if ($this->table->upload_dir_size_limit > 0) {
				$total_size = dirsize ( $dir );
				$total_size = round ( $total_size / 1024 ) + 1;
				$max_dir_size = round ( $this->table->upload_dir_size_limit / 1024 ) + 1;
				$perc = round ( 100 * $total_size / $max_dir_size );
			}
			$foot_info .= "Solo se permiten im&aacute;genes en formato <strong>JPG</strong>.<br />";
			if ($this->table->upload_size_limit > 0) {
				$max_size = round ( $this->table->upload_size_limit / 1024 ) + 1;
				$foot_info .= "Tama&ntilde;o m&aacute;ximo de archivo de imagen permitido: $max_size Kb<br />";
			}
			if ($this->table->upload_dir_size_limit > 0) {
				$foot_info .= "Almacenamiento utilizado: $total_size de $max_dir_size Kb ($perc%)<br />";
			}
			if ($this->table->upload_dir_size_limit > 0 && ($max_dir_size - $total_size) <= 1) {
				$foot_info .= "<i style=\"background-color: yellow\">Se sobrepas&oacute; el tama&ntilde;o l&iacute;mite para el directorio de $max_dir_size Kb</i><br />";
			}
			$foot_info .= "<br />";
		}
		
		if (in_array ( 'file', $this->table->columns_format )) {
			$foot_info_files = "";
			$dir = $this->table->upload_dir;
			if ($this->table->upload_size_limit > 0) {
				$max_size = round ( $this->table->upload_size_limit / 1024 ) + 1;
				$foot_info_files .= "Tama&ntilde;o m&aacute;ximo de archivo permitido: $max_size Kb<br />";
			}
			if ($this->table->upload_dir_size_limit > 0) {
				$total_size = dirsize ( $dir );
				$total_size = round ( $total_size / 1024 ) + 1;
				$max_dir_size = round ( $this->table->upload_dir_size_limit / 1024 ) + 1;
				$perc = round ( 100 * $total_size / $max_dir_size );
				$foot_info_files .= "Almacenamiento utilizado: $total_size de $max_dir_size Kb ($perc%)</font><br />";
				if (($max_dir_size - $total_size) <= 1) {
					$foot_info_files .= "<i style=\"background-color: yellow\">Se sobrepasdo el tama&ntilde;o l&iacute;mite para el directorio de $max_dir_size Kb</i><br />";
				}
			}
			
			if ($foot_info_files != "") {
				$foot_info_files = "<strong>Restricciones para ficheros</strong><br />" . $foot_info_files . "<br /><br />";
				$foot_info .= $foot_info_files;
			}
		
		}
		
        
        //Preguntamos a la colección de columnas si alguna de ellas es requerida
        if ($this->table->columns_col->has_required_columns() && ! $this->table->columns_required_all && $this->table->show_required_footer ) {
			$foot_info .= 'Los campos marcados con <img src="' . website::$theme->get_img_dir () . '/icon_required.gif" alt="*" style="vertical-align:text-bottom;" />' . ' son <b>obligatorios</b><br />';
		}
		
		if ($foot_info != "") {
			$foot_info = "<center><div class=\"s5_foot_info\">".$foot_info."</div></center><br />";
		}
        
        return $foot_info;
	}
	protected function get_controls_js() {
		$js = ''; $i=0;
		foreach($this->table->columns_col as $key => $column) {
		    //  echo $i++." ";
			$js1 = $column->get_js_after_control() ;
			if ( $js1 != '' ) {
                $js .= $js1."\r\n";
            }
		}
		if ( $js != '' ) {
			 $js = "\r\n<script type=\"text/javascript\">\r\n" .$js. "</script>\r\n";
		}
		return $js;
        
	}
	protected function add_single($control) {
		if ($control instanceof control_edit) {
			throw new ExceptionDeveloper('Can\'t add control_edit to a control_edit object');
		}
		parent::add_single($control);
	}
}
?>