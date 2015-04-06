<?php

class ExceptionDatabaseQuery extends ExceptionDatabase {
	protected $sql = '';
	public function __construct($message='',$sql='', $code=0) {
		$message .= '<br />'.$this->format_sql_error( $sql );
		$this->sql = $sql;
		parent::__construct($message, $code);
	}
	public function get_formatted_sql_error() {
		return self::format_sql_error($this->sql);
	}
	protected static function format_sql_error($sql) {
		if ($sql=='') return '';
		$msg = '';
		if ( class_exists('website') && website::in_developer_mode() ) {
	        //if (website::in_developer_mode() && mysql_errno() == 1064) {
            $msg .= '<u><a href="#" onclick="javascript: document.getElementById(\'mysql_error_\').style.display= ( document.getElementById(\'mysql_error_\').style.display == \'block\' ) ? \'none\' : \'block\'; \'block\'; return false;">';
            $msg .= 'Dev extended info</u></a></span>';
            $msg .= '<div id="mysql_error_" style="display:none">' . mysql_error() . '<br />';
            $msg .= '<pre style="width: 600px; text-wrap: unrestricted; white-space:pre-wrap;">' . $sql . '</pre>';
        }
        return $msg;
	}
}