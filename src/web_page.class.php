<?php
class web_page {
	/**
	 * @var string
	 */
	public $file = '';
	/**
	 * @var string
	 */
	public $title = '';
	/**
	 * @var string
	 */
	public $default_title = '';
	/**
	 * @var string
	 */
	public $content_file = '';
	/**
	 * Array de web_objects
	 * @var Array
	 */
	protected $webObjects = array ();
	/**
	 * Fichero a incluir como cabecera de la página.
	 * @var string
	 */
	public $header_file = 'header.inc.php';
	/**
	 * Menu de la página
	 * @var menu
	 */
	public $menu = null;
	/**
	 * Array con las urls de las hojas de estilo
	 * @var array
	 */
	public $stylesheets = array ();
	/**
	 * Array con las urls a los ficheros javascript que incluir en cabecera
	 * (sin definir medio, es decir, aplicables a todos.
	 * @var array
	 */
	public $stylesheets_media = array ();
	/**
	 * Array with CSS to apply on header, between <style> tags
	 * @var array
	 */
	public $styles = array ();
	/**
	 * @var array
	 */
	public $js_files = array ();
	/**
	 * Array de metacontenido con formato key => content
	 * @var array
	 */
	public $meta = array ();
	/**
	 * @var boolean
	 */
	public $cache_control = false;
	/**
	 * @var boolean
	 */
	public $initialised = false;
	/**
	 * Array con nombres de grupo con permiso de acceso a esta página, vacio o '*' para todos
	 * @var array
	 */
	public $user_allowed_groups = array ();
	/**
	 * Charset to send it http header
	 * @var string
	 */
	 public $charset = 'iso-8859-1';
	//--------------------------------------------------------------
	static function is_file_action() {
		
		$action_keyword = 'action';
		//$action_keyword = 'command';

		if (isset ( $_GET [$action_keyword] ) && ($_GET [$action_keyword] == 'DOWNLOAD' || $_GET [$action_keyword] == 'SHOW_IMAGE')) {
			return true;
		}
		if (isset ( $_POST [$action_keyword] ) && ($_POST [$action_keyword] == 'DOWNLOAD' || $_POST [$action_keyword] == 'SHOW_IMAGE')) {
			return true;
		}
		return false;
	}
	function init_config() {
		if ($this->initialised)
			return;
		$this->initialised = true;
		if ($this->title == '') {
			$this->title = $this->default_title;
		}
        if ( ! isset( $this->menu ) )
            $this->menu = new menu_simple();
	}
	function set_content_file($content_file) {
		$this->content_file = $content_file;
	}
	function get_content_file() {
		return $this->content_file;
	}
	//--------------------------------------------------------------
	public function print_menu() {
		if ($this->menu != null && $this->menu->menu_ui != null) {
			$this->menu->print_menu ();
		}
	}

	//--------------------------------------------------------------
	public function send_http_headers() {
		if (website::in_developer_mode()) {
			$file = ''; $line = '';
			if ( headers_sent($file, $line) ) {
				echo "**Error: Headers sent.<br />";
				var_dump($file, $line);
				die;
			}
		}
        //SET CHARSET
		header ( "Content-type: text/html; charset=".$this->charset );
        //ALLOW INTERNET EXPLORER COOKIES FROM WITHIN FRAMES TO OTHER SITES
		if (website::$ie_allow_frame_cookies) {
			header ( 'P3P: CP="CAO PSA OUR"' );
		}
        //CACHE CONTROL
		if ( ! $this->cache_control ) {
			//header("Cache-Control: no-cache");
			$offset_cache_expire = 0;
			// Don't use cache (required for Opera)
			$now = gmdate ( 'D, d M Y H:i:s' ) . ' GMT';
			@header ( 'Expires: 0' ); // rfc2616 - Section 14.21
			@header ( 'Last-Modified: ' . $now );
			@header ( 'Cache-Control: no-store, no-cache, must-revalidate' ); // HTTP/1.1
			//header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
			@header ( 'Pragma: no-cache' ); // HTTP/1.0
		} else {
			//Válido para Firefox
			$offset_cache_expire = 30;
			@header ( "Cache-Control: must-revalidate" );
			@header ( "Expires: " . gmdate ( "D, d M Y H:i:s", time () + $offset_cache_expire ) . " GMT" );
		}
        //SESSION START
        if ( ! isset( $_SESSION ) ) session_start(); //This is now automatic in PHP
        
        //if ( $this->compression ) {
        //  ob_start();
        //}
	}
	//--------------------------------------------------------------
    
