<?php
class command_table extends acommand_external implements icommand_external {
	
	//Definiciones adicionales
	/**
	 * @var columns_collection
	 */
	protected $columns_col;
	
	//TODO: Revisar esto
	protected $search_string;
	//protected $filters_url;
	
	private $html_table_numcols;
	
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return command_table
	 */
	public static function &cast(icommand $cmd) {
		if (! ($cmd instanceof command_table)) {
			throw new ExceptionDeveloper ( "Clase incorrecta" );
		}
		return $cmd;
	}
    
    public $item_prototype = null;
    public $no_data_msg = "<br />No hay datos<br /><br />";
    
    public $pagination = null;
    
	public function get_name() {
		return "Ver Tabla";
	}
	public function get_key() {
		return "table";
	}
	public function execute() {
		$this->print_table ();
	}
	//------------------------------------------------------------------------------------------------
	function print_table() {
		$this->table->init_config ();
		
        if ( $this->pagination == null ) {
            $this->pagination = new pagination_ui( $this->table );
        }
        
		//TODO: Cambiar esto para definirlo sobre esta instancia de objeto
		//$this->columns = $this->table->columns_table_view;
		if (is_null ( $this->table->columns_table_view ) || ! is_array ( $this->table->columns_table_view ) || count ( $this->table->columns_table_view ) == 0) {
			$source = $this->table->columns_col;
		} else {
			$source = new columns_collection ($this->table);
			foreach ( $this->table->columns_table_view as $col_name ) {
				
				if ( ! isset($this->table->columns_col[$col_name])) throw new ExceptionDeveloper("Column $col_name not in table");
				
				if ( ! $this->table->columns_col->get ( $col_name ) instanceof column_hidden &&
					$this->table->columns_col->get ( $col_name )->get_visible()	) {
					$source->append ( $this->table->columns_col->get ( $col_name ) );
				}
			}
		}
		$this->columns_col = $source;
		
        $this->print_table_init();
        if ( $this->item_prototype == null ) {
    		//$this->print_table_js();
    		$this->print_table_pre ();
    		$this->print_table_header ();
    		$this->print_table_title ();
    		$this->print_table_filter ();
    		//$this->print_table_pre_rows_header();
    		$this->print_table_rows_header ();
    		$this->print_table_rows_body ();
    		$this->print_table_footer ();
        } else {
            $this->print_item_collection();
        }
        $this->print_pagination();
    
        if ( $this->table->inline_new_record && isset($this->table->commands['new']) ) {
            $result .= '<div id="inline_new_record_" style="margin-top:5px; display:none;">';
            $result .= '<a name="inline_new_record_anchor_"></a>';
            $this->table->commands['new']->execute();
            $result .= '</div>';
        }	
	}
    function print_item_collection() {
		//Impresion de datos ------------------------------------------------------
		$odd = true;
		$i = 0;
		$extra_class = "";
		$row = website::$database->fetch_result ( $this->table->table_data );
		while ( $row ) {
			$i ++;
			$this->table->columns_col->set_values_array ( $row );
			$item = clone($this->item_prototype);
            $item->set_data( $row );
            echo $item->__toString();
            
			$odd = ! $odd;
			$row = website::$database->fetch_result ( $this->table->table_data );
		}
		if ($i == 0) {
			echo $this->no_data_msg;
		}
	}
    
