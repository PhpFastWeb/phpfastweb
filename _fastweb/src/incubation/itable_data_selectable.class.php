<?php
interface itable_data_selectable {
	/**
	 * @return table_data_select_config
	 */
	public function init_select_config(table_data_select_config $sc);
	/**
	 * 
	 * @param bool $allow_empty
	 * @param table_data_select_config $select_config
	 * @return array
	 */
	public static function get_select_array_internal($allow_empty = false, 
		table_data_select_config $select_config = null);
		
	/**
	 * 
	 * @param bool $allow_empty
	 * @return array
	 */
	public static function get_select_array($allow_empty = false);
}
?>