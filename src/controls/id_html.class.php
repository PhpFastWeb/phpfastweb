<?php
class id_html {
	
	
	

	
	public function __construct($id='') {
		if ($id=='') {
			$id = $this->generate_auto_id();	
		} else {
			if ( self::exists_id($id) ) {
				throw new ExceptionDeveloper('Duplicated ID');
			}
		}
		$this->add_id($id);
	}
	public function __toString() {
		return $this->id;
	}
	public static function exists_id($id) {
		return (in_array($id,self::$ids));
	}
	//PROTECTED --------------------------------------------------
	protected $id = '';
	static protected $ids = array();
	static protected $auto_count = 0;
	protected function add_id($id) {
		self::$ids[] = $id;
	}
	protected function generate_auto_id() {
		$t = self::$auto_count + 1;
		$id = 's5_auto_id_'.$t.'_';
		return $id;
	}
	protected function validate($id) {
		//TODO: validate non-generated id
		return true;
	}
	
}