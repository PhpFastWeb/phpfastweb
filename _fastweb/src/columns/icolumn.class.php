<?php 

interface icolumn {
	/**
	 * 
	 * @param string $column_name 
	 * @return icolumn
	 */
	function &set_column_name($column_name);
	/**
	 * @return string
	 */
	function get_column_name();
	
	/**
	 * When not all columns comes from the same row, instead of defining key_set in table,
	 * each column should have it defined here
	 * @param key_set $key_set pk of the corresponding row for this column
	 * @return icolumn
	 */
	public function &set_row_key_set(key_set $key_set);
	/**
	 * @return key_set
	 */
	public function get_row_key_set();
	
	/**
	 * 
	 * @param string $title
	 * @return icolumn
	 */
	function &set_title($title);
	/**
	 * 
	 * @return string
	 */
	function get_title();
	/**
	 * @param string $value
	 */
	/**
	 * Sets the short title to use for the control where 
     * text space is reduced (tables, lists of controls)
	 * @param string $title
	 * @return icolumn
	 */
	function &set_short_title($title);
	/**
	 * Gets the short version of the control title. If not set, it just returns the title 
	 * @return string
	 */
	function get_short_title();
	/**
	 * Text in html format to render after the input reder
	 * @return string
	 * @return icolumn
	 */    
    function &set_post_text($post_text);
        
	/**
	 * @param string $value
	 * @return icolumn
	 */	 
	function &set_value($value);

	/**
     * Sets what value is filled in control when displaying new element form
	 * @param string $default_value
	 * @return icolumn
	 */
	function &set_default_value($default_value);    
	/**
	 * @return string
	 */
	function get_default_value();
	/**
     * Sets what value is inserted in database in case an empty string '' (not null) is found for that column in form.
     * Useful for substituing unmarked radio button controls for a certain value.
	 * @param string $empty_value
	 * @return icolumn
	 */
	function &set_empty_value($empty_value);
	/**
	 * @return string
	 */
	function get_empty_value();
    
	/**
	 * @return icolumn
	 */
	function &set_value_from_array($array_with_value);
	/**
	 * 
	 * @param string $value
	 */
	public function get_value();
	/**
	 * 
	 * @param string $formatted_value
	 * @return icolumn
	 */
	function &set_formatted_value($formatted_value);
	
	/**
	 * Returns the inner value of the column formatted for humans
	 * @return string
	 */
	function get_formatted_value();
	
	/**
	 * Returns the complex HTML representation of the inner value
	 * formatted for human.
	 */
	function get_control_render(); 
    
    /**
     * Returns class name to be applied to container DIV for this kind of column/control
     */
	function get_control_render_class();
    
	/**
	 * @return string
	 */
	static function convert_format_user_to_db($value);
	/**
	 * @return string
	 */
	function get_input();
	
	/**
	 * @return string
	 */
	function get_input_hidden_prev_value();
	/**
	 * Value for filtering queries and to be allways written to new records
	 * @param string $restricted_value
	 * @return icolumn
	 */
	function &set_restricted_value($restricted_value);
    /**
     * Returns true if it has some values restricted, or all restricted to one
     * @return bool
     */
	function is_restricted();
    
    /**
     * Returns true if it has all values restricted to one
     * @return bool 
     */
	function is_restricted_to_one_value();
    /**
     * Returns true if it can only have one possible value, due to readonly, uneditable pk, o restricted values
     * @return bool
     */
    function can_only_have_one_value();
    /**
     * Returns true if passed value is compatible with current restrictions
     * @return bool
     */
    function is_value_allowed_by_restrictions($value);    
    
	/**
	 * 
	 * @param boolean $required
	 * @return icolumn
	 */
	function &set_required($is_required=true);
	/**
	 * 
	 * @param string $html
	 * @return icolumn
	 */
	function &set_required_html($html);
	
	/**
	 * @return bool
	 */
	function is_required();
	/**
	 * 
	 * @param boolean $is_primary_key
	 * @return icolumn
	 */
	function &set_primary_key($is_primary_key=true);
	/**
	 * 
	 * @param boolean $is_primary_key_editable
	 * @return icolumn
	 */
	function &set_primary_key_editable($is_primary_key_editable=true);
	
	/**
	 * 
	 * @param bool $is_readonly
	 * @return icolumn
	 */
	function &set_readonly($is_readonly=true);
	/**
	 * @return bool
	 */
	function is_readonly();
	
	/**
	 * @return boolean
	 */
	function validate();
	/**
	 * @return array
	 */
	function get_validation_messages();
	
