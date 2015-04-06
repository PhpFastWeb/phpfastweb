<?php

class command_sync_files extends acommand_external implements icommand_external {
	
	public $table = null;
	public $name = "Sincronizar ficheros";
	
	public function get_key() {
		return "sync_files";
	}     
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return command_new
	 */
	public static function &cast(icommand $cmd) {
		if (! ($cmd instanceof command_sync_files)) {
			throw new ExceptionDeveloper ( "Clase incorrecta" );
		}
		return $cmd;
	}
    //--------------------------------------------------------------       
	public function get_execute_link() {
		$url = $this->get_execute_url();
        if ( ! $this->table->inline_new_record ) {
            return $url->get_a_link( $this->get_name () );
        } else {
            $result = '<a href="'.$url->__toString().'" onclick="javascript: showhide(\'inline_new_record_\'); document.location=\'#inline_new_record_anchor_\'; return false;">'.
                $this->get_name()."</a>";
            return $result;
        }
	}        
    
	public function execute() {
		//Inicializamos la nueva información
        if ( ! isset( $_POST) || ! isset($_POST['confirmed']) ) {
            echo $this->get_confirmation();
        }
        //Obtenemos todos los ficheros del directorio
        
        $dir_files = scandir($this->table->upload_dir, 1);
        $table_files = array();
        foreach ($this->tables->columns_col as $col ) {
            if ($col instanceof column_file ) {
                //Nos traemos de la base de datos todos los nombres de fichero
                $sql_str = new sql_str("SELECT {0} FROM {1} WHERE {0}<>",
                    $col->get_column_name(),
                    $this->table->table_name);
                $table_files[$col->get_column_name()] = website::$database->execute_get_array($sql_str);
            }
        }

	}
    public function get_confirmation() {
        $result = '';
        $result .= 'Esta función realizará un recorrido comprobando que los ficheros adjuntos a la tabla se encuentran correctamente asignados.<br />';
        $result .= "Concrétamente se comprobará:";
        $result .= '<ul><li>Que no falta ningún archivo que esté referenciado en la tabla.</li>';
        $result .= '<li>Que ningun archivo asignado a la tabla no está sin referenciar.</li>';
        $result .= '</ul>';
        $result .= '<form method="post" target="'.html_template::get_php_self().'">';
        $result .= '<input type="button" value="Volver" onclick="javascript: document.history(-1);"> ';
        $result .= '<input type="submit" value="Continuar">';
        $result .= '</form>';
        return $result;   
    }

}

?>