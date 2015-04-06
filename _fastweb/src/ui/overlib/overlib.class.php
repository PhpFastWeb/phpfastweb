<?php
class overlib extends aweb_object implements iweb_object {
	
	public function get_html_post_body_ini() {
		return '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'."\r\n";
	}
	
	public function get_js_files_array() {
		return array( website::$lib_base_url.'/ui/overlib/overlib.js' );
	}
}

?>