<?php

class session  extends aweb_object implements iweb_object {
	public function get_css_files_media_array() {
		return array( 'all' => website::$base_url.'/_blogic/ui/session/session.css' );
	}
	public function get_js_files_array() {
		return array( website::$base_url.'/_blogic/ui/session/session.js' );
	}	
	public function __toString() {
		$result = '';
		$result .= '<div id="sesion">';				
						
		$grupos = array('administrador'=>'Administrador/a','tecnico'=>'Técnico/a','supertecnico'=>'Supertécnico/a');
        	
		if ( website::$user->is_logged_in() ) {
			//$result = '<div id="sesion" style="width:190px;">';	
			$text_name_user = website::$user->data['nombre'].' '.website::$user->data['apellidos'].'<br />';
			$text_group = "Grupo: ".usuarios::$groups[website::$user->data['grupo']]."<br />";
			$provincias = info::get_provincias_andalucia();
			$text_provincias = '';
			//if (isset($provincias[website::$user->data['provincia']])) {
			//	$text_provincias = "Provincia: ".$provincias[website::$user->data['provincia']]."<br />";
			//}
			//$msg = '<div style="text-align:left;">'.$text_name_user.$text_group.$text_provincias.'</div>';
			$msg = $text_name_user.$text_group.$text_provincias;
			//echo "*".website::$user->data['provincia']."*";
		
			$result .= '<div id="sesion_usuario">';
			$result .= '<a href="'.website::$base_url.'/usuarios/login.php" onmouseover="return overlib(\''.$msg.'\', AUTOSTATUS, WRAP, FGCOLOR,\'#FFFFE1\');"';
		 	$result .= ' onmouseout="nd();">';
			$result .= website::$user->data['nombre'].' '.website::$user->data['apellidos']."</a>";
			$result .= "</div>";
			
			$result .= '<div id="sesion_cerrar">';
			$result .= '<a class="sesion_a_but" onclick="showhide(\'sesion_float\');return false;" href="'.website::$base_url.'/usuarios/login.php">';
			$result .= 'Sesión <img src="'.website::$base_url.'/img/triangle.gif" alt="" /></a></div>';
			
			$result .= '<div id="sesion_float" style="display:none;position:relative;">';
			$result .= '<div style="padding:8px 8px 0 8px; margin-bottom:5px;">';
			$result .= '<a class="sesion_a_but" style="float:right; padding:3px 3px;" href="#" onclick="showhide(\'sesion_float\');return false;">X</a>';
			$result .= '<b style="font-size:13px;">'.$text_name_user.'</b>';
			
			/*if (! empty(website::$user->data['foto'])) {
				$dir = website::$base_dir.'/__upload/usuarios/';
				
				$img = new image();
				$img->filename = website::$user->data['foto'];
				$img->dir = $dir;
				$img->generator_url = website::$base_url.'/_inc/get_img_usuario.php';
				$img->pass_dir_in_url = false;
				$result .= $img->get_thumb_img();
				$result .="<br style=\"clear:both\" />";
			}*/
		      
			//$result .= $text_group;
			//$result .= $text_provincias;
			$result .= '</div>';
            
            if ( website::$user->is_in_any_group(array('administrador','tecnico'))) {
		          $result .= '<a class="sesion_a_bar" href="'.website::$base_url.'/usuarios/">Ver mi perfil</a>';
            }
			$result .= "<form id=\"logout_min\" name=\"logout_min\" style=\"margin:2px 0 2px 0;\" action=\"".website::$base_url."/usuarios/login.php\" method=\"post\">";
			$result .= "<input type=\"hidden\" id=\"action\" name=\"action\" value=\"LOGOUT\" />\r\n";
			$result .= "<a class=\"sesion_a_bar\" href=\"#\" onclick=\"document.logout_min.submit();\">Cerrar sesión</a>";
			$result .= "</form>";
			$result .= "</div>";			
    		
    		$u = website::$user->loginout_failure_url;
            $result .= '
                <script type="text/javascript">
                setTimeout("session_expire(\''.$u.'\')", '.(website::$user->max_seconds_session * 1000).');
                </script>
            ';
        } else {
			$result .= '<a class="sesion_a_but" href="'.website::$base_url.'/info/iniciar_sesion.php">Iniciar sesión</a>';
		}        
        $result .= '</div>';
		return $result;
	}
}
?>