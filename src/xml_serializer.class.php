<?php
class xml_serializer {

    // functions adopted from http://www.sean-barton.co.uk/2009/03/turning-an-array-or-object-into-xml-using-php/
	
    public static function generateValidXmlFromObj(stdClass $obj, $generate_empty=false, $node_block='nodes', $node_name='node') {
        $arr = get_object_vars($obj);
        return self::generateValidXmlFromArray($arr, $generate_empty, $node_block, $node_name);
    }

    public static function generateValidXmlFromArray($array, $generate_empty=false, $node_block='nodes', $node_name='node') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
        $xml = '';
        
		if ($node_block!='')
        	$xml .= '<' . utf8_encode($node_block) . '>';
        	
        $xml .= self::generateXmlFromArray($array, $generate_empty, $node_name);
        
        if ($node_block!='')
        	$xml .= '</' . utf8_encode($node_block) . '>'; //."\r\n";

        return $xml;
    }

    private static function generateXmlFromArray($array, $generate_empty, $node_name) {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach( $array as $key => $value ) {
            	if ($value!='' || $generate_empty ) {
	                if ( is_numeric($key) ) {
	                    $key = $node_name;
	                }
	                $xml .= '<' . utf8_encode($key) . '>' . self::generateXmlFromArray($value,$generate_empty, $node_name) . '</' . utf8_encode($key) . '>'; //."\r\n";
            	}
            }
        } else {
        	if ($array!=='' || $generate_empty ) {
           	 	$xml = htmlspecialchars(utf8_encode($array), ENT_QUOTES);
        	}
        }

        return $xml;
    }
    private static function node_to_string($xml_node) {
    	$result = '';
    	$name = $xml_node->getName();
    	$result = '<'.$name.'>';
    	$result = self::childre_to_string($xml_node);
    	$reuslt = '</'.$name.'>';
    }
    private static function children_to_string($xml_node) {
    	$result = '';
    	foreach($xml->children as $child) {
			self::node_to_string($child);
		}  
    }
	public static function xml_highlight($s)
	{      
//		$xml = simplexml_load_string($s);
//		$s = self::node_to_string($xml);
		$s = utf8_decode($s);
	    $s = htmlspecialchars($s);
	    
	    
	    //< y > en azul
	    $s = preg_replace("#&lt;([/]*?)(.*)([\\s]*?)&gt;#sU",
	        "<font color=\"#0000FF\">&lt;\\1\\2\\3&gt;</font>",$s);
	    

	    //Etiqueta apertura
	    $s = preg_replace("#&lt;([^\\s\\?/=])(.*)([\\[\\s/]|&gt;)#iU",
	        "\r\n<blockquote>&lt;<font color=\"#808000\">\\1\\2</font>\\3",$s);
	    
	    //Etiqueta cierre
	    $s = preg_replace("#&lt;([/])([^\\s]*?)([\\s\\]]*?)&gt;#iU",
	        "&lt;\\1<font color=\"#808000\">\\2</font>\\3&gt;</blockquote>\r\n",$s);
	    
//	    //Atributos
//	    $s = preg_replace("#([^\\s]*?)\\=(&quot;|')(.*)(&quot;|')#isU",
//	        "<font color=\"#800080\">\\1</font>=<font color=\"#FF00FF\">\\2\\3\\4</font>",$s);
//	    
//	    //metatipos
//	    $s = preg_replace("#&lt;(.*)(\\[)(.*)(\\])&gt;#isU",
//	        "&lt;\\1<font color=\"#800080\">\\2\\3\\4</font>&gt;",$s);
	    

	    return $s;
	    //return nl2br($s);
	}

}
?>