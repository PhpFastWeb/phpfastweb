<?php

class user_ui extends aweb_object {
    

    /**
     * @var user
     */
    public $user;
   	
    public function get_css_files_media_array() {
		return array( 'all' => website::$lib_base_url.'/ui/user_ui/user_ui.css' );
	}
    
	public function print_login_logut_page() {
		echo "<div style=\"text-align:center;\">";
		if ( $this->user->is_logged_in()) {
		//El usuario está logado
		//Quizás quiera ver sus datos o salir;	
			echo "<h2>Usuario registrado en el sistema</h2><br /><br />\r\n";
			echo "Login de usuario: <strong>";
			echo $this->user->get_username();
			echo "</strong>";
			echo "<br /><br /><br />\r\n";
			$this->print_logout_form();
		} else {
			echo "<h2>Acceso usuarios registrados</h2>\r\n";
			echo "Introduzca sus datos de acceso<br /><br />\r\n";
			//echo "<strong>Comenzar sesión:</strong><br /><br />";
			//Ha intentado login sin éxito?
			if ( isset($_GET['wrong_login'])) {
				echo "<span style=\"background-color:orange; display: inline-block; padding:10px 10px; margin: 10px 10px; border: 1px solid red;\">Usuario o contraseña incorrectos</span><br /><br />\r\n";
			}
			echo "<div class=\"login_div\">";
			$this->print_login_form();
			echo "</div>";
		}
		echo "</div>";
	}
    
    //----------------------------------------------------------------------------------
    
	function print_login_form( $css_login_class = 'login_form' , $action_url = null  ) {
		if ($action_url == null ) {
			$action_url = html_template::get_php_self();
		}
		$user_field = 'username';
		$pass_field = 'password';
		echo "<form name=\"form_login\" id=\"form_login\" class=\"$css_login_class\"";
		echo " action=\"$action_url\" method=\"post\">\r\n";
		if (isset($_GET['return_url'])) {
			echo "<input type=\"hidden\" name=\"return_url\" value=\"".$_GET['return_url']."\" />";
		}
        
        echo "<div style=\"text-align:right;\">";
		echo "<label id=\"label_id\">Usuario / email: </label><input type=\"text\" size=\"10\" name=\"$user_field\" id=\"$user_field\" /><br />\r\n";
		echo "<label id=\"label_password\">Contraseña: </label><input type=\"password\" size=\"10\" name=\"$pass_field\" id=\"$pass_field\" /><br />\r\n";
        echo "</div>";
        
		echo "<input type=\"hidden\" name=\"command_\" value=\"login\" />";
		//echo "Recordar usuario <input type=\"checkbox\" name=\"remember\" id=\"remember\" /><br />";
        echo "<br />\r\n";
		if (isset($_POST['command_']) && $_POST['command_'] == 'login' ) {
			echo "<i style=\"color:red\">** error de inicio de sesión **</i><br />";
		}
        if (isset($_GET['command_']) && $_GET['command_'] == 'logout_success' ) {
			echo "<b>Se cerró su sesión de usuario</b><br />";
		}
        if (isset($_GET['command_']) && $_GET['command_'] == 'session_expired' ) {
			echo "<b>Su sesión ha permanecido demasiado tiempo inactiva,<br />vuelva a identificarse para continuar</b><br />";
		}
        
		$this->print_login_submit();		
		echo "</form>\r\n";
	}
    
	function print_login_submit($submit_text='Entrar') {
		echo "<input type=\"submit\" class=\"login_submit\" value=\"$submit_text\" /><br />\r\n";
	}
    
	function print_logout_form( $action_url = null , $css_logout_class = 'logout_form'  ) {
		if ($action_url == null ) {
			$action_url = html_template::get_php_self();
		}
		echo "<form name=\"form_logout\" id=\"form_logout\" class=\"$css_logout_class\"";
		echo " action=\"$action_url\" method=\"post\" >\r\n";
		echo "<input type=\"hidden\" name=\"command_\" value=\"logout\" />";
        echo "<input type=\"button\" value=\"Continuar al sitio web\" onclick=\"javascript:this.disabled=true;document.location='".website::$base_url."/';\" />";
		$this->print_logout_submit();
		echo "</form>\r\n";
	}
    
	function print_logout_submit($submit_text='Cerrar sesión') {
		echo "<input type=\"submit\" value=\"$submit_text\" onclick=\"javascript:this.disabled=true;this.form.submit();return false;\"/>\r\n";
	}
    
	function print_logout_image( $image_url , $action=null , $prefix_string = '' ) {
		if ($action == null ) {
			$action = html_template::get_php_self();
		}
		echo '<form action="'.$action.'" method="post" style="margin:0 0 0 0;">';
		echo '<input type="hidden" name="command_" value="logout" />';
		echo $prefix_string;
		echo "<input type=\"image\" src=\"".$image_url."\" name=\"Cerrar sesión\" value=\"Cerrar sesión\" alt=\"Cerrar sesión\" style=\"vertical-align:middle;\" />\r\n";
		echo "</form>";		
	}

    function get_header_user_info() {
        $result = "";
        $result = "<div class=\"header_user_info_name\">";
        if ( ! $this->user->is_logged_in() ) {
            $result .= '<span>No ha iniciado sesión</span><br />';
           	$target = new url(website::$base_url."/fw-login.php");
            if ( html_template::get_php_self() != $this->user->loginout_failure_url ) {
                // If already on login page, we do not need a circular redirection here,
                // specially with predefined login parameters in URL
                $target->set_var('return_url',url::get_request_url());
            }
            $result .= '<a href="'.$target->__toString().'">Iniciar sesión</a><br />';
        } else {
            $result .= '<span>';
            $result .= "<b>".$this->user->get_full_name(). "</b><br />";
            $result .= $this->user->get_username();
            $gl = $this->user->get_group_names_list();
            if ( $gl != '' ) $result .= " <i>(". $gl .")</i></span><br />";
            //$result .= $this->user->get('id_empresa')."<br />";
            $target = new url(website::$base_url."/fw-login.php?command_=logout");
            $result .= '<a href="'.$target->__toString().'">Cerrar sesión</a><br />';
        }
        $result .= "</div>";
        return $result;
    }
    
    /* TO REFACTOR */
    /*
    	function print_user_data_tag($username) {
		$data = $this->get_user_data($username);
		$html = "<p align=\"left\">";
		if ( $data === false ) {
			$html .= $this->user_table_columns[$this->user_table_username]." <b>$username</b><br />";
			$html .= "<i>Usuario no encontrado</i>";
		} else {
			$html .= "<table>";
			foreach ($data as $key => $value ) {
				$html .= "<tr><td style=\"text-align: right;\">".$this->user_table_columns[$key]."</td><td><b>$value</b></td></tr>";
			}
			$html .= "</tr></table>";

		}
		$html .= "</p>";
		$url = '';
		if ( isset($data['email']) ) {
			$url="mailto: $data[email]";
		}
		html_template::print_overlib_alink($html,250,$url );
		echo $username;
		echo "</a>";
	}
     */
    
}

