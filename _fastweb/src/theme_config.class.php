<?php

class theme_config {
	public $base_url_dir = '';
	public $base_file_dir = '';
  public $theme_name = '';
  
  function theme_config($theme_name='', $base_url_dir='', $base_file_dir='') {
  	if ( $theme_name    == '' ) $theme_name = 'default';
  	//if ( $base_url_dir  == '' ) $base_url_dir = html_template::get_php_self().'/';
  	if ( $base_file_dir == '' ) $base_file_dir = dirname(__FILE__);
  	if ( $base_url_dir == '' ) $base_url_dir = website::$base_url;
  	$this->theme_name    = $theme_name;
  	$this->base_url_dir  = $base_url_dir;
  	$this->base_file_dir = $base_file_dir;
  }
  
  function get_img_dir() {
  	return $this->base_url_dir.'/_fastweb/themes/'.$this->theme_name.'/img';
  }
  function get_css_dir() {
  	return $this->base_url_dir.'/_fastweb/themes/'.$this->theme_name;
  }
  function get_theme_dir() {
  	return $this->base_url_dir.'/_fastweb/themes/'.$this->theme_name;
  }  
  function get_lib_dir() {
  	return $this->base_url_dir.'/_fastweb';
  }
}
?>