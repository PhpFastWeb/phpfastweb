<?php
class link_button extends aweb_object implements iweb_object {
	public function get_css_files_media_array() {
        $css_files_media_array = array(
            0=>array('all' => website::$base_url.'/_fastweb/src/ui/link_button/link_button.css')
			);
		return $css_files_media_array;
	}
	//---------------------------------------------------------------------------------------------------------	
}
?>