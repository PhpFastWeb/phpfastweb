<?php

$log_url = dirname(__FILE__).'/../../../extra/stats/'; //Change here default folder to write logs
$filename = rawurlencode(urlencode($_SERVER['PHP_SELF'])); 
$filename_t = $log_url.'__total.csv';
$filename_tc = $log_url.'__c_total.txt';
$filename_pc = $log_url.$filename.'.txt';

// Particular URL visits counter
if ( is_writable( $filename_pc ) && is_readable( $filename_pc ) ) {
	$c = file_get_contents( $filename_pc );
	if ( is_numeric( $c ) ) @file_put_contents($filename_pc, $c + 1);
} else {
    if( is_dir( $log_url ) && is_writable( $log_url ) && ! file_exists( $filename_pc ) )
        @file_put_contents($filename_pc, '1');
}

//Global site visits counter
if ( is_writable($filename_tc) && is_readable($filename_tc) ) {
	$c = file_get_contents($filename_tc);
	if ( is_numeric($c) ) @file_put_contents($filename_tc, $c + 1);
} else {
    if( is_dir($log_url) && is_writable($log_url) && ! file_exists($filename_tc))
        @file_put_contents($filename_tc, '1');
}

//Now we register data from each visits
$header =
	'"IP";'.
	'"Time";'.
	'"Referer domain";'.
	'"PHP File";'.
	'"Referer";'.
	'"User Agent";'.
	'"Request Uri";'.
	"\r\n";
//Get domain name without protocol
$rdomain = str_replace('http://','',$_SERVER['HTTP_REFERER']);
$rdomain = str_replace('https://','',$rdomain);
$rdomain = substr($rdomain,0,strpos($rdomain,'/'));
//Get remote IP address
if ( isset($_SERVER["REMOTE_ADDR"]) )    {
    $cip = $_SERVER["REMOTE_ADDR"] . ' ';
} else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    {
    $cip = $_SERVER["HTTP_X_FORWARDED_FOR"] . ' ';
} else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )    {
    $cip = $_SERVER["HTTP_CLIENT_IP"] . ' ';
} else $cip = '';
//Create record string
$visit = 
	'"'.$cip.'";'.
	'"'.date('Y-m-d H:i:s').'";'.
	'"'.$rdomain.'";'.
	'"'.$_SERVER['PHP_SELF'].'";'.
	'"'.$_SERVER['HTTP_REFERER'].'";'.	
	'"'.$_SERVER['HTTP_USER_AGENT'].'";'.
	'"'.$_SERVER['REQUEST_URI'].'";'.
	"\r\n";
//Store record string
if ( is_writable($filename_t) ) {
	$fh = fopen($filename_t, 'a') or die;
	@fwrite($fh, $visit);
	fclose($fh);
} else {
    if( is_dir( $log_url ) && is_writable( $log_url ) && ! file_exists( $filename_t ) ) {
		$fh = fopen($filename_t, 'a') or die;
		@fwrite($fh, $header);		
		@fwrite($fh, $visit);
		fclose($fh);
	}
}
?>