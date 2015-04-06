<?php
class control_fieldset extends control_group implements icontrol {
	/**
	 * @var array
	 */
	protected $controls;
		
	protected $title;
	protected $pre_message = '';
	protected $post_message = '';
	
    /**
     * @param $table table_data
     * @param $id string HTML Identificator
     * @param $title string Title overlay
     * @param $pre_message string Message string just before the content
     * @param $column_names_array mixed Array of column names strings or other icontrol elements
     * @param $post_message string Message string just after the content
     */
	public function __construct($table=null, $id='' , $title='' , $pre_message='' , 
								$column_names_array=null , $post_message='' ) {
		$this->table = $table;
		$this->id = $id;
		if ( $column_names_array != null ) {
			foreach($column_names_array as $col) {
				if ($col instanceof icontrol ) {
					$this->add($col);
				} else {
					$this->add($col);
				}
			}
		}
		$this->set_title($title);
		$this->set_pre_message($pre_message);
		$this->set_post_message($post_message);
	}
		
	public function set_title($title) {
		$this->title = $title;
	}
	public function set_pre_message($pre_message) {
		$this->pre_message = $pre_message;
	}
	public function set_post_message($post_message) {
		$this->post_message = $post_message;
	}

	/**
	 * @return string
	 */
	public function get_control_render() {
		$result = '';
		$i = '';
		if ($this->id != '') {
			$i="id=\"".$this->id."\"";
		}
		$result .= "<fieldset $i>\r\n";
		$result .= "<legend>$this->title</legend>\r\n";
		if ($this->pre_message != '' ) {
			$result .= '<p style="margin:3px 0 3px 0;">';
			$result .= $this->pre_message;
			$result .= '</p>';
		}
		foreach ($this->controls as $control) {
			$result .= $control->get_control_render();
		}
		$result .= "<div style=\"margin:8px 0;\">";
		$result .= $this->post_message;
		$result .= "</div>\r\n";
		$result .= "</fieldset>\r\n";
		return $result;
	}
	
}
?>