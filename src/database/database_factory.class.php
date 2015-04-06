<?php
	class database_factory {
		
		/**
		 * 
		 * @param int $type
		 * @return idatabase_provider
		 */
		static public function create($type) {
			switch($type) {
				case database_config::TYPE_MYSQL:
					return new database_mysql();
					break;
				case database_config::TYPE_INTERBASE:
					return new database_interbase();
					break;
				case database_config::TYPE_ORACLE:
					return new database_oracle();
					break;
				default:
					throw new ExceptionDeveloper('database provider type not found: '.$type);
			}
		}
	}



?>