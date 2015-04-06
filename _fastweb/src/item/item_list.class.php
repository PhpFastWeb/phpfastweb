<?php
class item_list extends item {
	public $keys_no_details = array(
		'ID','CLASE','TIPO','TEXTO_LARGO','DIRECCION','DOMICILIO','PROVINCIA','FOTO',
		'CODIGO_POSTAL','MUNICIPIO', 'NOMBRE','EPOCA'
		,'CONTACTO_NOMBRE','CONTACTO_TELEFONO','CONTACTO_EMAIL'
		,'IMAGEN1','IMAGEN2','IMAGEN3','IMAGEN4','IMAGEN5',
		'PARRAFO_1_IMAGEN','PARRAFO_1_IMAGEN_PIE','PARRAFO_1_CABECERA','PARRAFO_1_TEXTO',
		'PARRAFO_2_IMAGEN','PARRAFO_2_IMAGEN_PIE','PARRAFO_2_CABECERA','PARRAFO_2_TEXTO',
		'PARRAFO_3_IMAGEN','PARRAFO_3_IMAGEN_PIE','PARRAFO_3_CABECERA','PARRAFO_3_TEXTO',
		'PARRAFO_4_IMAGEN','PARRAFO_4_IMAGEN_PIE','PARRAFO_4_CABECERA','PARRAFO_4_TEXTO',
		'AREA','CATEGORIA','CATEGORIA','TITULO','SUBTITULO','TITULO_IMAGEN','TITULO_IMAGEN_PIE'
	);	
	
	public $modo_directorio=true;
	public $title = '';
	//--------------------------------------------------------------------------------------	
	
	function init_config() {
		parent::init_config();
		$this->set_title();
	}
	private function set_title() {
		if ( $this->title != '' ) return;
		if ( isset($this->data['TITULO']) ) {
			$this->title = $this->data['TITULO'];
		} elseif ( isset($this->data['NOMBRE']) ) {
			$this->title = $this->data['NOMBRE'];
			if (isset($this->data['APELLIDOS'])) {
				$this->title .= ' '.$this->data['APELLIDOS'];
			}
		} else { 
			//El título será el segundo campo, ya que el primero es ID
			$this->title = $this->data[ next(array_keys($this->data)) ]; 
		}
		if ( $this->title=='') $this->title='(sin título)';
	}
	function print_thumb() {
		echo "<div class=\"item_thumb\" >\r\n";
		//$id = $this->data['ID'];
		$descripcion_corta ='';
		if ( isset($this->data['SUBTITULO']) ) {
			$descripcion_corta = $this->data['SUBTITULO'];
		} elseif ( isset($this->data['TEXTO_CORTO']) ) {
			$descripcion_corta = $this->data['TEXTO_CORTO'];
		}
		if ( ! empty($descripcion_corta) ) {
			$descripcion_corta .= '<br />';
		}
		
		
		if (isset($_GET['view_state'])) {
			$view_state = $_GET['view_state'];
		}

		$url_view = $this->get_key_set()->get_url();
		$url_view->set_var('action','VIEW');
		//$url_view->set_var('ID',$id);
		if ($view_state != 'publicado' && $view_state != '') {
			$url_view->set_var('view_state',$view_state);
		}
		
		
		if ( ! empty($this->data['TITULO_IMAGEN'])) {
			$imagen_col = 'TITULO_IMAGEN';
		} elseif ( ! empty($this->data['IMAGEN'])) {
			$imagen_col = 'IMAGEN';
		} elseif ( ! empty($this->data['IMAGEN1'])) {
			$imagen_col = 'IMAGEN1';
		} elseif ( ! empty($this->data['FOTO'])) {
			$imagen_col = 'FOTO';
		} elseif ( ! empty($this->data['FOTO1'])) {
			$imagen_col = 'FOTO1';
		} else {
			$imagen_col = '';
		}
		
		$tam_lado = 45;
		if ( $imagen_col != '' ) {
			echo "<div style=\"width: ".$tam_lado."px; height: ".$tam_lado."px; float: left; background-color: #EFEFEF;\">";
			$img = new image($this->data[$imagen_col],$this->data_set->data_source->image_dir);
			$img->thumb_max_height = $img->thumb_max_width = $tam_lado;
			$img->thumb_cache_dir_sufix = "thumb_cache_mini";
			$img->view_link = false;
			echo $img->__toString();			
			echo " </div>";
		}
		
		echo "<div class=\"item\" style=\"width:475px; float: right;\">";
		echo "<div class=\"item_title\"><a href=\"{$url_view->__toString()}\">$this->title</a></div>";
		echo "\r\n";
		//echo "<a href=\"{$url_view->__toString()}\">";
		if ($this->modo_directorio) {
			echo nl2br($descripcion_corta);
			$this->print_localizacion_thumb();	
		} else {
			echo nl2br($descripcion_corta);	
		}
		
		echo "</div>\r\n";
		
		echo "</div>\r\n";
		echo "<br style=\"clear:both;\" /><hr class=\"item_thumb_line\"  />\r\n";

	}

