<?php
class database_errors_spanish {
	
	public static function get_error($num) {
		//TODO: Pasar m�s informaci�n de par�metro (por ejemplo el mensaje de error original), no todo est� en el n�mero
		$result = '';
        
        switch($num) {
			case '1062':
				$result = 'El valor especificado ya existe en la tabla';
				break;
			case '1451':
				$result = 'No se puede actualizar o borrar el elemento, existen otros elementos asociados a �l.';
				break;
			case '1048':
				$result = 'Algun campo obligatorio ha quedado sin especificar.';
				break;	
			case '1064':
				$result = 'Error de sintaxis en consulta SQL.';
				break;
			case '1054':
				$result = 'Error ejecutando consulta SQL.';
				break;
			case '1452':
				$result = 'No se pudo guardar la informaci�n introducida ya que incumple una restricci�n.';
				break;
			case '1146':
				$result = 'No existe la tabla referenciada.';
				break;
 			case '1241':
				$result = 'N�mero incorrecto de columnas de la subconsulta.';
				break;
            case '1242':
				$result = 'N�mero incorrecto de registros de la subconsulta.';
				break;
            case '1093':
				$result = 'Tabla usada incorrectamente en la subconsulta.';
				break;               










                
			default:
				$result = "Error #".$num;
				break;
				
		}
        
        return $result;
	}
}