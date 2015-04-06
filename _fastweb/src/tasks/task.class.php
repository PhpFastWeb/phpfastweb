<?php
class task {
	public $error = false;
	public $error_num = 0;
	public $text = 'Tarea vacia.';
	public $state = 'empty'; //posible states: empty, created, running, finishe
	
	function execute_command() {
		$this->state='running';
		$this->execute();
		$this->state='finished';
	}
	function execute() {
		return '';
	}
	function __construct() {
		$this->state = 'created';
	}
	function get_text() {
		return $this->text;
	}
}
?>