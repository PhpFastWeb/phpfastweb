<?php
	interface icommand_row extends icommand_external {	
		/**
		 * Devuelve un link en html a realizar ese comando sobre el elemento especificado mediante $key_set y $row
		 * @param key_set $key_set
		 * @param array $key_values
		 */	
		public function set_key_set(key_set $key_set, $key_values=null);
		/**
		 * Devuelve el key_set que define este command_row
		 * @return key_set
		 */
		public function get_key_set();		
	}
?>