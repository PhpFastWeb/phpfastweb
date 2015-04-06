<?php

class apagination {
	
	//Fuente de datos
	private $table_data;
	
	//Valores presentes en data_source
	/** Fila por la que comienza la página actual
	 * @var int
	 */
	private $row_ini;
	/** Fila por la que finaliza la página actual
	 * @var int
	 */	
	private $row_end;
	/** Current page
	 * @var int
	 */	
	private $pag_current;
	/** Last page
	 * @var int
	 */	
	private $pag_end;
	/** Filas a mostrar por pï¿½gina
	 * @var int
	 */	
	private $pag_items_pag;
	/** Filas en total
	 * @var int
	 */	
	private $pag_total_items;
	/** Establece si se leeran todas las filas o se irï¿½ adivinando el tamaï¿½o total.
	 *  True por defecto. Poner a false para que vaya mï¿½s rï¿½pido en tablas con muchos datos (cientos de miles de registros con restricciones).
	 * @var bool
	 */	
	private $pag_read_items_total;
	
	//Valores propios
	//private $pag_total_pag;
	
    
    public $search_string; 
    
	//Initialization
	public $initialised = false;
	
	public function __construct( $table_data = null ) {
		if ( $table_data != null ) {
			$this->table = $table_data;
		}
	}
	
	public function init_config() {
		if ( $this->initialised ) return;
		
		if ( $this->table_data != null ) {			
			$this->row_ini = $this->table->row_ini;
			$this->row_end = $this->table->row_end;
		
			$this->pag_current = $this->table->pag_ini;
			$this->pag_end = $this->table->pag_end;
			$this->pag_items_pag = $this->table->pag_items_pag;
			$this->pag_items_total = $this->table->pag_items_total;		
			$this->pag_read_items_total = $this->table->pag_read_items_total;
		}
		
		//Valores calculados
		
		//$this->pag_total_pag = ceil( $this->pag_items_total / $this->pag_items_pag );
		
		$this->initialised = true;
	}
    /*
	public function __echo() {
		$this->init_config();
		
		echo 'row_ini : '.$this->row_ini.'<br />';
		echo 'row_end : '.$this->data_source->table->row_end.'<br />';
		
		echo 'pag_current : '.$this->pag_current.'<br />';
		echo 'pag_end : '.$this->pag_end.'<br />';
		echo 'pag_items_pag : '.$this->pag_items_pag.'<br />';
		echo 'pag_items_total : '.$this->pag_items_total.'<br />';
		echo 'pag_read_items_total : '.$this->pag_read_items_total.'<br />';
		//echo 'pag_total_pag : '.$this->pag_total_pag.'<br />';
	}
    */
    
	/**
	 * Devuelve la fila inicial para la pï¿½gina especificada.
	 * Las pÃ¡ginas comienzan por 1, las filas comienzan por 0.
	 *
	 * @param unknown_type $pag
	 */
	public function get_row_ini($pag) {
		return $this->pag_items_pag * ($pag-1);
		
	}
	/**
	 * Devuelve la fila final para la pï¿½gina especificada.
	 * Las pÃ¡ginas comienzan por 1, las filas comienzan por 0.
	 * @param unknown_type $pag
	 */
	public function get_row_end($pag) {
		return $this->pag_items_pag * $pag - 1;
	}
	
	
	public function get_url_first() {
		if ($this->pag_current == 1) {
			return '';
		}
		$row_ini = $this->get_row_ini(1);
		$row_end = $this->get_row_end(1);
		$url = new url();
		$url->set_var('row_ini',$row_ini);
		$url->set_var('row_end',$row_end);	
		$url->add_vars_def($this->data_source->get_href_vars());	
		return $url;
	}
	public function get_url_previous() {
		if ($this->pag_current == 1) {
			return '';
		}		
		$npag = max ( $this->pag_current-1 , 1);
		$row_ini = $this->get_row_ini($npag);
		$row_end = $this->get_row_end($npag);
		$url = new url();
		$url->set_var('row_ini',$row_ini);
		$url->set_var('row_end',$row_end);		
		$url->add_vars_def($this->data_source->get_href_vars());
		return $url;
	}
	public function get_url_next() {
		if ($this->pag_current == $this->pag_end) {
			return '';
		}		
		$row_ini = $this->get_row_ini($this->pag_current+1);
		$row_end = min( $this->get_row_end($this->pag_current+1) , ($this->pag_items_total-1) ) ;	
		$url = new url();
		$url->set_var('row_ini',$row_ini);
		$url->set_var('row_end',$row_end);		
		$url->add_vars_def($this->data_source->get_href_vars());		
		return $url;		
	}
	public function get_url_last() {
		if ($this->pag_current == $this->pag_end) {
			return '';
		}		
		$row_ini = $this->get_row_ini($this->pag_end);
		$row_end = $this->pag_items_total-1;
		$url = new url();
		$url->set_var('row_ini',$row_ini);
		$url->set_var('row_end',$row_end);		
		$url->add_vars_def($this->data_source->get_href_vars());		
		return $url;
	}
	public function get_url_pag($pag) {
		$row_ini = $this->get_row_ini($pag);
		$row_end = $this->get_row_end($pag);
		$url = new url();
		$url->set_var('row_ini',$row_ini);
		$url->set_var('row_end',$row_end);		
		$url->add_vars_def($this->data_source->get_href_vars());		
		return $url;
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
    function has_to_show_pagination() {
        return ( $this->table->pag && 
            ( $this->table->pag_end > 1 || $this->table->pag_show_allways ) 
            && $this->table->pag_items_total>0 );
    }
	
}

?>