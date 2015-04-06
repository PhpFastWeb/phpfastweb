<?php
class validation_nss extends avalidation_rule implements ivalidation_rule {
	public function set_show_expected($show=true) {
		$this->show_expected = $show;
	}
	protected $show_expected = true;
	/**
	 * @param icolumn $column
	 */

	function check_column(icolumn $column) {
		if ($this->skip_validation($column)) return $this->validates;
		//--------------------------------
		$val = $column->get_value ();
		//Eliminamos guiones sobrantes, espacios, y ponemos en mayúsculas
		$val = trim (  str_replace(".","",$val)  );
		//
		//Si $val tiene los dígitos correctos pero sin '/', los añadimos
		if (  preg_match ( '/\A[0-9]{2}[0-9]{7,8}[0-9]{2}\z/', $val ) && strlen($val) <= 12) {
			$len = strlen($val)-2-2;
			$val = substr($val,0,2).'/'.substr($val,2,$len).'/'.substr($val,-2);
		}
		$column->set_value ( $val );
		//comparamos con máscara
		$ok = preg_match ( '/\A[0-9]{2}\/[0-9]{7,8}\/[0-9]{2}\z/', $val );
		
		//en caso negativo, avisamos
		if (! $ok) {
			$this->messages [] = 'El NSS debe ser de la forma AA/BBBBBBBB/CC, donde AA son 2 dígitos, B pueden ser 7 u 8 dígitos, y CC son dos dígitos.';
		}
		if ($ok) {
			//Intentamos validar el nímero
			$numero = str_replace('/','',$val);

			//Integers: The maximum value depends on the system. 32 bit systems have a 
			//maximum signed integer range of -2147483648 to 2147483647. So for example 
			//on such a system, intval('1000000000000') will return 2147483647. The maximum 
			//signed integer value for 64 bit systems is 9223372036854775807.

			//$numero = 280421646361;//Si empieza por 0 y SI FUNCIONA O VERIFICA COMO CORRECTO

			//$numero = 461050199640; // no empieza por 0, NO ME LO DA COMO CORRECTO
			//Son correctos pero no verifican: 46 01846894 18   28 04194075 80

			//$this->messages [] = $numero . " de tipo " . gettype ( $numero );
			

			$a = substr ( $numero, 0, 2 ); //a los dos primeros numeros
			//$this->messages [] ="<br>A los dos primeros numeros= " . $a . " de tipo " . gettype ( $a );
			;
			$c = substr ( $numero, - 2 ); //a los dos ultimos
			//$this->messages [] ="<br>C los dos ultimos= " . $c . " de tipo " . gettype ( $c );
			;
			$b = substr ( $numero, 2, - 2 ); // al resto
			//$this->messages [] = "<br>B al resto= " . $b . " de tipo " . gettype ( $b );
			//"<br>";
			//Algunos servidores tienen problemas si $b empieza por cero, eliminamos ese caso
			if (substr($b,0,1)=='0') $b = substr($b,1);

			if ($b < 10000000) {
				$d = $b + $a * 10000000;
				//$this->messages [] ="<br>D b<10.000.000 ahora vale = " . $d . " de tipo " . gettype ( $d );
			} else {
				//$bb=int($b);
				$d = $a . $b;
				//$this->messages [] = "<br>D b NO <10.000.000 ahora vale = " . $d . " de tipo " . gettype ( $d ); // ESTA LA HACE MAL
			}
			//$this->messages [] ="<br> valor de d (a+b)= " . $d . " de tipo " . gettype ( $d );
			$resto = $this->my_bcmod($a . $b,'97'); // Mod resto de la división entera 
			//$this->messages [] = "<br> el resto es ==== " . $resto . " de tipo " . gettype ( $resto );
			
			// Ahora COMPARAMOS
			//$this->messages [] ="<br>A COMPARA CON VAlor de C (DC de los dos ultimos numeros) = " . $c . " de tipo " . gettype ( $c );
			if ($c == $resto) {
				//$this->messages [] ="<br>CORRECTO";
			} else {
				//$this->messages [] ="<br>NO CORRECTO deberia de ser " . $resto;
				$ok = false;
				if ($this->show_expected) {
					$this->messages [] = "En NSS dígitos de control no válidos, se esperaba: ".$resto;
				} else {
					$this->messages [] = "Dígitos de control no válidos para el resto del NSS.";
				} 
			}
			
		}
		
		if ( ! $ok ) $this->invalidate();
		return $this->validates;
	}
	function my_bcmod( $x, $y )
	{
	    // how many numbers to take at once? carefull not to exceed (int)
	    $take = 5;    
	    $mod = '';
	
	    do
	    {
	        $a = (int)$mod.substr( $x, 0, $take );
	        $x = substr( $x, $take );
	        $mod = $a % $y;   
	    }
	    while ( strlen($x) );
	
	    return (int)$mod;
	} 
}
?>