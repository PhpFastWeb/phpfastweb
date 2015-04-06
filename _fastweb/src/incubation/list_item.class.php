<?php

class list_item {
	
	public $data  = array();
	public $row = array(); //Almacena toda la fila sin filtrar
	public $theme = null;
	
	public $fields_header  = array();
	public $fields_corener = array();
	public $fields_main    = array();
	public $fields_footer  = array();
	public $fields_hidden  = array();
	
	public $fields_fields  = array();
	
	public $fields_resume  = array();
	
	/**
	 * Tabla de la que se obtuvieron los datos. En caso de ser nulo deberían tomarse los datos de otro sitio.. por implementar
	 * Es decir, NO dejar este campo sin referenciar. De aqui se toman por ejemplo los nombres de las columnas, etc.
	 *
	 * @var table_data
	 */
	public $table = null;

	public $fields = array();

	public $show_empty_fields = false;
	
	public $view_details = true;
	
	/**
	 * Constructor
	 * @param unknown_type $data
	 * @param unknown_type $table
	 * @return list_item
	 */
	function list_item($data=array(),$table=null) {
		$this->data = $data;
		$this->table = $table;
	}
	
	/**
	 * Prepara la configuración del objeto.
	 * Debe ser llamado despues de establecer todos los atributos del mismo.
	 */
	function init_config() {
		$i=0;
		//Dejamos solo los datos que están visibles y no han sido resumidos
		$data_filtered = array();
		foreach ( $this->data as $key => $value ) {
			if ( in_array($key,$this->table->columns) ) {
				$data_filtered[$key] = $value;
			}
		}
		$this->row = $this->data;
		$this->data = $data_filtered;
		foreach ( $this->data as $key => $value ) {

			//Nos saltamos el primero que será clave primaria
			if ($key==$this->table->primary_key || !(in_array($key,$this->table->columns))) { }
			else if ($i==1) { $this->fields_header[$key] = $value; }
			else if ( ! $this->view_details && !in_array($key,$this->fields_resume) && ( !isset($_GET['action']) || $_GET['action'] != 'VIEW' ) ) {
				/* No hacer nada */
			} else if ( $value == '' ) {
				/* No hacemos nada */
			}
			else {
				$ukey = strtoupper($key);

				switch ($ukey) {
					case "TEXTO_CORTO":
						if ( $this->view_details == false ) //&& ( ! isset($_GET['action']) || ! $_GET['action'] != 'VIEW' ) ) )
							$this->fields_main[$key] = $value;
						break;
					case "TEXTO_LARGO":
						if ( ( $this->view_details == false && ( ! isset($_GET['action']) || ! $_GET['action'] == 'VIEW' ) ) )
						break;								
					
					case "DESCRIPCION":
					case "TEXTO":
					case "IMAGEN":
					case "FOTO":
						$this->fields_main[$key] = $value;
					break;			
						
					case "AUTOR":
						$this->fields_footer[$key] = $value;
					break;
					
					case "ACTIVIDAD":
					case "TITULO":
					case "NOMBRE":
					case "APELLIDOS":						
						$this->fields_header[$key] = $value;
					break;
					
					default:
						$this->fields_fields[$key] = $value;
				}
			}
			$i++;
		}
		//Si no se ha establecido cabecera, ponemos la primera
		if ( count($this->fields_header) == 0 && isset( $this->data[1] ) ) {
			$this->fields_header = $this->data[1];
			//Deberíamos quitarlo tambien de donde esté
		}
	}
	
	
	function is_asigned($field) {
		$found = false;
		if (key_exists($field,$this->fields_header)) return true;
		if (key_exists($field,$this->fields_main)) return true;
		if (key_exists($field,$this->fields_fields)) return true;
		if (key_exists($field,$this->fields_footer)) return true;
		if (key_exists($field,$this->fields_hidden)) return true;
		return $found;
	}
	/**
	 * Imprime el item.
	 *
	 */
	function print_item() {
		$this->init_config();		
		//echo "<br clear=\"left\" />";
		$this->print_title();
		$this->print_main();
		
		if ($this->view_details == true || isset($_GET['action']) && $_GET['action'] == 'VIEW' || count($this->fields_resume)>0 ) {
		
			if (count($this->fields_fields) > 0 ) {
				echo "<img src=\"".$this->theme->get_theme_dir()."/item/item_fields_top.jpg\" alt=\"\" /><br clear=\"left\" />";
				echo "<div style=\"background-image:url(".$this->theme->get_theme_dir()."/item/item_fields_bg.jpg); width: 480px; padding: 3px 0px 3px 0px;\">";
				$this->print_fields();
				echo "</div>";
				echo "<img src=\"".$this->theme->get_theme_dir()."/item/item_fields_bottom.jpg\" alt=\"\" /><br clear=\"left\" />";
			}
//			if (count($this->fields_footer) > 0 ) {
				echo "<div style=\"background-image:url(".$this->theme->get_theme_dir()."/item/item_footer.jpg); width: 474px; height: 29px; padding: 3px 3px 3px 3px;\">";
				$this->print_footer();
				
				if ( count($this->fields_main) == 0 && $this->view_details == false && ( ! isset($_GET['action']) || ! $_GET['action'] == 'VIEW' ) ) {
					echo '<br clear="left" />';
					echo "<div style=\"text-align: right; margin: 0 07px 3px 0;\">";
					$url = new url();
					$url->set_var('action','VIEW');
					$url->set_var('ID',$this->data['ID']);
					echo "<a href=\"{$url->__toString()}\" >Leer más...</a><br clear=\"left\" /></div>";
			
				}	
				
				
				echo "</div>";
//			} else {
//				echo "<div style=\"background-image:url(".$this->theme->get_theme_dir()."/item/item_footer.jpg); width: 474px; height: 0px; padding: 1px 3px 0px 3px; overflow:hidden;\">";
//			}
		
		}	
		
		if ($this->in_view_details()) {
			echo "<br clear=\"left\" /><a href=\"#\" onclick=\"javascript:history.go(-1);\" class=\"link_button\"><< Atrás <<</a><br clear=\"left\" /><br clear=\"left\" />";
		}

		//echo "<br clear=\"left\" />";
		
	}
	/*-----------------------------------------------------------------------------------------*/
	function print_title() {
		
		if (count($this->fields_header) > 0 ) {
			echo "<div style=\"background-image: url(".$this->theme->get_theme_dir()."/item/item_header.jpg); width: 471px; padding: 3px 3px 3px 6px;\">";
			foreach($this->fields_header as $text) {
				echo "<b>".stripslashes($text)."</b> ";
			}
			echo "</div>";
			echo "<img src=\"".$this->theme->get_theme_dir()."/item/item_header_main.jpg\" alt=\"\" /><br />\r\n";
		}
	}
	
