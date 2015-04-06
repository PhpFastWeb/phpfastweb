<?php
class upload {
	public $input_name = 'file0';
	public $file_name = '';
	public $target_dir = '';
	
	public $previous_file_overwrite = true;
	public $previous_file_name = '';

	public $max_file_size = 2048;
	public $max_dir_size = 0;
	public $ext_forbidden = array();
	public $ext_allowed = array();

	public $auto_create_dir = true;
	public $overwrite = true;
	
	public $error_msg = null;
	
	public $pk_to_name = true; //Añadir la clave primaria al principio del fichero
	public $pk_compatibility = true; //TODO: Esto desaparecerá con el tiempo, si no esta, probar sin pk
	public $pk_string = '';
	
	public static $dir_separator = "";
	
	//Constructor
	function __construct($input_name = 'file0', $target_dir='', $max_file_size=2048,$pk='') {
		$this->input_name = $input_name;
		$this->max_file_size = $max_file_size;
		$this->target_dir = $target_dir;	
		$this->pk_string = $pk;
		

		
	}
	//-------
	function get_input_plain($text='Fichero :') {
		$result = "$text ";
		$result .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$this->max_file_size\" />";
		$result .= "<input type=\"file\" name=\"".$this->input_name."\" size=\"24\" />";
		return $result;
	}
	//-------
	function check_file() {
		if (is_uploaded_file($_FILES[$this->input_name]['tmp_name'])) return true;
		else return false;
	}
	function check_target_dir() {
		
		if ( $this->target_dir == '' ) return true;
		
		//Configuramos el separador de directorios
		if ( self::$dir_separator == '' ) {
			if (substr(__FILE__,0,1) == '/' ) {
				self::$dir_separator  = '/';
			} else {
				self::$dir_separator  = "\\";
			}
			
		}
			
		if ( substr($this->target_dir,-1,1) != self::$dir_separator ) {
			$this->target_dir .= self::$dir_separator;
		}
		
		if ( ! is_dir($this->target_dir) ) {
			if ( $this->auto_create_dir ) {
				
				//No existe el directorio, intentamos crear el directorio destino
				$dir = clean_path($this->target_dir,self::$dir_separator);
				$ok = @mkdir( $dir , 0777, true );
				if ( ! $ok ) {
					$this->error_msg = 'No se pudo crear el directorio destino automáticamente: '.$dir;
					return false;
				} else {
					$this->error_msg = 'Directorio destino creado automáticamente: '.$dir;
					return true;
				}
			} else {
				//No existe el directorio y no debemos crearlo
				$this->error_msg = 'No existe el directorio destino: '.$this->target_dir;
				return false;
			}
		}
		//Existe el directorio destino
		return true;
		
	}
	function check_file_size($source) {
		//Comprobamos que el fichero no excede las dimensiones de tamaño
		if ( $this->max_file_size > 0 ) {
			$file_size = filesize($source); //Cacheamos para no llamar dos veces
			if ( $file_size > $this->max_file_size ) {
				$max_file_size_kb = $this->bytes_to_kb($this->max_file_size);
				$this->error_msg = 'El fichero '.$source.' tiene un tamaño de '.$file_size.' superior al maximo permitido de '.$max_file_size_kb.' Kb.';
				return false;
			}
		}
		return true;
		
	}
	function check_dir_size($dir) {
		if($this->max_dir_size>0) {
			$dir_size = dirsize($dir);
			if ($dir_size > $this->max_dir_size ) {
			     $max_dir_size_kb = $this->bytes_to_kb($this->max_dir_size);
				$this->error_msg = 'Al subir el fichero se supera el tamaño máximo permitido para el directorio de '.$max_dir_size_kb.' Kb.';
				return false;
			}
		}
		return true;	
	}	
        
    
	function bytes_to_kb($bytes) {
		$result = (ceil($bytes/1024));
        return $result;
	}
    
	function check_file_ext($filename) {
		//Comprobamos las prohibiciones sobre extensiones
		$ok = true;
		$file_ext = strtolower(substr($filename,strrpos($filename,'.')));
		foreach($this->ext_forbidden as $ext) {
			if ($ext=='*') {
				$ok = false;
			}
			else {
				$file_ext = substr(strtolower($filename),-1*strlen($ext ));
				if ( strtolower($ext) == $file_ext ) {
					$ok = false;
				}
			}
		}
		//Ahora comprobamos las extensiones SIEMPRE permitidas
		foreach($this->ext_allowed as $ext) {
			if ($ext=='*') $ok = true;
			else {
				if ( strtolower($ext) == $file_ext ) {
					$ok = true;
				}
			}
		}
		if (!$ok) {
			$this->error_msg = 'Extensión no permitida: '.$file_ext.'.';
			return false;
		}
		return true;
	}

