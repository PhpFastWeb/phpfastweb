<?php
class validation_nif extends avalidation_rule implements ivalidation_rule {
	public function set_show_expected($show=true) {
		$this->show_expected = $show;
	}
	protected $show_expected = true;
	function check_column(icolumn $column) {	
		if ($this->skip_validation($column)) return $this->validates;
		
		$val = $column->get_value();
		//Eliminamos guiones sobrantes, espacios, y ponemos en mayúsculas
		$val = trim(strtoupper(str_replace('-','',$val)));
		$val = trim(strtoupper(str_replace(' ','',$val)));
		$val = trim(strtoupper(str_replace('.','',$val)));
		$column->set_value($val);
		
		if ($val=='') {
			$this->validates = true;
			return $this->validates;
			
		}
		//comparamos con máscara
		$ok = preg_match('/\AX?[0-9]{7,8}[A-Z]\z/', $val);
	
		//en caso negativo, avisamos
		if ( ! $ok ) {
			$this->messages[] = 'El campo "'.$column->get_title().'" debe estar formado por letra X opcional, 7 u 8 dígitos, y letra NIF.';
		}
		if ($ok) {
			//Intentamos validar la letra
			$l = substr($val,-1);
			$dni = substr($val,0,-1);
			//Quitamos posible X de extranjero
			if (substr($dni,0,1)=='X') $dni = substr($dni,1);
			
			/* Obtiene letra del NIF a partir del DNI */
			$valor= (int) ($dni / 23);
			$valor *= 23;
			$valor= $dni - $valor;
			$letras= "TRWAGMYFPDXBNJZSQVHLCKEO";
			$letraNif= substr ($letras, $valor, 1);
			if ($l != $letraNif) {
				$ok = false;
				if ($this->show_expected) {
					$this->messages[] = "Letra DNI no coincide con números, se esperaba '".$letraNif."'.";
				} else {
					$this->messages[] = "La letra del DNI no coincide con el resto del número.";
				} 
			}
		}
		
		if ( ! $ok ) $this->invalidate();
		return $this->validates;
	}
}
?>