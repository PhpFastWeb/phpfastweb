<?php


class web_object {
  public $stylesheets = array();
  public $stylesheets_lib = array();
  public $javascripts = array();
  public $javascripts_lib = array();
  public $javascripts_absolute = array();
  public $css_header = '';
  public $javascript_code_header = '';
  public $javascript_code_post_body = '';
  public $html_code_post_body = '';
  public $js_body_events = array();
  
  //-- Inicialización
  public $initialised = false;
  function init() {
    if ( $this->initialised ) return true;
    $this->initialised = $this->init_config();
  }
  function init_config() {
    return true;
  }
  //-- Despliegue
  function render() {
    return '';
  }
}
