<?php
	class column_checkbox extends acolumn implements icolumn, icolumn_checkbox {
		
		
		private $limit_len;
		public function set_limit_len($max_len) {
			$this->limit_len = $max_len;
		}
		public function get_formatted_value() {
			if ( $this->value == 1) return 'Si';
			return 'No';

		}
		
		protected $pre_title ='';
		public function set_pre_title($pre_title) {
			$this->pre_title = $pre_title;
		}
	
		public function get_input_plain() {
			$js = $this->get_js_onchange();
			if ($js != '') {
				$js_click = 'onclick="this.onchange();" ';
			}
			$result = '';
			
			//Esto hace que tenga siempre un valor, 0 o 1, aunque no se marque el checkbox
			//$result .= "<input type=\"hidden\" name=\"$this->column_name\" value=\"0\" />";
			
			$result .= $this->pre_title;
			$result .= "<input type=\"checkbox\" name=\"{$this->get_html_id()}\" id=\"{$this->get_html_id()}\" class=\"form_edit_row_checkbox\" $js $js_click";
			if ($this->value==1) {
				$result .='checked="checked" ';
			}
			$result .="value=\"1\" ";
			$result .=" />\r\n";
			return $result;
		}
		public function get_db_type() {
			return 'bool';
		}
	}

?>