	function print_table_pre() {
		
		//Inicio tabla            		
        // <script type="text/javascript">
        // </script>
            
	}
	function print_table_init() {
		//Si aun no se han traido los datos, nos los traemos
		if ($this->table->table_data == null)
			$this->table->fetch_data ();
			
		//Contamos las columnas visibles
		$i = 0;
		foreach ( $this->columns_col as $col ) {
		
			
			if (! $col instanceof column_hidden && $col->get_visible())
				$i ++;
		}
		if ($this->table->print_actions) {
			$i = $i + 2;
		}
		$this->html_table_numcols = $i;
		
		//Establecemos las alineaciones
		$this->table->process_config_array ( $this->columns_align, 'left', array ('right', 'left' ) );
	
	}
	function print_table_js() {
		//
	}
	function print_table_header() {
		echo "<div class=\"data_table_outline\">\r\n";
		echo "\t<table class=\"data_table\" summary=\"{$this->table->table_summary}\">\r\n";
		//Cabecera
		if ($this->table->table_caption != "") {
			echo "\t\t<caption>{$this->table->table_caption}</caption>\r\n";
		}
		echo "\t\t<thead>\r\n";
	}
	function print_table_title() {
		echo "\t\t\t<tr>\r\n";
		
		$title = $this->table->table_title != "" ? $this->table->table_title : ucfirst ( $this->table->table_name );
		
		echo "\t\t\t\t<td class =\"table_title\" colspan=\"$this->html_table_numcols\" >\r\n";
		
		
		echo "<table summary=\"Zona titulo de la tabla\" width=\"100%\"><tr><td nowrap=\"nowrap\">";
		if ($this->table->pag && $this->table->pag_end > 1) {
			$this->print_pag_icons ();
		} else {
			echo "<img src=\"" . website::$theme->get_img_dir () . "/spacer.gif\" width=\"78\" height=\"17\" alt=\"\" id=\"filter_spacer3\" /><br />";
		}
		echo "</td>";
		echo "<td width=\"90%\" style=\"text-align: center;\">";
		echo "\t\t\t\t\t<p>$title</p>";
		echo "</td><td>";
		//echo "<img src=\"" . website::$theme->get_img_dir () . "/spacer.gif\" width=\"78\" height=\"1\" alt=\"\" id=\"filter_spacer4\" /><br />";
		
		//if ($this->filter != true || count($this->filter_fields) == 0 ) {
		if ($this->table->filter != true) {
			echo "<img src=\"" . website::$theme->get_img_dir () . "/spacer.gif\" width=\"78\" height=\"17\" alt=\"\" id=\"filter_spacer5\" />";
		} else {
			echo "<div id=\"show_filter\" ><a href=\"#\" onclick=\"JavaScript:showhide('filter',true); showhide('show_filter',false); return false;\" >";
			echo "<img class=\"no_print\" src=\"" . website::$theme->get_img_dir () . "/mostrar_filtro.gif\" alt=\"Mostrar filtro\" border=\"0\" align=\"right\" style=\"vertical-align: middle; float:right;\"/></a></div>";
		}
		echo "</td></tr></table>";
		//echo "</p>\r\n";
		

		echo "\t\t\t\t</td>\r\n";
		echo "\t\t\t</tr>\r\n";
	}
	function print_table_filter() {
		//Impresion de filtro ------------------------------------------------------------------
		echo "\t\t\t<tr>\r\n";
		echo "\t\t\t\t<td colspan=\"$this->html_table_numcols\">\r\n";
		$this->print_filter ();
		echo "\t\t\t\t</td>\r\n";
		echo "\t\t\t</tr>\r\n";
	}
	function print_filter() {
		if ($this->table->filter != true)
			return;
		echo '<div style="display: none; text-align: center; " id="filter">';
		
		echo "<table summary=\"Cabecera de Filtros\" align=\"center\" class=\"filter_table_title\" width=\"100%\" ><tr><td><img src=\"" . website::$theme->get_img_dir () . "/spacer.gif\" width=\"75\" height=\"17\" alt=\"\" id=\"filter_spacer1\" /><br /></td>";
		//class=\"no_print\"
		echo "<td width=\"90%\" style=\"text-align: center;\">";
		echo "\t\t\t\t\t<p><b><img src=\"" . website::$theme->get_img_dir () . "/search.gif\" border=\"0\" alt=\"Filtro\" /> Filtro</b></p>";
		echo "</td><td>";
		echo "<img src=\"" . website::$theme->get_img_dir () . "/spacer.gif\" width=\"75\" height=\"1\" alt=\"\" id=\"filter_spacer2\" /><br />";
		echo "<a href=\"#\" onclick=\"javascript: showhide('show_filter',true); showhide('filter',false); return false;\">";
		echo "<img src=\"" . website::$theme->get_img_dir () . "/ocultar_filtro.gif\" alt=\"Ocultar filtro\" align=\"right\" border=\"0\" style=\"vertical-align: middle; \" />";
		echo "</a></td></tr></table><center>";
		$this->table->filters->print_filter_form ();
		echo "</center></div>\r\n";
	}
	function print_table_rows_header() {
		// --- inline edit ---
		if ($this->table->inline_edit_use) {
			echo "<form name=\"data_table_form\" id=\"data_table_form\" action=\"";
			//echo $_SERVER['PHP_SELF'];
			echo html_template::request_url ();
			echo "\" method=\"post\" style=\"margin:0 0 0 0;\" >";
		}
		//Impresion de cabeceras columnas ------------------------------------------------------
		echo "\t\t\t<tr>\r\n";
		$order_text ['ASC'] = 'ascendentemente';
		$order_text ['DESC'] = 'descendentemente';
		foreach ( $this->columns_col as $key => $column ) {
			if (! $column instanceof column_hidden && $column->get_visible()) {
				echo "\t\t\t\t<td class=\"column_header\" ";
				if (! in_array ( $key, $this->table->columns_no_order )) {
					//Preparmos la URL de ordenación a la que se irï¿½ al pulsar sobre la columna
					if ($this->table->order_column == $key && $this->table->order_order == "ASC") {
						$order = 'DESC';
					} else {
						$order = 'ASC';
					}
					$url = new url ();
					$url->set_var ( 'order_column', $key );
					$url->set_var ( 'order_order', $order );
					$url->add_vars_def ( $this->get_href_vars ( false ) );
					echo "onclick=\"window.location='{$url->__toString()}';\" ";
				}
				//if ($this->table->columns_width [$key] != '') {
				//	echo 'style="width:' . $this->table->columns_width [$key] . ';" ';
				//}
				echo ">\r\n";
				echo "\t\t\t\t\t<p>\r\n";
				
				if (! in_array ( $key, $this->table->columns_no_order )) {
					$url = new url ();
					$url->set_var ( 'order_column', $key );
					if ($this->table->order_column == $key && $this->table->order_order == "ASC") {
						$url->set_var ( 'order_order', 'DESC' );
					} else {
						$url->set_var ( 'order_order', 'ASC' );
					}
					$url->add_vars_def ( $this->table->filters_url );
					$value = $column->get_short_title ();
					echo "\t\t\t\t\t\t<a href=\"{$url->__toString()}\">$value</a>";
					if ($this->table->order_column == $key && $this->table->order_order == "ASC") {
						echo "<img src=\"" . website::$theme->get_img_dir () . "/l_downarrow.gif\" border=\"0\" alt=\"orden descendente\"  />\r\n";
					} else {
						if ($this->table->order_column == $key) {
							echo "<img src=\"" . website::$theme->get_img_dir () . "/l_uparrow.gif\" border=\"0\" alt=\"orden ascendente\" />\r\n";
						} else {
							echo "<img src=\"" . website::$theme->get_img_dir () . "/l_noarrow.gif\" border=\"0\" alt=\"\" />\r\n";
						}
					}
				} else {
					echo $value;
				}
				echo "\t\t\t\t</p></td>\r\n";
			}
		
		}
		if ($this->table->print_actions) {
			$this->print_actions_header ();
		}
		echo "\t\t</tr>\r\n";
		echo "\t</thead>\r\n";
	
	}
	function print_actions_header() {
		echo "\t\t\t<td class=\"actions_cell\" nowrap=\"nowrap\" colspan=\"2\" >";
		foreach ( $this->table->commands as $cmd ) {
			$cmd = acommand::cast ( $cmd );
			if ($cmd instanceof icommand_external 
				&& ! ($cmd instanceof icommand_row) 
				&& ! ($cmd instanceof command_table)) {
				$cmd = acommand_external::cast ( $cmd );
				$result = $cmd->get_execute_link ();
				if ($cmd->get_key() == $this->table->default_click_command) {
					$result = "<b>$result</b>";
				}
				echo $result;
				
			}
		}
		echo "\t\t\t</td>\r\n";
		//			echo "\t\t\t<td class=\"actions_cell\" nowrap=\"nowrap\" >";
	//			//** checkbox **
	//			echo "\t\t\t\r\n";
	//			echo "\t\t\t</td>\r\n";		
	}
	
