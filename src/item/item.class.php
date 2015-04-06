<?php


class item extends item_abstract {	
	public $data = array();
	public $view_mode = 'thumb';
	public $initialised = false;
	/**
	 * Base URL Object
	 *
	 * @var url
	 */
	public $base_url_obj=null;
	public $data_formated = array();
	/**
	 * Enter description here...
	 *
	 * @var key_set
	 */
	public $key_set = null;
	
	/**
	 * Enter description here...
	 *
	 * @var collection_ui
	 */
	public $collection_ui = null;
	//-----------------------------------------------------------------
	function init_config() {
		if ($this->initialised) return;
		
		//Establecemos las claves primarias
		$this->key_set = $this->data_set->data_source->key_set;
		$this->key_set->set_keys_values($this->data);
		
		//Cambiamos el modo de visualización en función de la acción global definida
		//$this->select_view_mode();
		
		//Si el objeto url no está inicializado, lo creamos
		if ( $this->base_url_obj == null ) {
			$this->base_url_obj = new url();
		}
	}
	function __echo() {
		$this->init_config();
		switch ($this->view_mode) {
			case 'thumb':
				$this->print_thumb();
				break;
			case 'details':
				$this->print_details();
				break;			
			case 'mini':
				$this->print_mini();
				break;
			case 'link':
				$this->print_link();
				break;
			default:
				$this->print_other($this->view_mode);
				//throw_new_ExceptionDeveloper($this->view_mode.' no soportado');
				break;
		}
		return;
	}
	function prepare_data() {		
	}
	//-----------------------------------------------------------------
	private function print_simple() {
		if ( is_array($this->data) ) {
			foreach ($this->data as $key => $value ) {
				echo $key.' : '.$value.'<br />';
			}
			echo "<br />\r\n";
		} elseif ( is_object($this->data) ) {
			echo $this->data;
		}
	}
	//-----------------------------------------------------------------
	public $mini_header = '';
	public $mini_footer = '';
	function print_mini_header() { echo $this->mini_header; }
	function print_mini_footer() { echo $this->mini_footer; }
	function print_mini() {
		$this->print_mini_header();
		$i=0;
		foreach ($this->data as $key => $value ) {
			if ($i==1) echo $key.' : '.$value.'<br />';
			$i++;
			if ($i==2) break;
		}
		$this->print_mini_footer();
	}
	//-----------------------------------------------------------------
	public $thumb_header = '';
	public $thumb_footer = '<hr />';
	function print_thumb_header() { echo $this->thumb_header; }
	function print_thumb_footer() { echo $this->thumb_footer; }	
	function print_thumb() {
		$this->print_thumb_header();
		$i=0;
		
		foreach ($this->data as $key => $value ) {
			echo $key.' : '.$value.'<br />';
			$i++;
			if ($i==0) $url_view = html_template::get_php_self().'?action=VIEW&'.$key.'='.$value;
			if ($i==3) break;
		}
		$url_view = $this->get_key_set()->get_url();
		$url_view->set_var('action','VIEW');
		echo '<a href="'.$url_view->__toString().'">ver detalles</a><br />'."\r\n";
		$this->print_thumb_footer();
	}
	//-----------------------------------------------------------------
	public $details_header = '';
	public $details_footer = '';
	function print_details_header() { echo $this->details_header; }
	function print_details_footer() { 
		
		echo '<a href="'.html_template::get_php_self().'" onclick="javascript: history.go(-1); return false;">&lt;&lt;&lt; Volver &lt;&lt;&lt;</a><br /><br style="clear:both" />'."\r\n";
		echo $this->details_footer; 
		$this->print_controls_edit();
	}		
	function print_details() {
		$this->print_details_header();
		$this->print_details_item();
		$this->print_details_footer();
	}
	function print_details_item() {
		$this->print_simple();
	}
	//-----------------------------------------------------------------
	function print_other($view_mode) {
		$this->data_set->data_source->print_data();
		//$this->print_details();
	}	
	//-----------------------------------------------------------------
	public $edit_header = "<strong>Edición</strong><br />\r\n";
	public $edit_footer = "<hr />\r\n";
	function print_edit_header() { echo $this->edit_header; }
	function print_edit_footer() {
		echo $this->edit_footer;
	}
	function print_edit() {
		$action = html_template::get_php_self();
		echo "<form action=\"$action\" method=\"post\" >\r\n";
		$this->print_edit_header();
		$this->print_edit_inputs();
		$this->print_edit_footer();
		echo "</form>\r\n";
	}
	function print_edit_inputs() {
		echo "<table style=\"border-collapse: collapse; border-width: 0px;\">\r\n";
		foreach($this->data as $key => $value) {
			$name = $this->data_set->data_source->columns_title[$key];			
			echo "<tr><td style=\"text-align: right; vertical-align: top; border-color: #EEE; border-style:none none dashed none;\">";
			echo "$name";
			echo "</td><td style=\"text-align: right; vertical-align: top; border-color: #EEE; border-style:none none dashed none;\">";			
			$this->data_set->data_source->print_col_input($key,$this->data);
			echo "</td></tr>";
			//echo "<input type=\"text\" value=\"$value\"><br />\r\n";
		}	
		
		echo "</table>";
		echo "<input type=\"button\" value=\"<<< Atrás <<<\" /> ";
		echo "<input type=\"submit\" value=\"Aceptar\" /><br />\r\n";	
	}
	//-----------------------------------------------------------------
	public $new_header = "Nuevo<br />\r\n";
	public $new_footer = "<hr />\r\n";
	function print_new_header() { echo $this->new_header; }
	function print_new_footer() { echo $this->new_header; }	
	function print_new() {
		throw new ExceptionDeveloper("Not implemented");
	}
	//-----------------------------------------------------------------
	function print_controls_edit() {
		if ( ! $this->check_permission('edit') ) return;
		
		echo "<hr />";
		
		$url = $this->key_set->get_url();
		$url->set_var('action','EDIT');		
		echo "<a href=\"{$url->__toString()}\">Editar</a> ";
		$url->set_var('action','DELETE');
		echo "<a href=\"{$url->__toString()}\" >Borrar</a> ";
	}
	function print_controls_new() {
		if ( ! $this->check_permission('new') ) return;
		$url = new url();
		$url->set_var('action','NEW');
		echo "<a href=\"{$url->__toString()}\">Nuevo</a>";
		
	}

	//-----------------------------------------------------------------
    
	function check_permission($action) {
		/**
		 * @todo Comprobar bien los permisos
		 */
		
		if (isset($this->data_set->data_source->permission[$action])) {
			return $this->data_set->data_source->permission[$action];
		} else {
			return false;
		}
	}
	//------------------------------------------------------------------
	function init_formated_data() {
		foreach ($this->data as $key => $value) {
			
			$this->data_formated[$key] = $this->data_set->data_source->get_formated_column($key,$this->data); 
		}
	}

}
?>