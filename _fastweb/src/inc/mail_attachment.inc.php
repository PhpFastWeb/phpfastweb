<?php
//-- Info
//$name = "Vicente Herrera";
//$email = "vherrera@iditconsultores.com";
//$to = "$name <$email>";
//$from = "robot@iditconsultores.com";
//$subject = "Listado inscripciones";
/**
 * Send an email as HTML with an attached file
 * @author vicenteherrera@vicenteherrera.com
 * @param $filename string file name without path
 * @param $path path string to file, ending in '/'
 * @param $mailto string destination email, like 'Vicente Herrera <vicenteherrera@vicenteherrera.com>'
 * @param $from_mail string mail that will appear as origin, like 'sender@example.com'
 * @param $replyto string mail that will appear as reply-to, like 'reply-to@example.com'
 * @param $subject string subject of the email
 * @param $message string message of the email, in HTML
 */
function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    $uid = md5(uniqid(time()));
    //Header
    $header = "From: ".$from_name." <".$from_mail.">\r\n";
    $header .= "Reply-To: ".$replyto."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    //HTML message
    $header .= "--".$uid."\r\n";
    $header .= "Content-type:text/html; charset=iso-8859-1\r\n";
	$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message."\r\n\r\n";
    //file attach
    $file = $path.$filename;
    if ( is_file($file) ) {
        $file_size = filesize($file);
        $handle = fopen($file, "r");
        $content = fread($handle, $file_size);
        fclose($handle);
        $content = chunk_split(base64_encode($content));
        $name = basename($file);
        $header .= "--".$uid."\r\n";
        $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
        $header .= "Content-Transfer-Encoding: base64\r\n";
        $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
        $header .= $content."\r\n\r\n";
    }    
    //End email
    $header .= "--".$uid."--";
    return @mail($mailto, $subject, "", $header);
}
//mail_attachment('inscripciones.csv','./inscripciones/','vherrera@iditconsultores.com','robot@iditconsultores.com','robot@iditconsultores.com','robot@iditconsultores.com','listado adjuntos','este es una prueba de listado adjuntos');
?>
