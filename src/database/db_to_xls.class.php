<?php
/*
Esta clase es una modificación de...

	apgDB2xls 1.1a (from Browser)
	By: Alvaro Prieto (apg88)
	E-Mail: webmaster@apg88.com
	Web Site: http://www.apg88.com/index.php?page=apgDB2xls
	
	Licensed under the Creative Commons Attribution 2.5 License - http://creativecommons.org/licenses/by/2.5/legalcode
	http://creativecommons.org/licenses/by/2.5/ (Summary)
	You can modify the code all you want, make money with it, release newer versions, etc...  just leave my name, email, and web site URL on the it.

*/
class db_to_xls {
	function send_xls($db,$query_result,$file_name='tabla.xls',$table_title=null,$add_keys=true) {	
		// Here we tell the browser that this is an excel file.
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$file_name");
		header("Pragma: no-cache");
		header("Expires: 0");
		if ( ! empty($table_title) ) {
			echo $table_title . "\n";		// Put the name of the table on the First line of the Excel file.
		}

		//$query_string = "select * from $tablename";
		//$result_id = mysql_query($query_string, $connection);
		//$column_count = mysql_num_fields($result_id);
		$data = '';
		$add_keys = true;
		$row = $db->fetch_result($query_result);
		while ( $row ) {
			// The folowing if/then statement prints out the column names.
			if ( $add_keys ) {
				$keys = array_keys($row);
				foreach($keys as $key){
					$data .= $key . "\t";
				}
				$data = substr($data,0,-1);
				$data .= "\n";
				$add_keys = false;
				$row = $db->fetch_result($query_result);
			}


			$lbChar=" ";
			foreach ($row as $key => $value ) {
				//Sustituimos las nuevas lineas por espacio
				$row[$key] = str_replace("\n",$lbChar,$row[$key]);
				//Sustituimos 
				//$row[$key] = preg_replace('/([\r\n])/e',"ord('$1')==10?'':''",$row[$key]);
				//Eliminamo las barras simples
				$row[$key] = str_replace("\\","",$row[$key]);
				$data .= "$row[$key]\t";				
			}
			$data = substr($data,0,-1);
			$data .= "\n";
		}
		echo $data;
		die;
	}
}
?>