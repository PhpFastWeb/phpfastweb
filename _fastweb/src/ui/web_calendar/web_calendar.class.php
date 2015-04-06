<?php

class web_calendar extends aweb_object implements iweb_object {
    
	public function get_css_files_media_array() {
        $css_files_media_array = array(
        0=>array('all' => website::$lib_base_url."/../calendar/calendar-win2k-2.css"));
		return $css_files_media_array;
	}
	public function get_js_files_array() {

        return array(
        website::$current_page->js_files[] = website::$lib_base_url."/../calendar/calendar.js",
        website::$current_page->js_files[] = website::$lib_base_url."/../calendar/lang/calendar-es.js",
        website::$current_page->js_files[] = website::$lib_base_url."/../calendar/calendar-setup.js");
	}
}