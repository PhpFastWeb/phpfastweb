<?php

class cache {
	

	/**
	 * Check if files exists before each "include" call
	 */
	public static $check_files = true;
	/**
	 * Valid filename formats for PHP files to search
	 */
	public static $fileNameFormats = array(
		  '%s.class.php',
		  '%s.interface.php'
		);
	//-------------------------------------------------
	
	private static $signature = '';
	private static $build = 'src';
	private static $autoload_dirs = array();
	
	private static $cache_dir = '/../../_cache/';
	private static $cache_autoload = array();

	//-------------------------------------------------	
	public static function set_signature($signature) {
		self::$signature = $signature;
	}
	public static function get_signature() {
		return self::$signature;
	}	
	public static function set_build($build) {
		self::$build = $build;
	}
	public static function get_build() {
		return self::$build;
	}
	//Inicialicaci?n y creaci?n
	public static function init_config() {
		$this->check_signature();
	}
	
	public static function check_signature() {
		return true;
		//@todo: hacer depender los valores de cache en funci?n de la signatura
		//if (empty(self::$signature)) {
		//	echo "**Error: Configuraci?n de signature de cache vac?a.";
		//	die;
		//}
	}
	// Singleton
	private static $singleton = null;
	private function __construct() {
		//Privado para que no pueda ser llamado externamente.
	}
	public static function create() {
		if ( self::$singleton == null ) {	
			self::$singleton = new cache();
		} 
		return cache::$singleton;
	}
	//A?adir carpetas a autoload
	public static function add_dir_to_autoload($dirname) {
		self::$autoload_dirs[] = $dirname;
	}
	private static function save_autoload_cache() {
		# save it to file 
        if ( ! is_dir( dirname(__FILE__) . self::$cache_dir ) ) die ("Cache directory not created");
		$filename = dirname(__FILE__) . self::$cache_dir . "autoload.cache";
		$fp = fopen($filename, 'w+') or die("I could not open $filename.");
		fwrite($fp, serialize(self::$cache_autoload)); 
		fclose($fp);
	}
	private static function load_autoload_cache() {
		$filename = dirname(__FILE__) . self::$cache_dir . "autoload.cache";
		if (!file_exists($filename)) { 
			// Si no existe el fichero, creamos un "escaneo" inicial
			//echo "Creating cache";
			$subdirs = array_merge(self::$autoload_dirs,array('./'));
			self::recursiveFileSearchDirectories($subdirs,self::$fileNameFormats);
			self::$changed = true;
			//self::save_autoload_cache();
			
		} else {
			try {
				self::$cache_autoload = unserialize(@file_get_contents($filename));
			} catch(Exception $e) { 
				self::$cache_autoload = array();
			}
		}
	}
	private static $changed = false;
	private static function set_autoload_cache($class_name,$relative_path) {
		if ( ! isset(self::$cache_autoload[$class_name]) || self::$cache_autoload[$class_name] != $relative_path) {
			self::$changed = true;
			self::$cache_autoload[$class_name]=$relative_path;
		}
	}
	function __destruct() {
		if (self::$changed) {
			self::save_autoload_cache();
		}
	}
	private static function invalidate_cache() {
		$filename = dirname(__FILE__) . self::$cache_dir . "autoload.cache";
		$fp = fopen($filename, 'w+') or die("I could not open $filename."); 
		fwrite($fp, serialize(array())); 
		fclose($fp);
	}
	//-----------------------------------------------------------------------------
	//Autoload
	public static function autoload($class_name) {
		if( class_exists($class_name,false) || interface_exists($class_name,false) ) {
		    return;
		}
		
		self::check_signature();

		//Cargamos cache si está vacia
		if (count(self::$cache_autoload) == 0 ) {
			self::load_autoload_cache();
		}
		
		//Por seguridad, eliminamos carácteres ilegales de la clase
		$original_class_name = $class_name;
		$class_name = str_replace('.','',$class_name);
		$class_name = str_replace('\\','',$class_name);
		// this is to take care of the PEAR style of naming classes
		//$class_name = str_ireplace('_', '/', $class_name);

		//Si la clase está definida en la caché, la cargamos
		$basedir = dirname(__FILE__).'/';
		if (isset(self::$cache_autoload[$class_name])) {
			if (self::$check_files && is_file($basedir.self::$cache_autoload[$class_name])) 
				@include_once($basedir.self::$cache_autoload[$class_name]);
			if( class_exists($original_class_name,false) ||
	    		interface_exists($original_class_name,false) 
	    		) {
	    			return; //Éxito
	    	} 
		}
		//buscamos el fichero

		$subdirs = array_merge(self::$autoload_dirs,array('./'));		
		


		//TODO: Recursive depth limit.
		
		$path = self::recursiveFileSearchDirectories($subdirs,self::$fileNameFormats,$class_name);
		if ($path !== false) {
			include_once dirname(__FILE__).'/'.$path;
		    if( class_exists($original_class_name,false) ||
		    	interface_exists($original_class_name,false)
		    ) {
		    	self::set_autoload_cache($original_class_name,$path);
		    	
		    	//Si la clase existía erroneamente enlazada, esto corregirá la referencia
		    	//self::save_autoload_cache();
		        return;
		    } else {
				//throw new ExceptionDeveloper("**Error: Archivo para la clase ".$class_name." incorrecto: ".$path);
				//Si se usa PHP 5.3- no se lanzará la excepción:
				//No se invalida la caché para no penalizar el resto de la aplicación hasta
				//que se pueda ubicar la clase
				echo "<pre>";
				//var_dump(debug_backtrace());
				die("**Error: No se encontró el archivo para la clase/interfaz<br />".
					"clase: ".$original_class_name."<br />".
					"fichero buscado: ".$class_name."<br />");
			}
		}
		
		//throw new ExceptionDeveloper("**Error: No se encontró la clase definida como : ".$class_name);
		//Si se usa PHP 5.3- no se lanzará la excepción:
		if ( $original_class_name != "BreadCrumbStack" ) {
			return;
		
			$trace = debug_backtrace();
			echo "\r\n**Error: No se encontró el archivo para la clase/interfaz<br />".
				 "clase: ".$original_class_name."<br /><pre>";
			var_dump($trace[2]);
			//var_dump($trace);
			die;
				//"fichero llamante: ".$trace[1]["file"]);
		}

	}
	
