<?php
	abstract class acontrol implements icontrol {

		
		/**
		 * @param icontrol $control
		 * @return icontrol
		 */
		public static function &cast(icontrol $control) {
			return $control;
		}
		
		protected $id = '';
		/**
		 * @param $id
		 */
		public function set_id($id) {
			$this->id = $id;
		}
		
		public function get_id() {
			return $this->id;
		}
		/*
		 * @var table_data
		 */
		protected $table;
		public function set_table(table_data &$table) {
			$this->table = $table;
		}
		
		protected $readonly = false;
		public function set_readonly($is_readonly=true) {
			$this->readonly = $is_readonly;
		}
		public function is_readonly() {
			return $this->readonly;
		}
		public function has_column($column_name) {
			return false; 
		}
        public function count_subcontrols() {
            return 0;
        }
	
	}
?>