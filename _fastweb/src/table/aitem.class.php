<?php

abstract class aitem {
    public $data;
    /**
     * Sets an array of data for the visual item.
     * @param $data array of data for the visual item
     * @return aitem self reference
     */
    public function &set_data( $data ) {
        if (is_object($data) ) $this->set_data_object($data);
        else $this->data = $data;
        return $this;
    }
    /**
     * Sets an object of data for the visual item.
     * Only public properties are considered.
     * @param $data object of data for the visual item
     * @return aitem self reference
     */
    public function &set_data_object( $data_object ) {
        $this->data = (array)$data_object;
        return $this;
    }
    /**
     * Gets an array of data for the visual item, or a specific value if key is specified.
     * @return Array
     */
    public function get_data($key=null) {
        if (is_null($key))
            return $this->data;
        return $this->data[$key];
    }
    /**
     * Gets an object with the data for the visual item, alias of get_data_object().
     * @return object
     */
    public function d() {
        return $this->get_data_object();
    }
    /**
     * Gets an object with the data for the visual item.
     * To be used un casting a specified data object.
     * @return object
     */
    public function get_data_object() {
        $d = $this->get_data();
        return (object)$d;
    }
    /**
     * Returns string implementing the visual representation of the item.
     * If item has several visual representations, you shall specify which one to give beforehand in some way.
     * @return string
     */
    abstract public function __toString();
}