	function place_file() {
		GLOBAL $_FILES;
		
		// Comprobamos que se especificó algún fichero
		if ( empty($_FILES)) {
			$this->error_msg = 'No se recibió ningun fichero del formulario.';
			return false;
		}
		
		//Comprobamos que el nombre de fichero no está en blanco
		if ( empty($_FILES[$this->input_name]['name'] ) ) {
			$this->error_msg = 'No se especificó ningun fichero.';
			return false;			
		}
		//Comprobamos que se recibió un fichero
		if ( ! $this->check_file() ) {
			$this->error_msg = 'No se recibió correctamente el fichero.';
			return false;
		}

		//Comprobamos que existe el directorio destino
		if ( ! $this->check_target_dir() ) {
			throw new ExceptionDeveloper( $this->error_msg );
		}		
		
		//Establecemos parámetros para desplazar el fichero temporal
		$source = $_FILES[$this->input_name]['tmp_name'];
		if ( $this->pk_to_name ) {
			$this->file_name = $this->pk_string.'__'.$_FILES[$this->input_name]['name'];
		} else {
			$this->file_name = $_FILES[$this->input_name]['name'];
		}
		$destination = $this->target_dir.$this->file_name;
		
		//Comprobamos que se cumplen los permisos en las extensiones de fichero
		if ( ! $this->check_file_ext($this->file_name)) {
			return false;
		}
		
		//Comprobamos que el tamaño de origen no es excesivo
		if ( ! $this->check_file_size($source) ) {
			return false;
		} 
		
		//Comprobamos que no se supera el tamaño máximo de directorio
		if ( ! $this->check_dir_size($this->target_dir) ) {
			return false;
		} 		
		
		//Comprobamos que se permite sobreescribir en caso de existir el fichero
		if ( ! $this->overwrite && is_file($destination) ) {
			$this->error_msg = 'Ya existe un fichero con ese nombre.';
			return false;
		}

		// Ejecutamos el desplazamiento del fichero temporal

		
		if ( ! move_uploaded_file($source,$destination ) ) {
			$this->error_msg = 'No se pudo reubicar el fichero temporal recibido.';
			return false;
		}

		// Eliminamos el fichero anterior
		
		$this->process_previous_file();
		
		return $this->file_name;
		
	}
	function process_previous_file() {
		if ( $this->previous_file_overwrite ) {
			if ( ! empty( $this->previous_file_name) ) {
				if ( ! is_file($this->target_dir.$this->previous_file_name ) ) {
					$this->error_msg = 'No se pudo encontrar archivo anterior para retirarlo.';
					return false;
				} else {
					if ( ! unlink( $this->target_dir.$this->previous_file_name ) ) {
						$this->error_msg = 'No se pudo retirar el archivo previo.';
						return false;
					}
				}
			}
		}
		return true;
	}
	function process_text_file(&$data, &$errors, $func_name, $skip_lines_ini=0, $stop_on_error=true, $error_on_empty=true) {
		
		$data = array();
		$errors = array();
		$i = 0;
		$valid = true;
		$handle = fopen( $this->target_dir.$this->file_name , 'r' );
		if ( ! $handle ) return false;
		//Nos saltamos las primeras lineas
		while ( $skip_lines_ini > $i ) {
			$str = trim( fgets( $handle , 4096 ) );
			if ( $str ) {
				$i++;
			} else {
				$errors[]='**Error: Se alcanzó el final del fichero en linea '.$i.' antes de saltar '.$skip_lines_ini.' lineas.';
				return false;
			}
		}
		$str = trim( fgets( $handle , 4096 ) );
		while ( $str  ) {
			$t = call_user_func($func_name,$str);
			if ( $t !== false ) {
				$data[] = $t;
			} else {
				$errors[] = '**Error: Al procesar la fila número '.$i.' no coincide con el formato esperado.<pre><b>[fila '.sprintf('%03u',$i).']</b> '.$str.'</pre>';
				if ( $stop_on_error ) {
					return false;
				}
			}
			$valid = $valid && $t;
			$i++;
			$str = trim( fgets( $handle , 4096 ) );
		}
		if ( count($data) == 0 && $error_on_empty ) {
			if ( $skip_lines_ini > 0 ) {
				$errors[] = '**Error: No se encontraron contenidos más allá de la fila '.$skip_lines_ini.'.';
				$valid = false;
			} else {
				$errors[] = 'No se encontraron contenidos en el fichero.';
				$valid = false;
			}
			
		}
		return $valid;
	}
	
	function send_download() {
		$file = $this->file_name;
		$dir = $this->target_dir;
		$pk = $this->pk_string;
		$real_file = ''; 
		
		if ( $this->pk_to_name ) {
			if ( is_file($dir.$pk.'__'.$file) ) {
				$real_file = $dir.$pk.'__'.$file;
			} elseif ( $this->pk_compatibility && is_file($dir.$file) ) {
				$real_file = $dir.$file;
			} 			
		} else {
			if ( is_file($dir.$file) ) {
				$real_file = $dir.$file;
			}					
		}
		
		if ($real_file == '' || ! is_file($real_file) ) {
			echo "No encontrado [$pk]: ".$file;
			die;
		} else {
			header('Content-type: application/force-download'); 
			header('Content-Transfer-Encoding: Binary'); 
			
			header('cache-control:private'); //Sin esta directiva al intentar "abrir"
			//directamente un archivo, una vez descargado se elimina de la cache
			//antes de que se pueda abrir.
			
			// set the content as octet-stream
			//header("Content-Type: application/octet-stream");
			// tell the thing the filesize
			header("Content-Length: " . filesize($real_file));
			// set it as an attachment and give a file name
			header('Content-Disposition: attachment; filename="'.$file.'"');
			// read into the buffer
			readfile($real_file);

		}
	}
}

?>
