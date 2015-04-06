<?php
class database_mysql implements idatabase_provider {
    protected $sql = '';
    
    public function connect($ip, $db_name, $user, $password, $charset = '') {
        $result = @mysql_connect($ip, $user, $password);
        if (! $result) {
            throw new ExceptionDatabase("**Error: mysql_connect, No se pudo conectar a " . $db_name .
                " en " . $ip . "<br />" . mysql_error());
        }
        $success = @mysql_select_db($db_name, $result);
        //mysql_query('USE `$db_name`');
        if (! $success) {
            throw new ExceptionDatabase("**Error: mysql_select_db : " . mysql_error());
        }
        return $result;
    }
    public function pconnect($ip, $db_name, $user, $password, $charset = '') {
        $result = @mysql_pconnect($ip, $user, $password);
        if (! $result) {
            throw new ExceptionDatabase("**Error: mysql_connect, No se pudo conectar a " . $db_name .
                " en " . $ip . "<br />" . mysql_error());
        }
        $success = @mysql_select_db($db_name, $result);
        //mysql_query('USE `$db_name`');
        if (! $success) {
            throw new ExceptionDatabase("**Error: mysql_select_db : " . mysql_error());
        }
        return $result;
    }
    public function close($conection) {
        return mysql_close($conection);
    }
    public function errmsg($query_result) {
        return mysql_error();
    }
    public function fetch_assoc($query_result) {
        if ($query_result == null)
            return null;
        return mysql_fetch_array($query_result, MYSQL_ASSOC);
    }
    public function num_rows($query_result) {
        if ($query_result != null && is_resource($query_result)) {
            return mysql_num_rows($query_result);
        } else {
            //It seems that queries like 'USE dbname' doesn't return a resource
            return null;
        }
    }
    public function affected_rows($conection) {
        return mysql_affected_rows($conection);
    }
    public function execute_query($conection, $sql) {
        $this->sql = $sql;
        $result = mysql_query($sql, $conection);
        if ($result === false) {
            //Should return resource, even with empty result, so it is an error
            $msg = '';
            if ( $conection ) {
            	try {
            		$msg = $this->get_last_error($conection);
           		} catch(Exception $e) {
           			$msg = 'Unknown error';
     			}
      		}
            $e = new ExceptionDatabaseQuery( $msg , $sql );
            throw $e;
        }
        return $result;
    }
    public function execute_nonquery($conection, $sql) {
        $this->sql = $sql;
        $result = mysql_query($sql, $conection);
        return $result;
    }
    public function execute_query_ranged($conection, $sql, $top_limit = -1, $bottom_limit =
        -1, $sql_order = '') {
        $sql .= " " . $sql_order;
        if ($top_limit >= 0) {
            $sql .= " LIMIT " . ($top_limit + 1);
            if ($bottom_limit > 0) {
                $sql .= " OFFSET " . $bottom_limit;
            }
        } else {
            if ($bottom_limit > 0) {
                $sql .= " LIMIT " . $bottom_limit;
            }
        }
        return $this->execute_query($conection, $sql);
    }
    public function get_last_insert_id($conection) {
        $query_result = $this->execute_query($conection, 'SELECT LAST_INSERT_ID() AS ID');

        $result = array();
        $i = 0;
        $item = mysql_fetch_array($query_result, MYSQL_ASSOC);
        while ($item) {
            $result[$i] = $item;
            $item = mysql_fetch_array($query_result, MYSQL_ASSOC);
            $i++;
        }
        if (count($result) == 1 && isset($result[0])) {
            $a = $result[0];
        } else {
            if (count($result) > 1) {
                throw new ExceptionDatabase("**Error: Deber&iacute;a encontrar un &uacute;nico valor, encontrados m&uacute;ltiples.<br />SQL: ");
            } else {
                return $result;
            }
        }
        if ($result)
            $result = $a['ID'];
        return $a;
    }

