<?php
	class column_password extends column_text implements icolumn {
		
		
		protected $uncover = false;
		public function set_uncover($uncover=true) {
			$this->uncover = $uncover;
		}
		public function get_input_plain() {
			if ( $this->uncover ) $result = parent::get_input_plain();
			else $result = "<input type=\"password\" autocomplete=\"off\" name=\"{$this->get_html_id()}\" id=\"{$this->get_html_id()}\" value=\"$this->value\" />";
			return $result;
		}
		public function get_formatted_value() {
			if ( $this->uncover ) return parent::get_formatted_value();
            return "****";
		}
		
	}
?>