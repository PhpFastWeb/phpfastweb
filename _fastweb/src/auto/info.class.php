<?php
class info {
	static function set_auto_options($table_data) {
		$option = null;
		foreach($table_data->columns as $col_name) {
			if ( isset($this->auto_col[$col_name]) ) {
				$options = self::auto_options($col_name);
				if ( ! is_null($options) ) {
					$table_data->columns_options[$col_name] = $options;
				}
			}
		}
	}
	static function auto_option($col_name) {
		$c = strtolower($col_name);
		switch($c) {
			case 'provincia_andalucia':
				return self::get_provincias_andalucia();
				break;
			case 'sexo':
				return self::get_sexo();
				break;
			case 'genero':
				return self::get_genero();
				break;
			default:
				return null;
				break;
		}
		
	}
	static function get_provincias_andalucia($index=null) {
		$result = array('','Almería','Cádiz','Córdoba','Granada','Huelva','Jaén','Málaga','Sevilla');
		if (is_null($index)) {
			return ($result);
		} else {
			return ($result[$index]);
		}
	
	}
	static function get_genero() {
		return array('Masculino','Femenino');
	}
	static function get_sexo() {
		return array('Hombre','Mujer');
	}
    static function get_provincias_espana_from_andalucia($index_andalucia) {
        $result = array(
            0=>0, //'',
            1=>5, //'Almería',
            2=>12, //'Cádiz',
            3=>17, //'Córdoba',
            4=>21, //'Granada',
            5=>24, //'Huelva'
            6=>27, //'Jaén',
            7=>34, //'Málaga',
            8=>43  //'Sevilla'
        );
        return $result[$index_andalucia];
    }
	static function get_provincias_espana($allow_empty=false) {
		$provincias = array(
			'2' =>'&Aacute;lava',
			'3' =>'Albacete',
			'4' =>'Alicante/Alacant',
			'5' =>'Almer&iacute;a',
			'6' =>'Asturias',
			'7' =>'&Aacute;vila',
			'8' =>'Badajoz',
			'9' =>'Barcelona',
			'10' =>'Burgos',
			'11' =>'C&aacute;ceres',
			'12' =>'C&aacute;diz',
			'13' =>'Cantabria',
			'14' =>'Castell&oacute;n/Castell&oacute;',
			'15' =>'Ceuta',
			'16' =>'Ciudad Real',
			'17' =>'C&oacute;rdoba',
			'18' =>'Cuenca',
			'19' =>'Girona',
			'20' =>'Las Palmas',
			'21' =>'Granada',
			'22' =>'Guadalajara',
			'23' =>'Guip&uacute;zcoa',
			'24' =>'Huelva',
			'25' =>'Huesca',
			'26' =>'Illes Balears',
			'27' =>'Ja&eacute;n',
			'28' =>'La Coru&ntilde;a',
			'29' =>'La Rioja',
			'30' =>'Le&oacute;n',
			'31' =>'Lleida',
			'32' =>'Lugo',
			'33' =>'Madrid',
			'34' =>'M&aacute;laga',
			'35' =>'Melilla',
			'36' =>'Murcia',
			'37' =>'Navarra',
			'38' =>'Ourense',
			'39' =>'Palencia',
			'40' =>'Pontevedra',
			'41' =>'Salamanca',
			'42' =>'Santa Cruz de Tenerife', //antes 46			
			'43' =>'Segovia', 	//antes 42
			'44' =>'Sevilla', 	//antes 43
			'45' =>'Soria',   	//antes 44
			'46' =>'Tarragona', //antes 45			
			'47' =>'Teruel', 
			'48' =>'Toledo',
			'49' =>'Valencia/Val&eacute;ncia',
			'50' =>'Valladolid',
			'51' =>'Vizcaya',
			'52' =>'Zamora',
			'53' =>'Zaragoza'
		);
		if ($allow_empty) {
			$provincias = (array(''=>'') + $provincias);
		}
		return $provincias;
	}
}