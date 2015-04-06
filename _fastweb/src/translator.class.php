<?php

class translator {

	protected static $default_original_language = 'english';
	public static function set_default_original_language( $default_original_language ) {
		self::$default_original_language = $default_original_language;
	}
    
	protected static $current_language = 'english';
	public static function set_current_language( $current_language ) {
		self::$current_language = $current_language;
	}
    
	public static $translation_set_general = array(
		'original_language' => 'spanish',
		'context' => 'general',
		'translations' => array(
		
			'abierto' => array(
				'english'=>'open'
			),
			'cerrado' => array(
				'english'=>'closed'
			)
			
		)			
	);
	
	protected static $translations_sets = array();
	
	public static function add_translation_set($translation_set) {
		assert::is_array($translations_sets);
		assert::is_in_array($translations_sets,'original_language');
		assert::is_in_array($translations_sets,'context');
		assert::is_in_array($translations_sets,'translations');
		self::$translations_sets[] = $translation_set;
	}
	
	protected static $cached_translations_sets = null;
	public static function get_translation_sets() {
		if ( is_null( self::$cached_translations_sets ) ) 
            self::$cached_translations_sets = array_merge( self::$translations_sets, array(self::$translation_set_general) );
		return self::$cached_translations_sets;
	}
	
	public static function get_translation( $original_message_lowercase, $original_language='', $context='general' ) {
        
        $message_key = $original_message_lowercase;
        $translations_sets = self::get_translation_sets();
		//We search
		$result = '';
		$translation_set = reset( $translations_sets );
		while ( $result == '' && $translation_set ) {
			if( $translation_set['context'] == $context && $translation_set['original_language'] == $original_language ) {
				//We search for the translation
				if ( isset ( $translation_set['translations'][$message_key] ) ) {
					$result = $translation_set['translations'][$message_key];
				}
			}
			$translation_set = next( $translations_sets );
		}
		return $result;
	}
	
    public static function translate( $message, $context='', $language='', $original_language='' ) {
		//set the language
		$use_language = ( ! empty($language) ) ? $language : self::$current_language;
        $use_context = ( ! empty($context) ) ? $context : 'general';
        $original_language = ( ! empty($original_language) ) ? $original_language : self::$default_original_language;
        
        //If same language, return original message
        if ( $language == $original_language ) return $message;
        
        $original_message_lowcase =  strtolower( trim( $message ) );
        $translation = self::get_translation( $original_message_lowcase );
        
        //If no translation, return original message
        if ( $translation == '' ) return $message;

        //We try to evaluate case

        if ( substr( trim($message), 0, 1 ) == strtolower( substr( trim($message), 0, 1 ) ) ) {
            //first letter lowercase, we then return everything lowercase
            $result = $translations[ $original_message_lowcase ][$language];
        } else {
            //we return first letter uppercase
            $result = ucfirst( $translations[ $original_message_lowcase ][$language] );
        }
        
        return $result;
    }
}