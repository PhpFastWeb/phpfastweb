<?php

class pagination_more extends apagination implements ipagination {
    public $target_url = '';
    public $target_class = 'pagination_more';
    public $target_text = 'Ver listado completo';
    public $hide_if_no_pages = false;
    public function __toString() {
        if ( ! $this->has_to_show_pagination() && $this->hide_if_no_pages ) return '';
        return '<a href="'.$this->target_url.'" class="'.$this->target_class.'">'.$this->target_text.'</a>';
    }
}