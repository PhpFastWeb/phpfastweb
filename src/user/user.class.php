<?php
class user {
    
	/** User data that is retrieved from database
     * @var array 
     */
	public $user_data = array();
    
	/** If true, each login and logout is logged to database
     * @var bool
    */
	public $log_access = false;	

	/** Table where user data is stored in database
     * @var string
    */
	public $user_table = 'fw_users';
    
	/** Mapping of internal field name to table field name: internal_name => nameombre_en_tabla */
    
	public $user_table_columns = array(
        'id'        => 'id',             //auto inc primary key
		'username' 	=> 'username',      //unike Username
		'password'  => 'password',
		'group' 	=> 'group',    //Stores comma separated group names
		'name' 	    => 'name',     //Real name of user
		'surname'   => 'surname',
		'email' 	=> 'email',
		'phone' 	=> 'phone',
        'verified' => 'verified'
	);
	/** Array of groups that the user belongs to
     * @var array
    */
	public $groups = array();
    
	/**
    * Cookie identifier (must be different for each site installation, specially on same domain).
    * If not set, it will be defaulted to a string constructed from website::$base_url.
    * @var string
    */
	public $cookie_username = "";
    
	/* URLs in case of login or logut actions */
	public $loginout_success_url = '';
    
    /** 
     * URL to redirect in case of successful login.
     * If not set, will default to website::$base_url
     * @var string
     */ 
	public $login_success_url = '';
    
    /**
     * URL to redirect in case of failure login or logout, or session expired.
     * If not set, will default to website::$base_url.'/fw-login.php'
     */ 
	public $loginout_failure_url = '';

    /** Timeout in seconds of normal login session
     * @var int	
     */
	public $max_seconds_session = 3600;
    /** Timeout in seconds of "remember be" option with cookie
     * @var int	
     */    
	public $max_seconds_remember = 31536000; //1 year = 60*60*24*365 = 31536000
    
    /**Timeout in seconds of cookie in general
     * @var int	
     */        
	public $max_seconds_cookie = 31536000; //we do not rely on cookie expire but internal session variable
	
  	/** Last action executed
     * @var string
     */
	private $action_last_action = '';
    
    /** If last action was succesfuk
     * @var bool
     */
	private $action_success = true;
    
	/** True if user is logged in succesfully
     * @var bool
     */
	public $logged_in = false;
	
    /** True if object initialised, will not run init_config in that case
     * @var bool
     */
	public $initialised = false;

    /** Object to represet user interface of "user" object
     * @var user_ui
     */
    public $user_ui = null;
    
    /** Cache of requests of user information when they are queried more than once on a single page run
     * @var array
     */
    private $cached_users = array();
    
    /** If user is loaded, we do not try to load it again
     * @var bool
     */
    protected $loaded = false;
    
	// ----------------------------------------------------------------------------------
    // Constructor, init_config and load methods
	// ----------------------------------------------------------------------------------

    /**
     * Constructor. If cookie_username property is not set, it's defaulted to SERVER_NAME
     */    
    public function __construct() {
        if ( $this->cookie_username == '' ) {
            $this->cookie_username = 'user__'.str_ireplace('/','_',website::$base_url);   
        }
        if ( $this->login_success_url == '' ) {
            $this->login_success_url = website::$base_url;
        }
        if ( $this->loginout_failure_url == '' ) {
            $this->loginout_failure_url = website::$base_url.'/fw-login.php';
        }
    }
    
	/**
     * User data is loaded, or user is logged in, or is logged out
	 * In case of succesful action, redirects to result url
	 * @return bool true if user logged in ..id
	 */
	public function load() {
        if ( $this->loaded ) return;
        $this->loaded = true;
        website::$database->init_config();
        if ( ! isset( $_SESSION ) ) session_start();
	
        //We check if this is an active login or logout
        $this->action_success = false; //We start with no success by default
        
        if ( $this->is_action() )  {
			$this->process_action();
			$this->init_config();
			$this->log_access();            
			if ($this->action_success) {
            	$this->jump_success();
            } else {
           	    $this->jump_failure();
            }
		} else {
            //This is no active login or logout, we try to load from session
			$this->action_last_action = 'load';
			$this->load_from_session();	
			$this->init_config();
		}
		
		return $this->logged_in;
	}  
     
