<?php
	
	/**
	 * @deprecated
	 * @author vherrera
	 *
	 */
	class database_procedures extends database {
		//-- Ejecución de procedimientos almacenados ---------------------------------------
		/**
		 * @deprecated
		 */
		function db_procedure_insert($package,$proc_name,&$parameters,$table_name='') {
			if ( $this->db_config->type != database_config::TYPE_ORACLE ) {
				$parameter2=array();
				foreach ($parameters as $key => $value) {
					$parameter2[strtoupper(substr($key,1))] = $value;
				}
				return $this->insert2($table_name,$parameter2);			
			} else {
				return $this->db_procedure($package,$proc_name,$parameters);
			}
		}
		/**
		 * @deprecated
		 */
		function db_procedure_update($package,$proc_name,&$parameters,$table_name='',$pks_list='') {
			if ( $this->db_config->type != database_config::TYPE_ORACLE  ) {
				$parameter2=array();
				foreach ($parameters as $key => $value) {
					$parameter2[strtoupper(substr($key,1))] = $value;
				}
				return $this->update2($table_name,$parameter2,$pks_list);			
			} else {
				return $this->db_procedure($package,$proc_name,$parameters);
			}
		}
		/**
		 * @deprecated
		 */	
		function db_procedure($package,$proc_name,&$parameters) {
			switch ( $this->db_config->type  ) {
				case database_config::TYPE_ORACLE:
					//(re)establecemos la conexiï¿½n
					$this->connect();
					//Construimos la lista de parï¿½metros
					$oci_param_list = '';
					foreach($parameters as $key => $value ) {
						$oci_param_list .= ":$key,";
					}
					$oci_param_list = substr($oci_param_list,0,-1);
					if ( $package != '' ) $package .= ".";
					//Construimos el sql
					$sql = "BEGIN $package$proc_name($oci_param_list); END;";
	
//					$stmt = ociparse($this->conection, $sql);
//					if ( ! $stmt ) { 
//						$erra=OCIError($this->conection);
//						throw new ExceptionDeveloper("**Error:\nOracle reports: ".$erra['code']." ".$erra['message']);  				
//					}
			
					// Bind the input num_entries argument to the $max_entries PHP variable
					//$len = 64;
					$len = -1;
					foreach ($parameters as $key => $vale ) {
						//$len = max(strlen($parameters[$key]),64);
//						$ok = ocibindbyname($stmt,":$key",$parameters[$key],$len);
//						if (! $ok ) {
//							echo "**Error: ocibindbyname al vincular la variable :$key<br />";
//						}
					}
	
//	
//					$success = ociexecute($stmt);
//					if ( ! $success ) { 
//						$erra=OCIError($stmt);
//						throw new ExceptionDeveloper("**Error:\nOracle reports: ".$erra['code']." ".$erra['message']); 				
//					}
	
//					return $success;
			
				break;
				case database_config::TYPE_INTERBASE:
					//(re)establecemos la conexiï¿½n
					$this->connect();
					
	
					$param_list = substr($param_list,0,-1);
	
					
					$sql = "EXECUTE PROCEDURE $proc_name ";
					foreach ($parameters as $key => $value) {
						$sql .="'$value',"; //TODO: Escapar la cadena
					}
					$sql = substr($sql,0,-1);
					
	
					
					error_log(var_export($sql));
					$success = $this->execute_sql($sql);
					return $success;				
				
				break;
				default:
					throw new ExceptionDeveloper('**Error: Not implemented');
				break;
			}
			return false;
		}
	}
	
?>