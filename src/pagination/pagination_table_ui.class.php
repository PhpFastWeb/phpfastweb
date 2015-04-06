<?php

class pagination_table_ui {
    public $table;
    public $search_string;
    function __toString() {
        if ( $this->table->pag && ( $this->table->pag_end > 1 || $this->table->pag_show_allways) && $this->table->pag_items_total>0) {
    			echo "<div class=\"pag_footer\" >";
    
    			//Actual
    			echo "<b>P&aacute;gina ";
    			echo round ( $this->table->row_ini / $this->table->pag_items_pag, 0 ) + 1;
    			echo "</b>";
    			if ($this->table->pag_items_total >= 0) {
    				echo " de " . $this->table->pag_end;
    				if (! $this->table->pag_read_items_total)
    					echo "?";
    			}
    
                
    			echo ", ".$this->table->pag_row_count_name." <b>" . ($this->table->row_ini + 1) . " a " . ($this->table->row_end + 1) . "</b>.\r\n";
    			if ($this->table->pag_items_total >= 0) {
    				echo "&nbsp;&nbsp;\r\n";
    				echo "Total <b>{$this->table->pag_items_total} {$this->table->pag_row_count_name}";
    				//if ($this->table->pag_items_total != 1)
    				//	echo "s";
    				echo "</b> en <b>";
    				echo $this->table->pag_end;
    				echo " p&aacute;gina";
    				if ($this->table->pag_end != 1)
    					echo "s";
    				echo "</b>.";
    			}
                
    			echo "<table summary=\"Lista de p&aacute;ginas\" class=\"no_print\"><tr><td style=\"text-align: left;\">\r\n";
                
    			if ($this->table->pag_read_items_total) {
    				echo "P&aacute;gina";
    				if ($this->table->pag_end > 1)
    					echo "s";
    				echo ": ";
    				
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
    							echo "<font class=\"pag_actual\">$i</font>&nbsp;\r\n";
    						} else {
    							$url = new url ();
    							$url->set_var ( 'row_ini', $ini );
    							$url->set_var ( 'row_end', $end );
    							$url->add_vars_def ( $this->get_href_vars () );
    							$this->print_a ( $url->__toString (), $i, 'link_page' );
    							echo "$i</a>&nbsp;\r\n";
    						}
    						
    						//calculamos siguiente iteración
    						$i ++;
    						$ini = $end + 1;
    					}
    				}
    			}
    			if (! $this->table->pag_read_items_total || $this->table->pag_end >= 21) {
    				
    				if ($this->table->pag_ini == 1) {
    					echo "&lt;&lt;&lt;&nbsp;&nbsp;|&nbsp;&nbsp;<font class=\"pag_actual\">1</font>&nbsp;\r\n";
    				} else {
    					$url = new url ();
    					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini - 1 ) );
    					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini - 1 ) );
    					$url->add_vars_def ( $this->get_href_vars () );
    					$this->print_a ( $url->__toString (), 'Anterior', 'link_page' );
    					echo "&lt;&lt;&lt;</a>&nbsp;&nbsp;|&nbsp;\r\n";
    					$url = new url ();
    					$url->set_var ( 'row_ini', $this->table->get_row_ini ( 1 ) );
    					$url->set_var ( 'row_end', $this->table->get_row_end ( 1 ) );
    					$url->add_vars_def ( $this->get_href_vars () );
    					$this->print_a ( $url->__toString (), '1', 'link_page' );
    					echo "1</a>&nbsp;&nbsp;\r\n";
    				}
    				if ($this->table->pag_ini > 2) {
    					echo "...&nbsp;&nbsp;\r\n";
    					$url = new url ();
    					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini - 1 ) );
    					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini - 1 ) );
    					$url->add_vars_def ( $this->get_href_vars () );
    					$this->print_a ( $url->__toString (), ($this->table->pag_ini - 1), 'link_page' );
    					echo ($this->table->pag_ini - 1) . "</a>&nbsp;\r\n";
    				
    				}
    				
    				if ($this->table->pag_ini != 1 && (! $this->table->pag_read_items_total || $this->table->pag_ini != $this->table->pag_end)) {
    					//Página actual, siempre se muestra si no se mostrá ya en la primera, o en la última
    					echo "<font class=\"pag_actual\">" . $this->table->pag_ini . "</font>&nbsp;\r\n";
    				}
    				
    				if (! $this->table->pag_read_items_total || $this->table->pag_ini < ($this->table->pag_end - 1)) {
    					//No se ha leido, o no es página última ni penultima
    					$url = new url ();
    					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini + 1 ) );
    					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini + 1 ) );
    					$url->add_vars_def ( $this->get_href_vars () );
    					$this->print_a ( $url->__toString (), ($this->table->pag_ini + 1), 'link_page' );
    					echo ($this->table->pag_ini + 1) . "</a>&nbsp;\r\n";
    					echo "...&nbsp;&nbsp;";
    				}
    				
    				if ($this->table->pag_read_items_total && $this->table->pag_ini == $this->table->pag_end) {
    					//Se ha leido y es página final
    					echo "<font class=\"pag_actual\" />" . $this->table->pag_end . "</font>&nbsp;&nbsp;|&nbsp;&nbsp;>>>";
    				} else {
    					
    					if ($this->table->pag_read_items_total) {
    						//Se ha leido y no es página final
    						$url = new url ();
    						$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_end ) );
    						$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_end ) );
    						$url->add_vars_def ( $this->get_href_vars () );
    						$this->print_a ( $url->__toString (), $this->table->pag_end, 'link_page' );
    						echo $this->table->pag_end . "</a>&nbsp;\r\n";
    					}
    					//Enlace a siguiente
    					echo "|&nbsp;&nbsp;";
    					$url = new url ();
    					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini + 1 ) );
    					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini + 1 ) );
    					$url->add_vars_def ( $this->get_href_vars () );
    					$this->print_a ( $url->__toString (), 'Siguiente', 'link_page' );
    					echo ">>></a>";
    				}
    			}
    			$this->print_paginator_jump ();
    			if (! $this->table->pag_read_items_total) {
    				echo "<i>Para acelerar la carga, no se est&aacute; calculando el total de filas de la tabla.</i>";
    			}
    			echo "</td></tr></table>";
    			//echo "<br />\r\n";
    		  echo "</div>";
    		//echo "\t</div>\r\n";
    		}
        }
    function get_href_vars($order_columns = true, $order_filters = true, $href_extra = true ) {
		$result = '';
		if ($this->search_string != '') {
			$result = url::$url_separator . $this->table->search_varname . "=" . url::encode_var ( $this->search_string );
		}
		if ($order_filters) {
			$result .= ($this->table->filters_url == '') ? '' : url::$url_separator . $this->table->filters_url;
			if ($this->table->pag) {
				$result .=  'pag_items_pag=' . $this->table->pag_items_pag;
			}
		}
		if ($order_columns) {
			if ($this->table->order_column != '') {
				$result .= url::$url_separator . 'order_column=' . $this->table->order_column . url::$url_separator . 'order_order=' . $this->table->order_order;
			}
		}
		if ($href_extra) {
			//TODO: Quitar esto
		//				$extra = $this->get_href_extra();
		//				$result = ( $extra == '' ) ? $result : $result.url::$url_separator.$extra;
		}
		return $result;
	}
    function print_a($href, $title = '', $class = '') {
		if ($this->table->submit_links) {
			if ($href [0] == '?') {
				$href = html_template::get_php_self() . $href;
			}
			$href = htmlentities ( $href );
			echo "<a href=\"#\" onclick=\"javascript:submit_jump('" . $href . "','$this->submit_form_name');\"";
		} else {
			echo "<a href=\"$href\"";
		}
		if ($title != '') {
			echo " title=\"$title\"";
		}
		if ($class != '') {
			echo " class=\"$class\"";
		}
		echo ">";
	}
    function print_paginator_jump() {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;\r\n<form action=\"" . html_template::get_php_self(). "\" method=\"post\" style=\"display:inline; margin: 0 0 0 0;\">";
		echo "P&aacute;gina <input type=\"text\" value=\"" . $this->table->pag_ini . "\" name=\"pag_jump\" id=\"pag_jump\" style=\"width: 38px; font-size: 9px;\" onclick=\"javascript: this.value='';\"/>&nbsp;&nbsp;";
		echo "<select name=\"pag_items_pag\" onchange=\"javascript: this.form.submit();\"  style=\"width: 116px; font-size: 9px;\">";
		echo "<option value=\"" . $this->table->pag_items_pag . "\">Items por p&aacute;gina (" . $this->table->pag_items_pag . ")</option>";
		echo "<option value=\"5\">5&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		echo "<option value=\"10\">10&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		echo "<option value=\"15\">15&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		echo "<option value=\"20\">20&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		echo "<option value=\"50\">50&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		echo "<option value=\"100\">100&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		echo "<option value=\"" . $this->table->pag_items_total . "\">todos (" . $this->table->pag_items_total . ")</option>";
		echo "</select>";
		echo "</form>";
	}
}