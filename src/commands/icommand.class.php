<?php
interface icommand {
	/**
	 * Establece la tabla asociada al comando
	 * @param table_data $table_data
	 */
	public function set_table(table_data &$table_data);

	/**
	 * Ejecuta el comando
	 */
	public function execute();
	/**
	 * Devuelve un iditificador único que define a esa clase de comando.
	 * @return string
	 */
	public function get_key();
	/**
	 * Devuelve el parámetro que se utiliza en enlaces para especificar la clase de comando
	 * que se ejecutará al pulsar el enlace.
	 */
	public static function get_command_label();

	/**
	 * Logs current command to the website database log table
	 */
	public function log_command();
	
	/**
	 * Returns a reference to the table that the command is operating on
	 * @return table_data
	 */
	public function get_table();
	
}
?>