	//--------------------------------------------------------------------------------------	
	function print_tabs() {
		$relations = $this->data_set->data_source->relations;
		if ($relations == null || count($relations)==0 ) return;
		$separator = " | ";
		if ( ! isset($_GET['_tab']) ) {
			$tab = 1;
		} else {
			$tab = $_GET['_tab'];
		}
		$url = $this->data_set->data_source->key_set->get_url();
		$url->set_var('action','VIEW');
		if ( $tab != 1 ) {
			$url->set_var('_tab',1);			
			$result = "<a href=\"$url\">Detalles</a>".$separator;
		} else {
			$result = "<strong>Detalles</strong>".$separator;
		}
		$i=2;
		foreach ($relations as $rel) {
			
			$t = $rel->collection_b->collection->data_set->data_source->table_title;
			$n = $rel->get_count_col_b($this->key_set);
			if ($i != $tab ) {
				$url->set_var('_tab',$i);
				$result .= "<a href=\"$url\">$t ($n)</a>".$separator;
			} else {
				$result .= "<strong>$t ($n)</strong>".$separator;
			}
			$i++;
		}
		$result = substr($result,0,-strlen($separator));
		echo $result."<br /><br />";
	}
	function print_details_item() {
		$this->print_tabs();

		
		if ( isset($this->data['SUBTITULO']) ) {
			$descripcion_corta = $this->data['SUBTITULO'];
		} else { $descripcion_corta =''; }

		if ( ! empty($this->data['TITULO_IMAGEN'])) {
			$imagen_col = 'TITULO_IMAGEN';
		} elseif ( ! empty($this->data['IMAGEN'])) {
			$imagen_col = 'IMAGEN';
		} elseif ( ! empty($this->data['IMAGEN1'])) {
			$imagen_col = 'IMAGEN1';
		} elseif ( ! empty($this->data['FOTO'])) {
			$imagen_col = 'FOTO';
		} elseif ( ! empty($this->data['FOTO1'])) {
			$imagen_col = 'FOTO1';
		} else {
			$imagen_col = false;
		}
		
		if ($imagen_col) $this->print_image_div('left','0 10px 6px 0',$imagen_col,$imagen_col.'_PIE');
		echo '<h1>';
		echo $this->title;
		echo "</h1>\r\n";
		if ($descripcion_corta != '' ) {
			echo "<h2>";
			echo $this->data['SUBTITULO'];
			echo "</h2>";
		}
		//echo "<br style=\"clear:both;\" />\r\n";
		
		for ( $i=1 ; $i<=3 ; $i++ ) {
			$this->print_parrafo($i);
		}
		
		if ( isset( $this->data['TEXTO_LARGO'])) {
			echo $this->get_data('TEXTO_LARGO');
			echo "<br />\r\n";
		}
		$this->print_rest_data();
		$this->print_contacto();
		$this->print_localizacion();
		
		
		echo "<br style=\"clear:both;\" />\r\n";
		echo "<a href=\"#\" onclick=\"window.print();\" style=\"float:right;\" >";
		//echo "<img src=\"".website::$base_url."/img/print_icon.gif\" alt=\"\" align=\"top\" /> ";
		echo "Imprimir</a>";
	}
	//--------------------------------------------------------------------------------------	
	
