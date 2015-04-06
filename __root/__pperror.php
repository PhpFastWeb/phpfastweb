<html>
<head>
<title>Recepci�n de informe de error</title>
<meta name="robots" content="noindex">
<style>
	* { font-family: sans-serif; font-size:12px;}
	h1 { font-size: 23px; margin-top:0 }
	h2 { font-size: 17px; }
	td { padding:5px 5px; }
	
</style>
</head>
<body>
	<div style="border:1px solid gray; padding:20px 20px; margin: 15px auto 0 auto; width:500px;">
	   <h1>Recepci�n de informe de error</h1>
<?php
    if (!isset($_POST) || count($_POST)==0) {
        echo '<h2>Informe de error no recibido</h2>';
        echo 'Ocurri� un error al recibir el informe de error.<br />Quiz�s no carg� esta p�gina desde el formulario de env�o de error.<br /><br />Adicionalmente, nuestros ingenieros han sido notificados de �sta circunstancia.';    
        echo '</div></body></html>';
        die;
    } 
    
    echo '<h2>Informe recibido correctamente.</h2>';
    echo 'Gracias por comunicarnos esta informaci�n.<br /><br />';
    
    $msg = @date('l jS \of F Y h:i:s A')."\r\n\r\n";
    foreach ($_POST as $k => $v) {
        $msg .= "$k : ".$v."\r\n\r\n";    
    }
    $title = 'Informe error';
    $headers = 'From: webmaster@sistemas5.info' . "\r\n" .
    'Reply-To: no-reply@sistemas5.info' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    
    $result = @mail('vherrera@iditconsultores.com','Informe de error',$msg, $headers);
    
    if ($result) {
        echo 'Se ha enviado una notificaci�n a nuestros ingenieros para que revisen el error.<br />';
    } else {
        echo 'Ocurri� un error al intentar notificar autom�ticamente a nuestros, por favor, p�ngase en contacto con nosotros por email.';
    }
    echo "<br /><br />No utilice el bot�n \"atr�s\" de su navegador para volver a la web, vuelva a escribir su direcci�n en la barra de direcciones.";
?>
    </div>
</body>
</html>