<?php

class calendar {
	public $tiempo_actual = null;
	public $dia = null;
	public $mes = null;
	public $ano = null;
	public $img_dir = '';
	
	public $use_day_links = true;
	
	public $show_other_dates_links = false;
	public $change_date_url = '';
	public $use_img_links = true;
	
	public $dias_especiales = array();
	
	private $initialised = false;
	
	public function __construct() {
	}
	public function init_config() {
		if ($this->initialised) return;
		if ( $this->tiempo_actual == null ) {
			$this->tiempo_actual = time();  
		}
		if ( $this->dia == null ) {
			$this->dia = date("j", $this->tiempo_actual);  
		}
		if ( $this->mes == null ) {
			$this->mes = date("n", $this->tiempo_actual); 
		}
		if ( $this->ano == null ) {
			$this->ano = date("Y", $this->tiempo_actual); 
		}	
		$this->initialised = true;
	}
	
	public function __toString() {
		$this->init_config();
		$result = $this->mostrar_calendario($this->dia,$this->mes,$this->ano); 
		return $result;
		//$this->formularioCalendario($this->dia,$this->mes,$this->ano); 
	}
	public function get_url() {
		$url = new url();
		$url->set_var('dia',$this->dia);
		$url->set_var('mes',$this->mes);
		$url->set_var('ano',$this->ano);
		return $url;
				
	}
	//-----------------------------------------------------------
	private function mostrar_cabecera($nombre_mes,$ano,$mes_anterior,$mes_siguiente,$ano_anterior,$ano_siguiente) {
		//construyo la cabecera de la tabla
		$result = ''; 
		$result .= "<table summary=\"\" class=\"s5_calendar\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\" ><tr><td colspan=\"7\" align=\"center\">\r\n"; 
		$result .= "<table summary=\"\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"s5_calendar_title\"><tr><td>\r\n"; 
		
		$iurl = $this->img_dir;
		if ($this->show_other_dates_links) {
			$url = new url();
			$url->url_base = $this->change_date_url;
			$url->set_var('nuevo_mes',$mes_anterior);
			$url->set_var('nuevo_ano',$ano_anterior);
			$result .= "<a href=\"".$url->__toString()."\">";
			//$result .= "<a href=\"$url?nuevo_mes=$mes_anterior".url::$url_separator."nuevo_ano=$ano_anterior\">";
			if ($this->use_img_links) {
				$result .= "<img src=\"${iurl}mes_prev.gif\" alt=\"Mes Previo\" /></a>";
			} else {
				$result .= "&lt;&lt; </a>";
			}
		}
		$result .= "</td>"; 
		$result .= "<td align=\"center\">$nombre_mes $ano</td>"; 
		$result .= "<td align=\"right\">\r\n"; 
		if ($this->show_other_dates_links) {
			$url = new url();
			$url->url_base = $this->change_date_url;
			$url->set_var('nuevo_mes',$mes_siguiente);
			$url->set_var('nuevo_ano',$ano_siguiente);
			$result .= "<a href=\"".$url->__toString()."\">";
			if ($this->use_img_links) {
				$result .= "<img src=\"${iurl}mes_sig.gif\" alt=\"Mes siguiente\" /></a>";
			} else {
				$result .= " &gt;&gt; </a>";
			}
		}
		$result .= "</td></tr></table></td></tr>"; 
		$result .= ' <tr> 
			<td width="14%" align="center" class="s5_calendar_weekdays">L</td> 
			<td width="14%" align="center" class="s5_calendar_weekdays">M</td> 
			<td width="14%" align="center" class="s5_calendar_weekdays">X</td> 
			<td width="14%" align="center" class="s5_calendar_weekdays">J</td> 
			<td width="14%" align="center" class="s5_calendar_weekdays">V</td> 
			<td width="14%" align="center" class="s5_calendar_weekdays">S</td> 
			<td width="14%" align="center" class="s5_calendar_weekdays">D</td> 
		</tr>'; 
		return $result;
	}
	private function mostrar_calendario($dia, $mes, $ano) {
		$result = '';
		
		//tomo el nombre del mes que hay que imprimir 
		$nombre_mes = self::dame_nombre_mes($mes); 

		//calculo el mes y ano del mes anterior 
		$mes_anterior = $mes - 1; 
		$ano_anterior = $ano; 
		if ($mes_anterior==0){ 
    		$ano_anterior--; 
    		$mes_anterior=12; 
		} 
		//calculo el mes y ano del mes siguiente 
		$mes_siguiente = $mes + 1; 
		$ano_siguiente = $ano; 
		if ($mes_siguiente==13){ 
    		$ano_siguiente++; 
    		$mes_siguiente=1; 
		} 
		
		$result .= $this->mostrar_cabecera($nombre_mes,$ano,$mes_anterior,$mes_siguiente,$ano_anterior,$ano_siguiente);
		
		//Variable para llevar la cuenta del dia actual 
		$dia_actual = 1; 

		//calculo el numero del dia de la semana del primer dia 
		$numero_dia = $this->calcula_numero_dia_semana(1,$mes,$ano); 

		//calculo el último dia del mes 
		$ultimo_dia = $this->ultimo_dia($mes,$ano); 

		//escribo la primera fila de la semana 
		$result .= "<tr>"; 
		for ($i=0;$i<7;$i++){ 
			if ( $i < $numero_dia ) {
				$result .= "<td></td>";
			} else {
				$result .= $this->get_day_td_html($dia_actual,$mes,$ano,$i,$numero_dia);
				$dia_actual++;
			}
		}
		$result .= "</tr>\r\n";

		//recorro todos los demás días hasta el final del mes 
		$numero_dia = 0;
		while ( $dia_actual <= $ultimo_dia ) {
			
			//si estamos a principio de la semana escribo el <TR>
			if ($numero_dia == 0) {
				$result .= "<tr>";
			}

			$result .= $this->get_day_td_html($dia_actual,$mes,$ano,$numero_dia,8);
		
			$dia_actual++;
			$numero_dia++;
			
			//si es el ultimo de la semana, pongo al principio de la semana y escribo el </tr>
			if ($numero_dia == 7){
				$numero_dia = 0;
				$result .= "</tr>\r\n";
			}
		}


		//compruebo que celdas me faltan por escribir vacías de la última semana del mes
		if ($numero_dia<7) {
			//$result .= "<tr>";

			for ($i=$numero_dia;$i<7;$i++){
				$result .= "<td class=\"s5_calendar_day\"></td>";
			}

			$result .= "</tr>\r\n";
		}
		$result .= "</table>\r\n";

		return $result;
	}
	
