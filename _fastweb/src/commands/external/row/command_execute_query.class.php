<?php

class command_execute_query extends acommand_row implements icommand_row {
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd 
	 * @return command_print
	 */
	protected $sql_field="sql";
	public function get_sql_field() {
		return $this->sql_field;
	}
	public function set_sql_field($sql_field) {
		$this->sql_field = $sql_field;
	}
	public static function &cast(icommand $cmd) {
		if ( ! ($cmd instanceof command_execute_query) ) {
			throw new ExceptionDeveloper("Clase incorrecta");
		}
		return $cmd;
	}
	public function get_name() {
		return "Mostrar informe";
	}
	public function get_key() {
		return "execute_query";
	}
	public function execute() {
	   
		$this->table->key_set->set_keys_values ( $_GET );
		$row = $this->table->fetch_row ( $this->table->key_set );
		$this->table->columns_col->set_formatted_values_array($row);
		$sql = $this->table->columns_col[$this->sql_field]->get_value();        
        $result = website::$database->execute_get_array($sql);        
        html_template::print_table_array($result);	
        
		return;
	}
    //-------------------------------------------------------------------
     //--------------------------------------------------------------------------------------------------------
    protected function send_data($result,$title) {
        if ($result!==null)
        if (isset($_GET['export']) && $_GET['export']=='xls') {
            $this->send_excel($result,$title);
            die;
        } else {
            html_template::print_table_array($result,$title,'left','static_table_data');
            echo "<br />";
            if (isset($this->table->columns_col['leyenda_al_pie'])) {
            	$leyenda = $this->table->columns_col->get('leyenda_al_pie')->get_formatted_value();
            	if ($leyenda != '') echo "<br /><br />".$leyenda."<br /><br />";
            }
            echo "<div style=\"text-align:center;\">";
            echo "[ <a href=\"".html_template::get_php_self()."\">&lt;&lt; Volver a informes &lt;&lt;</a> ] &nbsp;&nbsp;&nbsp;";
            $url = new url();
            $url->set_var('command_','execute_query');
            $url->set_var('id',$this->table->key_set->keys_values['id']);
            echo "[ <a href=\"".$url->__toString()."\"> Cambiar parámetros</a> ] &nbsp;&nbsp;&nbsp;";
            if ( is_array($result) && count($result) > 0) {
            	$url->set_var('export','xls');
            	$url->set_var('subcommand_','run_query');
            	echo "[ <a target=\"_blank\" href=\"".$url->__toString()."/".urlencode($this->table->table_title).".xls";
            	echo "\">Exportar a XLS</a> ]";
            }
            echo "</div>";
            echo "<br />";
        }
    }
    protected function excel_coord($x,$y) {
        return $this->excel_colum($x).$y;
    }
    protected static $excel_column_letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    protected function excel_colum($x) {
        $x--;
        $mod = (strlen(self::$excel_column_letters)-1);
        $pre = "";
        $n = $x % $mod;
        $d = $x - $n;
        if ($d>0) {
            //$d = $d % $mod;
            $pre = "A";
        }
        return $pre.substr(self::$excel_column_letters,$n,1);
    }
    
    private function str_equals_to($str1,$str2) {
    	$i = 0;
    	if ($str1 == '' || $str2 == '') return 0;
    	$eq = ( $str1[$i] == $str2[$i] );
    	while($eq) {
    		$i++;
    		$eq = ( $str1[$i] == $str2[$i] );
    	}
    	return $i-1;
    }
    
    //--------------------------------------------------------------------------------------------------------------
    
