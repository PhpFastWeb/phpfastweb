<?php

class auto_str {
    

    /**
     * Creates a string object with number substitution.
     * e.g.: new auto_str('You have {0} email(s) pending',$nemail);
     * For each braquet tag, following parenthesis are searched an filtered
     * taking number to include in account.
     * You can use ! to use tag for filtering, but not substitute it for the number
     * e.g: '{!0}You have (an/) email(s) awaiting',$num_emails
     * If you specify three definitions, first one is used for zero instaces.
     * e.g.: '{!0}You (don\'t//) have (/an/) email(s//s) awaiting',$num_emails 
     */
    public function __construct() {      
        $this->parameters = func_get_args();
        if (count($this->parameters) > 0) {
            $this->query_string = $this->parameters[0];
            $this->parameters = array_slice($this->parameters, 1);
        }
        $this->result_string = self::process_result($this->query_string,$this->parameters);
    }
    public function __toString() {
    	return $this->result_string;
   	}    
    protected $parameters = array();
    protected $query_string = '';
    protected $result_string = '';
    //---------------------------------------------
    /**
     * Returns a string with number substitution.
     * e.g.: new auto_str('You have {0} email(s) pending',$nemail);
     * For each braquet tag, following parenthesis are searched an filtered
     * taking number to include in account.
     * You can use ! to use tag for filtering, but not substitute it for the number
     * e.g: '{!0}You have (an/) email(s) awaiting',$num_emails
     * If you specify three definitions, first one is used for zero instaces.
     * e.g.: '{!0}You (don\'t//) have (/an/) email(s//s) awaiting',$num_emails 
     */    
    public static function get() {
        $parameters = func_get_args();
        assert::is_in_array($parameters,0);
        //$parameters = $parameters[0];   
        
        if ( count($parameters) > 0 ) {
            $query_string = $parameters[0];
            $parameters = array_slice($parameters, 1);
        }
        return self::process_result($query_string,$parameters);        
    }    
    protected static function process_result($query_string,$parameters) {
        $mode = 'string'; //Possible modes are 'string','tag' and 'parenthesis'
        $result = ''; $tag = ''; $definition='';
        $i = 0;
        $mychar = ''; $prevchar = '';
        while ( $i < strlen($query_string) ) {
            
            $prevchar = $mychar;
            $mychar = substr($query_string,$i,1);
            
            switch($mode) {
                case 'string':
                    if ( $mychar == '(' && $prevchar != '\\' ) {
                        $mode = 'parenthesis';
                        $definition = '';
                    } else if ( $mychar == '{' && $prevchar != '\\' ) {
                        $result .= $mychar;
                        $mode = 'tag';
                        $tag = '';
                    } else {
                        $result .= $mychar;
                    }
                break;
                case 'tag':
                    if ( $mychar == '}' ) {
                        $result .= $mychar;
                        $mode = 'string';
                    } else {
                        $result .= $mychar;
                        $tag .= $mychar;
                    }
                
                break;
                case 'parenthesis':
                    if ( $mychar == ')' && $prevchar != '\\' ) {
                        $result .= self::select_numeral($definition,$tag,$parameters);
                        $mode = 'string';
                    } else {
                        $definition .= $mychar;
                    }
                break;
            }
            
            $i++;
        }
        
        
        //Finally, we replace numbers in the string.
        foreach ($parameters as $key => $number) {
            $result = str_replace('{!'.$key.'}','',$result);
            $result = str_replace('{'.$key.'}',$number,$result);
            //TODO: ignore substitution for \{0} type strings
            
        }
        //TODO: remove \\ from all result
     
        return $result;
    }
    protected static function select_numeral($definition,$tag,$parameters) {
        if ( substr($tag,0,1) == '!' ) $tag = substr($tag,1);
        assert::is_in_array($parameters,$tag);
        $number = $parameters[$tag];
        //$num_defs = substr_count($definition,'/');
        $defs = explode('/',$definition);
        if ( count($defs) == 3 ) {
            $zero = $defs[0];
            $singular = $defs[1];
            $plural = $defs[2];            
        } else if ( count($defs) == 2 ) {
            $zero = $defs[0];
            $singular = $defs[0];
            $plural = $defs[1];
        } else if ( count($defs) == 1 ) {
            $zero = $defs[0];
            $singular = '';
            $plural = $defs[0];
        }
        
        if ( $number == 0 ) {
            return $zero;
        } else if ( $number == 1 || $number == -1 ) {
            return $singular;
        } else {
            return $plural;
        }
    }

}
