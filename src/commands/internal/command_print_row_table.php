<?php
	class command_print_row_table extends acommand_internal implements icommand_internal {
		public function get_key() {
			return 'print_row_table';
		}
		public function execute() {
			$this->print_row_table();
		}
		function print_row_table($row,$title='') {
			echo $this->get_row_table($row,$title);
		}
		function get_row_table($row,$title='') {
		  $result = "";
		  $pks = $this->key_set->primary_keys;
//		  $title = '';
//		  foreach ($pks as $pk) {
//			$col_tit = "";
//			if (isset($this->columns_title[$pk]) ) {
//				$col_tit = $this->columns_title[$pk]." ";
//			}
//		  	$title .= $col_tit .$row[$pk].", ";
//		  }
//		  $title = substr($title,0,-2);
		  //$title = $this->table->
		  $result .= "<table class=\"data_table\" summary=\"$title\" style=\"width:580px;\">\r\n";
		  $result .= "<thead><tr><td class=\"table_title\" colspan=\"2\">\r\n";
		  $result .= $title."\r\n";
		  $result .= "</td></tr></thead>\r\n";
	
		  $result .= "<tbody>\r\n";
	
		  $par = true;
		  $class[true] = "class=\"odd\"";
		  $class[false] = "class=\"even\"";
	
		  foreach($this->columns as $col_name) {
		  	if ($this->columns_format[$col_name] != "none" && isset($row[$col_name]) && $row[$col_name] != "") {
			  	$result .= "<tr ".$class[$par]."><td class=\"data_cell\" ><b>";
			  	$result .= $this->columns_title[$col_name];
			  	$result .= "</b></td><td class=\"data_cell\" style=\"text-align:left; width:300px; \" >";
			  	if (isset($row[$col_name])) {
			  		$result .= $this->get_formated_column($col_name,$row);
			  	}
			  	$result .= "</td></tr>\r\n";
			  	$par = ! $par;
		  	}
		  }
		  $result .= "</tbody>\r\n";
		  $result .= "</table>\r\n";
		  return $result;
		}
	}
?>