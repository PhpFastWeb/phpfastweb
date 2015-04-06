<?php
class collection_prototype_basic extends acollection_prototype implements icollection_prototype {

	public function get_header() {

		$result = $this->get_pre_header ();
		$result .= $this->get_title ();
		$result .= $this->get_filter ();
		//$this->print_table_pre_rows_header();
		//$this->get_rows_header ();
		return $result;
	}

	//--------------------------------------------------------------------------------------

	function get_pre_header() {
		$result = "";

		//$result .= $this->table->table_caption ."<br />\r\n";
		
		return $result;
	}
	function get_title() {
		$result = "";
		$title = $this->table->table_title != "" ? $this->table->table_title : ucfirst ( $this->table_name );
		//Paginación al principio de la colección
//		if ($this->table->pag && $this->table->pag_end > 1) {
//			//$this->print_pag_icons ();
//		} 
		$result .= "<h2>$title</h2>";
		$result .= $this->table->table_summary ."<br />\r\n";
		if ($this->table->filter == true) {
			$result .=  "<div id=\"show_filter\" ><a href=\"#\" onclick=\"JavaScript:showhide('filter',true);showhide('show_filter',false); return false;\" >";
			$result .=  "<img class=\"no_print\" src=\"" . website::$theme->get_img_dir () . "/mostrar_filtro.gif\" alt=\"Mostrar filtro\" border=\"0\" align=\"right\" style=\"vertical-align: middle; float:right;\"/></a></div>";
		}
		return $result;
	}
//	function get_footer() {
//		$pagination = new pagination($this);
//		$pag_ui = new pagination_ui($pagination);
//		
//		return $pag_ui->get_pag_navigation();
////		if ($this->filter == null ) {
////			$this->filter = new filter();
////			$this->filter->data_set = $this->collection->data_set;
//	}
	function get_filter() {
		if ($this->table->filter != true)
			return '';
		$result = '';
		$result .= '<div style="display: none; text-align: center; " id="filter">';
		
		$result .= "<table summary=\"Cabecera de Filtros\" align=\"center\" class=\"filter_table_title\" width=\"100%\" ><tr><td><img src=\"" . website::$theme->get_img_dir () . "/spacer.gif\" width=\"75\" height=\"17\" alt=\"\" id=\"filter_spacer1\" /><br /></td>";
		//class=\"no_print\"
		$result .= "<td width=\"90%\" style=\"text-align: center;\">";
		$result .= "\t\t\t\t\t<p><b><img src=\"" . website::$theme->get_img_dir () . "/search.gif\" border=\"0\" alt=\"Filtro\" /> Filtro</b></p>";
		$result .= "</td><td>";
		$result .= "<img src=\"" . website::$theme->get_img_dir () . "/spacer.gif\" width=\"75\" height=\"1\" alt=\"\" id=\"filter_spacer2\" /><br />";
		$result .= "<a href=\"#\" onclick=\"javascript: showhide('show_filter',true); showhide('filter',false); return false;\">";
		$result .= "<img src=\"" . website::$theme->get_img_dir () . "/ocultar_filtro.gif\" alt=\"Ocultar filtro\" align=\"right\" border=\"0\" style=\"vertical-align: middle; \" />";
		$result .= "</a></td></tr></table><center>";
		$this->table->filters->print_filter_form ();
		$result .= "</center></div>\r\n";
	}
//	
//	function get_actions_header() {
//		$result = '';
//		
//		foreach ( $this->table->commands as $cmd ) {
//			$cmd = acommand::cast ( $cmd );
//			if ($cmd instanceof icommand_external 
//				&& ! ($cmd instanceof icommand_row) 
//				&& ! ($cmd->get_key() != $this->table->default_command ) ) {
//				$cmd = acommand_external::cast ( $cmd );
//				$result = $cmd->get_execute_link ();
//				if ($cmd->get_key() == $this->table->default_click_command) {
//					$result = "<b>$result</b>";
//				}	
//			}
//		}
//		return $result;
//	}
	


