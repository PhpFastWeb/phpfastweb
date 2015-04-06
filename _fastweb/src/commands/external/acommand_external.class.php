<?php
abstract class acommand_external extends acommand implements icommand_external {
	
	
	/**
	 * Cast for the sake of intellisense
	 * @param icommand $cmd
	 * @return icommand_external
	 */
	public static function &cast(icommand $cmd) {
		if ( ! ($cmd instanceof icommand_external) ) {
			throw new ExceptionDeveloper("Clase incorrecta");
		}
		return $cmd;
	}
	public function get_execute_onclick() {
		return "window.location='".$this->get_execute_url()."';";
	}
	public function get_execute_url() {
		$url = new url();
		$url->set_var ( $this->get_command_label (), $this->get_key () );
        $url->set_access_key($this->access_key);
		return $url;
	}
    public function get_execute_link() {
		$url = $this->get_execute_url();
		return $url->get_a_link( $this->get_name () );
	}
	protected $name = '';
	public function set_name($name) {
		$this->name = $name;
	}
	public function get_name() {
		if ($this->name=='') throw new ExceptionDeveloper('Command name not defined');
		return $this->name;
	}
    
    protected $commands = array();
    public function add_command(icommand_external $command) {
        $this->commands[$command->get_key()] = $command;
    }
    public function get_command($index) {
        return $this->commands[$index];    
    }
 	protected $access_key = '';
	public function set_access_key($access_key) {
		$this->access_key = $access_key;
	}   
	public function get_access_key() {
		return $this->access_key;
	} 
}
?>