	function print_main() {
		if (count($this->fields_main) == 0 ) return;
		//Averiguamos si tenemos que mostrar las etiquetas antes de cada campo
		$labels = 0;
		foreach($this->fields_main as $key => $value ) {
			if ($this->table->columns_format[$key] != 'image' && $value != '' ) {
				$labels++;
			}
		}
		$show_labels = $labels > 1;

		//echo "<img src=\"".$this->theme->get_theme_dir()."/item/item_header_main.jpg\" /><br />\r\n";
		echo "<div style=\"background-image:url(".$this->theme->get_theme_dir()."/item/item_main_bg.jpg); word-wrap: break-word; width: 471px; padding: 5px 3px 0px 6px;\">\r\n";
		
		//Mostramos primero los campos imágenes si no se imprimen etiquetas
		foreach($this->fields_main as $key => $value ) {
			if ( ! $show_labels && $this->table->columns_format[$key]=='image') {
				
				echo $this->table->get_formated_value($key,$value);
			}
		}
		//Mostramos los campos que no son imágenes, o todos si se imprimen etiquetas
		foreach($this->fields_main as $key => $value ) {
			if ( $show_labels || $this->table->columns_format[$key]!='image') {

				if ( $show_labels && $value != '' ) {
					echo "<b>".$this->table->columns_title[$key]."</b><br />\r\n";
					echo $this->table->get_formated_value($key,$value);
					echo "<br style=\"clear:both\" />\r\n";
				} else {
					//Convertimos urls a links
					$text = html_template::urls_to_links($this->table->get_formated_value($key,$value),false,false);
					echo $text;
					echo "<br style=\"clear:both\" />\r\n";
				}
			} 
		}
		
		
		//Mostramos enlace a "Ver más" si fuera necesario
		if ( $this->view_details == false && ( ! isset($_GET['action']) || ! $_GET['action'] == 'VIEW' ) ) {
			//echo '<br clear=\"left\" />';
			echo "<div style=\"text-align: right; margin: 0 7px 10px 0;\">\r\n";
			$url = new url();
			$url->set_var('action','VIEW');
			$url->set_var('ID',$this->data['ID']);
			echo "<a href=\"{$url->__toString()}\" class=\"link_button\" >Leer más...</a><br clear=\"left\" /></div>\r\n";
		}	
	
		//echo "<br clear=\"left\" />\r\n";
		echo "</div>\r\n";
		echo "<img src=\"".$this->theme->get_theme_dir()."/item/item_main_bottom.jpg\" alt=\"\" /><br clear=\"left\" />\r\n";
		


	}
	function print_fields() {
		$i=0; $first = true;
		foreach( $this->fields_fields as $key => $value ) {
			//Prepara el valor segun el tipo de contenido.
				
			$format=$this->table->columns_format[$key];
			if ( $key=='EMAIL' || $this->table->columns_format[$key]=='email' ) $format='email';
			if ( $key=='URL' || $key=='WEB' || $key=='ENLACE' || $key=='LINK' || $this->table->columns_format[$key]=='url' ) $format='link';
			
			$text = $this->table->get_formated($value,$format,-1,$key,$this->row);
	
			if ($text != '' | $this->show_empty_fields ) {
				if ( ! $first ) {
					echo "<img src=\"".$this->theme->get_theme_dir()."/item/item_fields_separator.jpg\" style=\"margin-bottom: 3px; margin-top: 2px; \" alt=\"\" /><br clear=\"left\" />";
				}
				$first = false;
				echo "<label style=\"width: 117px; float:left; text-align: right; margin: 0 8px 0px 4px;\">";
				echo $this->table->columns_title[$key];
				echo "</label>";		
				echo "<label style=\" float:left; text-align: left; margin: 0 4px 0px 8px;\">";
				echo "$text";
				echo "</label>";
				echo "<br clear=\"left\" />";
			}
		}
	}
	function print_footer() {
		foreach($this->fields_footer as $text) {
			echo "$text<br clear=\"left\" />";			
		}		
		
	}
	/*-----------------------------------------------------------------------------------------------------*/
	function print_fields_label($text) {
		
	}
	function print_fields_value($col_name,$text,$format='text') {

	}
	/*-------------------------------------------------------------------------------------------------------*/
	function in_view_resume() {
		return ( $this->view_details == false && ( ! isset($_GET['action']) || ! $_GET['action'] == 'VIEW' ) );
	}
	function in_view_details() {
		return ( ( isset($_GET['action']) && $_GET['action'] == 'VIEW' ) );		
	}

	
}


?>
