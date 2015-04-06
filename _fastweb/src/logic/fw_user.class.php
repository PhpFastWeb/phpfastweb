<?php

class fw_user extends apersistant implements ipersistant {
    public $id;
    public $pk;
    public $name;
    public $surname;
    public $password;
    public $group;
    public $email;
    public $phone;
    public $avatar;
    public $notes;
    
    public $_table_name = 'fw_users';
    
    public function get_fullname() {
        if ($this->name != '' ) {
            return $this->name.' '.$this->surname;
        }
        return $this->surname;
    }
}