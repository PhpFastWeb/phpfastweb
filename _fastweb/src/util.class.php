<?php


class util {

	
	/**
	 * Cleans a source string to produce a valid Windows file name. Return string is UTF-8 encoded.
	 * @param $source_str string
	 * @return string
	 */ 
	public static function clean_str_filename($source_str) {        
        $file_title = substr($source_str,0,255-4);
        $file_title = strtr($file_title,
			array("^"=>"_" , "|"=>"_" , "?"=>"_" , "*"=>"_" , "<"=>"_" , "\""=>"_" , ":"=>"_" , ">"=>"_" , '/'=>'_', "\\"=>"_"));        
        $file_title = utf8_encode($file_title);
        
        if ( in_array( strtoupper($file_title), 
			array('CON','AUX','COM1','COM2','COM3','COM4','LPT1','LPT2','LPT3','PRN','NUL'))) {
        		$file_title = $file_title .'_';	
        }
        return $file_title;
	}
	
	
	
	
	
	
	
	
	
	
	
	
}