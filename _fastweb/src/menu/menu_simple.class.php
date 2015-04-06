<?php 

class menu_simple extends aweb_object {
    
    public function get_breadcrums() {
        return '';
    }
    public function __toString() {
        return $this->get_menu();
    }
    public function get_menu() {
        return '';
    }

}