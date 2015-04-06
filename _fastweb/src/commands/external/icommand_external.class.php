<?php 
interface icommand_external extends icommand {
	
	/**
	 * Devuelve el nombre en español del comando, la primera letra en mayúsculas
	 * @return string
	 */
	public function get_name();
		
	/**
	 * Devuelve un enlace que conduce a la ejecución del comando.
	 * Los parámetros adicionales que sean necesarios deberán estar definidos según el subtipo de comando.
	 * @return string
	 */
	public function get_execute_link();
	/**
	 * Devuelve la url que conduce a la ejecución del comando.
	 * Los parámetros adicionales que sean necesarios deberán estar definidos según el subtipo de comando.
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