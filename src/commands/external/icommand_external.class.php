<?php 
interface icommand_external extends icommand {
	
	/**
	 * Devuelve el nombre en espa�ol del comando, la primera letra en may�sculas
	 * @return string
	 */
	public function get_name();
		
	/**
	 * Devuelve un enlace que conduce a la ejecuci�n del comando.
	 * Los par�metros adicionales que sean necesarios deber�n estar definidos seg�n el subtipo de comando.
	 * @return string
	 */
	public function get_execute_link();
	/**
	 * Devuelve la url que conduce a la ejecuci�n del comando.
	 * Los par�metros adicionales que sean necesarios deber�n estar definidos seg�n el subtipo de comando.
	 * @return url
	 */
	public function get_execute_url();
	public function get_execute_onclick();
    

    public function add_command(icommand_external $command);
    public function get_command($index);
    
    public function set_access_key($access_key);
    public function get_access_key();
}


?>