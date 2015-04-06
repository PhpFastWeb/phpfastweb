<?php
class message_float extends aweb_object implements iweb_object {
	protected $messages = array();
	protected $types = array();
	public $rightOffset = 0;
	public function __construct($rightOffset=0) {
		$this->rightOffset = $rightOffset;
	    if ( ! isset($_SESSION['floating_messages_']) ||
    		count ($_SESSION['floating_messages_'])==0) {
    			return;
    	}
		$this->messages = $_SESSION['floating_messages_'];
    	unset($_SESSION['floating_messages_']);
	}
	public static function add_message($message,$type='notice') {
		if(!isset($_SESSION)) @session_start();
		$_SESSION['floating_messages_'][] = array($message,$type);
	}
    public function get_html_post_body_ini() {
    	if (count($this->messages)==0) return '';
    	$text = '';
    	foreach ($this->messages as $message) {
    		$text .= $message[0]; //Nos quedamos con el texto, ignoramos el "tipo"
    	}
    	//-----
        $color = 'white';
        $leftPadding = (100+$this->rightOffset)."px";
        
		$style = "background-color:$color; text-align:center; margin: 130px auto 0 auto; font-size:12px; width:300px; padding:20px 5px; background-color:#DEDEDE; border:3px solid green; ";
        $style .= "  position:absolute; zoom:1; left:46%";
        
		$result = "";
        //$result .= "<div style=\"padding-left:$leftPadding;  position:absolute; width:100%; zoom:1;\" id=\"ok_message\">\r\n";
        $result .= "<div style=\"$style\" id=\"ok_message\">\r\n";
        $result .= "<img src=\"" . website::$theme->get_img_dir () . "/icon_ok.gif\" align=\"top\" border=\"0\" alt=\"\" /> ";
        $result .= "<b>$text</b>\r\n";
		$result .= "</div>\r\n";
		$result .= "</div>\r\n";
		$result .= '<script type="text/javascript">addEvent(window,"load",function(){ opacity(\'ok_message\', 100, 0, 3000); setTimeout(\'document.getElementById("ok_message").parentNode.removeChild(document.getElementById("ok_message"));\',3000); });</script>';
		//$result .= '<script type="text/javascript">addEvent(window,"load",function(){alert("ok"); changeOpac(50,\'ok_message\');});</script>';
        return $result;
    }

}
