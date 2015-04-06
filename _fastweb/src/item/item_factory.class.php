<?php


class item_factory {
	public $prototype = null;
	
	function create($data) {
		//--
		if ( $this->prototype == null ) $this->prototype = new item();
		//--
		$result = clone($this->prototype);
		$result->data = $data;
		return $result;
	}
	
	
}



?>