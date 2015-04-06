<?php
	class database_config {
		public $ip;
		public $db_name;
		public $user;
		public $password;
		public $type;
		public $caller_host;
		
		const TYPE_MYSQL = 0;
		const TYPE_ORACLE = 1;
		const TYPE_INTERBASE = 2;
	}
	
?>