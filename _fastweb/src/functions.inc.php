<?php	
       
       
    function __($message, $domain='') {
        return translator::translate( $message, $domain );
    }
    function _e($message, $domain='') {
        print translator::translate( $message, $domain );
    }
    
    /* --------------------------------- */
    
    /**
     * Calculates BCMOD
     */
	function my_bcmod( $x, $y ) { 
	   // how many numbers to take at once? carefull not to exceed (int) 
	   $take = 5;    
	   $mod = ''; 
	   do { 
	       $a = (int)$mod.substr( $x, 0, $take ); 
	       $x = substr( $x, $take ); 
	       $mod = $a % $y;    
	   } 
	   while ( strlen($x) ); 
	
	   return (int)$mod; 
	}

    
	/**
     * Cleans a path from . and .. references, and substitues \\ and / with $dir_separator
     */
	function clean_path($path, $dir_separator='/', $translate_separator = true ) {
		if ( $translate_separator ) {
			$path = str_replace('\\',$dir_separator,$path);
			$path = str_replace('/',$dir_separator,$path);
		}
		
		//Averiguamos si la ruta es absoluta
		if ( ! ( substr($path,0,1) == $dir_separator     ) && 
			 ! ( substr($path,1,1) == ':' )    ) {
			 //Añadimos directorio actual
			$path = getcwd().$dir_separator.$path;
			if ( $translate_separator ) {
				$path = str_replace('\\',$dir_separator,$path);
				$path = str_replace('/',$dir_separator,$path);
			}			
		}
		$parts = explode($dir_separator,$path);
		$result = '';		
		//Construimos la ruta
		$new_path = array();
		foreach ( $parts as $subdir ) {
			if ( $subdir == '..' ) {
				$new_path = array_slice($new_path,0,-1);
			} else if ( $subdir != '.' && $subdir != '' ) {
				$new_path[] = $subdir;
			}
		}
		//Volvemos cadena otra vez
		$result = implode($dir_separator,$new_path);
		
		return $result;
		
	}
    
    /**
     * Gets the size of all files on a directory
     */
	function dirsize($directory) {
		$size = 0;
		if (!is_dir($directory)) return false;
 		$d = dir($directory);
   		while (false !== ($entry = $d->read())) {
   			if($entry != '.' && $entry != '..' && is_file($directory.$entry))
           		$size += filesize($directory.$entry);
   		}
   		$d->close();
   		return $size;
	}
  	
  	//-----------------
	
  	function process_session_var($var_name,$reset_value='off',$default_value=null) {
		//Buscamos si está definido en GET
		if ( isset($_GET[$var_name]) ) {
			//¿Se pulsó resetear?
			if ($_GET[$var_name]==$reset_value) {
				//Tiene el valor de reinicio: borramos la variable de sesión
				if (isset($_SESSION[$var_name])) unset($_SESSION[$var_name]);
			} else {
			//Contiene un valor
				//Lo añadimos a la sesion para que su valor se mantenga
				$_SESSION[$var_name]=$_GET[$var_name];
			}
			//Devolvemos lo encontrado
			return $_GET[$var_name];
		}
		//No esta definido en GET
		
		//Buscamos si está definido en SESSION
		if ( isset($_SESSION[$var_name]) ) {
			return $_SESSION[$var_name];
		}
		
		//No esta definido en ninguno sitio
		if (isset($default_value)) {
			//Definimos el valor por defecto en sesión
			return $_SESSION[$var_name]=$default_value;
		} else {
			//No hay valor por defecto, devolvemos falso
			return false;
		}
	}
	//-------------------

	//-------------------
	
	//Late static binding class name resolution
	if(!function_exists('get_called_class')) {
        class class_tools {
                static $i = 0;
                static $fl = null;

                static function get_called_class() {
                    $bt = debug_backtrace();

                        if (self::$fl == $bt[2]['file'].$bt[2]['line']) {
                            self::$i++;
                        } else {
                            self::$i = 0;
                            self::$fl = $bt[2]['file'].$bt[2]['line'];
                        }

                        $lines = file($bt[2]['file']);

                        preg_match_all('/([a-zA-Z0-9\_]+)::'.$bt[2]['function'].'/',
                            $lines[$bt[2]['line']-1],
                            $matches);

                return $matches[1][self::$i];
            }
        }

        function get_called_class() {
            return class_tools::get_called_class();
        }
	}
	
 
	//-------------------
	function exception_handler($exception) { 
		echo $exception;
	}
	set_exception_handler("exception_handler");
	

    function errorHandler($type, $msg) {
        //Intentamos asegurar que estamos en modo desarrollador
        $dev = false;
        try {
            $dev = @website::in_developer_mode();
        } catch(Exception $e) { 
            $dev = false; 
        }      
        
        if ($dev) {
            switch($type) {
                 case E_NOTICE:
                      //echo "<p>Notice: $msg </p>";
                      break;
                 case E_WARNING:
                      echo "<p>Non-fatal error</p>";
                      break;
                 default:
                      die("<p>Fatal error</p>");
                      break;
            }           
        } else {
            switch($type) {
                 case E_NOTICE:
                      echo "<p>Notice: $msg </p>";
                      break;
                 case E_WARNING:
                      echo "<p>Non-fatal error: $msg </p>";
                      break;
                 default:
                      die("<p>Fatal error: $msg </p>");
                      break;
           }
       }
    }
    //set_error_handler('errorHandler');
    
	function handleShutdown(Exception $exception = null) {
      
        //Intentamos asegurar que estamos en modo desarrollador
        $dev = false;
        try {
            $dev = @website::in_developer_mode();
        } catch(Exception $e) { 
            $dev = false; 
        }
        
//        if ($dev && $exception != null) {
//        	//Relanzamos excepción con su manejador
//	        $restored = restore_exception_handler();
//	        if ($restored) { //prevent infinite loops
//	        	throw $exception;
//			}
//        }
        
		if ( $exception != NULL ) {
			//TODO: Simplificar para minimizar errores al obtener estos datos
			$error = array(
				'file' => $exception->getFile(),
				'line' => $exception->getLine(),
				'message'  => $exception->getMessage(),
				'type'=> E_ERROR
			);
		} else {
			$error = error_get_last();
            //var_dump($error); die;	
		}        
            
		if( $error !== NULL ) { 
           $error_type = array( 
                E_ERROR=>'E_ERROR',
                E_WARNING => 'E_WARNING',
                E_PARSE => 'E_PARSE',
                E_NOTICE => 'E_NOTICE',
                E_CORE_ERROR => 'E_CORE_ERROR',
                E_CORE_WARNING => 'E_CORE_WARNING',
                E_COMPILE_ERROR => 'E_COMPILE_ERROR',
                E_COMPILE_WARNING => 'E_COMPILE_WARNING',
                E_USER_ERROR => 'E_USER_ERROR',
                E_USER_WARNING => 'E_USER_WARNING',
                E_USER_NOTICE => 'E_USER_NOTICE',
                E_STRICT => 'E_STRICT',
                E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
                E_DEPRECATED => 'E_DEPRECATED',
                E_USER_DEPRECATED => 'E_USER_DEPRECATED',
                E_ALL => 'E_ALL'
            );
            
            
            //Mostramos el error
            if ( $dev ) {
                //ob_clean();
				if ( $exception == null ) {
				    $error_type_str = $error['type'];
                    if ( isset($error_type[$error['type']] ) ) $error_type_str = $error_type[$error['type']];
                	$ini = '<div style="border:5px solid black; padding:10px 15px; margin:20px auto; background-color: yellow; color:black; font-size:13px;">';
                	$breakout = '</script></a><br style="clear:both" /><br style="clear:both" /></ul></div></ul></div></ul></div></ul><br style="clear:both" />';
                	$info = "<p style=\"margin:5px 0\"><span style=\"background-color:#EA7C1B;color:white;\">&nbsp;[".$error_type_str."]&nbsp;</span>&nbsp;<a name=\"error_foot\"> </a> <b>".$error['message']."</b></p><p style=\"margin:5px 0\"><i>File:</i> <b>".basename($error['file'])."</b>, <i>Line:</i> <b>".$error['line']."</b></p><p style=\"margin:5px 0\"><i>Path:</i> ".dirname($error['file'])."/<b>".basename($error['file'])."</b></p>".PHP_EOL;
                
					//$info .= '<pre>'.var_dump(debug_backtrace()).'</pre>';

					$icon = '<div style="opacity: 0.8; background-color:yellow;position:absolute;top:16px;right:3px;font-size:12px; border:5px solid #4E4E4E;"><b><a href="#error_foot" style="display:block;padding:7px 7px; text-align:middle;"><span style="font-size:24px;">!</span> &nbsp;'.$error_type[$error['type']].'</a></b></div>';
                
					echo $breakout.$breakout.$ini.$info.'</div><br /><br />'.$icon;
				} else {
					echo $exception->__toString();
				}
            } else {  
                if ( ! ( $error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_COMPILE_ERROR ) ) return;
                //TOMADO DE PROJECT PIER http://www.projectpier.org
                //$logger_session = Logger::getSession();
                //if (($logger_session instanceof Logger_Session) && !$logger_session->isEmpty()) {
                //  Logger::saveSession();
                //} // if
                $breakout = '</ul></div></ul></div></ul></div></ul><br style="clear:both" />';
                $ini = '<div style="overflow:auto; background-color: #f1f1f1; border:2px solid #888; width:745px; padding:15px 25px; margin:-30px auto 10px auto; font-family: sans-serif; color:black; font-size:13px;">';
                $last_error = $error;
                //if($last_error['type'] === E_ERROR) {
                $js  = '<script type="text/javascript">';
                $js .= '     function pp_set_form_action() {';
                $js .= '        var f = document.getElementsByTagName("form");';
                $js .= '        for(var i = 0; i < f.length; i++) f[i].action="/__pperror.php";';
                $js .= '    }';
                $js .= '</script>';
                $form = '<form action="/__pperror.php" method="post" target=_self>';
                $form .= '<p>Por favor, para que nuestros ingenieros puedan solucionarlo, descríba qué acciones estaba realizando hasta llegar a este error:<br /><textarea cols=90 rows=15 name=explain></textarea><br />';
                $form .= '</p><p>Indíquenos si lo desea su nombre y una forma de contacto para poder preguntarle más detalles:<br /><textarea cols=90 rows=4 name=reportes></textarea><br />';
                $form .= '</p><input type=submit style="width:200px; height:30px;" value="Enviar informe de error" /><br />';
                
                $form .= 'Tenga en cuenta que para enviar el informe es necesario contar con conexión a Internet.<br /><br /><hr />';
                $form .= '<b>Detalles del error</b><br />';
                $form .= 'Error: '.$last_error['message'].'<input size=70 type=hidden readonly=readonly name=message value="' . $last_error['message'] . '"><br />';
                $form .= 'Fichero: '.basename($last_error['file']).'<input size=70 type=hidden readonly=readonly name=file value="' . $last_error['file'] . '"><br />';
                $form .= 'Linea: '.$last_error['line'].'<input size=70 type=hidden readonly=readonly name=line value="' . $last_error['line'] . '"><br />';
                
                //$form .= 'Versión:<br /><input type=text readonly=readonly name=release value="sernetcanf1.0"><br />';
                
                //$form .= 'PHP version:<br /><input type=text size=70 name=phpversion value="'.phpversion().'"><br />';
                $url = $_SERVER['HTTPS'] ? 'https://' : 'http://';
                $url .= $_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
                
                $form .= 'URL: '.$url.'<input type=hidden size=70 readonly=readonly name=request value="'.$url.'"><br />';
                $form .= 'Navegador';
				$form .= '<a href="#" onclick="document.getElementById(\'htrace\').style.display=\'block\'; return false;" style="color: black; text-decoration:none; cursor: text;">:</a> ';
				$form .= $_SERVER["HTTP_USER_AGENT"].'<input type=hidden size=70 readonly=readonly name=browser value="'.$_SERVER["HTTP_USER_AGENT"].'"><br />';
                $form .= '<div id="htrace" style="display:none;">';
                $form .= '...';
                //$form .= "<p style=\"margin:5px 0\"><span style=\"background-color:#EA7C1B;color:white;\">&nbsp;[".$error_type[$error['type']]."]&nbsp;</span>&nbsp;<a name=\"error_foot\"> </a> <b>".$error['message']."</b></p><p style=\"margin:5px 0\"><i>File:</i> <b>".basename($error['file'])."</b>, <i>Line:</i> <b>".$error['line']."</b></p><p style=\"margin:5px 0\"><i>Path:</i> ".dirname($error['file'])."/<b>".basename($error['file'])."</b></p>".PHP_EOL;
                //$form .= ExceptionDeveloper::get_pretty_trace(debug_backtrace());
				//$form .= '<pre style="position:relative;">'.htmlspecialchars(print_r(debug_backtrace(),1))."</pre>";
				$form .= '</div>';
                $form .= '</form>';
                ob_clean();
                echo "$breakout $breakout $js $ini<h1 style=\"font-size:17px;\">Lo sentimos, ha ocurrido un error</h1>$form</div>";
                
            }
           
        }
        die;
    }
	//set_exception_handler('handleShutdown');
	register_shutdown_function('handleShutdown');
    set_exception_handler('handleShutdown');
    //------------------------------------------------------------
    
    /**
     * Custom Error Handler
     */
    function myErrorHandler($errno, $errstr, $errfile, $errline) {
	    if (!(error_reporting() & $errno)) {
	        // This error code is not included in error_reporting
	        return;
	    }
		
        if ( 0 == error_reporting () ) {
            // Error reporting is currently turned off or suppressed with @
            return;
        }
        
	    switch ($errno) {
	    case E_USER_ERROR:
	        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
	        echo "  Fatal error on line $errline in file $errfile";
	        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
	        echo "Aborting...<br />\n";
	        exit(1);
	        break;
	
	    case E_USER_WARNING:
	        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
	        break;
	
	    case E_USER_NOTICE:
	        echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
	        break;
	
	    default:
	        echo "Unknown error type: [$errno] $errstr<br />\n";
	        break;
	    }
	
	    // Don't execute PHP internal error handler 
	    return true;
	    
	    //echo '****<pre>'.var_dump(debug_backtrace()).'</pre>';
	    //handleShutdown();
	    //return true;
	}
	//$old_error_handler = set_error_handler("myErrorHandler");
	
