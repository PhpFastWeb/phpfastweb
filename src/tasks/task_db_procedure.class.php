<?

/**
 * Tarea de ejecución de PROCEDURE de base de datos
 */
class task_db_procedure extends task {
	/**
	 * Base de datos sobre la que se ejecutará el procedure
	 * @var database
	 */
	public $db = null;
	public $package = '';
	public $proc_name = '';
	public $parameters = '';
	public $text = '';
	function __construct($db,$package,$proc_name,$parameters,$text='Execute Procedure') {
		$this->db = $db;
		$this->package = $package;
		$this->proc_name = $proc_name;
		$this->parameters = $parameters;
		$this->text = $text;
		parent::__construct();
	}

	function execute() {
		//Comprobamos si la conexión de la base de datos es válida
		//(deja de serlo al serializar y desserializar)
		$ok = true;
		$this->db->conection = false;
		$this->db->db_procedure($this->package,$this->proc_name,$this->parameters);
		//if ($ok) {
			//$numrows = $db->execute_get_value('SELECT SQL%ROWCOUNT FROM DUAL');
		//}
		//var_export($this->parameters); die;
		//test
		
		//usleep(0.7*1000000);
		$this->error = ! $ok;
	}
}

?>