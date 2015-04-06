<?php
class validation_date extends avalidation_rule implements ivalidation_rule {
	function check_column(icolumn $column) {
		if ($this->skip_validation($column)) return $this->validates;

		//-----------
		$fv = $column->get_formatted_value ();

		if (
            $fv  == '00-00-0000' || $fv == '00-00-0000 00:00:00' ||
            $fv  == '00/00/0000' || $fv == '00/00/0000 00:00:00'
        
        ) {
			$column->set_value ( '' );
			$column->set_formatted_value ( '' );
		}
		if ($fv  != '') {
			if (preg_match ( '$\d\d/\d\d/\d\d\d\d$', $fv  ) ) 
            {
				$tokens = explode ( "/", $column->get_formatted_value () );
				$result = $tokens [2] . "/" . $tokens [1] . "/" . $tokens [0];
				//$column->set_value ( $result );
			} else {
				$this->messages[] = "Debe escribir la fecha en formato DD/MM/AAAA.";
				$this->invalidate();
			} 
		}
		return $this->validates;
	}
}
?>