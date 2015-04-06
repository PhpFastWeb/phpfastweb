<?php
/**
 * Clase para realizar sustituciones de palabras con acentos mal escritos,
 * para palabras que sin ninguna ambigüedad se escriben con acento,
 * diéresis, etc.
 * 
 * Muy util para transformación de nombres de columnas en bases de datos,
 * variables programadas, o para preprocesar textos escritos por usuarios descuidados.
 */
class auto_dictionary {
	public $equ_list = null;
	
    public function process_table_data(table_data $table) {        
		//Redefinimos los títulos añadiendo corrección ortográfica y
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
		//Sustituimos por los títulos especificados
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
		//También podemos modificar esta función para trabajar con una base de datos
		if ($this->equ_list==null) {
			$this->equ_list = array(
				'minimo' => 'mínimo',
				'maximo' => 'máximo',
				'recepcion' => 'recepción',
				'reposicion' => 'reposición',
				'planificacion' => 'planificación',
				'prevision' => 'previsión',
				'preparacion' => 'preparación',
				'creacion' => 'creación',
				'codigo' => 'código',
				'codigos' => 'códigos',
				'autorizacion' => 'autorización',
				'area' => 'área',
				'categoria' => 'categoría',
				'anyo' => 'año',
				'telefono' => 'teléfono',
				'descripcion'=>'descripción',
				'numero' => 'número',
				'direccion' => 'dirección',
				'salon' => 'salón',
				'credito' => 'crédito',
				'calificacion' => 'calificación',
				'informacion' => 'información',
				'interes' => 'interés',
				'epoca' => 'época',
				'ubicacion' => 'ubicación',
				'genero' => 'género',
				'puntuacion' => 'puntuación',
				'calsificacion' => 'clasificación',
				'anyo' => 'año',
				'direccion' => 'dirección',
				'credito' => 'crédito',
				'salon' => 'salón',
				'orientacion' => 'orientación',
				'ultima' => 'última',
				'ultimo' => 'último',
				'actualizacion' => 'actualización',
				'categoria' => 'categoría',
				'clasificacion' => 'clasificación',
				'delegacion' => 'delegación',
				'extension' => 'extensión',
				'movil' => 'móvil',
				'tutorizacion' => 'tutorización',
				'minimos' => 'mínimos',
				'optimos' => 'óptimos',
				'caracteristicas' => 'características',
				'titulo' => 'título',
				'diagnostico' => 'diagnóstico',
				'economica'=>'económica',
				'economico'=>'económico',
				'situacion'=>'situación',
				'retribucion'=>'retribución',
				'basico'=>'básico',
				'cursandolo'=>'cursándolo',
				'formacion'=>'formación',
				'aplicacion'=>'aplicación',
				'ocupacion'=>'ocupación',
				'adaptacion'=>'adaptación',
				'titulacion'=>'titulación',
				'validacion'=>'validación',
				'finalizacion'=>'finalización',
				'proxima'=>'próxima',
				'proximo'=>'próximo',
				'actuacion'=>'actuación',
				'duracion'=>'duración',
				'incorporacion'=>'incorporación',
				'pais'=>'país',
				'vehiculo' => 'vehículo',
				'valoracion'=>'valoración',
				'satisfaccion'=>'satisfacción',
				'poblacion'=>'población',
				'reclamacion'=>'reclamación',
				'organizacion'=>'organización',
				'contratacion'=>'contratación',
				'edicion'=>'edición',
				'audicion'=>'audición',
				'vision'=>'visión',
				'dia'=>'día',
				'dias'=>'días',
				'definicion'=>'definición',
				'ambito'=>'ámbito',
				'analisis'=>'análisis',
				'publicacion'=>'publicación',
				'metodo'=>'método',
				'busqueda'=>'búsqueda',
				'tecnico'=>'técnico',
				'anotacion'=>'anotación',
				'seccion'=>'sección',
				'autonoma'=>'autónoma',
				'ip' => 'IP',
				'cif' => 'CIF',
				'nif' => 'NIF',
				'dni' => 'DNI',
				'razon' => 'razón',
				'prevencion' => 'prevención',
				'insercion' => 'inserción',
				'cotización' => 'cotización',
				'imparticion' => 'impartición',
                'resolucion' => 'resolución'
			);
		}
	}
}
?>
