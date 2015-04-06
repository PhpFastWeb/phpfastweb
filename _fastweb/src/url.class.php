<?php

class url {
	/**
	 * Array con las variables a desplegar en la url
	 * @var array
	 */
	public $vars = array();
	/**
	 * Separador entre parámetros intermedios a usar en la URL
	 * @var string
	 */
	public static $url_separator = '&';
	/**
	 * Separador de comienzo entre parámetros de la URL
	 * @var string
	 */
	public static $url_separator_starter = '?';
	
	public $relative = true;
	public $protocol = '';
	public $domain = '';
	public $url_base = '';
	
	private $url;
	private $extra_vars_def="";

	public $initialised = false;
	public function init_config() {
		if ( $this->initialised ) return;
		if ( ! $this->relative ) {
			$this->set_url_base_absolute();
		} else {
			$this->set_url_base_relative();
		}
		
	}
	public function __construct( $url_base = '' ) {
		$this->url_base = $url_base;
	}
	public static function set_url_separator($separator_str) {
		self::$url_separator = $separator_str;
	}
	private function set_url_base_absolute() {
		throw new ExceptionDeveloper("Not implemented");
		//--
		if ( $this->protocol == '' ) $this->protocol = 'http';
		if ( $this->domain == '' ) {
			$this->domain = '';
		}
		if ( $this->url_base == '' ) {
			$this->url_base = $this->protocol.'://'.$this->domain;
		}
		
	}
	private function set_url_base_relative() {
		if ($this->url_base == '' ) {
			$this->url_base == html_template::get_php_self();
		}
		
	}
	
	public function set_var($var_name, $var_value) {
		$this->vars[$var_name] = $var_value;
	}
	public function add_vars_def($vars_string) {
		$this->extra_vars_def = $vars_string;
	}
	public function get_var($var_name) {
		return $this->vars[$var_name];
	}
	public function __toString() {
		$this->init_config();
		$s = self::$url_separator;
		$query = '';
		foreach($this->vars as $key => $value) {
			$query .= $key . '=' . $this->encode_var($value) . $s;
		}
		if ( $this->extra_vars_def != '' ) {
			$query .= $this->extra_vars_def;
		} else {
			$query = substr($query,0,-strlen($s));
		}
		$query = htmlentities($query);
		if ($query != '' ) {
			$result = $this->url_base . self::$url_separator_starter . $query;
		} else {
			$result = $this->url_base;
		}
		return $result;
	}
	public function get_a_link($link_text,$extra='') {
		$result = ''; $e = ''; 
		if ($extra != '') {
			$e = ' $extra ';
		} 
		$result = '<a href="'.$this->__toString().'"'.$e;
        if ($this->access_key != '' ) $result .= ' accesskey="'.$this->access_key.'" ';
        $result .= '>'.$link_text.'</a>';
		return $result;
	}
	protected $access_key = '';
	public function set_access_key($access_key) {
		$this->access_key = $access_key;
	}   
	public function get_access_key() {
		return $this->access_key;
	} 
	static public function encode_var($value) {
		
		//return rawurlencode(utf8_encode($value));
		return urlencode($value);
		
	}
	static private function decode_var($value) {
		//return utf8_decode(rawurldecode($value));
		return urldecode($value);
		
	}
	
	/**
	 * Lee una variable desde la url actual. La devuelve decodificada.
	 *
	 * @param string $var_name
	 */
	static public function read($var_name) {
		if (isset($_GET[$var_name])) {
			return self::decode_var($_GET[$var_name]);
		}
	}
	//--------------------------------------------------------------------
    public static function get_request_url() {
        return html_template::get_request_url();
    }
	//-------------------------------------------------------------------
	
	/**
	 * Returns true if both URL strings are 'equivalent'.
	 * It disregars endings with '/', '?', or 'index.php/htm/html'.
	 * @param $url_string1 string
	 * @param $url_string2 string
	 * @return bool
	 */
	public static function compare_url_strings($url_string1,$url_string2) {
		$url1 = $url_string1; $url2 = $url_string2;
		
		//Si una de las dos direcciones termina únicamente en "?", lo quitamos
        if ( substr($url1,strlen($url1)-1,1) == '?' ) {
           $url1 = substr($url1,0,-1);
        }
        if ( substr($url2,strlen($url2)-1,1) == '?' ) {
           $url2 = substr($url2,0,-1); 
        }
        
        //Si alguna le falta protocolo y servidor, lo añadimos
        if ( substr($url1,0,7) != 'http://' && substr($url1,0,8) != 'https://') {
        	$url1 = ( $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1' ? 'http' : 'https' ).'://'.$_SERVER['SERVER_NAME'].$url1;
       	}
       	if ( substr($url2,0,7) != 'http://' && substr($url2,0,8) != 'https://') {
        	$url2 = ( $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1' ? 'http' : 'https' ).'://'.$_SERVER['SERVER_NAME'].$url2;
       	}

		//Hacemos equivalente el terminar en "/", "/index.php", "/index.html", etc
		if (substr($url1,-1*strlen("index.php")) == "/index.php") {
			$url1 = substr($url1,0,-1*strlen("/index.php"));
		}
		if (substr($url2,-1*strlen("index.php")) == "/index.php") {
			$url2 = substr($url2,0,-1*strlen("/index.php"));
		}
		if (substr($url1,-1*strlen("index.htm")) == "/index.htm") {
			$url1 = substr($url1,0,-1*strlen("/index.php"));
		}
		if (substr($url2,-1*strlen("index.htm")) == "/index.htm") {
			$url2 = substr($url2,0,-1*strlen("/index.php"));
		}
		if (substr($url1,-1*strlen("index.html")) == "/index.html") {
			$url1 = substr($url1,0,-1*strlen("/index.html"));
		}
		if (substr($url2,-1*strlen("index.html")) == "/index.html") {
			$url2 = substr($url2,0,-1*strlen("/index.html"));
		}
		//Quitamos la posible barra "/" final
		if (substr($url1,-1*strlen("/")) == "/") {
			$url1 = substr($url1,0,-1*strlen("/"));
		}
		if (substr($url2,-1*strlen("/")) == "/") {
			$url2 = substr($url2,0,-1*strlen("/"));
		}		
		
		//Si iguales, devolver verdadero
		if ($url1==$url2) {
			return true;
		}
		
		//Strpos no puede usar un needle vacio, lo comparamos a mano
		if ($url2 == "") {
			if ($url1 == "") { 
				return true; //No debería ocurrir por el if anterior
			} else {
				return false;
			}
		}
		
		//Buscamos la coincidencia
		$i = strpos($url1,$url2);
		if ($i !== false && $i > 0 ) {
			//una URL está incluida dentro de la otra
			if ($url1[$i-1] =='/' ) {
				return true;
			}
		}

		return false;
	}
}


?>