	private function print_parrafo($n) {
		if ( ! isset( $this->data["PARRAFO_{$n}_IMAGEN"] ) && ! isset( $this->data["PARRAFO_{$n}_IMAGEN_PIE"]) && 
			! isset($this->data["PARRAFO_{$n}_CABECERA"]) && ! isset($this->data["PARRAFO_{$n}_TEXTO"])) return;
		//echo '<pre>';
		//var_dump($this->data);die;
		echo "<p style=\"clear:both;\">";
		if ( $this->data["PARRAFO_{$n}_IMAGEN"] != '' || $this->data["PARRAFO_{$n}_IMAGEN_PIE"] != '' ) {
			if ( $n % 2 != 0 ) {
				$align = 'left';
				$margin = '6px 10px 6px 0';
			} else {
				$align = 'right';
				$margin = '6px 0 6px 10px';
			}
			$align = ($n % 2 != 0 ) ? 'left' : 'right';
			$this->print_image_div($align,$margin,"PARRAFO_{$n}_IMAGEN","PARRAFO_{$n}_IMAGEN_PIE");
		}
		
		echo "<p style=\"font-size:13px; margin-bottom:6px;\"><strong>";
		echo $this->data["PARRAFO_{$n}_CABECERA"];
		echo "</strong></p>";
		
		echo nl2br($this->data["PARRAFO_{$n}_TEXTO"]);
		
		echo "<br style=\"clear: both\" /></p>";
	}
	private function print_image_div($align,$margin,$imagen_col,$pie_col) {
		if ($this->data[$imagen_col]=='') return;
		echo "<div style=\"width: 100px; padding: 4px 4px 4px 4px; border: 1px solid #EFEFEF; float: ${align}; text-align: center; margin: $margin; \"  >";	
		$img = new image($this->data[$imagen_col],$this->data_set->data_source->image_dir);

		//$img->thumb_max_height = $img->thumb_max_width = 25;
		$img->view_link = true;
		$img->thumb_align = '';
		echo $img->__toString();
		if ( isset($this->data[$pie_col]) &&  $this->data[$pie_col] != '' ) {
			echo '<br />';
			echo "<font style=\"font-size:9px; font-variant: italic; color: #303030; \">";
			echo $this->data[$pie_col];		
			echo "</font>";
		}
		echo "</div>";
	}
	
//	function print_edit() {
//	}
	private function get_data($key) {
		$result = ( ! empty( $this->data[$key] ) ) ?  $this->data[$key] : '';
		$this->keys_no_details[]=$key;
		return nl2br($result);	
	}
	private function print_rest_data() {
		$result = '';
		foreach($this->data as $key => $value) {
			if ( ! in_array($key,$this->keys_no_details) && $value != '' ) {
			   	//var_dump($this->data);
			   	
				$value_formatted = $this->data_set->data_source->get_formated_column($key,$this->data);
				if ( $value_formatted != '' ) {
					$result .= "<strong>{$this->get_column_title($key)}</strong>: $value_formatted<br />\r\n";
				}
			}
		}
		
		if ( ! empty($result) ) {
			$result = "<br />". $result;
		}
		echo $result;
		
	}
	private function get_column_title($column) {
		return $this->data_set->data_source->columns_title[$column];
	}
	private function print_localizacion() {
		if ( ! empty($this->data['DIRECCION']) || 
			 ! empty($this->data['CODIGO_POSTAL']) ||
			 ! empty($this->data['MUNICIPIO']) ||
			 ! empty($this->data['DOMICILIO']) ||
			 ! empty($this->data['PROVINCIA'])
			
		) {
			
			$result ='';
			$result .=  $this->get_formated_column('DOMICILIO');
			$result .=  $this->get_formated_column('DIRECCION');
			$result .=  $this->get_formated_column('CODIGO_POSTAL');
			$result .=  $this->get_formated_column('MUNICIPIO');
			$result .=  $this->get_formated_column('PROVINCIA');

			if ( $result != '' ) {
				echo '<div style="width:250px; border: 1px dashed lightgrey; margin:8px 3px; padding: 8px 8px;">';
				echo $result;
				echo "</div>";
			
			}
			echo "<br />\r\n";
			echo "<br />\r\n";
		}
	}
	private function get_formated_column($col_name,$separator='<br />') {
		$result = $this->data_set->data_source->get_formated_column($col_name,$this->data);
		
		if ($result != '') {
			$result = '<b>'.$this->data_set->data_source->columns_title[$col_name]."</b>: ".$result;
			$result .= $separator;
		}
		return $result;
	}
	private function print_contacto() {
		
		$nombre   = $this->get_data('CONTACTO_NOMBRE');
		$telefono = $this->get_data('CONTACTO_TELEFONO');
		$email    = $this->get_data('CONTACTO_EMAIL');
		if ( ! empty( $nombre ) || ! empty( $email ) || ! empty( $telefono ) ) {
			echo '<div style="width:200px; border: 1px solid lightgrey; margin:8px 8px; padding: 8px 8px;">'."\r\n";
			echo '<strong>Contacto</strong><br />'."\r\n";
			if ( ! empty( $nombre ) ) {
				echo $nombre.'<br />';
			}
			if ( ! empty( $telefono ) ) {
				echo 'Teléfono: '.$telefono.'<br />';
			}
			if ( ! empty( $email ) ) {
				echo $email.'<br />';
			}						
			
			echo '</div>';
		}
	}
	private function print_localizacion_thumb() {
		$result = '';
		$separator = " | ";
		//var_dump($this->data);
		$result .= $this->get_formated_column('MUNICIPIO',$separator);
		$result .= $this->get_formated_column('LOCALIDAD',$separator);		
		$result .= $this->get_formated_column('DIRECCION',$separator);
		$result .= $this->get_formated_column('CODIGO_POSTAL',$separator);

		$result .= $this->get_formated_column('CONTACTO_EMAIL',$separator);
		$result .= $this->get_formated_column('ENLACE',$separator);
		$result .= $this->get_formated_column('DNI',$separator);
		
		$result .= $this->get_formated_column('FECHA_COMIENZO',$separator);
		$result .= $this->get_formated_column('FECHA_FIN',$separator);
		
		$result .= $this->get_formated_column('TELEFONO_RESERVAS',$separator);
		$result .= $this->get_formated_column('CLASIFICACION',$separator);
		
		if (!empty($result) ) {
			$result = substr($result,0,-1*strlen($separator));
			echo $result;
		}
		
		
	}


}



?>