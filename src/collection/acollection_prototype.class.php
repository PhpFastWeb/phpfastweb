<?php
abstract class acollection_prototype implements icollection_prototype {

	/**
	 * @var table_data
	 */
	public $table;
	public function set_table(table_data &$table) {
		$this->table = $table;		
	}
	protected $item_prototype = null;
	public function set_item_prototype(iitem_prototype $item_prototype) {
		$this->item_prototype = $item_prototype;
	}
	
	public function init_config() {
		//Si aun no se han traido los datos, nos los traemos
		if ($this->table->table_data == null)
			$this->table->fetch_data ();
	}
	public function get_render() {
		$this->init_config();
		$this->item_prototype->set_table($this->table);
		$result  = $this->get_header();
		$result .= $this->get_body();
		$result .= $this->get_footer();
		return $result;
	}
	abstract public function get_header();

	abstract public function get_footer();
	
    public $no_data_msg = 'No hay datos';
	function get_body() {
		//Impresion de datos ------------------------------------------------------
		$result = '';
		
		$row = website::$database->fetch_result ( $this->table->table_data );

		if ( ! $row ) $result = "<br />".$this->no_data_msg."<br /><br />";
		while ( $row ) {

			//$row = $this->table->row_add_empty_cols($row);
			$this->table->columns_col->set_values_array ( $row );
			$this->item_prototype->set_row($row);
			$result .= $this->item_prototype->get_render();
			$row = website::$database->fetch_result ( $this->table->table_data );
		}		
		

		return $result;
	}
}
?>