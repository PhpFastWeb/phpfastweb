<?php
//require_once(dirname(__FILE__)."/../require_once_all_class.inc.php");
class image {

	public $dir;
	
	public $filename;
	public $height;
	public $width;
	
	public $type=''; // 'jpg','gif',bmp';
	
	
	public $max_height;
	public $max_width;
	
	public $thumb_cache_dir = '';		
	public $thumb_cache_dir_sufix = "thumb_cache";

	public $thumb_max_width = 100;
	public $thumb_max_height = 100;	
	public $thumb_width;
	public $thumb_height;
	public $thumb_background_color = "FFFFF";
	public $thumb_align = "left";
	//var $thumb_square = true;
	
	public $initialized = false;
	
	public $view_mode = 'thumb';
	public $view_link = true;
	
	public $pass_dir_in_url = true;
	public $generator_url = NULL;	
	
	function __construct( $filename='' , $dir='' ) {
		$this->dir = $dir;
		$this->filename = $filename;
		if ( is_null($this->generator_url) ) {
			$this->generator_url = website::$lib_base_url."/public/get_thumb.php";
		}
		
	}
	/**
	 * Establece el objeto tabla que tiene establecidos los atributos básicos para mostrar imágenes
	 *
	 * @param table_data
	 */
//	function set_table($table) {
//		$this->dir = $table->image_dir;
//		$this->generator_url 	= $table->thumb_generator_url;
//		$this->thumb_max_width 		= $table->thumb_max_width;
//		$this->thumb_max_height 	= $table->thumb_max_height;
//		$this->thumb_background_color = $table->thumb_background_color;
//		$this->thumb_square 		= $table->thumb_square;
//	}
	function init_config() {
		
		if ($this->initialized) return true;

		//Comprobamos que el nombre de archivo es válido
		if ( strpos($this->filename,'..')!== false ||
			strpos($this->filename,'\\')!== false ||
			strpos($this->filename,'/')!== false) {
				die ("**Error: El nombre del fichero contiene caracteres no válidos");
		}
				
		//Comprobamos que existe la imagen
		if ( ! is_file($this->dir.$this->filename) ) {
			//die("**Error: No se encuentra el fichero de imagen: ".$this->filename);
			$this->send_error_img('No se encuentra el fichero');
			 //$this->dir.$this->filename.<br />cwd: '.getcwd());
		}
		
		//Obtenemos las dimensiones de la imagen
		list($this->width, $this->height) = getimagesize($this->dir.$this->filename);
		
		//Establecemos el tipo de imagen por la extensión
		$partes_ruta = pathinfo($this->filename);	
		$this->type = strtolower($partes_ruta['extension']);
		
		//Establecemos el directorio de miniaturas
		if ($this->thumb_cache_dir == '') {
			$this->thumb_cache_dir = $this->dir.$this->thumb_cache_dir_sufix."/";
			// Comprobamos si podemos realizar copia en lugar de resample
			if ( $this->height <= $this->thumb_max_height && $this->width <= $this->thumb_max_width ) {
				$this->thumb_cache_dir = $this->dir;
			} 
		}
		
		//Si no existe, lo creamos
		if ( ! is_dir($this->thumb_cache_dir) ) {
			$result = mkdir($this->thumb_cache_dir,0777);
			if ( ! $result || ! is_dir($this->thumb_cache_dir) ) {
				die("**Error: No se pudo crear el directorio de las miniaturas $this->thumb_cache_dir");
			}
		}
		
		//Establecemos las dimensiones de la miniatura
		if ( $this->height <= $this->thumb_max_height && $this->width <= $this->thumb_max_width ) {
			$this->thumb_height = $this->height;
			$this->thumb_width = $this->width;			
		} else {
			assert($this->thumb_max_width > 0 && $this->thumb_max_height > 0 );
			if ( ($this->width/$this->thumb_max_width) > ($this->height/$this->thumb_max_height) ) {
				$proportion = $this->width/$this->thumb_max_width;
				$this->thumb_height = $this->height / $proportion;
				$this->thumb_width = $this->thumb_max_width;
			} else {
				$proportion = $this->height/$this->thumb_max_height;
				$this->thumb_width = $this->width / $proportion;
				$this->thumb_height = $this->thumb_max_height;
			}
		}
		
		return $this->initialized = true;
	}
	//-- Trabajo con la imagen ---------------------------------------------------------------
    function clean_url_href($url, $dir_separator='/', $translate_separator = true) {
		$result = $url; //clean_url($path,$dir_separator,$translate_separator);
		$parts = explode($dir_separator,$result);
		foreach ( $parts as $key => $value ) {
			$parts[$key] = rawurlencode(utf8_encode($value));
		}
		$result = implode($dir_separator,$parts);
		return $result;
	}
	function get_thumb_link() {
		if ( $this->filename == '' ) return '';
		$url = new url();
		if ( empty($this->generator_url)) {
			$url->url_base = $this->clean_url_href($this->dir.$this->filename); //TODO: sobra el clean?
		} else {
			$url->url_base = $this->generator_url;
			$url->set_var('file',$this->filename);
		}
		return "<a href=\"".$url->__toString()."\" target=\"_blank\" onclick=\"javascript: image_popup(this);return false;\">";
	}
	function get_thumb_img() {
		if ( $this->filename == '' ) return '';
		//$source = $this->dir.$this->filename;
		$url = new url();
		$url->url_base = $this->generator_url;
		$url->set_var('file',$this->filename);
		$url->set_var('view_mode','thumb');
		if ($this->pass_dir_in_url) {
			$url->set_var('dir',$this->clean_url_href($this->dir));
			$url->set_var('thumb_cache_dir_sufix',$this->thumb_cache_dir_sufix);
			$url->set_var('thumb_max_width',$this->thumb_max_width);
			$url->set_var('thumb_max_height',$this->thumb_max_height);
		}
		return "<img src=\"{$url->__toString()}\" border=\"0\" class=\"thumbnail\" alt=\"Miniatura: $this->filename\" align=\"$this->thumb_align\" />";
	}
	function print_thumb_link() {
		echo $this->get_thumb_link();
	}
	function print_thumb_img() {
		echo $this->get_thumb_img();
	}
	function __toString() {
		if ( $this->view_mode != 'thumb' ) throw new ExceptionDeveloper("Not implemented");
		$result = '';
		$result .= $this->get_thumb_img();
		if ( $this->view_link ) {
			$result = $this->get_thumb_link() . $result . '</a>';
		}		
		return $result;
	}
	// -- Trabajo con la miniatura -----------------------------------------------------------
	function send_thumbnail() {
		//Establecemos configuración
		$this->init_config();
		
		if ( true === strpos( $this->filename , ".." ) ) {
			die('fichero no autorizado');
		}
		
		//Comprobamos si existe thumbnail creado previamente
		$full_filename = $this->thumb_cache_dir.$this->filename;
		$result = true;
		if ( ! is_file($full_filename) ) {
			$result = $this->thumb_generate();
			if ( ! $result ) {
				die("**Error: No se pudo crear la miniatura en: ".$this->filename);
			}
		}
		$this->passthru_img($full_filename);
	}
	function send_image() {
		$this->init_config();
		if ( true === strpos( $this->filename , ".." ) ) {
			die('nombre de fichero no autorizado');
		}
		$this->passthru_img($this->dir.$this->filename);
	}
	function send_error_img($error_msg) {
		//http://php.net/manual/en/function.imagejpeg.php
		// Create a blank image and add some text
		$im = imagecreatetruecolor(140, 20);
		
		imagefill($im,1,1,imagecolorallocate($im,200,200,200));
		
		$text_color = imagecolorallocate($im, 233, 14, 91);
		imagestring($im, 1, 5, 5,  $error_msg, $text_color);
		// Set the content type header - in this case image/jpeg
		header('Content-Type: image/jpeg');
		// Output the image
		imagejpeg($im,null,99);
		// Free up memory
		imagedestroy($im);
		
		die;
	}
	function send_image_or_thumb() {
		$this->init_config();
		if ( isset($_GET['view_mode']) && $_GET['view_mode']=='thumb' ) {
			$this->view_mode = 'thumb';
			$this->send_thumbnail();
		} else {
			$this->send_image();
		}
	}
	