    protected $html_header_printed = false;
	public function print_html_header() {
        if ($this->html_header_printed) return;
        $this->html_header_printed = true;        
		echo $this->get_html_header ();
	}
	public function get_html_header() {
		$result = "";
        
        //public $doctype = '"-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"';
        //public $htmltype = ' xmlns="http://www.w3.org/1999/xhtml"';
        //public $charset 	= 'iso-8859-1';
        //echo '<!DOCTYPE HTML PUBLIC '.$this->doctype." >\r\n";
		//if ( ! $this->cache ) {
		//	echo "\t\t<meta http-equiv=\"CACHE-CONTROL\" content=\"NO-CACHE\" />\r\n";
		//}	
		//if ( $this->favicon != '' ) {
		//	echo "\t\t<link rel=\"shortcut icon\" href=\"$this->base_url$this->favicon\" type=\"image/x-icon\" />\r\n";
		//}        
        
        //GET TITLE
		$pre_title = '';
        if (website::in_developer_mode()) $pre_title = 'Dev: ';
		if ($this->title != "") {
			$result .= "<title>" . $pre_title . $this->title . "</title>\r\n";
		} else {
			$result .= "<title>" . $pre_title . website::$default_web_page_title . "</title>\r\n";
		}
		
        //GET META TAGS
		foreach ( $this->meta as $key => $content ) {
			$result .= $this->get_html_header_meta ( $key, $content );
		}
		
        //GET CSS LINKED
		// First load all CSS so they can be loaded in parallel 		
		//if ($this->menu != null && $this->menu->menu_ui != null && $this->menu->menu_ui->stylesheet != '') {
		//	$result = $this->get_html_header_stylesheet ( $this->menu->menu_ui->stylesheet );
		//}
        
		foreach ( $this->stylesheets_media as $media => $list ) {
			if ( is_array ( $list ) && ! empty ( $list ) ) {
				foreach ( $list as $sheet ) {
				    $result .= $this->get_html_header_stylesheet ( $sheet, $media );
				}
			}
		}
        
		foreach ( $this->stylesheets as $sheet ) {
			$result .= $this->get_html_header_stylesheet ( $sheet );
		}
		
        //GET INLINE CSS HEADER STYLES
		if (count ( $this->styles ) > 0) {
			$result .= "\t\t<style type=\"text/css\">\r\n";
			foreach ( $this->styles as $key => $style ) {
				$result .= $style . "\r\n/* **** Style: $key **** */\r\n";
			}
			$result .= "\r\n\t\t</style>\r\n";
		}
		
        //GET WEBOBJECTS HEADER CSS LINKED AND INLINE STYLES
		foreach ( $this->webObjects as $wo ) {			
			$ss = $wo->get_css_files_media_array();
			foreach ( $ss as $media => $sheet ) {
                if (is_array($sheet)) {
                    foreach($sheet as $media2 => $sheet2) {
                        $result .= $this->get_html_header_stylesheet ( $sheet2, $media2 );
                    }
                } else {
				    $result .= $this->get_html_header_stylesheet ( $sheet, $media );
                }
                
			}
		}
		
		//GET JS LINKED
		foreach ( $this->js_files as $file ) {
			$result .= $this->get_html_header_js ( $file );
		}
        
        //GET WEBOBJECTS JS
		foreach ( $this->webObjects as $wo ) {			
			$js_files = $wo->get_js_files_array();
			foreach ( $js_files as $file) {
				$result .= $this->get_html_header_js ( $file );
			}
			$result .= $this->getWebObjectHtmlHeader ( $wo );
		}

        //TODO: Sanitize header file
        
        if ( $this->header_file != '' && file_exists( $this->header_file ) ) {
            $result .= file_get_contents( $this->header_file );
        }

		return $result;
	}
	//-------------------------------------
    public function include_main_content() {
        if ( ! website::user_is_allowed() ) {
            echo "<h1>Acceso no autorizado</h1>";
            echo "El acceso a esta zona requiere estar registrado en el sistema, ";
            echo "así como pertenecer a un grupo de usuarios concreto que tenga ";
            echo "permiso de acceso.<br /><br />";
        } else {
            include($this->content_file); 
        }
    }
    //-------------------------------------
    