    public function get_last_error($conection) {
        $num = $this->get_last_error_num($conection);
        $msg = database_errors_spanish::get_error($num);
        return $msg;
    }
    public function get_last_error_original($conection) {
        return mysql_error($conection);
    }    
    public function get_last_error_num($conection) {
        return mysql_errno($conection);
    }
    public function get_affected_rows($conection) {
        return mysql_affected_rows();
    }
    public function reset($query_result) {
        return mysql_data_seek($query_result, 0);
    }
    public function query_columns($conection, $table) {
        $columns = array();
        $sql = "SHOW COLUMNS FROM $table";
        $result = $this->query($conection, $sql);
        $row = $this->fetch_assoc($result);
        while ($row) {
            $columns[] = current($row);
            $row = $this->fetch_assoc($result);
        }
        return $columns;
    }
    public function query_primary_keys($conection, $table) {
        $primary_keys = array();
        $sql = "SHOW COLUMNS FROM $table";
        $result = $this->query($conection, $sql);
        $row = $this->fetch_assoc($result);
        while ($row) {
            if ((isset($row['KEY']) && $row['KEY'] == 'PRI') || (isset($row['Key']) && $row['Key'] ==
                'PRI')) {
                if (isset($row['FIELD'])) {
                    $primary_keys[] = $row['FIELD'];
                } else {
                    $primary_keys[] = $row['Field'];
                }
            }
            $row = $this->fetch_assoc($result);
        }

        return $primary_keys;
    }
    public function escape_column_name($col_name) {
        return $result = '`' . $col_name . '`';
    }
    function escape_string($var) {
        //If not specified, will use last connection to DB
        if (is_null($var))
            return null;
        if (is_object($var) || is_array($var) || is_resource($var))
            throw new ExceptionDatabase('$var not convertible to string');
        //Other types (like number, bool), can and should be converted to string
        try {
            $result = @mysql_real_escape_string($var);
        }
        catch (exception $e) {
            $result = false;
        }

        if ($result === false) {
            //throw new ExceptionDeveloper();
            //echo '<pre>'.var_dump(debug_backtrace()); die;
            //Conection to DB failed, we let the next query to deal with the problem
            return '';
        }
        return $result;
    }
    function query_tables($conection) {
        $query_result = $this->query($conection, 'show tables');
        $result = array();
        $i = 0;
        $item = $this->fetch_assoc($query_result);
        while ($item) {
            $result[$i] = $item;
            $item = $this->fetch_assoc($query_result);
            $i++;
        }
        return $result;
    }

    /**
     * This function require access to INFORMATION_SCHEMA database 
     */
    public function table_or_view_exists($conection, $database_name, $table_name) {
        if ($database_name == '' || $table_name == '') {
            throw new ExceptionDatabase('Table or database name empty');
        }
        $dbn = $this->escape_column_name($database_name);
        $tn = $this->escape_column_name($table_name);
        $sql = "select `TABLE_NAME` from `INFORMATION_SCHEMA`.`TABLES` WHERE TABLE_SCHEMA='$dbn' and TABLE_NAME='$tn'";
        //WHERE information_schema.views.table_schema LIKE 'view%';

        return $this->check_object_exists($conection, $sql, $table_name);
    }
    /**
     * Returns if a table or view exists with given name.
     * Requires MySQL 5.0.1 to show also views.
     */
    public function table_exists($conection, $table_name) {
        $sql = "SHOW TABLES LIKE '" . $this->escape_column_name($table_name) . "'";
        return $this->check_object_exists($conection, $sql, $table_name);
        //http://dev.mysql.com/doc/refman/5.0/en/show-tables.html
        //SHOW FULL TABLES displays a second output column.
        //Values for the second column are BASE TABLE for a table and VIEW for a view.
    }
    protected function check_object_exists($sql_check, $object_name) {
        //TODO: Optimize this not to look for all objects, or cache object lists
        $query_result = $this->execute_query($conection, $sql);
        //fetch_array
        if ($query_result === false) {
            throw new ExceptionDatabase("Error querying object existance");
        }
        $result = array();
        $i = 0;
        $item = $this->fetch_result($query_result);
        while ($item) {
            $result[$i] = $item;
            $item = $this->fetch_result($query_result);
            $i++;
        }
        //search array
        if (count($result) == 1) {
            $r = reset($result);
            if (reset($r) == $object_name) {
                return true;
            }
        }
        return false;
    }


}

?>