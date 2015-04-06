<?php
class command_collection extends acommand_external implements icommand_external {
	
	//Definiciones adicionales
	/**
	 * @var columns_collection
	 */
	protected $columns_col;
	
	
	/**
	 * 
	 * @var iitem_prototype
	 */
	protected $item_prototype = null;
	public function set_item_prototype(iitem_prototype $item_prototype) {
		$this->item_prototype = $item_prototype;
	}
	/**
	 * Returns an instance to the item prototype
	 * @var iitem_prototype
	 */
	public function &get_item_prototype() {
		return $this->item_prototype;
	}
	/**
	 * 
	 * @var icollection_prototype
	 */
	protected $collection_prototype = null;
	public function set_collection_prototype(icollection_prototype $collection_prototype) {
		$this->collection_prototype = $collection_prototype;
	}	
	public function get_collection_prototype() {
	   return $this->collection_prototype;
	}
	private $html_table_numcols;
	
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return command_table
	 */
	public static function &cast(icommand $cmd) {
		if (! ($cmd instanceof command_table)) {
			throw new ExceptionDeveloper ( "Clase incorrecta" );
		}
		return $cmd;
	}

	public function get_name() {
		return "Ver listado";
	}
	public function get_key() {
		return "collection";
	}
	public function execute() {

		$this->table->init_config ();
		
		if ($this->collection_prototype == null) $this->collection_prototype = new collection_prototype_basic();
		if ($this->item_prototype == null) $this->item_prototype = new item_prototype_basic();
		//TODO: Cambiar esto para definirlo sobre esta instancia de objeto
		//$this->columns = $this->table->columns_table_view;
		if (is_null ( $this->table->columns_table_view ) || ! is_array ( $this->table->columns_table_view ) || count ( $this->table->columns_table_view ) == 0) {
			$source = $this->table->columns_col;
		} else {
			$source = new columns_collection ($this->table);
			foreach ( $this->table->columns_table_view as $col_name ) {
				if ( ! $this->table->columns_col->get ( $col_name ) instanceof column_hidden &&
					$this->table->columns_col->get ( $col_name )->get_visible()	) {
					$source->append ( $this->table->columns_col->get ( $col_name ) );
				}
			}
		}
		
		$this->columns_col = $source;
		
		$this->collection_prototype->set_table($this->table);
		$this->collection_prototype->set_item_prototype($this->item_prototype);
		
		echo $this->collection_prototype->get_render();

	}

}
?>