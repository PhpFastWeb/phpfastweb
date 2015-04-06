<?php

//TODO: Test this static class
class array_util {
    
	//----------------------------------------------------------------------------------------
	//-- Funciones para procesamiento avanzado de datos en arrays
	//----------------------------------------------------------------------------------------
    /**
     * Return elements that are not in either array
     */
	static public function array_diff_final($arr1, $arr2) {
		//Leer más: http://emilio.aesinformatica.com/2009/04/28/comparar-arrays-en-php/#ixzz0kwFSdSih
		$result = array ();
		foreach ( $arr1 as $word1 ) {
			if (! (in_array ( $word1, $arr2 )))
				$result [] = $word1;
		}
		foreach ( $arr2 as $word2 ) {
			if (! (in_array ( $word2, $arr1 )))
				$result [] = $word2;
		}
		return $result;
	}
    
    /**
     * Merges array1 with elements from array2 that doesn't have same keys
     */
	static public function array_merge_unique_values($array1,$array2) {
		$result = $array1;
		foreach($array2 as $value) {
			if ( ! in_array($value,$result) ) {
				$result[] = $value;
			}
		}
		return $result;
	}	
  	static public function array_sum_cols_row(&$row,$num_row,$num_rows_skip,$res='') {
  		static $row_temp = null;
  		if ( $res=='reset' ) {
  			$row_temp=array();
  			return;
  		}
  		if ( $res=='result' ) return $row_temp;
  		$i = 0;
  		foreach($row as $key => $value ) {
  			if ( empty($row_temp[$key]) ) $row_temp[$key] = '';
  			if ( $i >= $num_rows_skip ) {
  				$row_temp[$key] += $row[$key];
  			}
  			$i++;
  		}
		return;  		 
  	}
  	
  	//-----------
  	
  	static public function array_add_acumulated( &$array, $key_orig, $key_sum ) {
  		$sum = 0;
  		foreach ($array as $num_row => $row ) {
  			$sum += $row[$key_orig];
  			//reconstruimos la fila en el orden correcto
  			$new_row = array();
  			foreach ( $row as $key=>$value) {
  				$new_row[$key]=$value;
  				if ($key == $key_orig) {
  					$new_row[$key_sum] = $sum;
  				}
  			}
  			//sustituimos por la que tiene la suma
  			$array[$num_row] = $new_row;
  		}
  	}
  	
  	//---------
  	
  	static public function array_format_row(&$row,$num_row,$format) {
  		foreach($row as $key => $value ) {
  			$row[$key] = sprintf($format,$value);
  		}
  	}
  	static public function array_format( &$array, $format ) {
  		array_walk($array,'array_util::array_format_row',$format);
  	}
  	
  	//---------
  	
  	static public function array_format_number_row(&$row,$num_row,$num_dec,$command='',$data=0) {
  		static $skip_cols = 0;
  		if ( $command == 'set_skip_cols' ) { $skip_cols = $data; return; }
  		$i = 0;
  		foreach($row as $key => $value ) {
  			if ( $i >= $skip_cols ) {
  				if (is_numeric($value)) {
	  				$row[$key] =number_format($value,$num_dec,',','.');
  				}
  			}
  			$i++;
  		}
  	}
  	
  	static public function array_format_number( &$array, $num_dec=0,$skip_cols=0) {
  		self::array_format_number_row($array,'','','set_skip_cols',$skip_cols);
  		array_walk($array,'array_util::array_format_number_row',$num_dec);
  	}    
    
    
    
    
}