<?php

class log_entrie {
	public $ip = '';
	public $browser = '';
	public $user = '';
	public $command = '';
	public $table_name = '';
	public $url = '';
	public $timestamp = '';
	public $parameters = '';
	
	protected $log_table_name = 'log';

	public function __construct() {
	
		$this->ip = $this->getRealIpAddr();
        $this->browser = $_SERVER['HTTP_USER_AGENT'] ;
        $this->user = website::$user->get_id();
        $this->url = url::get_request_url();
        $this->timestamp = date("Y-m-d H:i:s");
	}
	public function set_command($command_name) {
		$this->command = $command_name;
	}
	public function set_table_name($table_name) {
		$this->table_name = $table_name;
	}
	public function set_parameters($parameters_str) {
		$this->parameters = $parameters_str;
	}
	
	public function persist() {		
  		website::$database->insert2($this->log_table_name,$this);
	}
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
	
	protected static $users_online = null;
	protected static function load_users_online() {
		if (self::$users_online != null) return;
		
		$sql = "SELECT 
					DISTINCT(user) as user, 
					SUM(CASE WHEN command='logout' THEN -id 
						WHEN command='login' THEN id
						ELSE 0 END) AS state
				FROM
					log
				WHERE user<>'' AND
					timestamp >= '".(date("Y-m-d H:i:s",time()-website::$user->max_seconds_session))."'
				GROUP BY user
				ORDER BY user";
		$r = website::$database->execute_get_array($sql);
		self::$users_online = array();
		foreach ($r as $row) {
			if ($row['state']>=0) {
				self::$users_online[] = $row['user'];
			}
		}
	}
	public static function get_users_online_count() {
		self::load_users_online();
		return count(self::$users_online);
	}
	public static function get_users_online_array() {
		self::load_users_online();
		return self::$users_online;
	}
	
}