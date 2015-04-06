<?php

abstract class item_abstract {
	/**
	 * @var data_set
	 */
	public $data_set = null;
	
	public function get_key_set() {
		return $this->data_set->data_source->key_set;
	}
}