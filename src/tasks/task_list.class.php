<?php
require_once dirname(__FILE__).'/task.class.php';
class task_list {
	public $list;
	function add_task(task $task) {
	//function add_task(task $task) {
		$this->list[] = $task;
	}
	function print_list_details() {
		echo $this->get_list_details();
	}
	function get_list_details() {
		$result = '';
		foreach($this->list as $task ) {
			$result .= $task->get_text();
		}
		return $result;		
	}
	function execute() {
		$result = '';
		foreach($this->list as $task ) {
			$result .= $task->execute();
		}
		return $result;
	}
	//---
	public $stats = array();
	function refresh_stats() {
		$this->stats = array();
		$this->stats['total']     = 0;
		$this->stats['created']   = 0;
		$this->stats['running']   = 0;
		$this->stats['finished']  = 0;
		$this->stats['error']     = 0;
		$this->stats['non_error'] = 0;
		foreach( $this->list as $task ) {
			$this->stats['total']++;
			switch($task->state) {
				case 'created':
					$this->stats['created']++;
				break;
				case 'running':
					$this->stats['running']++;
				break;
				case 'finished':
					$this->stats['finished']++;
					if ( $task->error ) {
						$this->stats['error']++;
					} else {
						$this->stats['non_error']++;
					}
				break;
			}
		}
	}
	function get_status_resume() {
		$this->refresh_stats();
		$perc = 0;
		if ($this->stats['total']>0) {
			$perc = 100 * $this->stats['finished'] / $this->stats['total'];
			$result = 'Completado: '.floor($perc).' %';
		}
		return $result;
	}
	function get_list_completed() {
		$result = '';
		//Recorremos la lista al reves, para que al mostrarla aparezcan primero las últimas
		//tareas procesadas
		for ($i=count($this->list)-1; $i >=0 ; $i-- ) {
			if ($this->list[$i]->state=='finished') {
				if ($this->list[$i]->error ) {
					$result .= '[x] ';
				} else {
					$result .= '[v] ';
				}
				$result .= $this->list[$i]->text;
			}
		}
		return $result;
	}
	function get_first_index() {
		return 0;
	}
	function get_next_index() {
		
	}
	function has_undone_tasks() {
		$this->refresh_stats();
		if ($this->stats['total'] > 0 && $this->stats['created'] > 0 ) {
			return true;
		} else { 
			return false; 
		}
	}
	function execute_index($index) {
		$task = $this->list[$index];
		$result = $task->execute_command();
		return $result;
	}
}
?>