<?php
abstract class atable_data_selectable extends table_data {
	/**
	 * @return table_data_select_config
	 */
	public function init_select_config() {
		$sc = self::get_select_config ();
		$this->key_set = new key_set ( $sc->primary_keys );
		$this->table_name = $sc->table_name;
		$this->order_default_column = $sc->order_default_column;
		$this->order_default_order = $sc->order_default_order;
	}
	public static function get_select_array_internal($allow_empty = false, table_data_select_config $select_config = null) {
		$sc = $select_config;
		if ($sc == null || count ( $sc->columns_select ) < 1 || count ( $sc->primary_keys ) < 1 || $sc->table_name == '') {
			throw new ExceptionDeveloper ( 'No se han definido algunas propiedades estáticas necesarias' );
		}
		
		$table_name_esc = website::$database->escape_column_name ( $sc->table_name );
		$id_field = reset ( $sc->primary_keys );
		$id_field_esc = website::$database->escape_column_name ( $id_field );
		$order_col_esc = website::$database->escape_column_name ( $sc->order_default_column );
		$order = $sc->order_default_order == 'DESC' ? 'DESC' : 'ASC';
		
		$sql = "SELECT " . $id_field_esc . ", ";
		$sql1 = "";
		foreach ( $sc->columns_select as $field ) {
			$sql1 .= website::$database->escape_column_name ( $field ) . ", ";
		}
		$sql = $sql . substr ( $sql1, 0, - 2 ) . " FROM " . $table_name_esc;
		if ($sc->order_default_column != '') {
			$sql .= " ORDER BY " . $order_col_esc . ' ' . $order;
		}
		die($sql);
		$result = website::$database->execute_get_associative_array ( $sql );
		
		if ($allow_empty) {
			$result =  array ('0' => '')  + $result ;
		}
		return $result;
	}
}
?>