    protected function send_excel($result, $title) {
    	
        $short_title = utf8_encode(strtr(
			substr($title,0,31), 
			array('*'=>'_', ':'=>'_', '/'=>'_', '\\'=>'_', '?'=>'_', '['=>'_', ']'=>'_')));
			        
        $file_title = util::clean_str_filename($title);
                
        // include PHPExcel
        require(dirname(__FILE__).'/../../../../../_ext/phpexcel/1.7.6/Classes/PHPExcel.php');
        // create new PHPExcel object
        $objPHPExcel = new PHPExcel;
        // set default font
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
        //Define current and number format. 
        $currencyFormat = '#.#0,## \€;[Red]-#.#0,## \€'; //currency format, &euro; with < 0 being in red color
        $numberFormat = '#.#0,##;[Red]-#.#0,##'; //number format, with thousands seperator and two decimal points.
        
        // writer will create the first sheet for us, let's get it
        $objSheet = $objPHPExcel->getActiveSheet();
        $objSheet->setTitle($short_title);
                  
        //Headers
        $first_row = reset($result);
        $headers = array_keys($first_row);
        for ($x = 1 ; $x <= count($headers) ; $x++ ) {
            $cell = $this->excel_coord($x,3);
            $objSheet->getCell($cell)->setValue(utf8_encode($headers[$x-1]));
        }
        
        //Combine shared title cells
        $cab_yf = 2;
        
        $uncombined = array();
        for ($i = 1 ; $i <= count($headers) ; $i++ ) {
        	$uncombined[$i] = true;
       	}
        for ($i = 1 ; $i <= count($headers)-1 ; $i++ ) { 
        	
			$j = $i + 1;
			$width = 0;
        	$cini = $c = $this->str_equals_to($headers[$i-1],$headers[$j-1]);
        	while ( $c > 0 ) {
        		$uncombined[$i] = false;
        		$uncombined[$j] = false;
        		$width++;     				
         		$cell = $this->excel_coord($j,3);
            	$objSheet->getCell($cell)->setValue(utf8_encode(substr($headers[$j-1],$c+1)));
       			$j++;
      			$c = $this->str_equals_to($headers[$i-1],$headers[$j-1]);
			}
			if ( $width > 0 ) {
			    
				$cab_yf = 3;
      			
                $cell = $this->excel_coord($i,3);
            	$objSheet->getCell($cell)->setValue(utf8_encode(substr($headers[$i-1],$cini+1)));  				
				$cell = $this->excel_coord($i,2);
            	$objSheet->getCell($cell)->setValue(utf8_encode(substr($headers[$i-1],0,$cini)));
        		$cellf = $this->excel_coord($i+$width,2);
        		$objSheet->mergeCells($cell.':'.$cellf);                
			}
  		}
  		
        
  		if (true && $cab_yf == 3) {
  			foreach($uncombined as $i => $value) {
  				if ($value) {
   					$cell = $this->excel_coord($i,2);
   					$cellf = $this->excel_coord($i,3);
   					$objSheet->getCell($cell)->setValue($objSheet->getCell($cellf)->getValue());
   					//echo $i."-";
				}
        		$objSheet->mergeCells($cell.':'.$cellf);
  			}
  		} 		
  
  		// Title       
  		$y = 4 - $cab_yf;
        $objSheet->getCell("A$y")->setValue(utf8_encode($title));
        $cab_xf = count($first_row);
        $cellCabEnd = $this->excel_coord(count($first_row),$y);
        $objSheet->mergeCells("A$y:".$cellCabEnd);
         
        // write contents
        $y = 3 +  1; reset($result);
        foreach ($result as $row) {
            $x = 1;
            foreach($row as $value) {
                $cell = $this->excel_coord($x,$y);
                $objSheet->getCell($cell)->setValue(utf8_encode(trim($value)));
                if ( is_numeric($value) || trim($value)=='0' ) {
                    $objSheet->getStyle($cell)->getNumberFormat()->setFormatCode('0');
                }
                $x++;
            }
            $y++;
        }

        //Styles
        //echo $cab_yf;
        $yini = 4 - $cab_yf;
        $xf = count($row);
        if ($cab_yf == 2) {
            $yf = count($result) + $cab_yf + 1;
        } else {
            $yf = count($result) + $cab_yf;
        }
        $cell1 = $this->excel_coord($xf,$yf);
        $cell1b = $this->excel_coord($xf,3);
        $cell2 = $this->excel_coord($xf,$yf);

		$objSheet->getStyle("A$yini:".$cell1b)->getFont()->setBold(true)->setSize(10);
		$range = "A$yini:".$this->excel_coord($cab_xf,3); 
        $objPHPExcel->getActiveSheet()->getStyle($range)->getFill()->applyFromArray(
		    	array( 'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	                   'startcolor' => array('rgb' => 'E9E9E9'))
		);		
        
        $objSheet->getStyle("A$yini:A$yini")->getFont()->setBold(true)->setSize(12);
        
        // set number and currency format to columns
        //$objSheet->getStyle('B2:B5')->getNumberFormat()->setFormatCode($numberFormat);
        //$objSheet->getStyle('C2:D5')->getNumberFormat()->setFormatCode($currencyFormat);
  
        // first, create the whole grid around the table
        $objSheet->getStyle("A$yini:".$cell2)->getBorders()->
            getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            
        // create medium border around the table
        $objSheet->getStyle("A$yini:".$cell2)->getBorders()->
            getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        
        // autosize the columns
        for ($i = 1 ; $i < $xf; $i++) {
            $c = $this->excel_colum($i);
            $objSheet->getColumnDimension($c)->setAutoSize(true);
        }   
  
		if (true) {
			if (headers_sent()) throw new ExceptionDeveloper("Headers sent");
	        
	        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	        //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007"); 		        
	
	
	        //ob_clean(); // Cancelamos el contenido actual a enviar al navegador
	
			//if ( eregi("(msie) ([0-9]{1,2}.[0-9]{1,3})", $_SERVER['HTTP_USER_AGENT'])){
			//	Header("Content-type: application/force-download");
			//} else {
			//	Header("Content-Type: application/octet-stream");
			//}
	        
	        header("Pragma: public"); // required 
    		header("Expires: 0"); 
    		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
    		header("Cache-Control: private",false); // required for certain browsers 
    		header("Content-type: application/vnd.ms-excel");
    		header("Content-Disposition: attachment; filename=\"".$file_title.".xls\"") ; 
    		header("Content-Transfer-Encoding: binary"); 
    		//header("Content-Length: ".$fsize); 
    		ob_clean(); 
    		flush();  
           	website::set_developer_mode(false); //cancelamos que se muestren warnings al generar caracteres UTF8 inválidos
			$objWriter->save('php://output');
			flush(); //To speed up file save dialog
        	return;
        }
        
       // website::set_developer_mode(false); //cancelamos que se muestren warnings al generar caracteres UTF8 inválidos
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
        $writer->writeAllSheets();
        $writer->setUseInlineCSS(true);
    	$writer->save('php://output');

    }
    
    protected function check_sql_string($sql) {
        //Eliminamos ciertas palabras clave por seguridad
        $sql_orig = $sql;
        $sql = str_ireplace('DROP','',$sql);
        $sql = str_ireplace('TRUNCATE','',$sql);
        $sql = str_ireplace('UPDATE','',$sql);
        $sql = str_ireplace('SET','',$sql);
        $sql = str_ireplace('SHOW','',$sql);
        $sql = str_ireplace('INSERT','',$sql);
        $sql = str_ireplace('DELETE','',$sql);
        $sql = str_ireplace('SET','',$sql);
        $sql = str_ireplace('ALTER','',$sql);
        $sql = str_ireplace('LOAD','',$sql);
        
        if ($sql_orig != $sql ) {
            echo "<b>ERROR AL EJECUTAR LA CONSULTA</b><br /><br />";
            echo "La consulta contiene una o más palabras reservadas.<br /><br />";
            echo "No se permite la ejecución de consultas que pueda alterar la información en la base de datos.<br /><br />";  
            return false;
        }
        return $sql;
    }
    protected function execute_get_array($sql) {
        $sql = $this->check_sql_string($sql);
        if ($sql === false) {
            return null;
        }
        $e = null;
        try {
            $result = website::$database->execute_get_array($sql);
        } catch(ExceptionDeveloper $e) {
            $result = false;
            if (website::in_developer_mode()) {
                //throw($e);
            }            
        }
        if ($e != null ) {
            echo "<b>ERROR AL EJECUTAR LA CONSULTA</b><br /><br />";
            echo "La consulta contiene un error y no se ha podido ejecutar.<br /><br />";
            echo "[ <a href=\"".html_template::get_php_self()."\">&lt;&lt; Volver &lt;&lt;</a> ]<br /><br />";            
            if (website::$user->is_in_any_group(array('administrador'))) {
                echo "<hr /><b>INFORMACIÓN VISIBLE PARA EL ADMINISTRADOR</b><br /><br />";
                echo "Revise el código SQL a ejecutar directamente en un cliente de base de datos, y corríjala. ";
                echo ' &nbsp;[ &nbsp;<a href="'.html_template::get_php_self().'?command_=edit&id='.$this->table->columns_col->get('id')->get_value().'"><b>Editar consulta</b></a> &nbsp;]<br /><br />';
                
                echo "Error informado por la base de datos:<br />";
                $error = website::$database->get_last_error();
                $pos=strpos($error,'at line');
                if ($pos !== false ) {
                    $error = substr($error,0,$pos).'<b style="font-family:sans-serif;">'.substr($error,$pos).'</b>';   
                }
                
                echo "<pre style=\"color:black; white-space:pre-wrap; position:relative; border:1px solid gray; width:712px; overflow:\">".$error."</pre><br />";
                echo "SQL ejecutado (tras aplicar filtros y desgloses):<br /><br />";
                echo "<div style=\"float:left; border:1px solid gray; padding:0 0 0 0px; background-color: #f7f7f7;\"><pre style=\"white-space:pre-wrap; position:relative; border:none; width:720px; margin:0 0 0 0;background-color:transparent; padding:5px 5px 5px 5px;\">";
                $sql = str_replace("\r","",$sql);
                $sqla = explode("\n",$sql);
                $i = 1;
                foreach ($sqla as $line) {
                    echo "<div style=\"float:left; text-align:right; width:24px; background-color: #f7f7f7; padding: 0 3px 0 0;\">$i</div><div style=\"padding:0 0 0 10px; float:left; width:683px; background-color:white; color:black;\">";
                    echo $line;
                    echo " </div><br style=\"clear:both;\" />";
                    $i++;
                }               
                echo "</pre></div>";
            }
            echo "<br style=\"clear:all\" />";
            $result = null; 
            die;
        }
        return $result;
        
    }
    //--------------------------------------------------------------------------------
    protected $time_values = false;
    protected function init_base_array($array_to_add,&$base_array=null, $added_keys) {
        if ( $base_array==null ) $base_array = array();

        if ($array_to_add==null || count($array_to_add)==0) return $base_array; 
        
        $zero = '0';
        
        foreach($array_to_add as $row) {
            $final = count($row); $new_key = ""; 
            //Creamos la clave
            $j = 1; 
            foreach($row as $key => $value) {
                if ( $j != $final ) $new_key .= $value." : ";
                else if ( $zero=='0' ) {
                    if (preg_match('/[0-9][0-9]:[0-9][0-9]:[0-9][0-9]/',$value)) {
                      $zero = '00:00:00';  
                      $this->time_values = true;
                    } 
                }
                $j++;
            }
            $j = 1;
            //Añadimos valores de fila, sin valor final
            foreach($row as $key => $value) {                        
                if ( $j != $final ) $base_array[$new_key][$key]=$value;
                else {
                    foreach ($added_keys as $ak) {
                        $base_array[$new_key][$ak]=" $zero ";
                    }
                }
                $j++;
            }
        }
        
        return $base_array;
    }
    protected function add_to_base_array(&$new_array,&$base_array, $added_key) {
        $final = count(reset($new_array));
        foreach ($new_array as $row) {      
            $j=1; $new_key = '';
            foreach($row as $key => $value) {
                if ( $j != $final ) {
                    $new_key .= $value." : "; 
                } else {
                    $base_array[$new_key][$added_key] = $value;
                }
                $j++;
            }
        }
        return $base_array;
    }
    
    protected function sum($a,$b) {
        if ($this->time_values) {
            //return "($a + $b)";
            $ta = 0; $tb = 0;
            if ($a != '00:00:00' && preg_match('/[0-9][0-9]:[0-9][0-9]:[0-9][0-9]/',$a)) {
                list($h, $m, $s) = explode (":", $a);
                $ta = (intval($h) * 3600) + (intval($m) * 60) + (intval($s));
            }
            if ($b != '00:00:00' && preg_match('/[0-9][0-9]:[0-9][0-9]:[0-9][0-9]/',$b)) {
       	        list($h, $m, $s) = explode (":", $b);
                $tb = (intval($h) * 3600) + (intval($m) * 60) + (intval($s));
            }
            $tt = ($ta + $tb);
            
            $result = sprintf("%02d:%02d:%02d",floor($tt/3600), ( floor($tt/60) % 60 ),($tt%60));
            //return "($a + $b) = $result";
            return $result;
        } else {
            return ($a + $b);
        }
    }
    protected function add_sum_to_base_array(&$base_array, $count_base_columns, $sum_each_pair = false) {
        $first_row = reset($base_array);
        // Preparamos la cabecera si hay que sumar por cada género
        if ($sum_each_pair) {
        	$pair_cols = array(); $num_pair_cols = 0;
        	$new_first_row = array(); $i = 1; $j = 1;
        	$num_pair_cols_title = array();
        	foreach($first_row as $key => $value ) {
        		$new_first_row[$key] = $value;
	            if ($i > $count_base_columns) {
					if ( $j == 1 ) $key_prev = $key;
					if ( $j == 2 ) {
	            		$num_pair_cols++; //nueva columna total pareja
	            		$c = $this->str_equals_to($key,$key_prev);
	            		if ($c>0) $num_pair_cols_title[$num_pair_cols] = substr($key,0,$c)." Subtotal";
	            		else $num_pair_cols_title[$num_pair_cols] = "Subtotal";
						$new_first_row[$num_pair_cols_title[$num_pair_cols]] = ' 0 ';
						$j = 0;
	            	}
	            	$j++;
				}		
				$i++;	
            }
			$first_row = $new_first_row; 
        }
        
        //echo "<pre>"; var_dump($first_row); die;
        //die ("*".$count_base_columns);
        $row_sums = array(); //Preparamos la última fila con las sumas
        
        $i=1;
        foreach ($first_row as $key => $value) {
            if ($i < $count_base_columns) {
                $row_sums[$key]='';
            } else if ($i == $count_base_columns) {
                //$row_sums[$key]='<div style="text-align:right;">Totales:</div>';
                $row_sums[$key]='Totales:';
            } else {
                $row_sums[$key]=0;
            }
            $i++;
        } 
		//echo "<pre>"; var_dump($row_sums); die;
        //Añadimos a los datos las columnas para sumar por parejas
        
		$new_base_array = array(); 
        foreach($base_array as $index => $row) {
        	$gdh = 0; $dgm = 0;
			$num_pair_cols = 0; $i = 1; $j = 1;
			$sum_row = 0;
            foreach($row as $key => $value) {	            	
                $new_base_array[$index][$key] = $value;
				if ( $i > $count_base_columns ) {
					$sum_row = $this->sum($value,$sum_row);
					$row_sums[$key] = $this->sum($row_sums[$key] , $value);
					if ($sum_each_pair) {
						if ( $j == 1 ) $gdh = $value;
						if ( $j == 2 ) {
							$gdm = $value;
	            			$num_pair_cols++; //nueva columna total pareja
							$new_base_array[$index][$num_pair_cols_title[$num_pair_cols]] = $this->sum($gdh , $gdm );
							$row_sums[$num_pair_cols_title[$num_pair_cols]] = $this->sum($row_sums[$num_pair_cols_title[$num_pair_cols]] , $new_base_array[$index][$num_pair_cols_title[$num_pair_cols]]);
							$j = 0;
	            		}
	            		$j++;
            		}
            		
            	}
            	if ($i==count($row)) {
            		$new_base_array[$index]['Totales'] = $sum_row; //La última es el total
            	}
        		$i++;
    		}
  		}
  		$base_array = $new_base_array;
        
        

        //Sumamos el total global
        $sum = 0;
        foreach($base_array as $index => $row) {
	        $sum = $this->sum($sum  , $new_base_array[$index]['Totales']);
		}
        $row_sums['Totales'] = $sum;
        
        //Añadimos la última fila
        $base_array['Totales'] = $row_sums;
        return $base_array;
        
    }
    protected function mix_arrays($associative_arrays_label_keys,$sum_each_pair = false) {

        $labels = array_keys($associative_arrays_label_keys);
        $result = array();
        //Inicializamos a 0 todos los resultados
        foreach ($associative_arrays_label_keys as $arr) {
            $result = $this->init_base_array($arr,$result,$labels);
        }
        //Añadimos cada resultado particular
        foreach ($associative_arrays_label_keys as $label => $arr) {
              $result = $this->add_to_base_array($arr,$result,$label);
        }
        
        //Sumamos si más de un array
        if ( count($associative_arrays_label_keys) > 1 ) {
            //Buscamos el primer array no vacio
            $row1 = false;
            foreach ($associative_arrays_label_keys as $arr) {
                if (is_array($arr) && count($arr)>0) {
                    $row1 = reset($arr);
                    if (count($row1)>0) break;
                }
            }
            
            //Añadimos totales si no vacio
            if ( $row1 !== false ) {
            	$result = $this->add_sum_to_base_array($result,count($row1)-1,$sum_each_pair);
           	}
        }
  
        return $result;
    }
    //----------------------------------------------------------------------------------------
    protected function add_to_where($sql, $sql_where,$conector = 'AND') {
        if ($sql_where=='') return $sql;
        $sql_where = str_ireplace('where','',$sql_where);
        $sql_where = "( $sql_where )";
        $post = array(
            "GROUP", 
            "HAVING",
            "ORDER",
            "LIMIT",
            "UNION"
        );
        $pos_where = strrpos(strtolower($sql),'where'); //buscamos el último where
        if ( $pos_where !== false ) {
            //Ya tiene where
            foreach ($post as $keyword) {
                $pos_post = strrpos(strtolower($sql),strtolower($keyword)); //buscamos el último keyword
                if ($pos_post !== false) break;
            }
            
            if ( ! $pos_post || $pos_post < $pos_where ) {
                //No hay nada detrás del último where existente
                $sql_where_orig = substr($sql, $pos_where + strlen('where'));
                $sql_where_orig = "( $sql_where_orig )";
                $result = ( substr($sql, 0, $pos_where)."WHERE $sql_where $conector $sql_where_orig" );
                return $result;                
            } else {
                //Hay algo detrás del where
                $sql_where_orig = substr($sql, $pos_where + strlen('where'), $pos_post-strlen($sql));
               
                $sql_where_orig = "( $sql_where_orig )";
                $result = substr($sql, 0, $pos_where)."WHERE $sql_where $conector $sql_where_orig".substr($sql, $pos_post);
                return $result;
            }
        } else {
            //No tiene where
            foreach ($post as $keyword) {
                //Buscamos el último post-where
                $pos_post = strrpos(strtolower($sql),strtolower($keyword));
                if ($pos_post !== false) {
                    //Hemos localizado antes de qué debe ir el where
                    $result = substr($sql, 0, $pos_post).' WHERE '.$sql_where.' '.substr($sql, $pos_post);
                    //$result = str_ireplace(strtolower($keyword), 'WHERE '.$sql_where.' '.substr($sql, $pos_post),$sql);
                    return $result;
                }
            }
            //Llegados a aquí, no se encuentra nada que deba ir tras el where
            $result =  $sql. ' WHERE '.$sql_where;
        }
        return $result;
    }
    
    protected function retrieve_row() {
    		$this->table->key_set->set_keys_values ( $_GET );
    		$row = $this->table->fetch_row ( $this->table->key_set );
    		$this->table->columns_col->set_formatted_values_array($row);
    }
}

?>