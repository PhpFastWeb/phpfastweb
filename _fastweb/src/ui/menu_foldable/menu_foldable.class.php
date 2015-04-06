<?php
class menu_foldable extends menu implements iweb_object {
	public function get_css_files_media_array() {
        $css_files_media_array = array(
            0=>array('all' => website::$base_url.'/_blogic/ui/menu_foldable/menu.css'),
            1=>array('all' => website::$base_url.'/_blogic/ui/menu_foldable/menu_foldable.css'));
		return $css_files_media_array;
	}
	public function get_js_files_array() {
		return array( website::$base_url.'/_blogic/ui/menu_foldable/menu_foldable.js' );
	}
	//---------------------------------------------------------------------------------------------------------	
    public function __construct() {
    	//website::$current_page->menu->user = website::$user;
		$this->menu_ui = new menu_ui2();
		$this->menu_ui->menu = $this;
		$this->end_level = 2;
		$this->breadcrums_separator =
			'  <img src="'.website::$base_url.'/img/sep_miga.gif" class="sep_miga" width="9" height="12" alt="-" /> ';
		$this->breadcrums_start = "<a href=\"".website::$base_url."\">Inicio</a> ".$this->breadcrums_separator;
		$this->breadcrums_first_item_parent = true;
		
		$this->developer_mode = false;
		//website::$current_page->menu->developer_prototype = 'usuarios.php';
		
   	}

	//--------------------------------------------------------------------------------------------------------
	
    public function __echo() {
        if ( ! website::user_is_allowed() ) return;
        ?>
        <div style="width:1px; height:1px; position:relative; float:left; ">
        	<div id="show_menu_div" style="position:relative; float:left; margin:-8px 0px 0px 10px; font-size:10px; width:240px; display:none; ">
                <a class="sesion_a_but" style="float:left; border:1px solid #1CB0BA; padding:2px 36px; " href="#" onclick="switch_peek_menu();return false;">Menú
                &nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo website::$base_url;?>/_blogic/ui/menu_foldable/img/triangle.gif" alt="" /></a>
        		<a href="#" onclick="javascript:return show_menu();" style="padding: 0 0 0px 1px; margin-left: 5px;">expandir <img src="<?php echo website::$base_url; ?>/_blogic/ui/menu_foldable/img/icono_mostrar.gif" style="margin-bottom:1px; vertical-align: bottom;" alt="" /></a>
        	</div>
            <div id="peek_menu_div" style="display: none;">
                <div class="menu_v_interior"  style="background-color: #F5F5F5; border:1px solid #1CB0BA;">
                    <?php parent::__echo(); ?>
                </div>
                <br />
                <br />
            </div>
        </div>
        <div id="menu_v" class="no_print">
        	<div id="menu_v_borde">
                <img src="<?php echo website::$base_url;?>/_blogic/ui/menu_foldable/img/menuizq_arriba.jpg" width="197" height="11" alt="" class="no_print" /><br />                    
                    <div style="width:0px; height:0px; position:relative; float:right; ">
                    	<div id="hide_menu_div" style="margin:-11px 1px 1px -54px; font-size:10px; width:55px; height:13px;">
                            <a href="#" onclick="javascript:return hide_menu();" style="padding: 0 0 0px 1px;"><img src="<?php echo website::$base_url; ?>/_blogic/ui/menu_foldable/img/icono_ocultar.gif" style="margin-bottom:1px; vertical-align: bottom;" alt="" /> ocultar</a><br />
                    	</div>
                    </div>
                <div class="menu_v_interior">
                    <?php echo parent::__echo();?>
                </div>
                <img src="<?php echo website::$base_url;?>/_blogic/ui/menu_foldable/img/menuizq_abajo.jpg" width="197" height="11" alt="" class="no_print" /><br />
            </div>
        </div>
        <?php
    }
    //------------------------------------------------------------------------------------
    public function get_html_pre_page_end() {
        
        if ( isset($_COOKIE["menu_foldable_hide_menu"]) && $_COOKIE["menu_foldable_hide_menu"] == 1 ) {
            return "<script type=\"text/javascript\">\r\nhide_menu();\r\n</script>\r\n";
        }
        return '';
	}
    //--------------------------------------------------------------------------------------
	public function get_html_post_body_ini() {
		return '';
	}
}
?>