<?php 
	class ExceptionDeveloper extends CustomException {
		public function __construct($message='',$code=0) {
			if ( ! website::in_developer_mode() ) {
				//$this->message = 'Excepción no controlada.';				
                //trigger_error($message, FATAL);
                //handleShutdown($this); die;
			}
			parent::__construct($message,$code);
		}
		public function get_message() { return $this->message; }
		public function get_line() { return $this->line; }
		public function get_file() { return $this->file; }
	    public function __toString() {
	    	
        	$result = get_class($this) . " <b>{$this->message}</b><br />in file: {$this->file}({$this->line})\n";
        	//return $result;

	        if ( website::in_developer_mode() ) {
       			$result = self::get_pretty_trace(
			   		$this->getTrace(),
			   		$this->get_message(),
			   		$this->file,
			   		$this->line);
                    //$this->class,
					//$this->type,
					//$this->function,
					//$this->args);
            }
            return $result;
         }
         public static function get_pretty_trace($trace,$message='',$file=null,$line=null,$class=null,$type=null,
                    $function=null,$args=null) {
        		$result = "<div style=\"width:800px; border:1px solid #CECECE;position:relative;margin:5px 5px;padding:5px 5px;background-color:white;font-family:'Courier New';word-wrap:break-word;\">";
            	//$result .= "$this->getTraceAsString()";

	            
	            $i=0;
	            $result .= '<table style=\"width:780px;\">';
	            $result .= '<tr><td style="border:1px solid #efefef;padding:2px 2px;">file</td><td style="border:1px solid #efefef;padding:2px 7px;">line</td><td style="border:1px solid #efefef;padding:2px 2px;">call</td></tr>';

	            $result .= "<tr>"; 
    			$result .= "<td  style=\"text-align:left; border:1px solid #efefef; background-color:#eee; padding:2px 2px; vertical-align:top\">";
        		if (isset($file)) {
        			$result .= '<a href="#" onclick="if(document.getElementById(\'backtrace_file'.$i.'_\').style.display==\'none\'){document.getElementById(\'backtrace_file'.$i.'_\').style.display=\'block\';}else{document.getElementById(\'backtrace_file'.$i.'_\').style.display=\'none\';} return false;" ';
    				$result .= 'title="'.$file.'" ';
    				$result .= '>';
    				$result .= basename($file).'</a>';
    			}
    			$result .= "</td>";
    			
    			if (isset($line)) {
    				$result .= "<td style=\"text-align:right; border:1px solid #efefef; background-color:#eee;  padding:2px 2px; vertical-align:top\">";
                    
                    if (isset($file)) {
                        $result .= '<a target="_blank" onclick="javascript:window.open(this.href,\'win_source\',\'left=20,top=20,width=1100,height=700,toolbar=0,resizable=1\'); return false;" href="/www_i/__phpcopilot/show_source.php?file='.urlencode($file).'&amp;line='.$line.'#'.$line.'">'.$line.'</a>';
    				} else {
    				    $result .= $line;
				    }
                    $result .= "</td>";
    			}
    			$result .= "<td style=\"text-align:left; border:1px solid #efefef; padding:2px 2px;\">";
	            if (isset($file)) {
    				//$result .= "\r\n";
    				$result .="<div style=\"display:none;\" id=\"backtrace_file{$i}_\">";
    				$result .= $file;
    				$result .="</div>";
    			}
    			if (isset($class)) {
    				$result .= $class;
    			}

    			if (isset($type)) {
    				$result .= $type;
    			}
    			if (isset($function)) {
    				$result .= $function."() ";
    			}
	            if (isset($args) && is_array($args) && count($args) > 0 ) {
    				$result .= '([<a href="#" onclick="document.getElementById(\'backtrace_args'.$i.'_\').style.display=\'block\'; return false;" ';
    				$result .= 'title="';
    				//$result .= print_r($t['args'],true);
    				$result .= '" ';
    				$result .= '><u>'.count($args).'</u></a>])';
    			} else {
    				$result .= "<b>{$message}</b>";
    			}
    			if (isset($args) && is_array($args) && count($args) > 0 ) {	
    				$result .="<div style=\"display:none;\" id=\"backtrace_args{$i}_\">";
    				$result .= htmlspecialchars(print_r($args,true));
    				$result .="</div>";
    			}	
    			$result .= "</td>";
    			$result .= "</tr>";
	            
	            
	            $i++;
	    		foreach ($trace as $t) {
	    			/*
	    			$result .= $t['file']." ".$t['line']." ";
	    			if (isset($t['class'])) {
	    				$result .= $t['class'];
	    				
	    			}
	    			if (isset($t['type'])) {
	    				$result .= $t['type'];
	    			}
	    			if (isset($t['function'])) {
	    				$result .= $t['function']."() ";
	    			}
	    			$result .= "\r\n";
	    			*/
	    			
	    			
		    		$result .= "<tr>"; 
	    			$result .= "<td  style=\"text-align:left; border:1px solid #efefef; background-color:#eee; padding:2px 2px; vertical-align:top\">";
        			if (isset($t['file'])) {
	    				$result .= '<a href="#" onclick="if(document.getElementById(\'backtrace_file'.$i.'_\').style.display==\'none\'){document.getElementById(\'backtrace_file'.$i.'_\').style.display=\'block\';}else{document.getElementById(\'backtrace_file'.$i.'_\').style.display=\'none\';  return false;}" ';
	    				$result .= 'title="'.$t['file'].'" ';
	    				$result .= '>';
	    				$result .= basename($t['file']).'</a>';
	    				
	    				
    				}

    				$result .= "</td>";
    				$result .= "<td style=\"text-align:right; border:1px solid #efefef; background-color:#eee; padding:2px 2px; vertical-align:top\">";
	    			if (isset($t['line'])) {
	    				if ( isset($t['file']) )
	    				   $result .= '<a target="_blank" onclick="javascript:window.open(this.href,\'win_source\',\'left=20,top=20,width=1100,height=700,toolbar=0,resizable=1\'); return false;" href="/www_i/__phpcopilot/show_source.php?file='.urlencode($t['file']).'&amp;line='.$t['line'].'#'.$t['line'].'">'.$t['line'].'</a>';
	    				else {
	    				   $result .= $t['line'];
                        }
	    			}
	    			$result .= "</td>";
    				$result .= "<td style=\"text-align:left; border:1px solid #efefef; padding:2px 2px;word-wrap:break-word;\">";
	    			if (isset($t['file'])) {
	    				//$result .= "\r\n";
	    				$result .="<div style=\"display:none;\" id=\"backtrace_file{$i}_\">";
	    				$result .= $t['file'];
	    				$result .="</div>";
	    			}
	    			if (isset($t['class'])) {
	    				$result .= $t['class'];
	    			}
//	    			if (isset($t['object'])) {
//	    				$result .= $t['object']." ";
//	    			}
	    			if (isset($t['type'])) {
	    				$result .= $t['type'];
	    			}
	    			if (isset($t['function'])) {
	    				$result .= $t['function'];
	    			}
	    		   if (isset($t['args']) && is_array($t['args']) && count($t['args']) > 0 ) {
    					$result .= '( [<a href="#" onclick="if(document.getElementById(\'backtrace_args'.$i.'_\').style.display==\'none\'){document.getElementById(\'backtrace_args'.$i.'_\').style.display=\'block\';}else{document.getElementById(\'backtrace_args'.$i.'_\').style.display=\'none\';} return false;" ';
	    				$result .= 'title="Show parameters';
	    				//$result .= print_r($t['args'],true);
	    				$result .= '" ';
	    				$result .= '><u>'.count($t['args']).'</u></a>] )';
    				} else {
    					$result .= "()";
    				}

	    			if (isset($t['args']) && is_array($t['args']) && count($t['args']) > 0 ) {
	    				$result .="<div style=\"display:none; max-width:700px;\" id=\"backtrace_args{$i}_\">";

	    				$result .= "<pre style=\"float:left; max-width:700px; \">";
		    				$result .= '<a href="#" onclick="document.getElementById(\'backtrace_args'.$i.'_\').style.display=\'none\';  return false;" ';
	    				$result .= 'title="Hide parameters';
	    				//$result .= print_r($t['args'],true);
	    				$result .= '" style="float:right;"';
	    				$result .= '><u>X</u></a>';    				
	    				$result .= htmlspecialchars(print_r($t['args'],true))."</pre>";
	    				$result .="</div>";
	    			}	
	    			$result .= "</td>";
	    			$result .= "</tr>";
					
					$i++;
	    		}
	    		$result .= "</table>";
	    		$result .= "</div>";
	    		return $result;

    	}

    	 
	}

	
	
	
	
	
	
	
	
?>