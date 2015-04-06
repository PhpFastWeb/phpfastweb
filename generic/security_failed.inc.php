<?php
    global $HTTP_SERVER_VARS;
  	//Rescatamos la URL de destino de vuelta a esta misma página
	// if 'REQUEST_URI' isn't available then ...
		if ( ! isset($_SERVER['REQUEST_URI'] ) ) {
		   $temp_request_url = $_SERVER['PHP_SELF'];
		   if (isset($HTTP_SERVER_VARS['QUERY_STRING'])) {
		       $temp_request_url .= (strpos($temp_request_url, '?')) ? "&" : "?";
		       $temp_request_url .= $HTTP_SERVER_VARS['QUERY_STRING'];
		   }
		} else {
		   $temp_request_url = $_SERVER['REQUEST_URI'];
		}
		$temp_request_url = 'http://'.$_SERVER['SERVER_NAME'].$temp_request_url;
		$browser = @get_browser(null,true);
		if ( ! ( $referer = $_SERVER['HTTP_REFERER'] ) ) {
			$referer = getenv("HTTP_REFERER");
		}
?>
<html>
<head>
	<style type="text/css">
		.icon { font-size: 32px; background-color: yellow; border-color: silver;
			    border-style: solid; border-width: 1px; margin-right: 10px; width: 25px; 
			    text-align: center;
		}
		h1 { font-size: 22px; color: red; }
		h2 { font-size: 18px; color: blue; }
		body { font-family: sans-serif; font-size: 12px; }
		label { width:82px; font-weight: bold;}
	</style>
</head>
<body>
	<h1><span class="icon">!</span>Acceso Denegado</h1>
	<h2>Por cuestiones de seguridad no se ha podido acceder a la página.</h2>
	<div style="background-color: #EEE; border-color: silver; border-style: solid; border-width: 1px;
	margin: 6px 6px 6px 6px; padding: 6px 6px 6px 6px; width: 600px;">
	<b>url</b>: <?php echo $temp_request_url?><br />
	</div>
	<br />
	<font style="font-size:14px;">
	Recuerde que para acceder a la página solicitada puede necesitar de ciertas credenciales de seguridad,<br />como acceder desde una IP dentro de algun rango permitido, 
	asi como posiblemente haber ingresado un<br />usuario y contraseña correctos.<br />
	<br />
	En muchos casos, es necesario acceder desde un navegador con javascript y cookies habilitados para este sitio web.<br /></font>	
	<br />
	Este aviso puede ser registrado como una alerta de seguridad en el sistema, entre los que se incluirían los siguientes datos:<br />
	<ul>
		<li><label>URL</label>: <?php echo $temp_request_url?></li>
		<li><label>IP</label>: <?php echo $_SERVER['REMOTE_ADDR']?></li>
		<li><label>Fecha y hora</label>: <?php echo date('D-m-y h:i:s')?></li>
		<li><label>Referer</label>: <?php echo $referer?></li>
		<li><label>User Agent</label>: <?php echo $_SERVER['HTTP_USER_AGENT']?></li>
		<li><label>Plataforma</label>: <?php echo $browser['platform']?></li>
		<li><label>Navegador</label>: <?php echo $browser['browser'].' '.$browser['version']?>
			<ul>
				<li><label>javascript</label>: <?php echo ( $browser['javascript']==1 ? 'Si' : 'No' )?> *</li>
				<li><label>cookies</label>: <?php echo ( $browser['cookies']==1 ? 'Si' : 'No' )?> *</li>
			</ul>
		</li>
	</ul>
</body>
</html>
<?php die; ?>
