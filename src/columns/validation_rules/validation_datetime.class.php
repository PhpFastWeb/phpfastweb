<?php
class validation_datetime extends avalidation_rule implements ivalidation_rule {
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
			if ((preg_match ( '$\d\d/\d\d/\d\d\d\d \d\d:\d\d:\d\d$', $fv  ) && strlen($fv)==19) ||
				(preg_match ( '$\d\d/\d\d/\d\d\d\d \d:\d\d:\d\d$', $fv  ) && strlen($fv)==18))
			{
				$value = $fv;
				$value = str_replace(" ","/",$value);
				$value = str_replace(":","/",$value);
				$tokens = explode("/",$value);
				$result = $tokens[2]."/".$tokens[1]."/".$tokens[0]." ".$tokens[3].":".$tokens[4].":".$tokens[5];
				//$column->set_value ( $result );
                
                //TODO: Allow not including seconds, and add 0s automatically
			} else {
				$this->messages[] = "Debe escribir la fecha en formato DD/MM/AAAA HH:MM:SS."; //.strlen($fv);
				$this->invalidate();
			} 
		}
		return $this->validates;
	}
}