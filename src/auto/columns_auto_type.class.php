<?php

class columns_auto_type {
	private $types;
	function __construct() {
		$this->types = array(
			'fecha' => 'date',
			'fichero' => 'file',
			'fichero1' => 'file',
			'fichero2' => 'file',
			'fichero3' => 'file',
			'fichero4' => 'file',
			'fichero5' => 'file',
			'adjunto' => 'file',
			'adjunto1' => 'file',
			'adjunto2' => 'file',
			'adjunto3' => 'file',
			'adjunto4' => 'file',
			'adjunto5' => 'file',			
			'imagen' => 'image',
			'imagen1' => 'image',
			'imagen2' => 'image',
			'imagen3' => 'image',
			'imagen4' => 'image',
			'imagen5' => 'image',
			'foto' => 'image',
			'foto1' => 'image',
			'foto2' => 'image',
			'foto3' => 'image',
			'foto4' => 'image',
			'foto5' => 'image',
			'direccion' => 'textarea',
			'texto' => 'textarea',
			'texto_corto' => 'textarea',
			'texto_largo' => 'textarea',
			'resumen' => 'textarea',
			'descripcion' => 'textarea',
			'descripcion_corta'=>'textarea',
			'descripcion_larga'=>'textarea',
			'domicilio' => 'textarea'

		);
	}
	function set_all_columns_types($table) {
		//$table->init_config();
		foreach ($table->columns as $column) {
			$this->set_column_type($table,$column);			
		}
	
	}
	function set_column_type($table, $column_name) {

		$column_name_lowercase = strtolower($column_name);
		if (!isset($table->columns_format[$column_name])) {
			if (isset($this->types[$column_name_lowercase])) {
				$table->columns_format[$column_name] = $this->types[$column_name_lowercase];
			} else if ( strpos($column_name_lowercase,'fecha') !== FALSE ) {
				$table->columns_format[$column_name] = 'date';
			} else if ( strpos($column_name_lowercase,'fichero') !== FALSE ) {
				$table->columns_format[$column_name] = 'file';
			} else if ( strpos($column_name_lowercase,'_texto') !== FALSE ) {
				$table->columns_format[$column_name] = 'textarea';
			} else {
				$res = info::auto_option($column_name);
				if ( $res ) $table->columns_format[$column_name] = $res;
			}
		}
	}
}