	public function set_user_allowed_groups($array_of_user_allowed_groups) {
		if (!is_array($array_of_user_allowed_groups)) throw new ExceptionDeveloper('Array expected');
		$this->user_allowed_groups = $array_of_user_allowed_groups;
		if ( ! website::user_is_allowed() ) {
			echo "<h1>Acceso no autorizado</h1>";
   			echo "El acceso a esta zona requiere estar registrado en el sistema, ";
      		echo "así como pertenecer a un grupo de usuarios concreto que tenga ";
      		echo "permiso de acceso.<br /><br />";
            if ( website::in_developer_mode() ) {
                echo "Usuario actual: <b>".website::$user->get_user_id()."</b><br />";
                echo "Grupo actual: <b>";
                foreach (website::$user->groups as $g) { echo $g." &nbsp;&nbsp;"; };
                echo "</b><br />Grupos permitidos: <b>";
                foreach ($this->user_allowed_groups as $g) { echo $g." &nbsp;&nbsp;"; };
                echo "</b><br /><br />";
            }
      		echo "<a href=\"".website::$user->loginout_failure_url."\">Iniciar sesión</a>";
      		die;
      		//TODO: Implement this so we don't break completely page flow
		}
	}
	public function get_user_allowed_groups_string() {
	   	if (count(website::$current_page->user_allowed_groups)==0) return '';
	    $t = '';
		foreach ($this->user_allowed_groups as $g) {
			$t = $t.', '.$g;
		}	
		return substr($t,2);
	}
	//-------------------------------------
	public function getHtmlPostBodyIni() {
		$result = "";
		foreach ( $this->webObjects as $wo ) {
			$result .= $this->getWebObjectHtmlPostBodyIni ( $wo );
		}
		return $result;
	}
	public function getHtmlPrePageEnd() {
		$result = "";
		foreach ( $this->webObjects as $wo ) {
			$result .= $this->getWebObjectHtmlPrePageEnd ( $wo );
		}
		return $result;
	}
	//------------------------------------
    /**
     * Adds CSS code to header of webpage.
     * Returns self instance
     * @param $css_style_code string CSS code for header
     * @return web_page
     */
    public function add_style($css_style_code) {
        //TODO: Valide correct CSS (or at least filter the use of HTML tags)
        $css_style_code = str_ireplace('<style type="text/css">','',$css_style_code);
        $css_style_code = str_ireplace('</style>','',$css_style_code);
        $this->styles[] = $css_style_code;
        return $this;
    }
	public function add_stylesheet($url, $media = 'all') {
        if ( ! isset($this->stylesheets_media[$media] ) ) {
           $this->stylesheets_media[$media] = array();
        }
        if ( !array_search ( $url, $this->stylesheets_media[$media] ) ) {
        	$this->stylesheets_media[$media][] = $url;
        
        }
	}
	public function add_js_file($url) {
		if (! array_search ( $url, $this->js_files )) {
			$this->js_files [] = $url;
		}
	}
	public function add_meta($key, $value) {
		$this->meta [$key] = $value;
	}
	public function add_web_object(iweb_object $web_object) {
	   //If object is already present, we omit it
       if (isset($this->webObjects[get_class($web_object)])) return $web_object;
	   $this->webObjects [get_class($web_object)] = $web_object;
	   return $web_object;
	}
	//-------------------------------------
	private function get_html_header_stylesheet($css_url, $media = 'screen') {
		if ( ! is_string($media) ) $media = 'screen';
		return "\t<link media=\"$media\" href=\"$css_url\" type=\"text/css\" rel=\"stylesheet\" />\r\n";
	}
	private function get_html_header_js($js_url) {
		return "\t<script src=\"" . $js_url . "\" language=\"JavaScript\" type=\"text/javascript\"></script>\r\n";
	}
	private function get_html_header_meta($meta_name, $meta_content) {
		return "\t<meta name=\"$meta_name\" content=\"$meta_content\" />\r\n";
	}
	
	//------------------------------------
	private function getWebObjectHtmlHeader(iweb_object $web_object) {
		return $web_object->get_html_header();
	}
	private function getWebObjectHtmlPostBodyIni(iweb_object $web_object) {
		return $web_object->get_html_post_body_ini();
	}
	private function getWebObjectHtmlPrePageEnd(iweb_object $web_object) {
		return $web_object->get_html_pre_page_end();
	}


}

?>