	protected function passthru_img($full_filename) {
		// Enviamos la imagen
		$img = fopen($full_filename,"r");
		$size = filesize($full_filename);
		@ob_end_clean();
		header('Last-Modified: '.date('r'));
		header('Accept-Ranges: bytes');
		header("Content-Length: ".$size);
		header('Content-Type: image/jpeg');
		fpassthru($img);
		fclose($img);
		//ob_end_flush();
	}
	function thumb_generate() {
		//Comprobamos si existe thumbnail creado previamente
		if ( ! is_file($this->thumb_cache_dir.$this->filename) ) {
			$this->init_config();

			// Comprobamos si podemos realizar copia en lugar de resample
			if ( $this->height == $this->thumb_height && $this->width == $this->thumb_width ) {
				$this->thumb_cache_dir = $this->dir;
				return true;
			}
			
			// Resample de la imagen a miniatura
			$image_thumb = imagecreatetruecolor($this->thumb_width, $this->thumb_height);
			if ( ! $image_thumb ) return false;
			$image = $this->imagecreatefrom($this->type,$this->dir.$this->filename);
			if ( ! $image ) return false;
			imagecopyresampled($image_thumb, $image, 0, 0, 0, 0, $this->thumb_width, $this->thumb_height, $this->width, $this->height);
			if ( ! $image ) return false;
			//Almacenamos la miniatura
			$result = $this->imagewrite($this->type,$image_thumb,$this->thumb_cache_dir.$this->filename);
			return $result;
		}
		return true;
	}
	//--------------------------------------------------------------------------------------------------------------
	private function imagecreatefrom($format,$filename) {
		if ( ! empty( $_GET['debug'] ) ) {
			echo "<pre>".$this->dir.$this->filename."</pre>";
			die;
		}		
		switch ($format) {
			case 'jpg':
				$image = imagecreatefromjpeg($filename);
				break;
			case 'gif':
				if (imagetypes() & IMG_GIF) {
					$image = imagecreatefromgif($filename);
				} else {
					die ("**Formato GIF no soportado");
				}
				break;
			case 'png':
				$image = imagecreatefrompng($filename);
				break;
			default:
				die("**Error: Formato de imagen no admitido $format");
				break;
		}
		return $image;
		
	}
	private function imagewrite($format,$image,$filename,$quality=90) {
		switch ($format) {
			case 'jpg':
				return imagejpeg($image,$filename,$quality);
				break;
			case 'gif':
				if (imagetypes() & IMG_GIF) {
					return imagegif($image,$filename);
				} else {
					die ("**Formato GIF no soportado");
				}
				break;
			case 'png':
				return imagepng($image,$filename);
				break;
			default:
				die("**Error: Formato de imagen no admitido $this->type");
				break;
		}
	}
	// -- Trabajo con el fichero de la imagen ------------------------------------------------------------------
	function delete() {
		if ( ! $this->initialized ) {
			//Establecemos el directorio de miniaturas
			if ($this->thumb_cache_dir=='') {
				$this->thumb_cache_dir= $this->dir.$this->thumb_cache_dir_sufix."/";
			}
		}
		
		$result = true;
		//Intenetamos borrar la miniatura
		if (is_file($this->thumb_cache_dir.$this->filename)) {
			$result = $result &&  unlink($this->thumb_cache_dir.$this->filename);
		}		
		//Intentamos borrar la imagen
		if (is_file($this->dir.$this->filename)) {
			$result = $result && unlink($this->dir.$this->filename);
		}
		return $result;
	}
}


		
