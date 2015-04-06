<?php

class web_footer extends aweb_object implements iweb_object {
	
	public function __toString() {
		?>

			<br style="clear:both;" />
			<p style="margin: 25px 0 5px 0;">
                <!--
				<a href="http://www.autogestionpreventiva.com">www.autogestionpreventiva.com</a> &copy;<?php echo Date('Y'); ?> <a href="http://www.iditconsultores.com" target="_top">Innovación y Desarrollo Internacional Consultores.</a><br />
                -->
			</p>
			<p style="margin: 5px 0;">
                <!--
				<a href="<?php echo website::$base_url;?>/info/politica_privacidad.php">Política de privacidad</a> &nbsp; | &nbsp;  
				<a href="<?php echo website::$base_url;?>/info/accesibilidad.php">Accesibilidad</a>
                --> 
			</p>
			<p style="margin: 15px 0;">
            
				<a href="http://validator.w3.org/check?uri=referer" target="_blank"><img src="<?php echo website::$base_url;?>/_fastweb/src/ui/web_footer/xhtml1.gif" alt="XHTML 1.0 Transicional" style="width:80px; height:15px;" /></a>
	            <a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank"><img src="<?php echo website::$base_url;?>/_fastweb/src/ui/web_footer/css2.gif" alt="CSS 2.0" style="width:72px; height:15px;" /></a>
	            <a href="http://www.w3.org/WAI/WCAG1AA-Conformance" target="_blank"><img src="<?php echo website::$base_url;?>/_fastweb/src/ui/web_footer/wai-aa.gif" alt="WAI-AA" style="width:80px; height:15px;" /></a>
	            <a href="http://www.phpfastweb.com" target="_blank"><img src="<?php echo website::$base_url;?>/_fastweb/src/ui/web_footer/phpfastweb.gif" alt="phpFastWeb" style="width:74px; height:15px;" /></a><br />
			</p>
			<br /><br />            
       <?php  
   }
   
}
?>