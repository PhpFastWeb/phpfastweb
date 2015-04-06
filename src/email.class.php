<?php

class email {
    public $from;
    public $to;
    public $subject;
    public $message_html;
    public $message_txt;
    public function send() {
        self::send_email($this->from, $this->to, $this->subject, $this->message_html);
    }
    static function send_email($from, $to, $subject, $message_html, $message_txt = '') {
        
        $email = $to;
        				
        //create a boundary for the email. This 
        $boundary = uniqid('np');
        				
        //headers - specify your from email address and name here
        //and specify the boundary for the email
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: $from \r\n";
        $headers .= "To: ".$email."\r\n";
        //'Reply-To: ' . $from,
        //'Return-Path: ' . $from,
        $headers .= 'Date: ' . date('r', $_SERVER['REQUEST_TIME'])."\r\n";
        $headers .= 'Message-ID: <' . $_SERVER['REQUEST_TIME'] . md5($_SERVER['REQUEST_TIME'].$subject) . '@' . $_SERVER['SERVER_NAME'] . '>'."\r\n";
        $headers .= 'X-Mailer: PHP v' . phpversion()."\r\n";
        $headers .= 'X-Originating-IP: ' . $_SERVER['SERVER_ADDR']."\r\n";
        
        $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
        
        //here is the content body
        $message = "This is a MIME encoded message.";
        
        //Plain text body
        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: text/plain;charset=iso-8859-1\r\n\r\n";
        
        if ( $message_txt == '' ) {
           $message_txt = self::get_txt_from_html($message_html);
        }
        $message .= $message_txt;
        //Html body
        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: text/html;charset=uiso-8859-1\r\n\r\n";
        $message .= $message_html;
        
        $message .= "\r\n\r\n--" . $boundary . "--";
        
        //error_log("mail to:$to from:$from subject:$subject\r\n");
        
		if ( website::in_developer_mode() ) {
			echo "mail to:$to from:$from subject:$subject<br /><br />";
			echo $message_html."<br /><br /><pre>".$message_txt."</pre>";
			return;
		} else {
        
            //invoke the PHP mail function
            mail('', $subject, $message, $headers);
        }
    
    }
    private static function get_txt_from_html($message_html) {
        $replace = array(
            '<br />' => "\r\n",
            '<br/>'  => "\r\n",
            '<br>'   => "\r\n",
            '</h2>'  => "\r\n",
            '</p><p>' => "\r\n",
            '<p>' => "\r\n",
            '</p>' => "\r\n",
            '</p>'   => "\r\n",
            '</tr>'  => "\r\n",
            '<table>' => "\r\n",
            '</table>' => "\r\n",
            '</td>' => ' '
        );
        //TODO: use regex to take into account attributes
        $result = str_ireplace("\n","",$message_html); //Removes all original intros (not used)
        $result = str_ireplace("\r","",$message_html); //Removes all original intros (not used)
        $result = str_ireplace("\t","",$message_html); //Removes all tabs (not used)
        foreach ($replace as $src => $new) {
            $result = str_ireplace($src, $new, $result);
        }
        $result = strip_tags($result); //Removes remaining html tags
        return $result;
    }
}