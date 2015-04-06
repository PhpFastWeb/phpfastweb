<?php
final class website {
    /**
	 * Directorio base del website dentro de la estructura de ficheros del disco duro del servidor.
	 * Si no se especifica, se averigua automï¿½ticamente al llamar al mï¿½todo init_config().
	 * @var string
	 */
	public static $base_dir = '';
	/**
	 * URL base del website, a partir de la cual se replica la estructura contenida en base_dir
	 * Si no se especifica, se averigua automï¿½ticamente al llamar al método init_config().
	 * @var string
	 */
	public static $base_url = '';
	/**
	 * Directorio base del framework
	 * @var string
     */	 
	public static $lib_base_dir = '';
	/**
	 * URL base del framework, para acceso a elementos pÃºblicos y javascript
	 * @var string
	 */
	public static $lib_base_url = '';
	/**
	 * Indica si se está accediendo desde la propia mÃ¡quina servidor en local
	 * @var string
	 */
	public static $local_access = null;
	/** 
	 * Página actual
	 * @var web_page
	 */
	public static $current_page = null;
	/** 
	 * Base de datos de la que obtener la informaciÃ³n
	 * @var database 
	 */
	public static $database = null;
	/** 
	 * Usuario actual 
	 * @var user 
	 */
	public static $user = null;
	/**
	 * Especifica si se ha inicializado la clase
	 * @var string
	 */
	public static $initialised = false;
	/**
	 * Espeficia la ubicación relativa de las clases php 
	 * @var string
	 */
	public static $class_base_dir = '/lib/src';
	/**
	 * Título por defecto a aplicar a cada página si no se especifica uno propio.
	 * @var string
	 */
	public static $default_web_page_title = '';
	
	/**
	 * Especifica si añadir automáticamente el header para que Internet Explorer
	 * admita que se almacenen cookies desde este sitio si está en un frame
	 */
	public static $ie_allow_frame_cookies = false;
	
	
	/**
	 * Directory to place upload, relative to base dir of website
	 * @var string
	 */
	public static $upload_dir = '/__upload/';
	
    /**
     * Directory to load layout from, relative to base dir of website
     */
    public static $layout_dir ='/_inc/';
    
	/**
	 * @var theme_config
	 */
	public static $theme = null;
	
	//---
	
	private static $live_server = true;
	
	private static $developer = false;
	
	public static $layout_loaded = false;
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	
	/**
	 * Constructor privado para eviar instanciar la clase
	 */
	private function __construct() {
	}
	/**
	 * Inicializa los miembros de la clase
	 */
	static public function init_config() {
		if (self::$initialised) return;
		self::$initialised = true;
		self::init_dirs();
		self::init_local_access();
		self::$current_page = new web_page();
		self::$current_page->default_title = self::$default_web_page_title;
		if (self::$database != null) {
			self::$database->init_config();
		}
		if ( self::$user != null ) {
			self::$user->load();
			self::$current_page->cache_control = self::$user->is_logged_in();
		}
		
		if ( ! self::$developer ) {
			//We set exception handler to display error user form, for exception not definded in this framework
			set_exception_handler('handleShutdown');
		}
	}
    
    
    //--------------------------------------------------------------------
    protected static $wip_start = null;
    protected static $wip_end = null;
    
    public static function set_wip_dates($start, $end) {
        self::$wip_start = $start;
        self::$wip_end = $end;
    }
    public static function set_wip_start($start) {
        self::$wip_start = $start;
    }
    public static function set_wip_end($end) {
        self::$wip_end = $end;
    }
    public static function get_wip_start() {
        return self::$wip_start;
    }
    public static function get_wip_end() {
        return self::$wip_end;
    }
    public static function is_near_wip($seconds_margin) {
		$start = strtotime(self::$wip_start);
		//$end   = strtotime(self::$wip_end);
		$now   = time();
        
		$wip = false;
		if ( (($start - $seconds_margin) <= $now ) AND ($now <= $start )) {
			return true;
		}
        return false;
    }
    //-------------------------------------------------------------------------------
	/**
	 * Analiza si se trata de acceso local
	 */
	static private function init_local_access() {
		if ( self::$local_access == null ) {
			if (isset($_SERVER['SERVER_ADDR']) && isset($_SERVER['SERVER_NAME']) && isset($_SERVER['REMOTE_ADDR'])) {
				self::$local_access = ( 
					$_SERVER['SERVER_ADDR'] == '127.0.0.1' && 
					$_SERVER['SERVER_NAME'] == 'localhost' && 
					$_SERVER['REMOTE_ADDR'] == '127.0.0.1' );
			} else {
				//No podemos asegurar que el acceso sea local
				self::$local_access = false;
			}
		}	
	}
		
