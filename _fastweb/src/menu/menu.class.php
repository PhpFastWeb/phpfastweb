<?php

class menu extends aweb_object {
	
	/** Configuración de tema a utilizar
	 * @var theme_config
	 */
	public $menu_id = 'menu';
	public $menu_style = 'menu1/';
	public $parent_url = '';
	public $structure = array();
	public $collapse_subitems = true;
	
	/**
	 * Item seleccionado. Solo se encuentra fijado si se ha impreso el menú.
	 * @var menu_item
	 */
	public $selected_item = null;
	/**
	 * @var menu_ui
	 */
	public $menu_ui = null;
	public $initialised = false;
	
	public $developer_mode = false;
	public $developer_prototype = '';


	public $breadcrums_first_item_parent = true;
	public $breadcrums_separator = ' -> ';
	public $breadcrums_start = ''; // 'Está usted en: ';
	public $breadcrums_end = '';
	
	
	function __construct() {
	}
	function __echo() {
		$this->init_config();
		$this->menu_ui->print_menu();
	}
	public function init_config() {
		if ($this->initialised ) return;
		if ( $this->menu_ui == null ) {
			$this->menu_ui = new menu_ui();
		}
		$this->menu_ui->menu = $this;
	}
	public function get_structure_html( $structure=null, $index=1 ) {
		if ( $structure==null ) return '';
		$result = ''; 
		foreach($structure as $item) {
			
			if ( ! $this->is_visible($item) ) {
				break; 
			}
			$selected = $this->is_selected($item);
			$show_submenu = ( ! $this->collapse_subitems || $this->find_item($item) );
			
			$result .= '<li class="'.$this->menu_id.'_item'.$index.'">';
			if ( $item->url != '' ) {
				$result .= '<a href="'.$item->url.'" '.$item->style.' >';
			}
			if ($selected)	{ 
				$result .= '<b>'.$item->title.'</b>';
			} else {
				$result .= $item->title;
			}
			if ( $item->url != '' ) {
				$result .= '</a>';
			}			
			if ($show_submenu) {
		        if (isset($item->submenu->structure)) {
					$result .= "\r\n<ul class=\"".$this->menu_id."_submenu\">\r\n";
					$result .= $this->get_structure($item->submenu->structure,$index+1);
					$result .= "</ul>\r\n";
		        }
			}
			$result .= "</li>\r\n";
		}	
		return $result;	
	}
	//----------------------------------------------------------------------------------------

	public function find_item($item) {
		if ( $item->url == '' ) return false;
		
		//Asumimos que si la parte derecha de la URL se llama index.php, se puede quitar
		$url1 = $item->url;
		if (isset($_GET[acommand::get_command_label()])) {
			$url2 = html_template::get_php_self();
		} else {
			$url2 = url::get_request_url();
		}
		
		if ( url::compare_url_strings($url1,$url2) ) return true;
		//if ($_SERVER['PHP_SELF']==$item->url) return true;		
		
		if ( $this->parent_url != '' ) {
		  //TODO: Replace this for the flexible url comparer
          //TODO: Maybe do an automatic ancestor search
			if ( strpos($this->parent_url,$item->url) !== false ) return true;
  		}
		
		if ( ! isset($item->submenu) && $item->submenu==null ) return false;
		foreach ( $item->submenu->structure as $subitem ) {
			$found = $this->find_item( $subitem );
			if ($found) return true;
		}
	 	return false;
		
	}
	
	function is_selected(menu_item $item) {
		$result = false;
		if ( $this->parent_url != '' && url::compare_url_strings($this->parent_url,$item->url) !== false) {
			$result = true;
		} else {
			if ($item->url=='') {
				$result = false;
			} else {
				if (isset($_GET[acommand::get_command_label()])) {
					$url1 = html_template::get_php_self();
				} else {
					$url1 = url::get_request_url();
				}
				$result = (url::compare_url_strings($url1,$item->url));
			}
		}
		
		if ( $result ) {
			$this->selected_item = $item;
		}
		return $result;
	}
	
