<?php
	class database_oracle implements idatabase_provider {
		public function connect($ip,$db_name,$user,$password,$charset) {
//			$conection = ocilogon($user,$password,$db_name);
//			if ( ! $conection ) { 
//				$erra=OCIError();
//				throw new ExceptionDeveloper("**Error: ocilogon ha fallado. \nOracle reports: ".$erra['code']." ".$erra['message']); 				
//			}
//			return $conection;
		}
		public function pconnect($ip,$db_name,$user,$password,$charset) {
//			$conection = ociplogon($user,$password,$db_name);
//			if ( ! $conection ) { 
//				$erra=OCIError();
//				throw new ExceptionDeveloper("**Error: ocilogon ha fallado. \nOracle reports: ".$erra['code']." ".$erra['message']); 				
//			}
//			return $conection;
		}
		public function close($conection) {
			
		}
		public function errmsg($query_result) {
//			$erra = OCIError($query_result);
//			return "#".$erra['code'].": ".$erra['message']; 
		}
		public function fetch_assoc($query_result) {
//			$result = null;
//			OCIFetchINTO($query_result,$result,OCI_ASSOC);
//			return $result;
		}
		public function num_rows($query_result) {
//			$i = 0; $dumy = '';
//			while ( OCIFetchINTO($query_result,$dumy,OCI_ASSOC) ) {
//				$i++;
//			}
//			//return ocifetchstatement($query_result,$dumy);
//			return $i;
		}
		public function query($conection,$sql) {
//				$this->connect();
//				$result = ociparse($this->conection,$sql);
//				if ( ! $result ) { 
//					return false; 
//				}
//				$success = @ociexecute($result,OCI_DEFAULT);
//				if (!$success) {
//					return false; 
//				}
//				$committed = ocicommit($this->conection);
//				if ( ! $committed ) { 
//					return false; 				
//				}	
		}
		public function query_ranged($conection,$sql,$top_limit=-1, $bottom_limit=-1, $sql_order='') {
			if ($top_limit >= 0 || $bottom_limit >= 0 ) {
				if ($bottom_limit == -1 ) $bottom_limit = 0; //Parche
				if ($top_limit >= 0 && $bottom_limit >= 0 ) {
					//El primer row_number empieza por 1, no por cero
					$bottom_limit++;
					$spos = strpos($sql, "SELECT");
					$sql2 = substr($sql,$spos+6);
					$sql = "select * from ( select row_number() over ($sql_order) r, $sql2 ) ";
					$sql .= "where r between $bottom_limit and ".($bottom_limit+$top_limit);
				} elseif ( $top_limit >= 0 ) {
					//Esto no se ejecuta por el parche, ya que he comprobado
					//que este código no reordena bien la consulta con el $sql_where

					throw new ExceptionDeveloper("Not implemented");
					/*
					$where = $this->str_sql_where_clause($sql);
					if ($where !== false ) {
					$sql = $this->str_sql_without_where($sql);
					$sql .= "WHERE ".$this->concat_sql_restrictions($where,"rownum <= $top_limit" );
					} else {
					$sql = $sql." WHERE rownum <= $top_limit";
					}
					$sql .= " ".$sql_order;
					*/
					$bottom_limit++;
					$spos = strpos($sql, "SELECT");
					$sql2 = substr($sql,$spos+6);
					$sql = "select * from ( select row_number() over ($sql_order) r, $sql2 ) ";
					$sql .= "where r between $bottom_limit and ".($bottom_limit+$top_limit);
				} elseif ( $bottom_limit >= 0 ) {
					//Normalmente se entra aqui por una select que no va a devolver ningun elemento.
					//TODO: Corregir esto y hacer que no sea llamado
					//El primer row_number empieza por 1, no por cero
					$bottom_limit++;
					$spos = strpos($sql, "SELECT");
					$sql2 = substr($sql,$spos+6);
					$sql = "select * from ( select row_number() over ($sql_order) r, $sql2 ) ";
					$sql .= "where r between $bottom_limit and ".($bottom_limit+$top_limit);
				}
			} else {
				$sql .= " ".$sql_order;
			}
			$result = $this->query($conection,$sql);
		}
		public function get_last_insert_id($conection) {
			throw new ExceptionDeveloper("Not Implemented");
		}
		public function get_affected_rows($conection) {
			throw new ExceptionDeveloper("Not Implemented");
		}
		public function reset($query_result) {
			throw new ExceptionDeveloper("Not Implemented");
		}
		public function query_columns($conection,$table) {
			$sql = "SELECT * FROM all_tab_columns WHERE table_name='$table'";
			$result = $this->query($conection,$sql);
			$row = $this->fetch_assoc($result);
			while ( $row ) {
				$columns[] = $row['COLUMN_NAME'];
				$row = $this->fetch_assoc($result);
			}
		}
		public function query_primary_keys($conection,$table) {
			$primary_keys = array();
			$sql  = "SELECT a.owner, a.table_name, b.column_name FROM all_constraints a, all_cons_columns b WHERE a.constraint_type='P' ";
			$sql .= " AND a.constraint_name=b.constraint_name AND a.table_name = '$table'";
			$result = $this->query($sql);
			$row = $this->fetch_assoc($result);
			while ( $row ) {
				$primary_keys[] = $row['COLUMN_NAME'];
				$row = $this->fetch_assoc($result);
			}
			return $primary_keys;
		}
		public function escape_column_name($col_name) {
			throw new ExceptionDeveloper("Not Implemented");
		}	
		function escape_string($var) {
			throw new ExceptionDeveloper("Not Implemented");	
		}
		function query_tables($conection) {
			$query_result = $this->query($conection,'SELECT table_name FROM user_tables');
			$result = array();
			$i=0;
			$item = $this->fetch_assoc($query_result);
			while ( $item ) {
				$result[$i] = $item;
				$item = $this->fetch_assoc($query_result);	
				$i++;
			}
			return $result;
		}
		public function get_last_error($conection) {
			throw new ExceptionDeveloper("Not Implemented");
		}
		/**
		 * @param unknown_type $conection
		 */
		public function get_last_error_num($conection) {
			throw new ExceptionDeveloper("Not Implemented");
		}
	}
?>