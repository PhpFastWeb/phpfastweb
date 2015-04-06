<?php

class pagination_ui extends apagination implements ipagination {


    public function __toString() {
        //return $this->get_pag_navigation();
        
        $result = '';
        if ( $this->table->pag && ( $this->table->pag_end > 1 || $this->table->pag_show_allways) && $this->table->pag_items_total>0) {
			$result .= "<div class=\"pag_footer\" >";

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
			$result .= $this->get_paginator_jump ();
			if (! $this->table->pag_read_items_total) {
				$result .= "<i>Para acelerar la carga, no se est&aacute; calculando el total de filas de la tabla.</i>";
			}
			$result .= "</td></tr></table>";
			//$result .= "<br />\r\n";
		  $result .= "</div>";
		//$result .= "\t</div>\r\n";
		}

        if ( $this->table->inline_new_record && isset($this->table->commands['new']) ) {
            $result .= '<div id="inline_new_record_" style="margin-top:5px; display:none;">';
            $result .= '<a name="inline_new_record_anchor_"></a>';
            $this->table->commands['new']->execute();
            $result .= '</div>';
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
}