	function get_footer() {
		
		$result = '';
		

		if ($this->table->pag && $this->table->pag_end > 1) {
			$result .=  "<div class=\"pag_footer\" />";
			//Actual
			$result .=  "<b>P&aacute;gina ";
			$result .=  round ( $this->table->row_ini / $this->table->pag_items_pag, 0 ) + 1;
			$result .=  "</b>";
			if ($this->table->pag_items_total >= 0) {
				$result .=  " de " . $this->table->pag_end;
				if (! $this->table->pag_read_items_total)
					$result .=  "?";
			}
			$result .=  ", filas <b>" . ($this->table->row_ini + 1) . " a " . ($this->table->row_end + 1) . "</b>.\r\n";
			if ($this->table->pag_items_total >= 0) {
				$result .=  "&nbsp;&nbsp;\r\n";
				$result .=  "Total <b>{$this->table->pag_items_total} fila";
				if ($this->table->pag_items_total != 1)
					$result .=  "s";
				$result .=  "</b> en <b>";
				$result .=  $this->table->pag_end;
				$result .=  " p&aacute;gina";
				if ($this->table->pag_end != 1)
					$result .=  "s";
				$result .=  "</b>.<br /><br />";
			}
			//$result .=  "<table summary=\"Lista de p&aacute;ginas\" class=\"no_print\"><tr><td style=\"text-align: left;\">\r\n";
			if ($this->table->pag_read_items_total) {
				$result .=  "P&aacute;gina";
				if ($this->table->pag_end > 1)
					$result .=  "s";
				$result .=  ": ";
				
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
							$result .=  "<font class=\"pag_actual\">$i</font>&nbsp;\r\n";
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
			if (! $this->table->pag_read_items_total || $this->table->pag_end >= 21) {
				
				if ($this->table->pag_ini == 1) {
					$result .=  "&lt;&lt;&lt;&nbsp;&nbsp;|&nbsp;&nbsp;<font class=\"pag_actual\">1</font>&nbsp;\r\n";
				} else {
					$url = new url ();
					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini - 1 ) );
					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini - 1 ) );
					$url->add_vars_def ( $this->get_href_vars () );
					$result .= $this->get_a ( $url->__toString (), 'Anterior', 'link_page' );
					$result .=  "&lt;&lt;&lt;</a>&nbsp;&nbsp;|&nbsp;\r\n";
					$url = new url ();
					$url->set_var ( 'row_ini', $this->table->get_row_ini ( 1 ) );
					$url->set_var ( 'row_end', $this->table->get_row_end ( 1 ) );
					$url->add_vars_def ( $this->get_href_vars () );
					$result .= $this->get_a ( $url->__toString (), '1', 'link_page' );
					$result .=  "1</a>&nbsp;&nbsp;\r\n";
				}
				if ($this->table->pag_ini > 2) {
					$result .=  "...&nbsp;&nbsp;\r\n";
					$url = new url ();
					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini - 1 ) );
					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini - 1 ) );
					$url->add_vars_def ( $this->get_href_vars () );
					$result .= $this->get_a ( $url->__toString (), ($this->table->pag_ini - 1), 'link_page' );
					$result .=  ($this->table->pag_ini - 1) . "</a>&nbsp;\r\n";
				
				}
				
				if ($this->table->pag_ini != 1 && (! $this->table->pag_read_items_total || $this->table->pag_ini != $this->table->pag_end)) {
					//Página actual, siempre se muestra si no se mostrá ya en la primera, o en la última
					$result .=  "<font class=\"pag_actual\">" . $this->table->pag_ini . "</font>&nbsp;\r\n";
				}
				
				if (! $this->table->pag_read_items_total || $this->table->pag_ini < ($this->table->pag_end - 1)) {
					//No se ha leido, o no es página última ni penultima
					$url = new url ();
					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini + 1 ) );
					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini + 1 ) );
					$url->add_vars_def ( $this->get_href_vars () );
					$result .= $this->get_a ( $url->__toString (), ($this->table->pag_ini + 1), 'link_page' );
					$result .=  ($this->table->pag_ini + 1) . "</a>&nbsp;\r\n";
					$result .=  "...&nbsp;&nbsp;";
				}
				
				if ($this->table->pag_read_items_total && $this->table->pag_ini == $this->table->pag_end) {
					//Se ha leido y es página final
					$result .=  "<font class=\"pag_actual\" />" . $this->table->pag_end . "</font>&nbsp;&nbsp;|&nbsp;&nbsp;>>>";
				} else {
					
					if ($this->table->pag_read_items_total) {
						//Se ha leido y no es página final
						$url = new url ();
						$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_end ) );
						$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_end ) );
						$url->add_vars_def ( $this->get_href_vars () );
						$result .= $this->get_a ( $url->__toString (), $this->table->pag_end, 'link_page' );
						$result .=  $this->table->pag_end . "</a>&nbsp;\r\n";
					}
					//Enlace a siguiente
					$result .=  "|&nbsp;&nbsp;";
					$url = new url ();
					$url->set_var ( 'row_ini', $this->table->get_row_ini ( $this->table->pag_ini + 1 ) );
					$url->set_var ( 'row_end', $this->table->get_row_end ( $this->table->pag_ini + 1 ) );
					$url->add_vars_def ( $this->get_href_vars () );
					$result .= $this->get_a ( $url->__toString (), 'Siguiente', 'link_page' );
					$result .=  ">>></a>";
				}
			}
			$result .= $this->get_paginator_jump ();
			if (! $this->table->pag_read_items_total) {
				$result .=  "<i>Para acelerar la carga, no se est&aacute; calculando el total de filas de la tabla.</i>";
			}
			$result .=  "";
			return $result;
		}

	}
	function get_paginator_jump() {
		$result = '';
		$result .=  "&nbsp;&nbsp;&nbsp;&nbsp;\r\n<form action=\"".html_template::get_php_self()."\" method=\"post\" style=\"display:inline; margin: 0 0 0 0;\">";
		$result .=  "P&aacute;gina <input type=\"text\" value=\"" . $this->table->pag_ini . "\" name=\"pag_jump\" id=\"pag_jump\" style=\"width: 38px; font-size: 9px;\" onclick=\"javascript: this.value='';\"/>&nbsp;&nbsp;";
		$result .=  "<select name=\"pag_items_pag\" onchange=\"javascript: this.form.submit();\"  style=\"width: 116px; font-size: 9px;\">";
		$result .=  "<option value=\"" . $this->table->pag_items_pag . "\">Items por p&aacute;gina (" . $this->table->pag_items_pag . ")</option>";
		$result .=  "<option value=\"5\">5&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		$result .=  "<option value=\"10\">10&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		$result .=  "<option value=\"15\">15&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		$result .=  "<option value=\"20\">20&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		$result .=  "<option value=\"50\">50&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		$result .=  "<option value=\"100\">100&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		$result .=  "<option value=\"" . $this->table->pag_items_total . "\">todos (" . $this->table->pag_items_total . ")</option>";
		$result .=  "</select>";
		$result .=  "</form>";
		return $result;
	}
	
	function get_a($href, $title = '', $class = '') {
		$result = '';
		if ($this->table->submit_links) {
			if ($href [0] == '?') {
				$href = html_template::get_php_self(). $href;
			}
			$href = htmlentities ( $href );
			$result .= "<a href=\"#\" onclick=\"javascript:submit_jump('" . $href . "','$this->submit_form_name');\"";
		} else {
			$result .=  "<a href=\"$href\"";
		}
		if ($title != '') {
			$result .=  " title=\"$title\"";
		}
		if ($class != '') {
			$result .=  " class=\"$class\"";
		}
		$result .=  ">";
		return $result;
	}
	function print_pag_icons() {
		if ($this->table->pag) {
			echo "<div style=\"vertical-align:middle;\">";
			//Determinamos nuestro posicionamiento en páginas
			$p_ini = $this->table->pag_ini;
			$p_fin = $this->table->pag_end;
			
			//Primera
			$ini = 0;
			$end = $this->table->pag_items_pag - 1;
			if ($this->table->row_ini != $ini) {
				$url = new url ();
				$url->set_var ( 'row_ini', $ini );
				$url->set_var ( 'row_endini', $end );
				$url->add_vars_def ( $this->get_href_vars () );
				$result .= $this->get_a ( $url->__toString (), '', "pag_link" );
				echo "<img src=\"" . website::$theme->get_img_dir () . "/first.gif\" border=\"0\" alt=\"Primera [$ini-$end]\" style=\"vertical-align:middle;\" class=\"no_print\" />";
				echo "</a>";
			} else {
				echo "<img src=\"" . website::$theme->get_img_dir () . "/first_disabled.gif\" border=\"0\"  alt=\"Primera [$ini-$end]\" style=\"vertical-align:middle;\" class=\"no_print\" />";
			}
			
			//Anterior
			

			$ini = ($p_ini - 2) * $this->table->pag_items_pag;
			$end = $ini + $this->table->pag_items_pag - 1;
			if ($ini >= 0) {
				$url = new url ();
				$url->set_var ( 'row_ini', $ini );
				$url->set_var ( 'row_endini', $end );
				$url->add_vars_def ( $this->get_href_vars () );
				$result .= $this->get_a ( $url->__toString (), '', "pag_link" );
				echo "<img src=\"" . website::$theme->get_img_dir () . "/previous.gif\" border=\"0\"  alt=\"Anterior [$ini-$end]\"  style=\"vertical-align:middle;\" class=\"no_print\" />";
				echo "</a>";
			} else {
				echo "<img src=\"" . website::$theme->get_img_dir () . "/previous_disabled.gif\" border=\"0\" alt=\"\" style=\"vertical-align:middle;\" class=\"no_print\" />";
			}
			
			//Actual
			//echo "<img src=\"".website::$theme->get_img_dir()."/info.gif\" border=\"0\"  alt=\"Actual [".$this->table->row_ini."-".$this->row_end."]\" class=\"no_print\" />\r\n";
			
			echo "<a class=\"table_pag_number\" ";
			echo "onclick=\"return false;\" href=\"#\" ";
			echo "title=\"P&aacute;gina $p_ini";
			if ($p_fin != '')
				echo " de $p_fin";
			echo ' - Filas ' . $this->table->row_ini . " a " . $this->table->row_end;
			echo "\" >";
			echo $p_ini;
			if ($p_fin != '')
				echo " / $p_fin";
			echo "</a>";
			
			//Siguiente
			$ini = $this->table->row_end + 1;
			$end = $this->table->row_end + $this->table->pag_items_pag;
			if ($end > ($this->table->pag_items_total - 1) && $this->table->pag_items_total >= 0) {
				$end = ($this->table->pag_items_total - 1);
			}
			if ($ini <= ($this->table->pag_items_total - 1) || ! $this->table->pag_read_items_total) {
				$url = new url ();
				$url->set_var ( 'row_ini', $ini );
				$url->set_var ( 'row_endini', $end );
				$url->add_vars_def ( $this->get_href_vars () );
				$result .= $this->get_a ( $url->__toString (), '', "pag_link" );
				echo "<img src=\"" . website::$theme->get_img_dir () . "/next.gif\" border=\"0\"  alt=\"Siguiente [$ini-$end]\" style=\"vertical-align:middle;\" class=\"no_print\" />";
				echo "</a>";
			} else {
				echo "<img src=\"" . website::$theme->get_img_dir () . "/next_disabled.gif\" border=\"0\" alt=\"\" style=\"vertical-align:middle;\" class=\"no_print\" />";
			}
			
			//Última
			if ($this->table->pag_read_items_total) {
				//die (($this->pag_items_total / $this->pag_items_pag));
				$ini = $this->table->pag_items_pag * (ceil ( $this->table->pag_items_total / $this->table->pag_items_pag ) - 1);
				//$ini = $this->pag_items_total - my_bcmod($this->pag_items_total,$this->pag_items_pag);
				$end = $this->table->pag_items_total - 1;
				if ($ini >= 0 && $this->table->row_end < ($this->table->pag_items_total - 1)) {
					$url = new url ();
					$url->set_var ( 'row_ini', $ini );
					$url->set_var ( 'row_endini', $end );
					$url->add_vars_def ( $this->get_href_vars () );
					$result .= $this->get_a ( $url->__toString (), '', "pag_link" );
					echo "<img src=\"" . website::$theme->get_img_dir () . "/last.gif\" border=\"0\"  alt=\"&Uacute;ltima [$ini-$end]\" style=\"vertical-align:middle;\" class=\"no_print\" />";
					echo "</a>";
				} else {
					echo "<img src=\"" . website::$theme->get_img_dir () . "/last_disabled.gif\" border=\"0\" alt=\"\" style=\"vertical-align:middle;\" class=\"no_print\" />";
				}
			}
			//echo "<br />\r\n";
			echo "</div>";
		}
	}
	
	function get_link_button($href, $text, $title = '') {
		$result = '';
		$result .=  "<a href=\"$href\" class=\"link_button\"";
		if ($title != '') {
			$result .= " title=\"$title\"";
		}
		$result .= ">";
		$result .= $text;
		$result .= "</a>";
		return $result;
	}

	function get_href_vars($order_columns = true, $order_filters = true, $href_extra = true, $view_state = true) {
		$result = '';
		if ($this->table->search_string != '') {
			$result = url::$url_separator . $this->table->search_varname . "=" . url::encode_var ( $this->table->search_string );
		}
		if ($order_filters) {
			$result .= ($this->table->filters_url == '') ? '' : url::$url_separator . $this->table->filters_url;
			if ($this->table->pag) {
				$result .=  'pag_items_pag=' . $this->table->pag_items_pag;
			}
		}
		if ($order_columns) {
			if ($this->table->order_column != '') {
				if ($this->table->order_column != $this->table->order_default_column) {
					$result .= url::$url_separator . 'order_column=' . $this->table->order_column;
				}
				if ($this->table->order_order != $this->table->order_default_order) {
					$result .= url::$url_separator . 'order_order=' . $this->table->order_order;
				}
				
			}
		}
		if ($href_extra) {
			//TODO: Quitar esto
		//				$extra = $this->get_href_extra();
		//				$result = ( $extra == '' ) ? $result : $result.url::$url_separator.$extra;
		}
		if ($view_state) {
			if ($this->table->meta_use && $this->table->meta_state_use && isset ( $_GET ['view_state'] )) {
				$result .= url::$url_separator . 'view_state=' . $_GET ['view_state'];
			}
		}
		return $result;
	}

}
?>