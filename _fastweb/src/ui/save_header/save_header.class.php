<?php
class save_header extends aweb_object {
	public function get_js_files_array() {
		return array( 
            website::$base_url.'/_blogic/ui/save_header/floating-1.7.js',
            website::$base_url.'/_blogic/ui/save_header/save_header.js' 
        );
	}    
    
    public $allways_visible = true;
	public function __toString() {
		$result = "";

        if ($this->allways_visible) $display = 'block';
        else $display = 'none';
        
		$result .= "<div id=\"cambios\" style=\"position:relative; background-color:#F5F5F5; display:$display; float:right; font-size:11px; width:164px; text-align:center; padding:2px 2px; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;\">";    
		$result .= "<input type=\"button\" id=\"cambios_deshacer\" disabled=\"disabled\" style=\"width:80px;height:20px;font-size:11px;vertical-align:middle;\" value=\"Descartar\" ";
        $result .= "onclick=\"undoChanges('".html_template::get_php_self()."');\" /> ";
        
		$result .= "<input type=\"button\" id=\"cambios_guardar\" disabled=\"disabled\" style=\"width:80px;height:20px;font-size:11px;vertical-align:middle;\" value=\"Guardar\" ";
        $result .= "onclick=\"formSending=true;this.disabled=true;document.forms['form_edit'].submit();\" /> ";
        
        $result .= "<b id=\"unsaved_msg\" style=\"display:none; color: #444;\"><br />Hay cambios sin guardar</b>";
        
		$result .= "</div>";
        
        $result .= '<script type="text/javascript">floatingMenu.add(\'cambios\', { targetLeft: 0, targetTop: 0, snap: true, distance: 1 });</script>';
		return $result;
	}
}