	static public function init_dirs($file_constant='') {
		//NOTA:
		//Bajo Linux, __FILE__ puede apuntar a un alias en el que se puede acceder al directorio,
		//pero que no es comparable con la parte común de la URL actual.
		//Para ello hay que usar $_SERVER["SCRIPT_FILENAME"], pero por otro lado, éste último
		//puede llevar a una ubicación similar a la parte pública de la URL, pero donde
		//con esa ruta no de permisos para escribir.
		//Además, éste cambia dependiendo de a qué script se esté llamando.
		
		
		//Configuramos los directorios si no lo estan ya
		
		//BASE DIR
		if ( self::$base_dir == '' ) {
			//Asumimos que este fichero se encuentra en {server_base_dir}{class_base_dir}/
			//(da igual que sea /src o /bin porque solo se tiene en cuenta el numero
			//de directorios, no el nombre
			$sbd = explode( '/' , dirname(__FILE__) );
			$sbd2 = explode( "\\" , dirname(__FILE__) );
			if ( count($sbd2)>count($sbd) ) $sbd = $sbd2;
			$cbd = explode('/',self::$class_base_dir);
			$sbd = array_chunk( $sbd , count($sbd) - count($cbd) +1 ); //+1 porque el primero de cbd esta en blanco
			$sbd = $sbd[0]; //array_chunk divide en dos arrays dentro del array
			//TODO: hacer mejor el comparar dos directorios de la dirección
			self::$base_dir = implode('/',$sbd);
		}
		
        $document_root = $_SERVER['DOCUMENT_ROOT'];
        
        //Some linux server returns document_root lacking some directorios in the beginning
        //      base_dir : /usr/home/iditconsultores/www/example_web_folder
        // document_root : /home/iditconsultores/www

		//Others do the opposite (1&1)
		//      base_dir : /homepages/6/d284652169/htdocs/culturapreventiva
		// document_root : /kunden/homepages/6/d284652169/htdocs

		//Normal behaviour (Windows 7 server)
        //     base_dir  : C:/Program Files (x86)/EasyPHP-12.1/www/medicion_cultura_preventiva
        // document_root : C:/Program Files (x86)/EasyPHP-12.1/www

		//die ( "document_root: $document_root<br />base_dir: ".self::$base_dir);

		//     strpos($HAYSTACK       , $NEEDLE        );
		$pos = strpos(self::$base_dir , $document_root );
        //$pos = strpos($document_root, self::$base_dir);
		
        if ( $pos === false ) {
			//This means document_root is not included in base_dir

			//There may be one element missing from document_root, or one missing from base_dir
			//$t1 = $document_ro
			$t_dr = explode('/',trim($document_root,'/'));
			$t_bd = explode('/',trim(self::$base_dir,'/'));

			//TODO: This can fail with repeated named directories like /var/var (very rare)
			if ( $t_dr[0] == $t_bd[1] ) {
				//base_dir has one too many directory
				$document_root = '/'.$t_bd[0].'/'.trim($document_root,'/');
			} else if ( $t_dr[1] == $t_bd[0] ) {
				//document_root has one too many directory (1&1)
				self::$base_dir = '/'.$t_dr[0].'/'.trim(self::$base_dir,'/');
			} else {
				throw new ExceptionDeveloper('Couldn\'t determine base_dir');
			}
			
			//self::$base_dir = '/'.$token[1].self::$base_dir;
			
			//if ( false === strpos($document_root, self::$base_dir) < 1) throw new ExceptionDeveloper('Could not set base_dir');
		} 
        
		//BASE URL
		if ( self::$base_url == '' ) {
			$bd = explode('/',self::$base_dir);
			$dr = explode('/',$document_root);
			
			//Quitamos los elementos "en blanco"
			$bd_trim = array();
			foreach($bd as $t) {
				if ($t != '' ) $bd_trim[] = $t;
			}
			$dr_trim = array();
			foreach($dr as $t) {
				if ($t != '' ) $dr_trim[] = $t;
			}
			
			$ini = count($dr_trim);
			
			$bu = array_slice($bd_trim,$ini);
			self::$base_url = implode('/',$bu);
			
			//Si no está en blanco, añadimos barra al principio y quitamos al final
			if ( self::$base_url != '' ) {
				self::$base_url = '/' . self::$base_url;
				if (substr(self::$base_url,-1)=='/') $b=substr(self::$base_url,0,-1);
			} 
			
		}
		//Comprobamos si base_url le falta "/" incial (comparación con linux, para casos url añadida a mano)
		if (substr(self::$base_url,0,1) != "/" && self::$base_url != '') {
			self::$base_url = "/". self::$base_url;
		}	
		
		//LIB BASE DIR
		if ( self::$lib_base_dir == '' ) {
			self::$lib_base_dir = clean_path(dirname(__FILE__)."/../" );
		}
		if ( self::$lib_base_url == '' ) {
			self::$lib_base_url = self::$base_url . "/_fastweb/src";
			//self::$lib_base_url =  str_replace('\\','/',substr( dirname($_SERVER["SCRIPT_FILENAME"]),strlen($document_root) ));
		}
		
		//Comprobamos si no se tratan de rutas windows, y si le faltan "/" inicial, hay que añadirlo
		if (substr(self::$base_dir,1,1) != ":" && substr(self::$base_dir,0,1) != "/" ) {
			self::$base_dir = "/". self::$base_dir;
		}
		if (substr(self::$lib_base_dir,1,1) != ":" && substr(self::$lib_base_dir,0,1) != "/" ) {
			self::$lib_base_dir = "/". self::$lib_base_dir;
		}		
		if (substr(self::$lib_base_url,0,1) != "/" ) {
			self::$lib_base_url = "/". self::$lib_base_url;
		}
		
		
	
	}	
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	static public function set_config_var($name, $value) {
		if (!isset(self::${$name})) throw new ExceptionDeveloper('Propertie $name nonexistant in '.__CLASS__);	
		//TODO: Comprobar permisos de acceso de forma central
		switch($name) {
			case 'live_server':
				self::${$name} = $value;
				break;
			default:
				 throw new ExceptionDeveloper('Propertie $name not accesible in '.__CLASS__." method ".__METHOD__);	
				 break;
		}
		return $value;
	}	
	static public function get_config_var($name) {
		if (!isset(self::${$name})) throw new ExceptionDeveloper('Propertie $name nonexistant in '.__CLASS__);	
		//TODO: Comprobar permisos de acceso
		switch($name) {
			case 'live_server':
				return self::${$name};
				break;
			default:
				 throw new ExceptionDeveloper('Propertie $name not accesible in '.__CLASS__." method ".__METHOD__);
				 break;
		}
		return false;
	}
	static public function user_is_allowed() {
		//Si comprobamos que no hay restriccion, está permitido
		if ( ! isset( self::$current_page->user_allowed_groups ) ||
			count( self::$current_page->user_allowed_groups )==0 ||
			in_array('*',self::$current_page->user_allowed_groups) ) {
			return true;
		}
		//Luego si hay restricción, entonces, si no hay usuario, no esta permitido
		if ( ! isset(self::$user) || self::$user==null )  {
			return false;
		}
		//Por último, comprobamos con las restricciones de la página, el grupo del usuario

		return count( array_intersect( 
			self::$current_page->user_allowed_groups, 
			self::$user->groups ) ) >0;
	
	}
	static public function is_mobile_browser($min_level=0) {
		$mobile_browser = '0';
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
		    $mobile_browser=0;
		    return $mobile_browser;
		}
		if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		    $mobile_browser++;
		    if ($mobile_browser > $min_level) return $mobile_browser;
		}

		if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		    $mobile_browser++;
		    if ($mobile_browser > $min_level) return $mobile_browser;
		}    

		
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
		$mobile_agents = array(
		    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
		    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
		    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
		    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
		    'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
		    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
		    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
		    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
		    'wapr','webc','winw','winw','xda','xda-');
		 
		if(in_array($mobile_ua,$mobile_agents)) {
		    $mobile_browser++;
		    if ($mobile_browser > $min_level) return $mobile_browser;
		}
		
		if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
		    $mobile_browser++;
		    if ($mobile_browser > $min_level) return $mobile_browser;
		}	
	}
	public static function in_developer_mode() {
		return self::$developer;
	}
	public static function set_developer_mode($in_dev_mode=true) {
		self::$developer = $in_dev_mode;
		if (self::$developer) {
			ini_set('display_errors', 1); 
			ini_set('log_errors', 1);
			ini_set('track_errors', 1);  
			ini_set('error_log', dirname(__FILE__) . '/phperror.log'); 
			ini_set('error_reporting',E_ALL | E_COMPILE_ERROR | E_COMPILE_WARNING | E_STRICT);
			//error_reporting(2147483647); 
			//error_reporting(E_ALL && E_COMPILE_ERROR && E_COMPILE_WARNING && E_STRICT);
		}
		
	}
	
	public static function load_layout($layout_file) {
		if (self::$layout_loaded) return;
		self::$layout_loaded = true;
		
		if ( empty ($layout_file) ) {
			throw new ExceptionDeveloper("Layout file not specified");
		}
		if ( empty(self::$current_page->content_file)) {
			$trace = debug_backtrace();
			self::$current_page->content_file = $trace[0]["file"];
			if (empty(self::$current_page->content_file)) {
				throw new ExceptionDeveloper('Calling file could not be automatically determined, set it on website::$current_page->file_contents');
			}
		}
		//Does the layout include base path?
        if ( substr($layout_file,0,1)=='/' || substr($layout_file,1,1)==':' ) {
            //TODO: Better checking if root path
            $full_layout_file = $layout_file;
        } else {
            $full_layout_file = self::$base_dir.self::$layout_dir.$layout_file;
        }
        if ( ! is_file($full_layout_file) ) throw new ExceptionDeveloper('Layout file not found: '.$full_layout_file.'<br />CWD: '.getcwd());
        
        /* We prepare some things for the layout */
        
        if ( isset( self::$current_page->menu ) ) { self::$current_page->add_web_object( website::$current_page->menu ); }
        
        require_once($full_layout_file);
	}
}
?>