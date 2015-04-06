<?php
class ajax_tree extends aweb_object {
	public function get_js_files_array() {
		return array( 
            website::$base_url.'/_blogic/ui/ajax_tree/ajax.js',
            website::$base_url.'/_blogic/ui/ajax_tree/tree.js',
			website::$base_url.'/_blogic/ui/ajax_tree/ajax_tree.js' 
        );
	}    
    public function get_css_files_media_array() {
		return array( 'all' => website::$base_url.'/_blogic/ui/ajax_tree/ajax_tree.css' );
	}
	public function __toString() {
		$result = "";
		$result .= "<div id=\"loadingbox\"></div>";
		$result .= "<div id=\"tree1\" class=\"nodecls\"></div>";
		
		return $result;
	}
	protected $service_url = 'services.php';
	public set_service_url($url) {
	}
}