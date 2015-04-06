<?php
class command_select extends acommand_row implements icommand_row {
	protected $name = "Seleccionar";
	
	protected $key = 'select';
	public function &set_key($new_key) {
		$this->key = $new_key;
		return $this;
	}		
	public function get_key() {
		return $this->key;
	}	
	
	protected $target = '';
    /**
     * Sets target URL to be redirected upon row selection
     * @return command_select instance to self
     */
	public function set_target($target) {
		$this->target = $target;
        return $this;
	}
	public function get_target($target) {
		if ($this->target=='') throw new ExceptionDeveloper('Target not defined');
		return $this->target;
	}

	public function __construct($target='') {
		if ($target != '') {
			$this->target = $target;
		}
	}
		
	/**
	 * If set, it will be used instead of primary key.
	 */
	public function set_redirect_column_name($col_name) {
		$this->redirect_column_name = $col_name;
	}
	protected $redirect_column_name = null;
	
	public function execute() {	
			
		$target = $this->get_final_target_url();
		html_template::redirect($target,"Continuar con elemento seleccionado");
		exit;
	}
	public function get_final_target_url() {
	   if ( count($this->key_set->primary_keys) != 1 ) throw new ExceptionDeveloper('Expected exactly one primary key column');
        $target = $this->target;
        //Si termina en /, la quitamos
		$target = ( substr($target,-1,1) == '/' ) ? substr($target,0,-1) : $target;
        //Añadimos máscara
		$target .= $this->target_url_mask;
        if ( is_null( $this->redirect_column_name ) ) {
			$pks = $this->key_set->get_pks_string();
			if ( $pks == '' ) throw new ExceptionDeveloper(); 
			$target .= $pks;				
		} else {
			$target .= $this->table->columns_col->get($this->redirect_column_name)->get_value();
		}
        //Si la máscara es /, añadimos otra al final
        if ( $this->target_url_mask == '/' ) $target .= '/';
        return $target;
    }
    public function set_target_url_mask($mask) {
        $this->target_url_mask = $mask;
    }
    private $target_url_mask = '/';
    
    public function get_execute_url() {
        
		//$url = new url();
		//$url->set_var ( $this->get_command_label (), $this->get_key () );
        //$url->set_access_key($this->access_key);
        $url = new url($this->get_final_target_url());
		return $url;
	}
    
	public static $id_selected = '';
	public static function get_selected_id($order=0) {
        $arr = self::get_selected_ids_array();
        if ( ! isset($arr[$order]) ) {
            throw new ExceptionDeveloper("Parameter index $order not defined in current URL");
        }
        return $arr[$order];
	}
    public static function get_count_selected_ids() {
        return count( self::get_selected_ids_array() );
    }
    protected static $ids_array = null;
    public static function get_selected_ids_array() {
        if ( ! is_null( self::$ids_array ) ) return self::$ids_array;
        
		$url = html_template::get_request_url();
        
        //Si la URL contiene un # final, lo quitamos
		if (substr($url,-1,1) == '#' ) $url = substr($url,0,strlen($url)-1);

		//Probamos por URL 
		$x = strpos(html_template::get_request_url(),'.php/');
		if ( $x === false) {
            self::$ids_array = array();
            return self::$ids_array;
			//throw new ExceptionDeveloper('Parámetro no definida en URL');
		}
		$t = substr(html_template::get_request_url(),$x+strlen('.php/'));
		$params = explode('/',$t);
        $n = count($params)-1;
        //eliminamos posible ? final
        if ( $n >= 0 && substr($params[$n],0,1) == '?' ) unset( $params[$n] );
        $n = count($params)-1;
        //eliminamos último si está en blanco
        if ( $n >= 0 && $params[$n]=='' ) unset( $params[$n] );
        self::$ids_array = $params;  
        return self::$ids_array; 
    }
	public static function is_set_selected_id($order=0) {
        if ( $order < self::get_count_selected_ids() ) return true;
		return false;
	}
	
	//---------------------------------------------------------------------

}
