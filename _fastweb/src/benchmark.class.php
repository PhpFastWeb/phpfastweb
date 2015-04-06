<?php
class benchmark {
	public static $script_start;
	public static $actual_time;
	public static $num_calls = 0;
	public static $file = '';
	public static $line = '';
	
	private static $initialised = false;
	
	
	private function __construct() {}
	
	static private function init_config() {
		if ( self::$initialised ) return;
		list($usec, $sec) = explode(' ', microtime());
		self::$script_start = (float) $sec + (float) $usec;
		self::$initialised = true;
	}
	static private function process_actual_time() {
		self::init_config();
		list($usec, $sec) = explode(' ', microtime());
		self::$actual_time = ( (float) $sec + (float) $usec) - self::$script_start;
		self::$num_calls++;
		
		//echo basename(self::$file) ."(". self::$line .")". " : " . round(self::$actual_time,3) . "<br />";
		
	}
	static public function time($file,$line) {
		self::$file = $file;
		self::$line = $line;
		self::process_actual_time();		
	}
}

?>