<?php

interface iweb_object {
	
	/**
	 * Returns an array of webobject class names required for this webobject to be correctly rendered.
	 * @return array 
	 */
	function get_dependencies();
	/**
	 * Returns an array of JS urls required.
	 * If common libraries are needed, it's better to define them as Dependencies
	 */
	function get_js_files_array();
	/**
	 * Returns an array of media types, containing an array of url of required CSSs files.
	 * If common CSS are needed, it's better to define them as Dependencies.
	 * @return array
	 */
	function get_css_files_media_array();
    
    /**
     * Returns CSS to be included in the header section of a webpage
     */
    function get_css_header();
	/**
	 * Returns HTML to be included in the header section of a webpage.
	 * @return string
	 */
	function get_html_header();
	/**
	 * Returns HTML to be included just after the BODY tag.
	 * @return string
	 */
	function get_html_post_body_ini();
	/**
	 * Returns HTML to be included just before the BODY closing tag
	 */
	function get_html_pre_page_end();
	
	/**
	 * Returns string to be ubicated inplace
	 * @returns string
	 */
	function __toString();

}

?>