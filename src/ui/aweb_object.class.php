<?php

abstract class aweb_object implements iweb_object {
	
	public function get_dependencies() {
		return array();
	}
	public function get_js_files_array() {
		return array();
	}
	public function get_css_files_media_array() {
		return array();
	}
    function get_css_header() {
    }
	public function get_html_header() {
		return '';
	}
	public function get_html_post_body_ini() {
		return '';
	}
	public function get_html_pre_page_end() {
		return '';
	}
	public function __toString() {
		return '';
	}
	/**
	 * @return iweb_object
	 */
	public function &_cast() {
		return $this;
	}

}

?>