<?php
interface iitem_prototype {
	
	/**
	 * Set table data configuration
	 * @param table_data $table
	 */
	function set_table(table_data &$table);
	
	/**
	 * Sets the row data and odd or even info
	 * @param array $row
	 * @param bool $odd
	 */
	function set_row ($row, $odd=true);
	
	function get_render();
}