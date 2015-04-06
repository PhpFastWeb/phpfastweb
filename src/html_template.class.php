<?php

class html_template {
	
	/* inclusiones */
	public $base_url = '';
	public $base_dir = '';
	
	
	/** Tema por defecto
	 * @var theme_config
	 */	
	public $theme;
  //----------------------------------------------------------------------------
  //-- Constructores

	function __construct($lib_base_url, $base_url='') {
		$this->lib_base_url = $lib_base_url;
		$this->base_url = $base_url;		
	}
  //----------------------------------------------------------------------------
	public static function redirect($url, $message='') {
        if ( ! headers_sent() ) {
            header('location: '.$url);
        }
        //TODO: Detect that this is not a circular jump       
		echo "<br /><br /><center>$message<br />";
		echo "Est· siendo redirigido a <a href=\"$url\">esta p·gina</a><br />";
        echo "Si tras unos segundos no es redirigido autom·ticamente, pulse <a href=\"$url\"><u>este enlace</u></a><br /></center><br />";
		echo "<script type=\"text/javascript\">\r\n";
		echo "window.location='$url';\r\n";
		echo "</script>\r\n";
        
        //echo "<pre>"; var_dump($_SESSION); var_dump($_COOKIE); 
        exit;
	}

    //---------------------------------------------------------------------------------------------
    public static function get_php_self() {
    	//Some hostings doesn't return $_SERVER['PHP_SELF'] with "panthom" directories
    	//  like /dir/file.php/5/ (they stop at file.php).
    	//This function returns allways all directories, even virtual ones.
    	
		//TODO: Optimize this from get_request_url
		
		$result = self::get_request_url();
    	$parameters = self::get_request_url_parameters();
    	if (strlen($parameters) > 0 ) {
    		$result = substr($result,0,-1*strlen($parameters));
    	}
    	if (substr($result,-1)=='?') $result = substr($result,0,-1);
    	return $result;
   	}
	public static function get_request_url() {
	  	//Rescatamos la URL de destino de vuelta a esta misma p·gina
	  	// if 'REQUEST_URI' isn't available then ...
	  	if(!isset($_SERVER['REQUEST_URI'])) {
	  		$temp_request_url = $_SERVER['PHP_SELF'];
	  		$temp_request_url .= (strpos($temp_request_url, '?')) ? url::$url_separator : "?";
	  		$temp_request_url .= $_SERVER['QUERY_STRING'];	  			
	  	} else {
	  		$temp_request_url = $_SERVER['REQUEST_URI'];
	  	}
	  	//$protocol = ( ! empty($_SERVER['HTTPS'] ) ) ? 'https://' : 'http://';
	  	//$temp_request_url = $protocol.$_SERVER['SERVER_NAME'].$temp_request_url;
	  	
	  	//$query = substr($temp_request_url,strlen($_SERVER['SCRIPT_NAME']));
	  	//$temp_request_url = $_SERVER['SCRIPT_NAME'] . htmlentities($query);
	  		  	
	  	return $temp_request_url;		

    } 
    /**
     * @deprecated Use get_request_url instead
     */
	public static function get_request_url_parameters() {
	  	if (isset($HTTP_SERVER_VARS['QUERY_STRING'])) {
	  		//$result .= (strpos($updateGoTo, '?')) ? "&" : "?";
	  		$result = $HTTP_SERVER_VARS['QUERY_STRING'];
	  	} else {
	  		$result = $_SERVER['QUERY_STRING'];
		}
	  	return $result;
	}
	public static function request_url() {
		throw new ExceptionDeveloper('request_url deprecated');	  	
	}
    static function print_overlib_alink($html_hint,$width='200',$url='') {
		if ($url=='') {
			$url = 'javascript:void(0);'; 
		}
		echo "<a href=\"$url\" ";
		echo "onmouseover=\"return overlib('".htmlentities($html_hint, ENT_QUOTES)."',WIDTH, $width);\" ";
		echo "onmouseout=\"return nd();\">\r\n";
  	}
  	//---------------------------------------------------------------------------------
  	public static function get_client_real_ip() {
	/*
	This function will try to find out if user is coming behind proxy server. Why is this important?
	If you have high traffic web site, it might happen that you receive lot of traffic
	from the same proxy server (like AOL). In that case, the script would count them all as 1 user.
	This function tryes to get real IP address.
	Note that getenv() function doesn't work when PHP is running as ISAPI module
	*/
		if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_X_FORWARDED')) {
			$ip = getenv('HTTP_X_FORWARDED');
		} elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip = getenv('HTTP_FORWARDED_FOR');
		} elseif (getenv('HTTP_FORWARDED')) {
			$ip = getenv('HTTP_FORWARDED');
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
  
  	//------------------------------------------------------------------------------------
	public static function print_table_array($array_table_var=null,$title='',$align='left',$aditional_classes='') {
		if (!isset($array_table_var) || !is_array($array_table_var) || count($array_table_var)==0) {
			$array_table_var = array(array('No se encontraron datos bajo el criterio especificado.'=>''));
		}
		if ( ! is_array($array_table_var) ) return false;
		//Comprobamos si se trata de un array con un elemento por fila,
		//o simplemente un array unidimensional.
		if (!is_array(reset($array_table_var) )) {
			//Solo chequeamos el primer elemento
			$num_col = 2;
			$headers = array('Campo','Valor');
		} else {
			$num_col = count(reset($array_table_var));
			if ( $num_col == 0 ) return true;
			$headers = array_keys(reset($array_table_var));		
		}
		

		echo "<table align=\"center\" class=\"data_table $aditional_classes\" summary=\"$title\" >\r\n";
		if ($title != '' ) {
			echo "<thead><tr><td class=\"table_title\" colspan=\"$num_col\">$title\r\n";		
			echo "</td></tr>\r\n</thead>\r\n";
		}
		echo "<tbody>\r\n";
		echo "<tr class=\"column_header\">";
		foreach ($headers as $value) {
			echo "<td style=\"padding: 1px 2px 1px 4px;\"><b>$value</b></td>\r\n";
		}
		echo "</tr>\r\n";
		$par = true;
		$class[true] = "class=\"odd\"";
		$class[false] = "class=\"even\"";
		if (!is_array(reset($array_table_var)) ) {
			foreach($array_table_var as $key => $value) {
				$row = array($key,$value);
				echo "<tr>";
				foreach ($row as $value) {
					echo "<td ".$class[$par]."  style=\"padding: 1px 2px 1px 4px; text-align: $align\">$value</td>\r\n";
				}
				echo "</tr>\r\n";
				$par = ! $par;
			}			
		} else {
			foreach($array_table_var as $row) {
				echo "<tr>";
				foreach ($row as $value) {
					echo "<td ".$class[$par]."  style=\"padding: 1px 2px 1px 4px; text-align: $align\">$value</td>\r\n";
				}
				echo "</tr>\r\n";
				$par = ! $par;
			}
		}
		echo "</tbody></table><br />\r\n";		
		return true;
	}

    /**
     * @deprecated
     */
	static function urls_to_links($text, $img_url ='../img/link.gif',  
								  $use_overlib=true, $class="auto_link", $target="_blank"
						   ) {
		//Esta funciÛn aÒade enlaces a urls http, https, ftp y simples direcciones sin los anteriores.
		//no aÒade direcciones de email.
		//No aÒade link a algo que ya sea link, o este entre comillas o cualquier caracter que lo delimite;
		//tiene que ser una palabra sola entre espacios o principio o fin de linea para que le aÒada el html
		//correspondiente.
		//
		//fallo conocido: No procesa el enlace si se encuentra en la primera linea de texto

		$html_overlib='';
		if ($use_overlib) {
			$html_overlib = "onmouseover=\"return overlib('\\0');\" onmouseout=\"return nd();\" ";
		}
		$html_class='';
		if ($class!='') {
			$html_class="class=\"auto_link\" ";
		}
		$html_target='';
		if ($target!='') {
			$html_target="target=\"$target\" ";
		}
		$html_extra = $html_overlib.$html_class.$html_target;
		$html_image = '';
		if ($img_url && $img_url != '' ) {
			$html_image = "<img src=\"$img_url\" alt=\"link\" \>";
		}

		$extensions="com|es|org|net|co\\.uk|pt|tv|info|tk";
		$alphanumeric="0-9a-zA-Z·ÈÌÛ˙‡ËÏÚ˘‰ÎÔˆ¸¡…Õ”⁄¿»Ã“ŸƒÀœ÷‹Ò—„ı√’Á«";

		//1∫ buscamos links sin http, solo extensiones conocidas
		$text = ereg_replace("([[:space:]]|\A)([${alphanumeric}_\.-]+)?(\.($extensions))(:[0-9]+)?([$alphanumeric/_?%=&+\.-]+)?",
		"\\1<a href=\"http://\\2\\3\\5\\6\" $html_extra>\\2\\3$html_image</a>", $text);

		//2∫ buscamos links con http o https, permitimos cualquier extension de 2 a 4 letras
		$text = ereg_replace("([[:space:]]|\A)(http://|https://)([${alphanumeric}_\.-]+)?(\.([[:alpha:]]{2,4}))(:[0-9]+)?([$alphanumeric/_?%=&+\.-]+)?",
		"\\1<a href=\"\\2\\3\\4\\6\\7\" $html_extra>\\3\\4$html_image</a>", $text);

		//3∫ buscamos links con ftp, permitimos cualquier extension de 2 a 4 letras
		$text = ereg_replace("([[:space:]]|\A)(ftp://)([${alphanumeric}_\.-]+)?(\.([[:alpha:]]{2,4}))(:[0-9]+)?([$alphanumeric/_?%=&+\.-]+)?",
		"\\1<a href=\"ftp://\\3\\4\\6\\7\" $html_extra>ftp://\\3\\4\\5$html_image</a>", $text);

		return $text;
	}
    /**
     * @deprecated
     */
	static function get_overlib_alink($html_hint,$url='',$width='200') {
		if ($url=='') {
			$url = 'javascript:void(0);'; 
		}
		$result = "<a href=\"$url\" ";
		$result .= "onmouseover=\"return overlib('".htmlentities($html_hint, ENT_QUOTES)."',WIDTH, $width);\" ";
		$result .= "onmouseout=\"return nd();\">";
		
		return $result;
		
	}

}
?>
