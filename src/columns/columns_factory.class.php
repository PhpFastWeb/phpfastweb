<?php 	
	class columns_factory {
		
		/**
		 * 
		 * @param string $column_type
		 * @return icolumn
		 */
		public static function create($column_type) {
			switch ($column_type) {
				case 'text':
					return new column_text();
					break;
				case 'textarea':
					return new column_textarea();
					break;
				case 'password':
					return new column_password();
					break;
				case 'email':
					return new column_email();
					break;	
				case 'url':
					return new column_url();
					break;					
				case 'date':
					return new column_date();
					break;
				case 'datetime':
					return new column_datetime();
					break;					
				case 'file':
					return new column_file();
					break;
				case 'image':
					return new column_image();
					break;
				case 'checkbox':
					return new column_checkbox();
					break;
				case 'select':
					return new column_select();
					break;
				case 'image':
					return new column_image();
					break;
				case 'file':
					return new column_file();
					break;										
				case 'hidden':
					return new column_hidden();
					break;
				case 'number':
					return new column_number();
					break;
			}
			throw new ExceptionDeveloper("No se pudo crear el tipo: ".$column_type);
		}
	}


?>