	public function is_visible($item) {
		$result = true;
		if (count($item->visible_to_groups)>0) {		
			if ( ! isset(website::$user) || ! isset(website::$user->groups) ) {
				$result = false;
			} else {
				if ( count(array_intersect(website::$user->groups,$item->visible_to_groups)) > 0 ) {
					$result = true;
				} else {
					$result = false;
				}
			}
		}
		return $result;
	}
	public function add_breadcrum_start($text,$url_string) {
		$this->breadcrums_start .= '<a href="'.$url_string.'">'.$text.'</a>'.$this->breadcrums_separator;
	}
	public function get_breadcrums() {
		//if (isset($this->menu_ui)) return $this->menu_ui->get_breadcrums();	
		$result = $this->get_breadcrums_level();
		if ( $result != '' ) {
			$result = $this->breadcrums_start.'<font class="s5_breadcrums">'.$result.'</font>'.$this->breadcrums_end;
		}
		return $result;	
	}
	public function get_breadcrums_level($structure=null, $level = 0 ) {
		$result = '';
		if ( $structure == null ) {
			$structure = $this->structure;
		}
		$found = false;
		
        if ($structure !== null && count($structure)>0) {
            reset($structure); //Puede estar ya fijado el puntero al hacer llamada recursiva
    		while ( ! $found && ( list(, $item) = each($structure) ) ) {
    		//foreach($structure as $item ) {
    
    			$found = $this->find_item($item);
    			
    			if ( $found ) {
    				
    				//Si se trata del primer item de nivel cero y esta activada la opcion correspondiente, lo saltamos
    				
    				if ( $this->breadcrums_first_item_parent && $level == 0 ) {
    					
    					if ( $structure[0] == $item ) {
    						//Es el nivel cero, 
    						$result .= $this->get_breadcrum_link($structure[0]) ;
    						break;
    					} else {
    						//Es otro nivel distinto del cero, añadimos al principio el principal
    						$result .= $this->get_breadcrum_link($structure[0]) . $this->breadcrums_separator;
    					}
    				}
    				
    				if ($item->has_submenu()) {
    					$result_submenu = $this->get_breadcrums_level($item->submenu->structure, $level+1);
    					if ( $result_submenu != '' ) {
    						$result .= $this->get_breadcrum_link($item).$this->breadcrums_separator.$result_submenu;	
    					} else {
    						$result .= $this->get_breadcrum_selected($item);
    					}
    				} else {	
    					$result .= $this->get_breadcrum_selected($item);
    				}
    			}
    		}
		}
		//Si se ha añadido al final un separador de más, se elimina
		if ( ! empty($result) ) {
			$tail = $this->breadcrums_separator;
			$tail_len = strlen($tail);
			
			if ( substr( $result , -1 * $tail_len ) == $tail ) {
				$result = substr($result, 0 , -1 * $tail_len );
			}			
		}
		return $result;
	}
	private function get_breadcrum_link(menu_item $item) {
		$result = "<a href=\"".$item->url."\">";
		$result .= $item->title;
		$result .= "</a>";	
		return $result;
	}
	private function get_breadcrum_selected(menu_item $item) {
		$result = "<a href=\"".$item->url."\">";
		$result .= $item->title;
		$result .= "</a>";	
		return $result;
	}
	//------------------------------------------------------------------------------
	public function add_menu_item(menu_item $menu_item) {
		if ( $menu_item->url == '' ) {
			//TODO: Diferencia enlaces automáticos de elementos sin enlace
			//$menu_item->url = $this->create_page($menu_item->title);
		}
		$this->structure[] = $menu_item;
	}
	
	public function add_submenu_item(menu_item $menu_item) {
		$last = count($this->structure) - 1;
		assert($last >= 0);
		if ($this->structure[$last]->submenu == null ) {
			$this->structure[$last]->submenu = clone $this;
			$this->structure[$last]->submenu->structure = array();
		}
		$this->structure[$last]->submenu->add_menu_item($menu_item);
	}
	public function get_last_menu_item() {
		
		$last = count($this->structure) - 1;
		assert( $last >= 0 );
		
		return $this->structure[$last];
	}	
	public function get_last_submenu_item() {
		$last_menu = $this->get_last_menu_item();
		assert( $last_menu->submenu != null );	
		$last_subitem = count($last_menu->submenu->structure) -1 ;
		assert($last_subitem >= 0);
		assert($last_menu->submenu->structure[$last_subitem] != null );
		
		return $last_menu->submenu->structure[$last_subitem];
	}
	//-------------------------------------------------------------------------------
	private function create_page($title) {
		$page = strtolower($title);
		$page = str_replace(' ','_',$page);
		$page = str_replace('á','a',$page);
		$page = str_replace('é','e',$page);
		$page = str_replace('í','i',$page);
		$page = str_replace('ó','o',$page);
		$page = str_replace('ú','u',$page);
		$page = str_replace('ö','o',$page);
		$page = str_replace('ñ','n',$page);
		$page = $page .".php";
		if ( $this->developer_mode && ! file_exists($page) ) {		
			if ($this->developer_prototype != '' ) {
				if (!copy($this->developer_prototype, $page)) {
  				 	error_log("No se pudo copiar $this->developer_prototype en $page, ".__FILE__);
				}
			} else {
				file_put_contents($page,'//Created automatically by Fastweb, class menu');
			}
		}
		return $page;
		
	}
	/**
	 * @return menu_item
	 */
	public function get_selected_item() {
		if ($this->selected_item != null ) return $this->selected_item;
		//Tratamos de averiguar cuál es el item seleccionado recorriendolos en anchura
		$this->search_selected_item($this);
		return $this->selected_item;
	}
	private function search_selected_item(menu $menu) {
		//Buscamos en anchura el item seleccionado
		$found = false;
		foreach($menu->structure as $menu_item) {
			if ( $menu->is_selected($menu_item) ) {
				$found = true;
				$this->selected_item = $menu_item; 
				break; return;
			}
		}
		if ( !$found ) {
			foreach ($menu->structure as $menu_item) {
				if ( $menu_item->has_submenu() ) {
					$this->search_selected_item($menu_item->submenu);
					if ($this->selected_item != null ) { break; return; }
				}
			}
		}
	}
	
	
}






?>
