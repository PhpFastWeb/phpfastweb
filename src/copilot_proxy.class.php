<?php

/**
 * A proxy that test if copilot classes are available, and if affirmative, loads them if needed
 */
class copilot_proxy {
    public const base_dir = 'I:/www_i/__phpcopilot/src';

    private static $cache_objects = array();
    
    public static function get_class_builder() {
        return self::retrieve_class('class_builder');
    }
    private static function retrieve_class($class_name) {
        if (isset(self::$cache_objects[$class_name])) {
            return self::$cache_objects[$class_name];
        }
        if ( ! is_file( self::base_dir.'/'.$class_name.'.class.php' ) ) return;
        require_once( self::base_dir.'/'.$class_name.'.class.php' );
        self::$cache_objects[$class_name] = new $class_name();
        return self::$cache_objects[$class_name];
    }
}