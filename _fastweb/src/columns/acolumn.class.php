<?php

	abstract class acolumn implements icolumn {

		protected $column_name;
		protected $title = '';
		protected $short_title = null;
        protected $post_text= '';
		protected $column_format;
		protected $value = "";
		protected $formatted_value = '';
		protected $default_value;
        protected $empty_value = '';
		protected $required = false;
		protected $required_html = "*";
		protected $link_html = "*";
		protected $restricted = false;
		protected $restricted_value = null;
		protected $is_primary_key = false;
		protected $is_primary_key_editable = false;
		protected $validates = true;
		protected $validation_messages = array();
		protected $validation_rules = array();
		protected $enforce_validation = true;
		protected $readonly = false;
		protected $help_text = '';
		protected $table = null;
		protected $is_changed = false;
		protected $nullify_empties = false;
		protected $original_column_name = '';
		protected $original_table_name = '';
		/**
		 * When not all columns comes from the same row, instead of defining key_set in table,
		 * each column should have it defined here
		 * @var key_set
		 */
		protected $row_key_set = null;
        
		/**
		 * @param icolumn $col
		 * @return icolumn
		 */
		public static function &cast(icolumn $col) {
			return $col;
		}		
        public function __construct() {
	        $reqhtml = '<a href="#" onmouseover="return overlib(\'Campo obligatorio\', AUTOSTATUS, WRAP, FGCOLOR,\'#FFFFE1\');" onmouseout="nd();" style="cursor:default; text-decoration:none;" onclick="return false;" tabindex="999">';
	        $reqhtml .= '<img src="' . website::$theme->get_img_dir () . '/icon_required.gif" alt="*" align="top" />';
	        $reqhtml .= '</a>';
	        $this->set_required_html($reqhtml);
	        
	        $linkhtml = '<a href="#" onmouseover="return overlib(\'Ciertos valores de este control muestran u ocultan otros\', AUTOSTATUS, WRAP, FGCOLOR,\'#FFFFE1\');" onmouseout="nd();" style="cursor:default; text-decoration:none;" onclick="return false;" tabindex="999">';
			$linkhtml .= '<img src="'.website::$theme->get_img_dir().'/link.png" alt="" style="vertical-align:middle;" />';
			$linkhtml .= '</a>';
			$this->set_link_html($linkhtml);
	        
        }
        public function get_control_render_class() {
            return 'fw_'.get_class($this).'_';
        }
		public function &set_table(table_data &$table) {
			if (is_null($table)) throw new ExceptionDeveloper('Expected non-null table_data');
			$this->table = $table;
			return $this;
		}
		public function get_table() {
			return $this->table;
		}   
        public function &set_help_text($help_text) {
            $this->help_text = $help_text;
            return $this;
        }
        public function get_help_text_html() {
            if ( $this->help_text == '' ) return '';
            //$h = json_encode("'".$this->help_text."'");
            $h = "'".$this->help_text."'";
   	        $result = ' <a href="#" onmouseover="return overlib('.$h.', AUTOSTATUS, WRAP, FGCOLOR,\'#FFFFE1\');" onmouseout="nd();" style="cursor:default; text-decoration:none;" onclick="return false;" tabindex="999">';
	        $result .= '<img src="' . website::$theme->get_img_dir () . '/icon_help.gif" alt="ayuda" align="top" />';
	        $result .= '</a>';
            return $result;
        }
        
        public function &set_original_table_name($original_table_name) {
            $this->original_table_name = $original_table_name;
            return $this;
        }
        public function &get_original_table_name() {
            if ($this->original_table_name !='' ) return $this->original_table_name;
            if ( isset($this->table) ) return $this->table->table_name;
            return '';
        }
    
		public function &set_original_column_name($original_column_name) {
			$this->original_column_name = $original_column_name;
			return $this;
		}
		public function &set_row_key_set(key_set $key_set) {
			$this->row_key_set = $key_set;
			return $this;
		}
		public function get_row_key_set() {
			return $this->row_key_set;
		}
		public function get_html_id() {
			if (!isset($this->row_key_set) || $this->row_key_set == '') {
				return $this->column_name;
			} else {
				$result = '';
				foreach($this->row_key_set->keys_values as $key => $value) {
					$result .= $key.":".$value.":";
				}
				$result .= $this->column_name;
				return $result;
			}
		}
		public function get_key_values_from_html_id($html_id) {
			$tokens = explode(":",$html_id);
			if (count($tokens)%2 == 0) { throw new ExceptionDeveloper('Tokens not odd, las token must be column name'); }
			$result = array();
			for( $i=0 ; $i<(count($tokens)-1) ; $i += 2) {
				$result[$tokens[$i]] = $tokens[$i+1];
			}
			return $result;
		}
		public function get_column_name_from_html_id($html_id) {
			$tokens = explode(":",$html_id);
			return end($tokens);
		}
		
		public function get_original_column_name() {
            if ( $this->original_column_name != '') {
		      return $this->original_column_name;
            }
            return $this->column_name;
		}
		public function get_table_name() {
			if ($this->original_table_name == '') {
				return $this->table->table_name;
			} else {
				return $this->original_table_name;
			}
		}
		public function &set_table_name($original_table_name) {
			$this->original_table_name = $original_table_name;
			return $this;
		}		
		public function &set_column_name($column_name) {
			$this->column_name = $column_name;
			return $this;
		}
		public function get_column_name() {
			return $this->column_name;
		}
		public function &set_title($title) {
			$this->title = $title;
			return $this;
		}
		public function get_title() {
			if ($this->title != '' ) return $this->title;
			else {
				$a = new auto_dictionary();
				return $a->get_auto_col_title($this->column_name);		
			}
		}
		public function &set_short_title($short_title) {
			$this->short_title = $short_title;
			return $this;		
		}
		public function get_short_title() {
			if (is_null($this->short_title) ) {
				return $this->get_title();
			} else {
				return $this->short_title;		
			}
		}
		function &set_post_text($post_text) {
		  	$this->post_text = $post_text;
		  	return $this;
		}

		public static function convert_format_user_to_db($value) {
			return $value;
		}
		public function &set_restricted_value($restricted_value) {
			$this->restricted_value = $restricted_value;
			$this->restricted = true;
			return $this;
		}
        
        /**
         * Returns true if it has some values restricted, or all restricted to one
         * @return bool
         */
		public function is_restricted() {            
			return ( $this->restricted );
		}
        /**
         * Returns true if it has all values restricted to one
         * @return bool 
         */
        public function is_restricted_to_one_value() {
            //If we restrict to multiple values, it must have different treatment
       	    return ( $this->restricted && ! is_array($this->restricted_value) );
        }
        /**
         * Returns true if it can only have one possible value, due to readonly, uneditable pk, o restricted values
         * @return bool
         */
        public function can_only_have_one_value() {
            return ( ( $this->is_primary_key && ! $this->is_primary_key_editable) ||
				    $this->is_restricted_to_one_value() || 
				    $this->readonly );
        }
        
		public function get_restricted_value() {
			return $this->restricted_value;
		}
		public function &set_required($required=true) {
			$this->required = $required;
			return $this;
		}
		public function is_required() {
			return $this->required;
		}
		public function &set_required_html($html) {
			$this->required_html = $html;
			return $this;
		}
		public function &set_link_html($html) {
			$this->link_html = $html;
			return $this;
		}
		public function &set_primary_key($is_primary_key=true) {
			$this->is_primary_key = $is_primary_key;
			return $this;
		}
		public function &set_primary_key_editable($is_primary_key_editable=true) {
			$this->is_primary_key_editable = $is_primary_key_editable;
			return $this;
		}
		public function is_primary_key() {
			return $this->is_primary_key;
		}
		public function is_primary_key_editable() {
			return $this->is_primary_key_editable;
		}
		public function &set_nullify_empties($nullify=true) {
			$this->nullify_empties = $nullify;
			return $this;
		}
		protected $empty_not_zero = false;
		public function &set_empty_not_zero($empty_not_zero=true) {
			$this->empty_not_zero = $empty_not_zero;
			return $this;
		}
		function &set_changed($changed=true) {
			$this->is_changed = $changed;	
			return $this;
		}
        function is_changed() {
            return $this->has_changed();
        }
		function has_changed() {
            if ( $this->is_forced_value && $this->get_value() != $this->forced_value ) return true;
			return $this->is_changed;	
		}
		
			
		
		public function &set_readonly($is_readonly=true) {
			$this->readonly = $is_readonly;
			return $this;
		}
		public function is_readonly() {
			return $this->readonly;
		}
		
		//--
		
        /**
         * Return HTML representation of input control
         * @return string
         */
		public function get_input() {
		  
			$result = '';
            $this->set_value_from_restriction();
            
			if ( false == $this->get_visible() ) {
                //Invisible control
				return $this->get_input_hidden();
			} else if ( $this->can_only_have_one_value() ) {
			     //Visible control not editable
				
				$result .= $this->get_formatted_value();
				$result .= $this->get_input_hidden();
			} else {					
			     //Visible control editable
				$result .= $this->get_input_plain();
				if ( count($this->get_column_binds())>0 ) {
					//TODO: Generalize this
					if ( ! ( $this instanceof column_select ) ) { 
						$result .= $this->link_html;
					} else {
						if ( $this->get_type() == 'select' ) {
							$result .= $this->link_html;				
						}
					}
				}
				if ( $this->is_required() && ! $this->table->columns_required_all ) {
					$result .= $this->required_html;
				}
                $result .= $this->get_help_text_html();
			}
            
            //Previous value for editing PK
			//if ($this->is_primary_key && $this->is_primary_key_editable) {
			//	//$result .= $this->get_input_hidden_prev_value();
			//}
            
            $result .= $this->post_text;
			return $result;
		}
		
		/**
		 * @return icolumn
		 */
		protected function &set_value_from_restriction() {
			if ($this->is_restricted_to_one_value()) $this->value = $this->restricted_value;
			return $this;
		}
		// Abstract Methods
		public abstract function get_input_plain();
		
		// Overridables Methods
		public function &set_formatted_value($value) {
			$this->value = $value;
			return $this;
		}
        
        /**
         * Returns true if passed value is compatible with current restrictions
         * @return bool
         */
        public function is_value_allowed_by_restrictions($value) {
            //TODO: Why we allow empty string values?
            if ( ! $this->is_restricted() || $value == '' ) return true;
            if ( is_array( $this->restricted_value ) ) {
                return in_array( $value, $this->restricted_value );
            } else {
                return $this->restricted_value == $value;
            }
        }
        
		public function &set_value($value) {
            if ( ! $this->is_value_allowed_by_restrictions($value) ) {
                throw new ExceptionDeveloper('**Error: intento de usar valor restringido: '.$value);
            }
			$this->value = $value;
			return $this;
	
		}
		public function reset_validation() {
			$this->validates = true;
			$this->validation_messages = array();
			foreach($this->validation_rules as $rule) {
				$rule->reset();
			}
			
		}
        public function set_validations_already_checked() {
            foreach($this->validation_rules as $validation_rule) {
                $validation_rule->set_already_checked();
                echo $this->column_name." checked<br />";
            }
        }
		public function &set_default_value($default_value) {
			$this->default_value = $default_value;
			return $this;
		}
		public function get_default_value() {
			return $this->default_value;
		}
        public function &set_empty_value($empty_value) {
			$this->empty_value = $empty_value;
			return $this;
		}
		public function get_empty_value() {
			return $this->empty_value;
		}
        
		function &set_value_from_array($array_with_value) {
			if ( ! isset($array_with_value[$this->column_name])) {
				throw new ExceptionDeveloper("Valor de columna ".$this->column_name." no definido en array");
			}
			$this->value = $array_with_value[$this->column_name];
			return $this;
		}
		public function get_value() {
		    
			if ( $this->is_restricted_to_one_value() ) {
                return $this->get_restricted_value();
            }
            
            if ( ! $this->is_value_allowed_by_restrictions($this->value) ) throw new ExceptionDeveloper("Value ".$this->value." restricted");
			//if ( $this->is_forced_value ) return $this->forced_value;
            
			return $this->value;
		}
		
		public function get_formatted_value() {
			return $this->formatted_value;
		}
		
		//--
		
		public function &set_control_state($state) {
			$this->control_state = $state;
			return $this;
		}
		public function get_control_state($state) {
			return $this->control_state;
		}
		public function get_control_render() {
			return $this->get_formatted_value();
			//TODO: Diferentiate this method from control::get_control_render
		}
		
		//--
		
		

		protected $already_validated = false;
		public function validate() {
			
			//TODO: Check when and why it's trying to validate twice
			if ($this->already_validated) {
			     throw new ExceptionDeveloper('Already validated');
                return $this->validates;
            }

            $this->validation_messages = array();
            
            if ( $this->required && ! $this->readonly ) {
            	$found = false;
            	foreach($this->validation_rules as $rule) {
            		if ($rule instanceof validation_not_empty) $found = true;
           		}
           		if ( ! $found ) $this->add_validation_rule( new validation_not_empty() );
           	}
            
			foreach ($this->validation_rules as $rule) {
                //echo get_class($rule)."<br />";
				$rule_ok = $rule->check_column($this);
				if ( ! $rule_ok && $this->enforce_validation) {
					$this->validates = false;
				}
				$this->validation_messages = array_merge( $this->validation_messages, $rule->get_validation_messages() );
			}
			$this->already_validated = true;
			return $this->validates;
		}
		public function get_validation_messages() {
			return $this->validation_messages;
		}
		public function invalidate() {
			$this->validates = false;
		}
		public function add_validation_message($msg) {
			$this->validation_messages[] = $msg;
		}
		public function &set_enforce_validation($enforce=true) {
			$this->enforce_validation = $enforce;
			return $this;
		}
		public function &add_validation_rule(ivalidation_rule $rule) {
			$this->validation_rules[] = $rule;
			return $this;
		}
		public function &remove_all_validation_rules() {
			$this->validation_rules = array();
			return $this;
		}
		
		// Protected methods
		protected function get_input_hidden() {
			$result = "<input type=\"hidden\" name=\"{$this->get_html_id()}\" id=\"{$this->get_html_id()}\" value=\"".$this->get_value()."\" />\r\n";
            return $result;
		}
		public function get_input_hidden_prev_value() {
            $result = "<input type=\"hidden\" name=\"{$this->get_html_id()}_prev_\" id=\"{$this->get_html_id()}_prev_\" value=\"".$this->get_value()."\" />\r\n";
			//$result = "<input type=\"hidden\" name=\"{$this->get_html_id()}_prev_\" id=\"{$this->get_html_id()}_prev_\" value=\"".htmlentities($this->get_formatted_value())."\" />\r\n";
			return $result;
		}
		
		//-----------------------------------------------------------------------------------
		// Binded column
		/**
		 * Array of columns_bind objects
		 */
		protected $binded_columns = array();
		/**
		 * Array of columns_bind objects
		 */		
		protected $binded_from_columns = array();
		protected function &add_binded_column(columns_bind $column_bind) {
			$this->binded_columns[] = $column_bind;
			return $this;
		}
		public function &add_binded_from_column(columns_bind $column_bind) {
			$this->binded_from_columns[] = $column_bind;
			return $this;
		}
		public function is_binded_enabled() {
			if ( count($this->binded_from_columns) == 0 ) return true;
			//We assume bindings use AND composition
			//TODO: Implement a switching mechanism to OR composition accesible to user
			$result = true;
			$cb = reset($this->binded_from_columns);
			while ($result && $cb) {
				$source_col = $this->table->columns_col->get($cb->column_name_source);
				$result = $result && $cb->is_value_to_enable($source_col->get_value());				
				$cb = next($this->binded_from_columns);
			}
			return $result;
		}
		public function get_column_bind($target_column_name) {
			//TODO: Implement this assigning as key to the array the target column name
			//That way we should also implement merging rules when more than one time a target column
			//is referenced.
			foreach($this->binded_columns as $col_bind) {
				if ( $col_bind->column_name_target == $target_column_name ) {
					return $col_bind;
				}
			}
			return false;
		}
		/**
		 * $column_name_or_array accepts an array of columns names or objects, a column name, or a column object
		 */
		public function &bind_to_column($column_name_or_array,$value_to_enable_or_array='',$control_id='') {
			if (is_array($column_name_or_array)) {
				foreach($column_name_or_array as $col_name) {
					if ($col_name instanceof icolumn) 
						$col_name = $col_name->get_html_id();
					$this->bind_to_column($col_name,$value_to_enable_or_array,$control_id);
				}
			} else {
				$column_name = $column_name_or_array;
				if ($column_name instanceof icolumn) { 
					$input_id_target = $column_name->get_html_id();
					$column_name_target = $column_name->get_column_name();
				} else {
					$input_id_target = $column_name;
					$column_name_target = $column_name;
				}				
				if ($control_id == '') {
					$control_id = $input_id_target."_control_";
				}
				
				$col_bind = new columns_bind(
					$this->get_column_name(),
					$this->get_html_id(),
					$input_id_target,
					$value_to_enable_or_array,
					$control_id	
				);
				$this->add_binded_column($col_bind);
				if (isset($this->table)) {
					//TODO: In case no table defined, think of an alternative
					$this->table->columns_col->get($column_name_target)->add_binded_from_column($col_bind);
				}
			}
			return $this;
		}
		public function &bind_to_columns(control_group $control_group, $value_to_enable) {
			$controls = $control_group->get_subcontrols();
			foreach($controls as $control) {
				if ($control instanceof control_simple) {
					$control = control_simple::cast($control);
					$cn = $control->get_column_name();
					//TODO: Definir funciones javascript que eviten invocar repetidas veces
					$this->bind_to_column($cn,$value_to_enable,$control_group->get_id());
				}
			}
			return $this;
		}
		/**
		 * Makes the specified columns be enabled or disabled depending of current column value.
		 * $columns_names_array accepts an array of strings (names) or the columns objects themselves
		 * @param $value_to_enable string
		 * @param $columns_names_array string
		 */
		public function &bind_to_columns_array($value_to_enable='',$columns_names_array) {
			foreach($columns_names_array as $col) {
				$this->bind_to_column($col,$value_to_enable);
			}
			return $this;
		}
		public function get_column_binds() {
			return $this->binded_columns;
		}
		
		public function get_js_onchange() {
			$js = $this->get_js_binded_columns_anim();
			$js .= 'hasChanges(this);';
			if ( $js != '') {
				 $js = ' onchange="'.$js.'" onkeyup="checkOnchangeKeyup(this);" onblur="checkOnchangeKeyup(this);" ';
			}
			return $js;
		}
		protected function get_js_binded_columns_anim() {
			$js='';
			foreach ($this->binded_columns as $bind) {
				$js .= $bind->get_js_anim();
			}
			return $js;
		}
        public function get_js_after_control() {
            return $this->get_js_binded_columns_simple();
        }
		public function get_js_binded_columns_simple() {
			$js='';
			foreach ($this->binded_columns as $bind) {
				$js .= $bind->get_js();
			}
			return $js;
		}
		//--------------------------------------------------------------------
		public function get_db_columns_str() {
			if ( $this->get_original_column_name() == $this->get_column_name() ) {
                $result = new sql_str("{#0}.{#1}",
                        $this->get_table_name(),
        				$this->get_column_name());
			} else {
                $result = new sql_str("{#0}.{#1} AS {#2}",
                        $this->get_table_name(),
                        $this->get_original_column_name(),
        				$this->get_column_name());			 
			}
            //TODO: split this function to one that will be called to build INSERT sql string (that can't use AS)
			return $result->__toString();
		}
		public function get_db_values_str($is_asignation=false) {
            $value = $this->get_value();
            if ( $is_asignation && $this->is_forced_value ) {
			    $value = $this->forced_value;
            }
            
			if ($this->nullify_empties && ($value === 0 || $value === '' || $value === '0')) {
				$result = 'NULL';
			} else if ($this->empty_not_zero && $value === '') {
				$result = 'NULL';
			} else if ( !is_null( $this->empty_value ) && $value === '' ) {
                 $result = new sql_str("'{0}'", $this->empty_value);
            } else {
                 $result = new sql_str("'{0}'", $value );
			}
            
			return $result;
		}
		public function get_db_equal_str() {
			//Already scaped on origin
			$result = $this->get_db_columns_str()." = ".$this->get_db_values_str();
			return $result;
		}
        public function get_db_asignation_str() {
			//Already scaped on origin
			$result = $this->get_db_columns_str()." = ".$this->get_db_values_str(true);
			return $result;
		}
		public function get_db_where_str() {
			$v = $this->get_db_values_str();
			if ($v=='NULL') {
				return 'ISNULL('.$this->get_db_columns_str().')';
			}
			$result = $this->get_db_columns_str() . ' = '.$v;
			return $result;
		}
		
		public function get_db_type() {
			return 'text';
		}
		//-----------------------------------------------------
		protected $visible=true;
		/**
		 * Sets if a column is visible in UI rendering
		 * @param bool $is_visible
		 */
		public function &set_visible($is_visible=true) {
			$this->visible = $is_visible;
			return $this;
		}
		/**
		 * Gets if a column is visible in UI rendering
		 * @param bool $is_visible
		 */
		public function get_visible() {
			return $this->visible;
		}
        public function is_visible() {
            return $this->visible;
        }
		//------------------------------------------------------
		protected $is_forced_value = false;
		protected $forced_value = null;
		public function &set_forced_value($value) {
			$this->forced_value = $value;
			$this->is_forced_value = true;
			return $this;
		}
		public function get_forced_value() {
			return $this->forced_value;
		}        
		//------------------------------------------------------
        protected $persistant_class_name = '';
        public function get_persistant_class_name() {
            if ( $this->persistant_class_name != '' ) return  $this->persistant_class_name;
            if ( substr($this->get_column_name(), 0, 3 ) == 'id_' ) {
                return substr($this->get_column_name(), 3 );
            }
            throw new ExceptionDeveloper('Couldn\'t determine persistant class name');
        }
        protected $cache_persistant_object_array = array();
        public function &get_object() {
            if ( isset( $this->cache_persistant_object_array[$this->get_value()] ) ) {
                return $this->cache_persistant_object_array[$this->get_value()];
            } else {
                //TODO: Test that it inherits apersistant
                $new_object = new $this->get_persistant_class_name();
                $new_object->id = $this->get_value();
                $new_object->load();
                $this->cache_persistant_object_array[$this->get_value()] = $new_object;
            }
        }
        public function &get($property_name) {
            $obj = $this->get_object();
            return $obj->get($property_name);
        }
        
	}

?>