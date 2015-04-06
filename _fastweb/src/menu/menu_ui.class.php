<?php

class menu_ui extends web_object  {
	
	
	/** Menu al que hace de envoltorio
	 * @var menu
	 */
	public $menu= null;
	
	
	function print_structure( $structure=null, $index=1 ) {
		if ( $structure==null ) return; 
		foreach($structure as $item) {
			
			if ( ! $this->menu->visible($item) ) {
				break; 
			}
			
			$selected = $this->menu->is_selected($item);
			$show_submenu = ( ! $this->menu->collapse_subitems || $this->menu->find_item($item) );
			
			echo '<li class="'.$this->menu->menu_id.'_item'.$index.'">';
			
			if ( $item->url != '' ) {
				echo '<a href="'.$item->url.'" '.$item->style.' >';
			}
			
			if ($selected)	{ 
				echo '<b>'.$item->title.'</b>';
			} else {
				echo $item->title;
			}
			if ( $item->url != '' ) {
				echo '</a>';
			}			
			
			if ($show_submenu) {

        if (isset($item->submenu->structure)) {
          echo "\r\n<ul class=\"".$this->menu->menu_id."_submenu\">\r\n";
				  $this->menu->print_structure($item->submenu->structure,$index+1);
				  echo "</ul>\r\n";
        }

			}
			echo "</li>\r\n";
		}		
	}
	
	function print_menu() {
      echo '<img src="'.website::$theme->get_theme_dir().'/menu/'.$this->menu->menu_style.$this->menu->menu_id.'_top.jpg" border="0" alt="" /><br />'."\r\n";
      echo '<ul class="'.$this->menu->menu_id.'">'."\r\n";
	    $this->menu->print_structure($this->menu->structure);
      echo "</ul>\r\n";
      echo '<img src="'.website::$theme->get_theme_dir().'/menu/'.$this->menu->menu_style.$this->menu->menu_id.'_bottom.jpg" border="0" alt="" /><br />'."\r\n";
	}	
	
}