<?php

/**
 * @version 1.0
 */
class horizontal_menu extends aweb_object implements iweb_object {
	public function get_css_files_media_array() {
		return array( 'all' => website::$lib_base_url.'/ui/horizontal_menu/horizontal_menu.css' );
	}
	//-------------------------------------------------------------------------
	public $urls = array(
		'INICIO' => array('/'),
		'QUÉ ES LA PREVENCION' => array('/info/que_es_prevencion.php'),
		'PREVENCIÓN 10' => array('/info/pack_prevencion_10.php'),
		'PREVENCIÓN 25' => array('/info/pack_prevencion_25.php'),
		'PREVENCIÓN 50' => array('/info/pack_prevencion_50.php'),
		'CONTACTO' => array('/info/contacto.php'),
		
	);
	//-------------------------------------------------------------------------	
	public function __toString() {
	   if (website::$user->is_logged_in()) {
	       if ( website::$user->is_in_any_group(array('administrador','tecnico')) ) {
    			$this->urls = array(
    				'INICIO' => array('/'),
    				'DATOS DE USUARIOS' => array('/empresas/index.php',array('administrador','tecnico')),
    				'MODELOS' => array('/modelos/',array('administrador','tecnico')),
    				//'GENERACIÓN' => array('/negocios.php',array('administrador','tecnico')),
    				'PERFIL' => array('/usuarios/')							
    			);	
            } else {
                
                $this->urls = array(
    				'INICIO' => array('/'),
                    'MI EVALUACIÓN' => array('/empresas/evaluar/index.php/'.context::get_id_empresa().'/'),
    				'PERFIL' => array('/usuarios/')							
    			);	        
            }
		}
		
		
		foreach ($this->urls as $title => $info) {
			$url1 = $url = reset($info);
		  	$protocol = ( ! empty($_SERVER['HTTPS'] ) ) ? 'https://' : 'http://';
	  		$url = $protocol.$_SERVER['SERVER_NAME'].website::$base_url.$url;			
			if ( url::compare_url_strings(url::get_request_url(),$url) 
				 ||
				($url1 != '/' && strpos(url::get_request_url(),$url)!== false)) {
				$is_selected[$title] = " class=\"selected\" ";
			} else {
				$is_selected[$title] = "";
			}
		}	
		
	    //$style_menu_h  = 'style="min-width: 850px;"';
	    $result = '';
		//$result .= '<div id="menu_h" class="no_print" >';//'.$style_menu_h.">\r\n";
		//$result .=  "<div id=\"menu_items\" >\r\n";
		foreach ($this->urls as $title => $info) {
			$url = reset($info);
			$grupos = next($info);
			
			if ( $url == '/' || 
				 ! $grupos  || 
				 is_array($grupos) && website::$user->is_in_any_group($grupos) ) {
				$result .=  '<a '.$is_selected[$title].' href="'.website::$base_url.$url.'">'.$title.'</a>';
			}
		}
		//$result .=  '</div>';
		//$s = new session();
		//$result .=  $s->__toString();
		//$result .=  '<br style="clear:both" />';
		//$result .=  '</div>';
		$result = $this->get_html($result);
		return $result;	
	}
	public function get_html($menu_html) {
		$result = '';
		$result .= '<div id="menu_h" class="no_print" >';//'.$style_menu_h.">\r\n";
		$result .=  "<div id=\"menu_items\" >\r\n";
 		$result .= $menu_html;
		$result .=  '</div>';
		$s = new session();
		$result .=  $s->__toString();
		$result .=  '<br style="clear:both" />';
		$result .=  '</div>';
		return $result;			
	}
}