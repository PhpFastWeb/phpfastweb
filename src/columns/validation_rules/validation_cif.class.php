<?php
class validation_cif extends avalidation_rule implements ivalidation_rule {
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
		//$ok = preg_match('/\A[0-9]{7,8}[A-Z]\z/', $val);
		$ok = true;
		//en caso negativo, avisamos
		//if ( ! $ok ) {
		//	$this->messages[] = 'El campo "'.$column->get_title().'" debe estar formado por 7 u 8 dígitos, y letra CIF.';
		//}
		//if ($ok) {
			//Intentamos validar la letra
			$result = $this->valida_nif_cif_nie($val);
			if ($result <= 0) {
					$this->messages[] = "CIF no coincide con letra.";
					$ok = false;

			}
		//}
		
		if ( ! $ok ) $this->invalidate();
		return $this->validates;
	}
	function valida_nif_cif_nie($cif) {
		//Copyright ©2005-2008 David Vidal Serra. Bajo licencia GNU GPL.
		//Este software viene SIN NINGUN TIPO DE GARANTIA; para saber mas detalles
		//puede consultar la licencia en http://www.gnu.org/licenses/gpl.txt(1)
		//Esto es software libre, y puede ser usado y redistribuirdo de acuerdo
		//con la condicion de que el autor jamas sera responsable de su uso.
		//Returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok, -1 = NIF bad, -2 = CIF bad, -3 = NIE bad, 0 = ??? bad
		   $cif = strtoupper($cif);
		   for ($i = 0; $i < 9; $i ++)
		      $num[$i] = substr($cif, $i, 1);
		//si no tiene un formato valido devuelve error
		   if (!@ereg('((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)', $cif))
		      return 0;
		//comprobacion de NIFs estandar
		   if (@ereg('(^[0-9]{8}[A-Z]{1}$)', $cif))
		      if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 0, 8) % 23, 1))
		         return 1;
		      else
		         return -1;
		//algoritmo para comprobacion de codigos tipo CIF
		   $suma = $num[2] + $num[4] + $num[6];
		   for ($i = 1; $i < 8; $i += 2) 
		      $suma += substr((2 * $num[$i]),0,1) + substr((2 * $num[$i]),1,1);
		   $n = 10 - substr($suma, strlen($suma) - 1, 1);
		//comprobacion de NIFs especiales (se calculan como CIFs o como NIFs)
		   if (@ereg('^[KLM]{1}', $cif))
		      if ($num[8] == chr(64 + $n) || $num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 1, 8) % 23, 1))
		         return 1;
		      else
		         return -1;
		//comprobacion de CIFs
		   if (@ereg('^[ABCDEFGHJNPQRSUVW]{1}', $cif))
		      if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1))
		         return 2;
		      else
		         return -2;
		//comprobacion de NIEs
		   //T
		   if (@ereg('^[T]{1}', $cif))
		      if ($num[8] == ereg('^[T]{1}[A-Z0-9]{8}$', $cif))
		         return 3;
		      else
		         return -3;
		   //XYZ
		   if (@ereg('^[XYZ]{1}', $cif))
		      if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X','Y','Z'), array('0','1','2'), $cif), 0, 8) % 23, 1))
		         return 3;
		      else
		         return -3;
		//si todavia no se ha verificado devuelve error 
		   return 0;
		}
}
?>