	private static function recursiveFileSearchDirectories($subdirs,$fileNameFormats,$className='') {
		//Buscamos en cada uno de los directorios especificados, en profundidad
		foreach($subdirs as $directory) {
			$path = self::recursiveFileSearch($directory,$fileNameFormats,$className);
			if ($path !== false) {
				return $path;
			}
		}
		return false;
	}
	
	private static function recursiveFileSearch($dir,$fileNameFormats,$className='') {
		
		$basedir = dirname(__FILE__).'/';
		//Comprobamos si existe en directorio actual
		if ( $className != '' ) {
			foreach($fileNameFormats as $fileNameFormat) {
				$path = $basedir . $dir . sprintf($fileNameFormat, $className);
				if (file_exists($path)) {
					return $dir . sprintf($fileNameFormat, $className);
				}
			}	
		}
		
		//Buscamos en subdirectorios del directorio actual
		$subdirs = array();

		$dh  = @opendir( $basedir . $dir );
		if (false===$dh) {
			die('**Error: No se puede abrir el directorio "'. $basedir . $dir.'"');
		}
		while (false !== ($filename = readdir($dh)) ) {
			if ($filename != "." && $filename != ".." && $filename != ".svn") {
				if ( is_dir( $basedir . $dir . $filename. "/") ) {
								
						$subdirs[] = $dir.$filename."/";
					
				} else {
					//Si los tipos de archivos coinciden, los añadimos a la cache
					foreach($fileNameFormats as $fileNameFormat) {
						$ext = str_replace('%s.','',$fileNameFormat);
						if (strpos($ext,$filename)!==false) {
							//Añadimos el fichero a la cache
							$c = str_replace($ext,'',$filename);
							self::set_autoload_cache($c,$dir.$filename);
						}
					}
				}
			}
		}
		if ( count($subdirs) > 0 ) {
			return self::recursiveFileSearchDirectories($subdirs,$fileNameFormats,$className);
		}
		return false;

	}
	//----------------------------------------------------------------------------


}


