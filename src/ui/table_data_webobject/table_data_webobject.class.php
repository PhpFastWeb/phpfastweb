<?php
class table_data_webobject extends aweb_object implements iweb_object {

    /**
     * @var table_data
     */
    protected $table_data;
    
	public function get_js_files_array() {
		return array( 
			website::$lib_base_url.'/public/js/lib.js',
			website::$lib_base_url.'/ui/save_header/save_header.js',
			);
	}
    public function get_css_files_media_array() {
        return array(
            'all' => website::$lib_base_url.'/ui/table_data_webobject/theme/modern/table_data.css'
        );
    }
}
