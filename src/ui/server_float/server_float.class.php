<?php
class server_float {
	public static function toString() {
		return '';
		$tipo='produccion';
		$local = false;
		if (strpos(website::$base_url,'sernetcanf_c') !== false) {
			$tipo = 'test_codigo';
		}
		if (strpos(website::$base_url,'sernetcanf_b') !== false) {
			$tipo = 'test_base_datos';
		}
		if ($_SERVER['SERVER_NAME']=='localhost') {
			$local = true;
		}

        $color = 'white';	
        $result = '';	$mant = false;
        if ( website::$user->is_logged_in() ) {
            $result = self::get_maintenance_msg();
            $mant = ($result != '');
        }
        if ( ! $local && $tipo=='produccion' && $result == '' ) return '';
        
        $result = '<img src="'.website::$base_url.'/img/server.png" alt="" style="width:31px; height:41px; margin: 0px 3px 2px -36px;float:left; " />' . $result;
		switch($tipo) {
			case 'produccion':    
				if ( $local && ! $mant) $result .= '<b>Acceso local</b>';
                if ( $local ) $color = '#35D5F1';
                else $color = 'lightgreen';
				break;
			case 'test_codigo':
                if ( ! $mant) { 
                    $result .= "Pruebas de código,<br /><b>sobre datos reales</b><br />";
                    $result .= 'Base de datos: '.website::$database->db_config->db_name;
                }
				$color = 'yellow';
				break;
			case 'test_base_datos':
                if ( ! $mant) {
				    $result .= "Pruebas de código,<br />sobre copia de los datos<br />";
                    $result .= 'Base de datos: '.website::$database->db_config->db_name;
                }
				$color = 'green';
				break;
		}
		if (website::in_developer_mode() && ! $mant) {
			$result .= '<br />Mensajes de error activados';
		}
		
		$result = "<div id=\"server_float_div\" style=\"z-index:100; position:absolute;top:12px;left:6px; font-size:12px; border:1px solid white;display:inline;\"><div style=\"border:1px solid #4E4E4E; min-height:40px; padding:7px 7px 7px 41px; opacity:0.95; background-color:$color;\">$result</div></div>";
		return $result;
	}
    protected static function duration_readable_str($seconds) {
        $dur = $seconds;
        $dur_s = $dur % 60;
        $dur_m = floor($dur / 60) % 24;
        $dur_h = floor($dur / 3600) % 24;
        $dur_d = floor($dur / (24*3600));
        
        $dur_f = '';
        if ($dur_d > 0) {
            //Dura uno o más días
            $dur_f .= ($dur_d == 1) ? "1 día" : $dur_d." días";
            if ($dur_h > 0) {
                $dur_f .= ($dur_h == 1) ? " y 1 hora, " : " y ".$dur_h." horas, ";
            }
        } else {
            //Dura menos de un día
            if ($dur_m>0) {
                $dur_f .= ($dur_m == 1) ? "1 minuto, " : $dur_m." minutos, ";
                $dur_f .= ($dur_s > 1) ? $dur_s." segundos" : "";
            } else {
                $dur_f .= ($dur_s > 0) ? $dur_s." segundos" : "";
            }
        }
        return $dur_f;
    }
    public static function get_maintenance_msg() {
        if ( ! website::is_near_wip(60*30+30) ) {
            return '';
        }
        $start = strtotime(website::get_wip_start());
		$end   = strtotime(website::get_wip_end());
        
        $ini = $start - time(); //$ini = 60*20;
        $dur = $end - $start;
        
        if (date('m-d-Y',$start) == date('m-d-Y',$end)) {
            $end_f = "a las ".date('H:ia',$end);
        } else {
            $end_f = "el ".date('m-d-Y',$end)." a las ".date('H:ia',$end);
        }
        
        $start = date('m-d-Y H:ia',$start);
        $end = date('m-d-Y H:ia',$end);
        
        $dur_f = self::duration_readable_str($dur);
        $ini_f = self::duration_readable_str($ini);
        
        if ($dur_f != '' ) {
            $dur_f = "Estará inaccesible durante ".$dur_f;
        }       
        
        $result = '';
        
        $result .= "<div id=\"wip_div_float\"><span id=\"wip_msg\">El servidor entrará en mantenimiento en </span><b><span id=\"countdown\" >".$ini_f."</span></b>.<br />";//, en ".$start."<br />";
        $result .= $dur_f."reactivándose ".$end_f.".<br />";
        $result .= "<span id=\"notifier\">Guarde su trabajo antes de que empiece el periodo de mantenimiento</b>.</span></div>";
        $result .= '<script type="text/javascript">       
                      setTimer('.$ini.', {
                        180: function () { 
                            document.getElementById("notifier").innerHTML="Falta <b>menos de 5 minutos</b> para el mantenimiento, <b>guarde su trabajo</b>."; 
                        },
                        30: function () { 
                            document.getElementById("notifier").innerHTML="<b>Falta menos de 30 segundos, se va a cerrar el sistema</b>.";        
                        },
                         0: function () { 
                              
  
                            startWIPEndCountdown();       
                         }
                      });
                      
                      function startWIPEndCountdown() {
                        
                        var divTag = document.createElement("div");
                        divTag.id = "div_block_website";
                        divTag.setAttribute("align","center");                            
                        divTag.style.cssText = "z-index:10; position:absolute; left: 0px; top: 0px; width:100%; height:100%; background-color: white; opacity:0.9;";                            
                        divTag.innerHTML = "&nbsp;";    
                        document.getElementById("wip_msg").innetHTML = "El mantenimiento durará ";                        
                        document.body.appendChild(divTag);
                        document.getElementById("wip_div_float").innerHTML="<b>Ha comenzado el mantenimiento del sistema</b>, este durará <span id=\"countdown\">&nbsp;</span>.<br />Espere o vuelva a acceder cuando haya finalizado.";  
                        if ( document.getElementById("cambios") && document.getElementById("cambios").display != "none") {
                            document.getElementById("wip_div_float").innerHTML += "<br /><b>Usted tenía cambios sin guardar, verifíquelos cuando finalice el mantenimiento</b>";
                        }
                        
                        setTimer('.$dur.', {
                            0: function () {
                                 document.body.removeChild(document.getElementById("div_block_website"));
                                 document.getElementById("wip_div_float").innerHTML="<b>Ha finalizado el mantenimiento del servidor</b>.<br />Puede seguir utilizando el sistema.";
                                 if ( document.getElementById("cambios") && document.getElementById("cambios").display != "none") {
                                    document.getElementById("wip_div_float").innerHTML += "<br /><b>Verifique que los cambios que tenía sin guardar se almacenan correctamente.</b>";
                                 }
                            }
                        });
                        document.activeElement.blur();
                      }

                      </script>';
                      
        return $result;

    }
}
