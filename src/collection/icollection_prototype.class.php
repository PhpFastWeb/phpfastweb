<?php
interface icollection_prototype {
	public function set_table(table_data &$table);
	public function set_item_prototype(iitem_prototype $item_prototype);
	public function init_config();
	public function get_render();

}
?>