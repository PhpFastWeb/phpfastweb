<?php

class menu_ui2 {
	
	/** Menu al que hace de envoltorio
	 * @var menu
	 */
	public $menu= null;
	public $stylesheet='';
	public $end_level = -1;
	public $start_level = 0;
	//------------------------------------------------------------------
	private function print_menu_structure($structure = null, $level = 0 ) {
		if ($structure== null ) $structure = $this->menu->structure;
		$this->print_structure_header();
		foreach ($structure as $item) {
			if ( $item->visible && $this->menu->is_visible($item) ) {

				if ( $level < $this->start_level ) {
					break;
				}
				$this->print_menu_item($item,$level);

				if ( $item->has_submenu() && ( $this->end_level == -1 || $this->end_level > ( $level +1 ) )) {

					//$show_submenu = ( $this->menu->collapse_subitems != false && $this->menu->find_item($item) );
					//$show_submenu = $this->menu->find_item($item);
					$show_submenu = true;
					if ( $show_submenu ) {
						$this->print_menu_structure($item->submenu->structure, $level+1);
					}
				}
			}
		}
		$this->print_structure_footer();
	}
	//----------------------------------------------------------------------
	public function print_menu() {
		$this->print_menu_header();
		$this->print_menu_structure();
		$this->print_menu_footer();
	}	
	
	//------------------------------------------------------------------
	private function print_structure_header() {
		return;
	}
	private function print_structure_footer() {
		return;
	}	
	private function print_menu_header() {
		echo "\t\t<div class=\"s5_menu\">\r\n";
	}
	private function print_menu_footer() {
		echo "\t\t</div>\r\n";
	}

	private function print_menu_item(menu_item $item, $level = 0 ) {

		if ($this->menu->is_selected($item)) {
			$is_selected = 's5_menu_item_selected ';
		} else {
			$is_selected = '';
		}
		echo "\t\t\t<p class=\"s5_menu_item${level}\">";
		
		if ($item->url != "") {
			echo "<a  class=\"${is_selected}s5_menu_text\" href=\"$item->url\">";
			echo $item->title;
			echo "</a></p>\r\n";
		} else {
			echo "<span class=\"${is_selected}\" class=\"s5_menu_text\">";
			echo $item->title;
			echo "</span></p>\r\n";			
		}

	}
	//--------------------------------------------------------------------
	
	public function print_web_map($structure = null) {
		if ($structure == null ) $structure = $this->menu->structure;
		echo "<ul class=\"s5_web_map\" >";
		foreach($structure as $item ) {
			if ( $item->visible ) {

				echo "<li>";

				if ( $item->description != '' ) {
					echo html_template::print_overlib_alink($item->description,200,$item->url);
				} else {
					echo "<a href=\"".$item->url."\">";
				}
				echo $item->title;
				echo "</a>";
				if ($item->has_submenu()) {
					echo '<br />';
					$this->print_web_map($item->submenu->structure);
				}
				echo "</li>";
			}
		}
		echo "</ul>";
	}
	
	//----------------------------------------------------------------------
	
	public $breadcrums_first_item_parent = true;
	public $breadcrums_separator = ' -> ';
	public $breadcrums_start = ''; // 'Está usted en: ';
	public $breadcrums_end = '';
	public function get_breadcrums() {
		$result = $this->get_breadcrums_level();
		if ( $result != '' ) {
			$result = $this->breadcrums_start.'<font class="s5_breadcrums">'.$result.'</font>'.$this->breadcrums_end;
		}
		return $result;
	}
	public function get_breadcrums_level($structure=null, $level = 0 ) {
		
		$result = '';
		if ( $structure == null ) {
			$structure = $this->menu->structure;
		}
		$found = false;
		
        if ($structure !== null && count($structure)>0) {
            reset($structure); //Puede estar ya fijado el puntero al hacer llamada recursiva
    		while ( ! $found && ( list(, $item) = each($structure) ) ) {
    		//foreach($structure as $item ) {
    
    			$found = $this->menu->find_item($item);
    			
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

	//----------------------------------------------------------------------
	public function get_menu_images() {
		$item_selected = $this->menu->get_selected_item();
		$result = '';
		foreach( $item_selected->submenu->structure as $item) {
			if ( ! $item->visible ) break;
			$result .= $this->get_menu_images_item($item);
		}
		return $result;
	}
	private function get_menu_images_item(menu_item $item) {
		$result  = '<div class="menu_item_images">';
		$result .= '<table summary="" cellspacing="0"><tr><td valign="bottom">';
		$result .= '<strong><a href="'.$item->url.'">';
		$result .= $item->title."\r\n";
		$result .= '</a></strong>';
		$result .= '</td></tr></table>';
		$result .= '<div class="menu_item_image_zone">';
		$result .= '<a href="'.$item->url.'" style="">';
		$img_src = $this->get_menu_images_img($item);
		$result .= '<img src="'.$img_src.'" alt="'.$item->title.'" /><br />'."\r\n";
		
		$result .= $item->subtitle;
		$result .= '</a>';
		$result .= '</div>';
		$result .= '</div>';
		return $result;
	}
	private function get_menu_images_img(menu_item $item) {
		$result = $item->image_url;
		if ( $result == '' && $item->image_auto ) {
			$result = 'menu/'.basename($item->url,'.php').'.jpg';
			if ( ! is_file($result) ) {
				$result = 'menu/menu_empty.jpg';
			}
		}
		return $result;
		
	}
}