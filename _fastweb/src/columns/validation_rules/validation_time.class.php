<?php
class validation_time extends avalidation_rule implements ivalidation_rule {
	function check_column(icolumn $column) {
		if ($this->skip_validation($column)) return $this->validates;
		//-----------
		$fv = $column->get_formatted_value ();
		if ($fv  == '00:00' || $fv == '00:00:00') {
			$column->set_value ( '' );
			$column->set_formatted_value ( '' );
		}
		if ($fv  != '') {
            $new_value = '';
			if (preg_match ( '/\d\d:\d\d:\d\d/', $fv ) ) {
				//Ya está formateado
				$new_value = $column->get_formatted_value();
			} else if (preg_match ( '/\d:\d\d:\d\d/', $fv ) ) {
				//Le falta 0 al principio
				$new_value = "0".$column->get_formatted_value();
			} else if (preg_match ( '/\d\d:\d\d/', $fv ) ) {
				//Le faltan segundos al final
				$new_value = $column->get_formatted_value().":00";
			} else if (preg_match ( '/\d:\d\d/', $fv ) ) {
				//Le falta 0 al principio y segundos al final
				$new_value = "0".$column->get_formatted_value().":00";
			} else if (preg_match ( '/\d\d/', $fv ) ) {
				//Le faltan horas y segundos
				$new_value = "00:".$column->get_formatted_value().":00";
			} else if (preg_match ( '/\d/', $fv ) ) {
				//Le faltan horas, segundos y 0
				$new_value = "00:0".$column->get_formatted_value().":00";				
			}  else if (preg_match ( '/\d\d:\d/', $fv ) ) {
				$this->messages[] = "Debe escribir la hora en formato HH:MM:SS o HH:MM o MM";
				$this->invalidate();
                return $this->validates;
			}  else if (preg_match ( '/\d:\d/', $fv ) ) {
				$this->messages[] = "Debe escribir la hora en formato HH:MM:SS o HH:MM o MM";
				$this->invalidate();
                return $this->validates;
			} else {
				$this->messages[] = "Debe escribir la hora en formato HH:MM:SS o HH:MM o MM";
				$this->invalidate();
                return $this->validates;
			}
            
            //Ya tenemos la hora formateada, promovemos los segundos y minutos mayores de 59.
            if ( ! preg_match ( '/\d\d:\d\d:\d\d/', $new_value ) ) {
                //throw new ExceptionDeveloper('wrong time format');
                //No se debería llegar aquí, pero como ha saltado alguna vez esta excepción,
                //enviamos en su lugar el mensaje para que sea más amigable.
				$this->messages[] = "Debe escribir la hora en formato HH:MM:SS o HH:MM o MM";
				$this->invalidate();
                return $this->validates;
            }
            $tokens = explode(':',$new_value);
            if ($tokens[2]>59) {
                $tokens[1] = $tokens[1] + ( $tokens[2] - ($tokens[2] % 60) ) / 60;
                $tokens[2] = $tokens[2] % 60;
            }            
            if ($tokens[1]>59) {
                $tokens[0] = $tokens[0] + ( $tokens[1] - ($tokens[1] % 60) ) / 60;
                $tokens[1] = $tokens[1] % 60;
            }  
            foreach($tokens as $k=>$t) {
                $tokens[$k] = sprintf("%02d",$t);    
            }          
            $new_value = implode(':',$tokens);

            $column->set_value($new_value);
		}
		return $this->validates;
	}
}
?>