    /**
     * If intialised property is false, object is initialised with defaults.
     * user::load() must be called prior to this
     */
    function init_config() {
		if ($this->initialised) return;
		$this->initialised = true;
        
        if ( $this->user_ui == null ) {
            $this->user_ui = new user_ui();
            $this->user_ui->user = $this;
        }
        
        //Beyond this point, we need to be logged in to continue object initialisation
		if ( ! $this->logged_in ) return;		
         
		$this->groups = explode(',',$this->user_data['group']);
		for ( $i=0 ; $i<count($this->groups) ; $i++ ) {
			$this->groups[$i] = trim($this->groups[$i]);
		}

	}
    //----------------------------------------------------------------------------------
    // Authentication methods
    //----------------------------------------------------------------------------------
    
	private function load_from_session() {
		//Comprobamos a través de la sesión
		if ( ! isset($_SESSION) ) @session_start();
		$this->action_success = true;

		if ( ! isset($_SESSION[$this->cookie_username]) ) {
            //We assume that if session exists, it's because a successful login
            $this->logged_in = false;
        } else {
            //We check for session expired
			if ( isset($_SESSION[$this->cookie_username]['session_time']) ) {
				if ($_SESSION[$this->cookie_username]['session_time'] < time() ) {
				    //Session has expired
					$this->logged_in = false;
					$this->jump_session_expire();
					return;
				}
			} 

            //Session only exists if login was successful once
			$this->logged_in = true;
            
            //We load user data from database
			$result = $this->load_user_data($_SESSION[$this->cookie_username]['username']);
                       
            if ( ! $result ) throw new ExceptionDeveloper();
            else {
                foreach ( $this->cached_users[$_SESSION[$this->cookie_username]['username']] as $key => $value ) {
                    $this->user_data[$key] = $value;
                }
            }
            
            //We refresh a new time limit
            $this->store_user_data_to_session();
		}
	}
    
	//--------------------------------------------------------------------
    
	protected function process_action() {
		$this->action_last_action = '';
		$this->action_success = false;	
		
		if (isset($_POST['command_']) && $_POST['command_']=="login") {
		  
			$this->process_action_login();

		} else if  ( (isset($_POST['command_']) && $_POST['command_']=='logout') ||
					 (isset($_GET['command_']) && $_GET['command_']=='logout') ) {
			$this->process_action_logout();
		}
	}

    /**
     * Loads user data from database and stores it in cache ::data
     * If user is not found, false is returned.
     * If password is specified, it is compared vs value stored, and if it doesn't match, false is returned.
     * If user found, and password matches, or is not specified, true is returned.
     * @param string $username user identifier to look for
     * @param string $password password to match
     * @param string $include_password_in_chache true if password is to be stored
     */
	protected function load_user_data( $username, $password=null, $include_password_in_cache=false ) {
        //$this->reset_cookie();
        //$this->reset_session();exit;
        assert::is_set($username);
        
        if ( empty($password) && isset( $this->cached_users[$username] ) ) {
            //User already loaded, we exit
            return true;
        }
        
        // Start of SQL query
  		$sql = "SELECT  ";
		
        // Query all fields specified on ::user_table_columns
        foreach ($this->user_table_columns as $inner_key => $table_col ) {
            
            $sql_str = new sql_str( '{#0},', $table_col );
			$sql .= $sql_str->__toString();
		}
        $sql = substr($sql,0,-1);
        
        // Table from where to ask for data, and user to ask for
		$sql_str = new sql_str(" FROM {#0} WHERE {#1}='{2}'",$this->user_table, $this->get_username_field_name(), $username);
        $sql .= $sql_str->__toString();
        
        // We retrieve the information
		$result = website::$database->execute_query($sql);
		$data_db = website::$database->fetch_result($result);
        
        // If a password was specified, we only keep the information if it matches
		if ( isset($password) && (
                ! isset( $data_db[$this->get_password_field_name()] ) || 
                $data_db[$this->get_password_field_name()] != $password 
                ) ) { 
            //Passwords don't match
            return false;
		}	      
        
        // Passwords match, or it was a petition without them.
        
        // We keep the data information
        foreach ($this->user_table_columns as $inner_key => $table_col ) {
            if ( isset($data_db[$table_col]) && ( $inner_key != 'password' || $include_password_in_cache ) ) { 
                $this->cached_users[$username][$inner_key] = $data_db[$table_col];
            }
		}
        return true;
    }

