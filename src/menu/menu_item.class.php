<?php
/**
 * Enter description here...
 *
 */
class menu_item {
	public $url = '';
	public $title = '';
	public $subtitle = '';
	public $description = '';
	public $image_url = '';
	public $image_auto = true;
	public $visible = true;
	/**
	 * Submenu contenido en este item
	 * @var menu
	 */
	public $submenu = null;
	
	public $visible_to_groups = array();
	public $style='';
	/**
	 * Enter description here...
	 *
	 * @param string $title
	 * @param string $url
	 * @param menu $submenu
	 * @param array $groups
	 * @param string $style
	 * @return menu_item
	 */
	function __construct($title,$url='',$submenu=null,$groups=null,$style='',$description='') {
		$this->url = $url;
		$this->title = $title;
		$this->submenu = $submenu;
		$this->description = $description;
		if (isset($groups) && $groups != null ) {
			$this->visible_to_groups = $groups;
		}
		$this->style = $style;
	}


	public function has_submenu() {
		return ( is_object($this->submenu) && is_array($this->submenu->structure) && count($this->submenu->structure) > 0 );
	}

}
?>