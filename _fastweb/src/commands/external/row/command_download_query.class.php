<?php

class command_download_query extends acommand_row implements icommand_row {
	
	
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return command_print
	 */
	public static function &cast(icommand $cmd) {
		if ( ! ($cmd instanceof command_print) ) {
			throw new ExceptionDeveloper("Clase incorrecta");
		}
		return $cmd;
	}
	
	public function execute() {
		$this->print_print ();
	}
	public function get_name() {
		return "Ver";
	}
	public function get_key() {
		return "print";
	}
	function print_print() {
		$this->table->key_set->set_keys_values ( $_GET );
		$row = $this->table->fetch_row ( $this->table->key_set );
		$this->table->columns_col->set_formatted_values_array($row);
		
		$result = "";
		$result .= "<div class=\"data_table_outline\">\r\n";
		//$result .= "<table align=\"center\"><tr><td>\r\n";
		//$result .= "<div style=\"font-family: sans-serif; font-size: 12px; margin-bottom:5px;\"><b>{$this->table->table_title}</b></div>";
		$result .= $this->get_row_table ( $row );

		$result .=  "<br />";
		$result .=  "<div style=\"text-align:center\">";
		
		$result .=  "<input type=\"button\" class=\"no_print\" onclick=\"history.go(-1);\" value=\"<<< Atr&aacute;s <<<\" />\r\n";
		
		if ($this->table->use_print_record) {
			$result .= "<input type=\"button\" class=\"no_print\" onclick=\"window.print()\" value=\"    Imprimir    \" />\r\n";
		}
		$result .= "</div>";
		//$result .= "</td></tr></table>\r\n";
		$result .= "</div>\r\n";
		
		echo $result;
	}
	public function get_row_table($row,$title='') {
	  $result = "";
	  if ($title == '') {
		  $title = $this->table->get_record_title();
	  }
	  $result .= "<table class=\"data_table\" summary=\"$title\" style=\"width:580px;\">\r\n";
	  $result .= "<thead><tr><td class=\"table_title\" colspan=\"2\">\r\n";
	  $result .= $title."\r\n";
	  $result .= "</td></tr></thead>\r\n";

	  $result .= "<tbody>\r\n";

	  $par = true;
	  $class[true] = "class=\"odd\"";
	  $class[false] = "class=\"even\"";

	  $this->table->columns_col->set_formatted_values_array($row);
	  foreach($this->table->columns_col as $col) {
	  	if ( ! ($col instanceof column_hidden) && $col->get_formatted_value() != "") {
	  		$col = acolumn::cast($col);
		  	$result .= "<tr ".$class[$par]."><td class=\"data_cell\" ><b>";
		  	$result .= $col->get_title();
		  	$result .= "</b></td><td class=\"data_cell\" style=\"text-align:left; width:300px; \" >";
		  	$result .= $col->get_formatted_value();
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