	/**
	 * Checks autentication information with database. In case this is valid, session and cookie are set according,
     * and information is intialised on current object.
	 * @param string $user user identifier to authenticate
	 * @param string $pass password of user
	 * @param string $compare_crypt true if password are crypted on database
	 * @return bool
	 */
    private function check_auth($user, $pass , $compare_crypt = false, $renew_session_expire = true) {
        if ( empty($pass) ) return false;
        
        $pass_to_compare = $pass;
        if ($compare_crypt) $pass_to_compare = crypt($pass);
        //TODO: Add more crrypt schemas
        
        $auth_valid = $this->load_user_data($user, $pass);

        if ( isset($this->user_table_columns['verified'] ) ) {
            //var_dump($this->user_data);
            //die( $this->get('verified') );
            $s = new sql_str ( "SELECT verified FROM fw_users WHERE username = '{@0}'", $user );
            $result = website::$database->execute_get_simple_value($s);
            if ( ! $result ) $auth_valid = false; 
            
        }
        
        if ( $auth_valid ) {
            //We copy user information to main properties
            $this->logged_in = true;   
            //TODO: Think of the best way for changing username or password from DB and session at the same time   
            foreach ( $this->cached_users[$user] as $key => $value ) {
                $this->user_data[$key] = $value;
            }
            
            $this->store_user_data_to_session();
            return true;
        } else {
            //We reset all information
            $this->logged_in = false;
            $this->reset_session();
            $this->reset_cookie();
            return false;
        }
    }
    
    private function reset_session() {
        if ( isset($_SESSION[$this->cookie_username]) )
            unset($_SESSION[$this->cookie_username]);
    }
    private function reset_cookie() {
        setcookie( $this->cookie_username .'__username','' );
        setcookie( $this->cookie_username.'__username','',time() - 3600);
        
    }
    private function store_user_data_to_session() {
        
        $_SESSION[$this->cookie_username]['username'] = $this->user_data['username'];
        $_SESSION[$this->cookie_username]['session_time'] = time() + $this->max_seconds_session;
 
    }
    private function store_cookie() {
        setcookie( $this->cookie_username .'__username',$this->user_data['username']);
		//setcookie( $this->cookie_username.'_'.$user_field, $_POST[$user_field], time()+$this->max_seconds_cookie );				
		//setcookie( $this->cookie_username.'_'.$pass_field, crypt($_POST[$pass_field]), time()+$this->max_seconds_cookie );
    }
    
	private function process_action_login() {
		$this->action_last_action = 'login';
		$this->action_success = $this->check_auth( $_POST['username'], $_POST['password'] );
		return $this->action_success;
	}
    
	private function process_action_logout() {
        $this->action_last_action = 'logout';
        $this->reset_cookie();
        $this->reset_session();
		return $this->action_success = true;
	}
	
	//----------------------------------------------------------------------------------
    // Auxiliary non-public methods
    //----------------------------------------------------------------------------------
    private function log_access() {
		if ($this->log_access && $this->action_success) {								
			$log = new log_entrie();
			$log->command = $this->action_last_action; 
			$log->user = $this->get_id();
			$log->persist();
		}
    }
	private function is_action() {
		$command_word = 'command_';
		return ( (isset($_POST[$command_word]) && ($_POST[$command_word] =='login' || $_POST[$command_word] == 'logout' ) )
				 ||
				 (isset($_GET[$command_word])) && ($_GET[$command_word] == 'logout') );
		
	}	
    
	//----------------------------------------------------------------------
	// Jumping functions
    //----------------------------------------------------------------------
	
	private function jump_success() {
		if ( ! $this->action_success ) return;
        
        if ( isset($_POST['return_url']) ) {
            $target = $_POST['return_url'];
            html_template::redirect($target,'Realizando redirección de usuario...');  			
 			exit;
        }
        
        $target = new url($this->loginout_success_url);
        //If $this->loginout_success_url=='' that defaults to html_template::get_php_self();
        
		if ( $this->action_last_action == 'login' && $this->login_success_url != '' ) {
			$target = new url( $this->login_success_url );
		} 
        if ( $this->action_last_action == 'logout' ) {
            $target->set_var('command_','logout_success');            
        }
        html_template::redirect($target,'Realizando redirección de usuario...');  			
 		exit;        
	}
    
