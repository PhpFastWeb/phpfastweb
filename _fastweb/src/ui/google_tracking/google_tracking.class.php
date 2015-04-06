<?php

class google_tracking extends aweb_object implements iweb_object {
	protected $track_id = '';
	public function set_tracking_id($tracking_id) {
		$this->track_id = $tracking_id;
	}
	protected $non_tracking_domains = array('localhost','127.0.0.1');
	public function set_non_tracking_domains($domains_array) {
		$this->non_tracking_domains = $domains_array();
	}
	public function add_non_tracking_domain($domain) {
		$this->non_tracking_domains[] = $domain;
	}
	public function get_html_pre_page_end() {
		
		if ( in_array($_SERVER['SERVER_NAME'],$this->non_tracking_domains ) ) return '';
		if ($this->track_id=='') return '';
		
		$result = '';
  		$result .= "<script type=\"text/javascript\">\r\n";
		$result .= "var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");\r\n";
		$result .= "document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));\r\n";
		$result .= "</script>\r\n";
		$result .= "<script type=\"text/javascript\">\r\n";
		$result .= "try {\r\m";
		$result .= "var pageTracker = _gat._getTracker(\"".$this->track_id."\");\r\n";
		$result .= "pageTracker._trackPageview();\r\n";
		$result .= "} catch(err) {}\r\n";
		$result .= "</script>\r\n";	
		return $result;
	}
	
}