	/**
	 * 
	 */
	public function invalidate();
	/**
	 * 
	 * @param $msg
	 */
	public function add_validation_message($msg);
	/**
	 * 
	 * @param $enforce
	 * @return icolumn
	 */
	function &set_enforce_validation($enforce=true);
	/**
	 * 
	 */
	public function reset_validation();
	/**
	 * 
	 * @param $rule
	 * @return icolumn
	 */
	function &add_validation_rule(ivalidation_rule $rule);
	
	/**
	 * @return icolumn
	 */
	function &remove_all_validation_rules();
	
	/**
	 * Sets a reference to parent table_data
	 * @param table_data &$table
	 * @return icolumn
	 */
	function &set_table(table_data &$table);
	/**
	 * @return table_data
	 */
	function get_table();
	
	/**
	 * Returns the name of the table for columns which value is not stored
	 * in the main table.
	 * @return string
	 */	
	public function get_table_name();
	/**
	 * Sets the name of the table for columns which value is not stored
	 * in the main table. 
	 * @param string $table_name
	 * @return icolumn reference to self
	 */	
	public function &set_table_name($table_name);
    
    /**
	 * Sets table name in database from original table when the column uses a different one than
	 * the one defined in table_data object
	 * @param string $original_table_name name of the original table
	 * @return icolumn reference to self
	 */	
	public function &set_original_table_name($original_table_name);

    /**
	 * Gets table name in database from original table when the column uses a different one than
	 * the one defined in table_data object.
	 * @return string name of the original table, or current table
	 */	
	public function &get_original_table_name();

	/**
	 * Sets column name from original table when the column uses a different
	 * alias in current table.
	 * @param string $alias
	 * @return icolumn reference to self
	 */	
	public function &set_original_column_name($original_column_name);
    	
	/**
	 * Sets column name from original table when the column uses a different
	 * alias in current table
	 * @return string name of original column, or current column
	 */	
	public function get_original_column_name();
	
	

			
	/**
	 * 
	 * @param $changed boolean
	 * @return icolumn
	 */
	function &set_changed($changed=true);
	
	/**
	 * @return boolean
	 */
	function has_changed();
		
	
	
	// Binded columns-------------------------------------------------------
    /**
     * Returns Javascript code to be used on onchange events
     * @return string
     */
	function get_js_onchange();
    /**
     * Returns Javascript code to be used on simple binded columns
     * @return string
     */
	function get_js_binded_columns_simple();
    /**
     * Returns true if binded is enabled
     * @return bool
     */
	function is_binded_enabled();
	
	/**
     * Binds other columns to be shown on some values of this one
     * @param $column_name_or_array name of column or columns that only show on some values
     * @param $value_or_array_to_enable value or values that enable said columns
     * @param $control_id html id of control div that groups a column (will be deprecated, can be empty)
	 * @return icolumn reference to self
	 */	
	function &bind_to_column($column_name_or_array,$value_or_array_to_enable='',$control_id='');
    
    /**
     * @param $columnd_bind columns_bind
     * @return icolumn 
     */
	function &add_binded_from_column(columns_bind $column_bind); 
	/**
	 * @return icolumn
	 */    
	function &bind_to_columns_array($value_to_enable='', $columns_names_array);
	/**
	 * @return icolumn
	 */	
	function &bind_to_columns(control_group $control_group, $value_to_enable);
	/**
	 * @return array
	 */
	function get_column_binds();
	
	//-----------------------------------------------------------------------
	
	/**
	 * @return string
	 */
	function get_db_values_str();
	/**
	 * @return string
	 */
	function get_db_columns_str();
	/**
	 * @return string
	 */
	function get_db_equal_str();
	/**
	 * Whenever to treat null values as empty relationships 
	 * @param boolean $nullify
	 * @return icolumn
	 */
	function &set_nullify_empties($nullify=true);
	
	
	/**
	 * Sets if a column is visible in UI rendering
	 * @param bool $is_visible
	 * @return icolumn
	 */
	public function &set_visible($is_visible=true);
	/**
	 * Gets if a column is visible in UI rendering
	 * @param bool $is_visible
	 */
	public function get_visible();
	/**
	 * Gets if a column is visible in UI rendering
	 * @param bool $is_visible
	 */
	public function is_visible();	
	/**
	 * Sets a value to be persisted despite what is retrieved from database
     * Note that after saving data, get_value will not give the forced value
	 * @param $value
	 * @return icolumn
	 */
	public function &set_forced_value($value);
    
    /**
     * Gets if there is a forced value
     */
    public function get_forced_value();
	
	/**
	 * Sets the behaviour where an empty value is treated like a null
	 * @param $empty_not_zero
	 * @return icolumn
	 */
	public function &set_empty_not_zero($empty_not_zero=true);
    
    /**
     * Gets the JS (without <script> tags) to be used after the control inside a control_edit
     * @return string
     */
    public function get_js_after_control();
}

?>