	private function jump_failure() {
		if ( ! $this->action_success ) {
      		$target = ( $this->loginout_failure_url != '' ) ? $this->loginout_failure_url : html_template::get_php_self();
      		$target .= url::$url_separator_starter.'wrong_login';
      		$target .= url::$url_separator.'return_url=';
      		$target .= url::get_request_url();
      		
			html_template::redirect($target,'Accediendo al sistema de usuarios...');  
 			exit;
		}
	}
    
	private function jump_session_expire() {
		//We erase sessión to prevent infinity loop
        $this->reset_session();
        $target = new url( $this->loginout_failure_url );
        $target->set_var('return_url', url::get_request_url() );
        $target->set_var('command_','session_expired');
        html_template::redirect($target, 'Su sesión ha expirado');
        exit;
	}
    
    //--------------------------------------------------------------------------
    // Auxiliary methods, getters and setters
    //--------------------------------------------------------------------------
    
    /**
     * Returns true if one of the groups the user belongs to is this one
     * @param string $groups
     */
    public function is_in_group($group) {
		$this->load();
		if (in_array($group,$this->groups)) {
			return true;
		}
		return false;
	}
    
    /**
     * Returns true if the user belongs to at least one of the groups
     * @param array $array_of_groups
     */
	function is_in_any_group($array_of_groups) {
	   assert::is_array($array_of_groups);
	   if ( is_array($array_of_groups) && $this->logged_in && count($this->groups) > 0 &&
			 array_intersect($array_of_groups,$this->groups) != null &&
			 array_intersect($array_of_groups,$this->groups) > 0
			 ) {
	       return true;
	   }
	   return false;
	}
    
	function generatePassword ($length = 8) {
		// start with a blank password
		$password = "";
		// define possible characters
		$possible = "0123456789bcdfghjkmnpqrstvwxyz";
		// set up a counter
		$i = 0;
		// add random characters to $password until $length is reached
		while ($i < $length) {
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			// we don't want this character if it's already in the password
			if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			}
		}
		// done!
		return $password;
	}
    
    public function &set_loginout_failure_url($url_string) {
		$this->loginout_failure_url = $url_string;
		return $this;
	}
	public function &set_session_expire_url($url_string) {
		$this->session_expire_url = $url_string;
		return $this;
	}
	public function &set_loginout_success_url($url_string) {
		$this->loginout_success_url = $url_string;
		return $this;
	}
    public function get_username_field_name() {
        return $this->user_table_columns['username'];
    }
    public function get_password_field_name() {
        return $this->user_table_columns['password'];
    }
    
	public function is_logged_in() {
		return $this->logged_in;
	}
    
    public function get($field_name) {
        //We try to guess if we are asked for some predefinied fields
        if ($field_name == 'username' ) return $this->get_username();
        if ( isset( $this->user_data[$field_name] ) )
            return $this->user_data[$field_name];
        return null;
    }
    
   	public function get_id() {
		if ( ! $this->is_logged_in() ) return '';
		return $this->user_data['id'];
	}
	public function get_language() {
		if ( ! $this->is_logged_in() ) return '';
		return $this->user_data['language'];
	}
    public function get_username() {
        if ( ! $this->is_logged_in() ) return '';
        return $this->user_data['username'];
    }
    
	public function get_full_name() {
        $result = '';
        if ( isset( $this->user_data['name'] ) ) $result .= $this->user_data['name'];
        if ( $result != "" ) $result .= " ";
        if ( isset( $this->user_data['surname'] ) ) $result .= $this->user_data['surname'];
		return trim($result);
	}
    
    /**
     * Returns a comma separated list of groups the user belongs to
     */
    public function get_groups_list() {
        if ( ! $this->is_logged_in() ) return '';
        $result = '';
        foreach ($this->groups as $g) {
            $result .= $g.", ";
        }
        if ($result != '') $result = substr($result, 0,-2);
        return $result;
    }
    
    /**
     * Array of names of group assiciated with each key name
     */
    public $group_names = array();
    
   /**
     * Returns a comma separated list of group names the user belongs to
     */    
    public function get_group_names_list() {
        if ( ! $this->is_logged_in() ) return '';
        $result = '';
        foreach ($this->groups as $g) {
            if (isset($this->group_names[$g])) {
                $result .= $this->group_names[$g].", ";
            } else {
                $result .= $g.", ";
            }
            
        }
        if ($result != '') $result = substr($result, 0,-2);
        return $result;
    }   
    //------------------------------------------------------------------------------
    
    public function print_login_logut_page() {
        $this->init_config();
        $this->user_ui->print_login_logut_page();
    }
    
}

