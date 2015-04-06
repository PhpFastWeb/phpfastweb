<?php
/*
  USAGE: 
  
	require 'inscripcion.inc.php';
	$result = inscribir(
		"vherrera@iditconsultores.com,vicenteherrera@vicenteherrera.com",
		"robot@iditconsultores.com",
		'evento',
		'inscripciones.csv',
		'Inscripción recibida con éxito');
	if ( !isset($_POST) || count($_POST)==0 || is_null($result) ) {
	   
    //Sometimes 'from' mail must be the same domain as hosted PHP
*/
require dirname(__FILE__)."/mail_attachment.inc.php";

/**
 * Send an email with the content of posted form, optionally save form data to CSV and attach CSV file to email
 * @author vicenteherrera@vicenteherrera.com
 * @param $para
 * @param $desde
 * @param $subject_key
 * @param $save_log
 * @param $success_msg
 */
function inscribir($para,$desde,$subject_key = 'curso', $save_log = false, 
	$success_msg='<b>Inscripción realizada con éxito.</b><br />Nos pondremos en contacto con usted para confirmar su asistencia'
	) {

	if (!isset($_POST['submit'])) return;
    
	$result = null;
	$save_folder = './extra/';
	//error_reporting(E_ALL);
	setlocale(LC_TIME, 'spanish');
	//---------------------------------------------------------
	//Procesamos los campos para enviar email;
	//$mensaje = $asunto."\r\n\r\n<table>";
	$fecha = strftime("%A %#d de %B del %Y, %H:%M");
	$lstyle = 'style=" padding:5px 5px;vertical-align:top;  border:1px solid #ccc; width: 150px;"';
	$vstyle = 'style=" padding:5px 5px;vertical-align:top; border:1px solid #ccc; width: 450px;"';
	$mensaje = "<table><tr><td $lstyle>Fecha:</td><td $vstyle>$fecha</td></tr>\r\n\r\n";
	$header = '"Fecha";'; $line = '"'.$fecha.'";';
	foreach ($_POST as $key => $value) {
		if ($key != 'submit') {
			$mensaje .= "<tr><td $lstyle>".ucfirst(str_replace('_',' ',$key)).": </td><td $vstyle>".$value."&nbsp;</td></tr>\r\n";
			$header .= '"'.str_replace('"','\\"',$key).'";';
			$line   .= '"'.str_replace('"','\\"',$value).'";';
		}	
	}
	$mensaje .= "</table>";
	//----------------------------------------------------------
	//Guardamos log y calculamos nº de inscripciones
	$num = 0;
	if ($save_log) {
		$error = false;
		$create = file_exists($save_folder.$save_log) ? false : true;
		$h = @fopen($save_folder.$save_log,'c+') or $error = true;
		if ( ! $error ) {
			while ( ! feof($h) ) { if (fgets($h)) $num++; }
			if ($create) @fwrite($h,$header."\r\n");
			@fwrite($h,$line."\r\n");
			@fclose($h);
		}
	}
	$num > 0 ? $num = "(". $num .")" : $num = '';
	//----------------------------------------------------------
	//Enviamos mensaje
	$asunto  = "Formulario de información $num";
    if ( $subject_key != '' && isset( $_POST[$subject_key] ) ) $asunto .= ': '.$subject_key;
	$mensaje = "<br />\r\n".$asunto."<br /><br />\r\n\r\n".$mensaje;
	if ( ! file_exists( $save_folder.$save_log ) ) { $save_log = ''; $save_folder = ''; }
	$result = @mail_attachment($save_log,$save_folder,str_replace(';',',',$para), $desde, $desde, $desde, $asunto, $mensaje);
	//Alternative regular mail sending
    //$cabeceras = "From: {$desde}\r\nReply-To: {$desde}\r\nX-Mailer: PHP/".phpversion()."\r\nContent-type: text/html; charset=iso-8859-1";		
	//$result = @mail($para, $asunto, $mensaje."\r\n\r\n", $cabeceras);

	//----------------------------------------------------------
	if ( $result ) {
		echo '<br /><div style="border:2px dotted green; padding:20px 20px; margin:10px auto; width:500px;">';
		echo $success_msg;
		echo "</div>";
		echo "<br style=\"clear:both;\" />";
	} else {
		echo '<br /><div style="border:2px dotted orange; padding:20px 20px; margin:10px auto; width:500px;">';
		echo "<b>Ocurrió un problema al enviar la información.<br />Por favor, inténtelo de nuevo más tarde.</b>";
		echo "</div>";
		echo "<br style=\"clear:both;\" />";
	}
	return $result;
}
?>