	function print_actions_row($row) {
		echo "\t\t\t<td class=\"actions_cell\" nowrap=\"nowrap\">\r\n";
		if (count ( $this->table->commands ) > 0) {
			echo " ";
			foreach ( $this->table->commands as $cmd ) {
				$cmd = acommand::cast ( $cmd );
                
				if (
                    $cmd instanceof icommand_row && 
                    ! ($cmd instanceof command_set) &&
                    $cmd->show_row_link_on_table  ) {
    					//$cmd = acommand_row::cast ( $cmd );
    					$cmd->set_key_set ( $this->table->key_set, $row );
    					$result = $cmd->get_execute_link ();
    					if ($cmd->get_key() == $this->table->default_click_command) {
    						$result = "<b>$result</b>";
    					}
    					echo $result;
    					echo " ";
				}
			}
		}
		echo "\t\t\t</td>\r\n";
		echo "\t\t\t<td class=\"no_print\" >\r\n";
		echo "\t\t\t</td>\r\n";
	}
	function print_table_rows_body() {
		//Impresion de datos ------------------------------------------------------
		echo "\t<tbody>\r\n";
		$odd = true;
		$i = 0;
		$extra_class = "";
		$row = website::$database->fetch_result ( $this->table->table_data );
		if (count ( $this->table->commands ) > 0 && 
            $this->table->print_actions &&
            $this->table->default_click_command != '' && 
            isset($this->table->commands[$this->table->default_click_command]) ) {
			$extra_class = "action_row";
		}
		while ( $row ) {
			//$row = $this->table->row_add_empty_cols($row);
			$i ++;
			$this->table->columns_col->set_values_array ( $row );
			$this->print_table_row ( $row, $odd, $extra_class );
			$odd = ! $odd;
			
			$row = website::$database->fetch_result ( $this->table->table_data );
		}
		if ($i == 0) {
			echo "<tr><td colspan=\"$this->html_table_numcols\" class=\"no_data\">\r\n<p>";
			echo $this->no_data_msg;
			echo "</p></td></tr>\r\n";
		}
	}
	function print_table_footer() {
		echo "\t</tbody>\r\n";
		echo "\t</table>\r\n";
        echo "</div>";
    }
    function print_pagination() {
        
        echo $this->pagination->__toString();
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
				$url->add_vars_def ( $this->pagination->get_href_vars () );
				$this->print_a ( $url->__toString (), '', "pag_link" );
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
				$url->add_vars_def ( $this->pagination->get_href_vars () );
				$this->print_a ( $url->__toString (), '', "pag_link" );
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
			echo ' - '.ucfirst($this->table->pag_row_count_name).' '.$this->table->row_ini . " a " . $this->table->row_end;
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
				$url->add_vars_def ( $this->pagination->get_href_vars () );
				$this->print_a ( $url->__toString (), '', "pag_link" );
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
					$url->add_vars_def ( $this->pagination->get_href_vars () );
					$this->print_a ( $url->__toString (), '', "pag_link" );
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
	
	function print_link_button($href, $text, $title = '') {
		echo "<a href=\"$href\" class=\"link_button\"";
		if ($title != '') {
			echo " title=\"$title\"";
		}
		echo ">";
		echo $text;
		echo "</a>";
	}
	function print_table_row($row, $odd, $extra_class = '') {
		$odd_class [false] = "even";
		$odd_class [true] = "odd";
		$this->table->row = $row;
		echo "\t\t<tr ";
		
		if ($extra_class != '') {
			$extra_class = " " . $extra_class;
		}
		echo "class=\"" . $odd_class [$odd] . $extra_class . "\" ";
		
		if ($this->table->has_primary_keys ()) {
			$id = $this->table->key_set->get_input_id ( $row );
			echo "id=\"$id\" ";
		}
		echo ">\r\n";
		$i = 0;
		foreach ( $this->columns_col as $col ) {
			if (! $col instanceof column_hidden && $col->get_visible()) {
				$col_name = $col->get_column_name ();
				echo "\t\t\t<td class=\"data_cell data_cell_{$col_name}_\" ";
				if ($this->table->has_primary_keys ()) {
					echo "id=\"$id:$col_name\" ";
				}
				$style = "";
				if (! empty ( $this->table->columns_align [$col_name] )) {
					$style .= "text-align: " . $this->table->columns_align [$col_name] . '; ';
				}
				if (! empty ( $this->table->columns_width [$col_name] )) {
					$style .= 'width:' . $this->table->columns_width [$col_name] . ';';
				}
				if ($style != "") {
					echo " style=\"$style\" ";
				}
				if ($this->table->print_actions && ! $this->table->select_dialog) {
					//if (count($this->table->commands)==0) {	
					if ($this->table->select_dialog) {
						echo " onclick=\"javascript: {$this->select_dialog_js_function}(this);\" ";
					} else {
						if ($this->table->default_click_command != '') {
							if (!isset($this->table->commands[$this->table->default_click_command])) {
								throw new ExceptionDeveloper('Default click command not defined: '.$this->table->default_click_command);
							}
							$cmd = $this->table->commands[$this->table->default_click_command];
							$cmd = acommand_row::cast ( $cmd );
							$cmd->set_key_set ( $this->table->key_set, $row );
							$oc = $cmd->get_execute_onclick ();
							echo " onclick=\"$oc\" ";
						}
					}
					//} 
				}
				echo " >";
				//TODO: Reescribir inline_edit
				echo $this->table->columns_col->get($col_name)->get_control_render();
				
				echo "</td>\r\n";
			}
			$i ++;
		}
		reset ( $row ); //reset($headers);
		if ($this->table->print_actions) {
			$this->print_actions_row ( $row );
		}
		echo "\t\t</tr>\r\n";
	}
	function print_col_inline_input($col_name, $row) {
		$this->print_table_row_col ( $col_name, $row );
		return;
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

}
?>