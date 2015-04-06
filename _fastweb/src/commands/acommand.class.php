<?php
	abstract class acommand implements icommand {
		
		protected $table;

		public static function get_command_label() {
			//Un ID en HTML debe comenzar por una letra
			return "command_";
		}
		public function set_table(table_data &$table_data) {
			$this->table = $table_data;
		}
	
		// Overridables methods
		
		/**
		 * @param icommand $cmd
		 * @return icommand
		 */
		public static function &cast(icommand $cmd) {
			return $cmd;
		}
		
		// Log
		
		public $log_table_name = 'log';
		public function log_command() {
			
			$log = new log_entrie();
			$log->command = $this->get_key();
			$log->table_name = $this->table->table_name;
			$log->parameters = $this->get_parameters_str();
	  		$log->persist();
		}
		protected function get_parameters_str() { return ''; }
		protected function getRealIpAddr() {
		    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		      $ip = $_SERVER['HTTP_CLIENT_IP']; //check ip from share internet
		    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		      $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; //to check ip is pass from proxy
		    } else {
		      $ip = $_SERVER['REMOTE_ADDR'];
		    }
		    return $ip;
		}
	
	
		public function get_table() {
			return $this->table;
		}
	}
?>