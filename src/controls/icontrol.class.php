<?php
interface icontrol {
	
	/**
	 * Set the value(s) of the columns of the array
	 * $row must be an associative array with keys as columns names needed by the control
	 * @param array $row_array
	 */
	public function set_values($row_array);
	/**
	 * Returns the renderd html of the control in the current mode
	 * @return string
	 */
	public function get_control_render();
	
	/**
	 * 
	 * @param string $id
	 */
	public function set_id($id);
	
	public function get_id();

	public function set_readonly($is_readonly=true);
	
	public function is_readonly();
	
	/**
	 * @return boolean
	 */
	public function has_column($column_name);
    
    /**
     * @return int
     */
    public function count_subcontrols();
	
}
?>