	//----------------------------------------------------------------
	private function get_day_td_html($dia,$mes,$ano,$dia_sem,$primer_dia_mes) {
		
		//if ( $dia_sem < $primer_dia_mes ) {
			//return "<td></td>";
		//}

		if ( $dia_sem < 5 ) {
			if ( isset($this->dias_especiales[$dia]) ) {
				$clase = "s5_calendar_day_special";	
			} else {
				$clase = "s5_calendar_day";	
			}		
		} else {
			if ( isset($this->dias_especiales[$dia]) ) {
				$clase = "s5_calendar_non_labor_special";
			} else {
				$clase = "s5_calendar_non_labor";
			}
		}
		
		$result = "<td class=\"$clase\">";
		$result .= $this->get_day_html($dia,$mes,$ano,$dia_sem);	
		$result .= "</td>";
		return $result;
	}
	
	private function get_day_html($dia,$mes,$ano) {
		$result = '';

		if ($this->use_day_links) {
			$url = new url();
			$url->url_base = $this->change_date_url;
			$url->set_var('dia',$dia);
			$url->set_var('mes',$mes);
			$url->set_var('ano',$ano);
			//$url->set_var('fecha',"$dia/$mes/$ano");
			$hint = '';
			if ( isset($this->dias_especiales[$dia] ) ) {
				$hint = $this->dias_especiales[$dia];
				$result .= html_template::get_overlib_alink($hint,$url->__toString());
			} else {
				$result .= "<a href=\"".$url->__toString()."\">";
			}
			
			$result .= "$dia</a>";
		} else {
			$result .= "$dia";
		}
		return $result;		
	}
	//-----------------------------------------------------------
	private function calcula_numero_dia_semana($dia, $mes, $ano) {
		$numerodiasemana = date('w', mktime(0,0,0,$mes,$dia,$ano));
		if ($numerodiasemana == 0)
			$numerodiasemana = 6;
		else
			$numerodiasemana--;
		return $numerodiasemana;
	}
	static public function dame_nombre_mes($mes) {
		switch ($mes){
			case 1:
				$nombre_mes="Enero";
				break;
			case 2:
				$nombre_mes="Febrero";
				break;
			case 3:
				$nombre_mes="Marzo";
				break;
			case 4:
				$nombre_mes="Abril";
				break;
			case 5:
				$nombre_mes="Mayo";
				break;
			case 6:
				$nombre_mes="Junio";
				break;
			case 7:
				$nombre_mes="Julio";
				break;
			case 8:
				$nombre_mes="Agosto";
				break;
			case 9:
				$nombre_mes="Septiembre";
				break;
			case 10:
				$nombre_mes="Octubre";
				break;
			case 11:
				$nombre_mes="Noviembre";
				break;
			case 12:
				$nombre_mes="Diciembre";
				break;
		}
		return $nombre_mes;

	}
	private function ultimo_dia($mes,$ano) {
		switch($mes) {
			case 1:
			case 01: return 31; break;
			case 2: case 02:
				if (((fmod($ano,4)==0) and (fmod($ano,100)!=0)) or (fmod($ano,400)==0)) {
					$dias_febrero = 29;
				} else {
					$dias_febrero = 28;
				}
				return $dias_febrero;
				break;
			case 3: case 03: return 31; break;
			case 4: case 04: return 30; break;
			case 5: case 05: return 31; break;
			case 6: case 06: return 30; break;
			case 7: case 07: return 31; break;
			case 8: case 08: return 31; break;
			case 9: case 09: return 30; break;
			case 10: return 31; break;
			case 11: return 30; break;
			case 12: return 31; break;
		}
	}
	//-----------------------------------------------------------
}

