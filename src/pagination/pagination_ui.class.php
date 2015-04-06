<?php

class pagination_ui extends apagination implements ipagination {

    public $show_page_count = true;
    
    public $show_page_jump = true;

    public function __toString() {
        //return $this->get_pag_navigation();
        
        $result = '';
        if ( $this->table->pag && ( $this->table->pag_end > 1 || $this->table->pag_show_allways) && $this->table->pag_items_total>0) {
			$result .= "<div class=\"pag_footer\" >";


            if ( $this->show_page_count ) {
    			//Actual
    			$result .= "<b>P&aacute;gina ";
    			$result .= round ( $this->table->row_ini / $this->table->pag_items_pag, 0 ) + 1;
    			$result .= "</b>";
    			if ($this->table->pag_items_total >= 0) {
    				$result .= " de " . $this->table->pag_end;
    				if (! $this->table->pag_read_items_total)
    					$result .= "?";
    			}
    
                
    			$result .= ", ".$this->table->pag_row_count_name." <b>" . ($this->table->row_ini + 1) . " a " . ($this->table->row_end + 1) . "</b>.\r\n";
    			if ($this->table->pag_items_total >= 0) {
    				$result .= "&nbsp;&nbsp;\r\n";
    				$result .= "Total <b>{$this->table->pag_items_total} {$this->table->pag_row_count_name}";
    				//if ($this->table->pag_items_total != 1)
    				//	$result .= "s";
    				$result .= "</b> en <b>";
    				$result .= $this->table->pag_end;
    				$result .= " p&aacute;gina";
    				if ($this->table->pag_end != 1)
    					$result .= "s";
    				$result .= "</b>.";
    			}
            }
            
			$result .= "<table summary=\"Lista de p&aacute;ginas\" class=\"no_print\"><tr><td style=\"text-align: left;\">\r\n";
            
			if ($this->table->pag_read_items_total) {
				$result .= "P&aacute;gina";
				if ($this->table->pag_end > 1)
					$result .= "s";
				$result .= ": ";
				
				//Si son pocas páginas, mostramos enlaces a todas:
				if ($this->table->pag_end < 21) {
					
					$ini = 0;
					$i = 1;
					while ( $ini <= ($this->table->pag_items_total - 1) ) {
						//calculamos el row final
						$end = $ini + $this->table->pag_items_pag - 1;
						if ($end > ($this->table->pag_items_total - 1))
							$end = $this->table->pag_items_total - 1;
							
						//Si estamos en la actual, no mostramos enlace
						if ($ini == $this->table->row_ini && $end == $this->table->row_end) {
							$result .= "<font class=\"pag_actual\">$i</font>&nbsp;\r\n";
						} else {
							$url = new url ();
							$url->set_var ( 'row_ini', $ini );
							$url->set_var ( 'row_end', $end );
							$url->add_vars_def ( $this->get_href_vars () );
							$result .= $this->get_a ( $url->__toString (), $i, 'link_page' );
							$result .= "$i</a>&nbsp;\r\n";
						}
						
						//calculamos siguiente iteración
						$i ++;
						$ini = $end + 1;
					}
				}
			}
			if ( ! $this->table->pag_read_items_total || $this->table->pag_end >= 21 ) {
				
				if ($this->table->pag_ini == 1) {
					$result .= "&lt;&lt;&lt;&nbsp;&nbsp;|&nbsp;&nbsp;<font class=\"pag_actual\">1</font>&nbsp;\r\n";
				} else {
					$url = new url ();
					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini - 1 ) );
					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini - 1 ) );
					$url->add_vars_def ( $this->get_href_vars () );
					$result .= $this->get_a ( $url->__toString (), 'Anterior', 'link_page' );
					$result .= "&lt;&lt;&lt;</a>&nbsp;&nbsp;|&nbsp;\r\n";
					$url = new url ();
					$url->set_var ( 'row_ini', $this->table->get_row_ini ( 1 ) );
					$url->set_var ( 'row_end', $this->table->get_row_end ( 1 ) );
					$url->add_vars_def ( $this->get_href_vars () );
					$result .= $this->get_a ( $url->__toString (), '1', 'link_page' );
					$result .= "1</a>&nbsp;&nbsp;\r\n";
				}
				if ($this->table->pag_ini > 2) {
					$result .= "...&nbsp;&nbsp;\r\n";
					$url = new url ();
					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini - 1 ) );
					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini - 1 ) );
					$url->add_vars_def ( $this->get_href_vars () );
					$result .= $this->get_a ( $url->__toString (), ($this->table->pag_ini - 1), 'link_page' );
					$result .= ($this->table->pag_ini - 1) . "</a>&nbsp;\r\n";
				
				}
				
				if ($this->table->pag_ini != 1 && (! $this->table->pag_read_items_total || $this->table->pag_ini != $this->table->pag_end)) {
					//Página actual, siempre se muestra si no se mostrá ya en la primera, o en la última
					$result .= "<font class=\"pag_actual\">" . $this->table->pag_ini . "</font>&nbsp;\r\n";
				}
				
				if (! $this->table->pag_read_items_total || $this->table->pag_ini < ($this->table->pag_end - 1)) {
					//No se ha leido, o no es página última ni penultima
					$url = new url ();
					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini + 1 ) );
					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini + 1 ) );
					$url->add_vars_def ( $this->get_href_vars () );
					$result .= $this->get_a ( $url->__toString (), ($this->table->pag_ini + 1), 'link_page' );
					$result .= ($this->table->pag_ini + 1) . "</a>&nbsp;\r\n";
					$result .= "...&nbsp;&nbsp;";
				}
				
				if ($this->table->pag_read_items_total && $this->table->pag_ini == $this->table->pag_end) {
					//Se ha leido y es página final
					$result .= "<font class=\"pag_actual\" />" . $this->table->pag_end . "</font>&nbsp;&nbsp;|&nbsp;&nbsp;>>>";
				} else {
					
					if ($this->table->pag_read_items_total) {
						//Se ha leido y no es página final
						$url = new url ();
						$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_end ) );
						$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_end ) );
						$url->add_vars_def ( $this->get_href_vars () );
						$result .= $this->get_a ( $url->__toString (), $this->table->pag_end, 'link_page' );
						$result .= $this->table->pag_end . "</a>&nbsp;\r\n";
					}
					//Enlace a siguiente
					$result .= "|&nbsp;&nbsp;";
					$url = new url ();
					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini + 1 ) );
					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini + 1 ) );
					$url->add_vars_def ( $this->get_href_vars () );
					$result .= $this->get_a ( $url->__toString (), 'Siguiente', 'link_page' );
					$result .= ">>></a>";
				}
			}
            
            if ( $this->show_page_jump ) {
			     $result .= $this->get_paginator_jump ();
			}
            if (! $this->table->pag_read_items_total) {
				$result .= "<i>Para acelerar la carga, no se est&aacute; calculando el total de filas de la tabla.</i>";
			}
			
            $result .= "</td></tr></table>";
			//$result .= "<br />\r\n";
		  $result .= "</div>";
		//$result .= "\t</div>\r\n";
		}

   
        return $result;
	}
    /**
     * @return string
     */
	function get_paginator_jump() {
	    $result = '';
        $result .= "&nbsp;&nbsp;&nbsp;&nbsp;\r\n<form action=\"" . html_template::get_php_self(). "\" method=\"post\" style=\"display:inline; margin: 0 0 0 0;\">";
        $result .= "P&aacute;gina <input type=\"text\" value=\"" . $this->table->pag_ini . "\" name=\"pag_jump\" id=\"pag_jump\" style=\"width: 38px; font-size: 9px;\" onclick=\"javascript: this.value='';\"/>&nbsp;&nbsp;";
        $result .= "<select name=\"pag_items_pag\" onchange=\"javascript: this.form.submit();\"  style=\"width: 116px; font-size: 9px;\">";
        $result .= "<option value=\"" . $this->table->pag_items_pag . "\">Items por p&aacute;gina (" . $this->table->pag_items_pag . ")</option>";
        $result .= "<option value=\"5\">5&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
        $result .= "<option value=\"10\">10&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
        $result .= "<option value=\"15\">15&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
        $result .= "<option value=\"20\">20&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
        $result .= "<option value=\"50\">50&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
        $result .= "<option value=\"100\">100&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
        $result .= "<option value=\"" . $this->table->pag_items_total . "\">todos (" . $this->table->pag_items_total . ")</option>";
        $result .= "</select>";
        $result .= "</form>";
        return $result;
	}
    function get_a($href, $title = '', $class = '') {
        $result = '';
		if ($this->table->submit_links) {
			if ($href [0] == '?') {
				$href = html_template::get_php_self() . $href;
			}
			$href = htmlentities ( $href );
			$result .= "<a href=\"#\" onclick=\"javascript:submit_jump('" . $href . "','$this->submit_form_name');\"";
		} else {
			$result .= "<a href=\"$href\"";
		}
		if ($title != '') {
			$result .= " title=\"$title\"";
		}
		if ($class != '') {
			$result .= " class=\"$class\"";
		}
		$result .= ">";
        return $result;
	}
    
    /*
	public function print_page_links( $url_base = '',$max_no_pag = 9, $links_per_pag = 9 ) {
		if ($url_base=='') $url_base = html_template::get_php_self();
		if ( $this->pag->pag_end <= $max_no_pag ) {
			//Imprimimos todos los enlaces a página
			echo 'Página: ';
			echo '<font class="pages" >';
			for ($i = 1 ; $i <= $this->pag->pag_end ; $i++ ) {
				$this->print_page_link($i,$url_base,$this->pag->pag_current != $i );
				echo ' ';
			}
			echo '</font>';
		} else {
			echo 'Página: ';
			//Imprimimos las primeras
			$this->print_n_center_page_links($url_base,$links_per_pag);

		}
	}
	private function print_n_page_links($url_base,$n=5) {
		for ($i=1;$i<=$n;$i++) {
			$this->print_page_link($i,$url_base);
		}
		$i = $this->pag->pag_current-floor($n/2);
		if ($i>$n+1) echo "<span class=\"page_elipsis\">...</span>";
		while ( $i < $this->pag->pag_current+ceil($n/2) ) {
			if ($i>$n && $i<$this->pag->pag_end-$n+1 ) {
				$this->print_page_link($i,$url_base);
			}
			$i++;
		}
		if ($i<$this->pag->pag_end-$n) echo "<span class=\"page_elipsis\">...</span>";
		for ($i=$n-1;$i>=0;$i--) {
			$this->print_page_link($this->pag->pag_end-$i,$url_base);
		}
	}
	private function print_n_center_page_links($url_base,$n=9) {
		//Imprimimos enlace a la anterior
		$this->print_nav_link('&lt;',$this->pag->pag_current-1,$url_base, ($this->pag->pag_current > 1 ) );	

		//Imprimimos la primera
		$this->print_page_link(1,$url_base);
		//Imprimimos las centrales
		$i = $this->pag->pag_current-floor($n/2);
		if ($i<2) $i=2;
		if ($i>2) echo "<span class=\"page_elipsis\">...</span> ";
		for ($j=1;$j<=$n;$j++) {
			if ($i<$this->pag->pag_end ) {
				$this->print_page_link($i,$url_base);
			}
			$i++;
		}
		if ($i<$this->pag->pag_end) echo "<span class=\"page_elipsis\">...</span> ";
		//Imprimimos la última
		if ($this->pag->pag_end > 1) {
			$this->print_page_link($this->pag->pag_end,$url_base);
		}
		//Imprimimos enlace a la siguiente
		$this->print_nav_link('&gt;',$this->pag->pag_current+1,$url_base, ($this->pag->pag_current < $this->pag->pag_end ) );	
	}
	private function print_page_link($pag_num,$url_base,$enabled=true) {
		if ($this->pag->pag_current == $pag_num ) {
			echo '<font class="page_button_current">'.$pag_num.'</font> ';
		} else {
			if ( $enabled ) {
				$url = $this->pag->get_url_pag($pag_num);
				//$url->base_url = $url_base;
				echo '<a href="'.$url->__toString().'" class="page_button">'.$pag_num.'</a> ';
			} else {
				echo '<font class="page_button_disabled">'.$pag_num.'</font> ';
			}
		}
	}
	private function print_nav_link($text,$pag_num,$url_base,$enabled=true) {
		if ( $enabled ) {
				$url = $this->pag->get_url_pag($pag_num);
				//$url->base_url = $url_base;
				echo '<a href="'.$url->__toString().'" class="page_button">'.$text.'</a> ';
		} else {
				echo '<font class="page_button_disabled">'.$text.'</font> ';
		}
		
	}
	public function print_goto_form($url_base) {
		if ($this->pag->pag_end > 1) {
			$msg = '\nEspecifique una página entre 1 y '.$this->pag->pag_end;
		} else $msg = '';
		echo '
			<script type="text/javascript">
			function fpag_jump(f) {
				var max_pag = '.$this->pag->pag_end.';				
				if ( f.pag_jump.value < 1 || f.pag_jump.value > max_pag ) {
					alert("Página no válida'.$msg.'");
					return false;
				} else {
					//f.submit();
					return true;
				}
			}
			</script>';
		echo '<form action="'.$url_base.'" method="post" onsubmit="fpag_jump(this); return false;">';
		echo 'Ir a p&aacute;gina: ';
		echo '<input type="text" name="pag_jump" id="pag_jump" value="'.$this->pag->pag_current.'" style="width: 22px;height:12px; font-size: 9px;"/>';
		echo ' <input type="button" value="Ir" style="width:28px; height:20px; vertical-align:bottom" onclick="javascript:return fpag_jump(this);" />';
		echo '</form>';

	}
	public function print_pag_navigation($url_base='') {
		echo $this->get_pag_navigation($url_base);
	}
	public function get_pag_navigation($url_base='') {
		$result = '';
		if ( $url_base=='' ) $url_base = html_template::get_php_self().'?';
		$result .= '<b>P&aacute;gina '.$this->pag->pag_current.'</b> : ';
		$url = $this->pag->get_url_first();
		if ( $url != '' ) {
			$result .= '<a href="'.$url_base.$url.'" class="link_page">l<&nbsp; Primera &nbsp;</a>&nbsp;&nbsp;&nbsp;';
		} else {
			$result .= '&nbsp;&nbsp;&nbsp;&nbsp;<i>Primera</i>&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		$url = $this->pag->get_url_previous();
		if ( $url != '' ) {
			$result .= '<a href="'.$url_base.$url.'" class="link_page"><< &nbsp;Anterior </a>&nbsp;&nbsp;&nbsp;';
		} else {
			$result .= '&nbsp;&nbsp;&nbsp;&nbsp;<i>Anterior</i>&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		$url = $this->pag->get_url_next();
		if ( $url != '' ) {
			$result .= '<a href="'.$url_base.$url.'" class="link_page">Siguiente >></a>&nbsp;&nbsp;&nbsp;';
		} else {
			$result .= '&nbsp;&nbsp;&nbsp;&nbsp;<i>Siguiente</i>&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		$url = $this->pag->get_url_last();
		if ( $url != '' ) {
			$result .= '<a href="'.$url_base.$url.'" class="link_page">&nbsp; &Uacute;ltima &nbsp;&nbsp;>l </a>&nbsp;&nbsp;&nbsp;';
		} else {
			$result .= '&nbsp;&nbsp;&nbsp;&nbsp;<i>&Uacute;ltima</i>&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		$result .= "<br />";
		return $result;

	}
    */
}