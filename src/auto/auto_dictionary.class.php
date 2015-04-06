<?php
/**
 * Clase para realizar sustituciones de palabras con acentos mal escritos,
 * para palabras que sin ninguna ambig�edad se escriben con acento,
 * di�resis, etc.
 * 
 * Muy util para transformaci�n de nombres de columnas en bases de datos,
 * variables programadas, o para preprocesar textos escritos por usuarios descuidados.
 */
class auto_dictionary {
	public $equ_list = null;
	
    public function process_table_data(table_data $table) {        
		//Redefinimos los t�tulos a�adiendo correcci�n ortogr�fica y
		//mejorando nombres de columnas
		$columns_title = array();
		//$auto_dic = new auto_dictionary();
		foreach ($table->columns as $col_name) {
			if ( empty($table->columns_title[$col_name] ) ) {
				$columns_title[$col_name] = $this->get_auto_col_title($col_name);
			} else {
				$columns_title[$col_name] = $table->columns_title[$col_name];
			}
		}
		//		if ($this->meta_use) {
		//			$columns_title['META_STATE']='Estado';
		//		}
		//Sustituimos por los t�tulos especificados
		$table->columns_title = $columns_title;     
    }
    
	function get_auto_col_title($col_name) {
		$words = explode('_',$col_name); //Separamos palabras por '_'
		foreach ($words as $word) {
			$new_words[] = $this->get_auto_word($word); //Procesamos equivalencia
		}
		$result = implode(' ',$new_words); //Pegamos palabras con ' ' (espacio)
		return ucfirst($result); //Enviamos la cadena, con la primera letra en mayuscula
	}
	function get_auto_word($word) {
		$this->load_equ_list($word); //Carga la lista de equivalencia desde esa palabra
		$word = strtolower($word);  //Convertimos la palabra a minuscula
		if ( key_exists($word,$this->equ_list) ) {
			$result = $this->equ_list[$word]; //Devolvemos la equivalente
		} else {
			$result = $word; //La devolvemos en minuscula
		}
		return $result;
	}
	function load_equ_list($word=null) {
		//De momento se cargan todas las listas de equivalencia a la vez
		//Tambi�n podemos modificar esta funci�n para trabajar con una base de datos
		if ($this->equ_list==null) {
			$this->equ_list = array(
				'minimo' => 'm�nimo',
				'maximo' => 'm�ximo',
				'recepcion' => 'recepci�n',
				'reposicion' => 'reposici�n',
				'planificacion' => 'planificaci�n',
				'prevision' => 'previsi�n',
				'preparacion' => 'preparaci�n',
				'creacion' => 'creaci�n',
				'codigo' => 'c�digo',
				'codigos' => 'c�digos',
				'autorizacion' => 'autorizaci�n',
				'area' => '�rea',
				'categoria' => 'categor�a',
				'anyo' => 'a�o',
				'telefono' => 'tel�fono',
				'descripcion'=>'descripci�n',
				'numero' => 'n�mero',
				'direccion' => 'direcci�n',
				'salon' => 'sal�n',
				'credito' => 'cr�dito',
				'calificacion' => 'calificaci�n',
				'informacion' => 'informaci�n',
				'interes' => 'inter�s',
				'epoca' => '�poca',
				'ubicacion' => 'ubicaci�n',
				'genero' => 'g�nero',
				'puntuacion' => 'puntuaci�n',
				'calsificacion' => 'clasificaci�n',
				'anyo' => 'a�o',
				'direccion' => 'direcci�n',
				'credito' => 'cr�dito',
				'salon' => 'sal�n',
				'orientacion' => 'orientaci�n',
				'ultima' => '�ltima',
				'ultimo' => '�ltimo',
				'actualizacion' => 'actualizaci�n',
				'categoria' => 'categor�a',
				'clasificacion' => 'clasificaci�n',
				'delegacion' => 'delegaci�n',
				'extension' => 'extensi�n',
				'movil' => 'm�vil',
				'tutorizacion' => 'tutorizaci�n',
				'minimos' => 'm�nimos',
				'optimos' => '�ptimos',
				'caracteristicas' => 'caracter�sticas',
				'titulo' => 't�tulo',
				'diagnostico' => 'diagn�stico',
				'economica'=>'econ�mica',
				'economico'=>'econ�mico',
				'situacion'=>'situaci�n',
				'retribucion'=>'retribuci�n',
				'basico'=>'b�sico',
				'cursandolo'=>'curs�ndolo',
				'formacion'=>'formaci�n',
				'aplicacion'=>'aplicaci�n',
				'ocupacion'=>'ocupaci�n',
				'adaptacion'=>'adaptaci�n',
				'titulacion'=>'titulaci�n',
				'validacion'=>'validaci�n',
				'finalizacion'=>'finalizaci�n',
				'proxima'=>'pr�xima',
				'proximo'=>'pr�ximo',
				'actuacion'=>'actuaci�n',
				'duracion'=>'duraci�n',
				'incorporacion'=>'incorporaci�n',
				'pais'=>'pa�s',
				'vehiculo' => 'veh�culo',
				'valoracion'=>'valoraci�n',
				'satisfaccion'=>'satisfacci�n',
				'poblacion'=>'poblaci�n',
				'reclamacion'=>'reclamaci�n',
				'organizacion'=>'organizaci�n',
				'contratacion'=>'contrataci�n',
				'edicion'=>'edici�n',
				'audicion'=>'audici�n',
				'vision'=>'visi�n',
				'dia'=>'d�a',
				'dias'=>'d�as',
				'definicion'=>'definici�n',
				'ambito'=>'�mbito',
				'analisis'=>'an�lisis',
				'publicacion'=>'publicaci�n',
				'metodo'=>'m�todo',
				'busqueda'=>'b�squeda',
				'tecnico'=>'t�cnico',
				'anotacion'=>'anotaci�n',
				'seccion'=>'secci�n',
				'autonoma'=>'aut�noma',
				'ip' => 'IP',
				'cif' => 'CIF',
				'nif' => 'NIF',
				'dni' => 'DNI',
				'razon' => 'raz�n',
				'prevencion' => 'prevenci�n',
				'insercion' => 'inserci�n',
				'cotizaci�n' => 'cotizaci�n',
				'imparticion' => 'impartici�n',
                'resolucion' => 'resoluci�n'
			);
		}
	}
}
?>
