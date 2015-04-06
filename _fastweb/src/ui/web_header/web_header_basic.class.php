<?php

class web_header_basic extends aweb_object implements iweb_object {
	
    
    public $show_expand = false;
	public function __toString() {
        if ( $this->show_expand ) {
            echo '<a href="#" onclick="switch_width(); return false;" onmouseover="return overlib(\'Expandir/Contraer tama&ntilde;o de web\', AUTOSTATUS, WRAP, FGCOLOR,\'#FFFFE1\');" onmouseout="nd();" style="display:block; float:right; margin-right:12px; width:20px;"><img src="'.website::$lib_base_url.'/ui/menu_foldable/img/icono_expandir.gif" style="float:right; margin: 3px 3px 0 0" alt="" /></a>';
        }
        echo website::$user->user_ui->get_header_user_info();
        
 	}
}

?>