<?php

class Database {

    private $pdo;
    private $bConnected = false;
    private $parameters;
    
    public function __construct() {
        $this->Connect();
        $this->parameters = array();
    }

    /**
     * 	This method makes connection to the database.
     * 	
     * 	1. Reads the database settings from a ini file. 
     * 	2. Puts  the ini content into the settings array.
     * 	3. Tries to connect to the database.
     * 	4. If connection failed, exception is displayed and a log file gets created.
     */
    private function Connect() {
        global $CFG;

        $dsn = 'mysql:dbname=' . $CFG->dbname . ';host=' . $CFG->dbhost . '';
        try {
            $this->pdo = new PDO($dsn, $CFG->dbuser, $CFG->dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->bConnected = true;
        } catch (PDOException $e) {
            echo $this->ExceptionLog($e->getMessage());
            die();
        }
    }

    /*
     *   You can use this little method if you want to close the PDO connection
     *
     */

    public function CloseConnection() {
        $this->pdo = null;
    }

    /**
     * 
     * @param string $table
     * @return array columns table
     */
    public function getTableColumns($table) {
        $query = $this->pdo->prepare("DESCRIBE $table");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }
    
    /**
     *  Returns the last inserted id.
     *  @return string
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * 
     * @param string $table
     * @param array $conditions
     * @param string $fields
     * @return \stdClass
     */
    public function getRecord($table, array $conditions = null, $fields = '*') {
        $where = '';
        $params = array();
        if (count($conditions) > 0) {
            $where .= " WHERE ";
            foreach ($conditions as $key => $value) {
                $where .= ' ' . $key . ' = :' . $key . ' AND ';
                $params[':' . $key] = $value;
            }
        }
        $where = substr($where, 0, -5);

        $fieldString = '';
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $fieldString .= $field . ',';
            }
            $fieldString = substr($fieldString, 0, -1);
        } else {
            $fieldString = $fields;
        }
        
        $statement = $this->pdo->prepare("SELECT " . $fieldString . " FROM " . $table . $where . ' LIMIT 1');
        $statement->execute($params);
        $result = $statement->fetchObject();
        
        return $result;
    }
    
    /**
     * 
     * @param string $table
     * @param array $conditions
     * @param string $fields
     * @return array \stdClass
     */
    public function getRecords($table, array $conditions = null, $fields = '*') {
        $where = '';
        $params = array();
        if (count($conditions) > 0) {
            $where .= " WHERE ";
            foreach ($conditions as $key => $value) {
                $where .= ' ' . $key . ' = :' . $key . ' AND ';
                $params[':' . $key] = $value;
            }
        }
        $where = substr($where, 0, -5);

        $fieldString = '';
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $fieldString .= $field . ',';
            }
            $fieldString = substr($fieldString, 0, -1);
        } else {
            $fieldString = $fields;
        }
        
        $statement = $this->pdo->prepare("SELECT " . $fieldString . " FROM " . $table . $where);
        $statement->execute($params);
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $resultsArray = array();
        foreach($results as $result) {
            $resultsArray[] = (object) $result;
        }
        
        return $resultsArray;
    }
    
    /**
     * 
     * @param string $table
     * @param array $properties
     * @return int id
     */
    public function createOrUpdate($table, $conditions) {
        $columns = $this->getTableColumns($table);
        //update
        if ($object = $this->getRecord($table, array('id' => $conditions['id']))) {
            $query = 'UPDATE ' . $table . ' SET ';
            foreach ($columns as $column) {
                if ($column != 'id') {
                    $value = $conditions[$column];
                    $query .= ' ' . $column . ' = :' .$column. ',';
                }
            }
            
            $query = substr($query, 0, -1);
            $query .= ' WHERE id = :id';
            
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':id', $conditions['id'], PDO::PARAM_INT);
            
            foreach ($columns as $column) {
                if ($column != 'id') {
                    $value = $conditions[$column];
                    if (is_int($value)) {
                        $statement->bindValue(':'.$column, $value, PDO::PARAM_INT);
                    } else {
                        $statement->bindValue(':'.$column, $value, PDO::PARAM_STR);
                    }
                }
            }
                        
            $statement->execute();
            return $conditions['id'];
        } else {
            //create
            $query = "INSERT INTO " . $table . ' (';
            $into = '';
            $values = '';

            foreach ($columns as $column) {
                if ($column != 'id') {
                    $value = $conditions[$column];
                    $into .= $column . ',';
                    $values .= ':'.$column . ',';
                }
            }

            $into = substr($into, 0, -1);
            $values = substr($values, 0, -1);
            $query .= $into . ') VALUES (' . $values . ')';
            $statement = $this->pdo->prepare($query);
            
            foreach ($columns as $column) {
                if ($column != 'id') {
                    $value = $conditions[$column];
                    if (is_int($value)) {
                        $statement->bindValue(':'.$column, $value, PDO::PARAM_INT);
                    }
                    else {
                        $statement->bindValue(':'.$column, $value, PDO::PARAM_STR);
                    }
                }
            }
            $statement->execute();
            
            return $this->pdo->lastInsertId($table);
        }
    }
    
    /**
     * 
     * @param string $table
     * @param array $conditions
     */
    public function deleteRecords($table, array $conditions) {
        $where = '';
        $params = array();
        if (count($conditions) > 0) {
            $where .= " WHERE";
            foreach ($conditions as $key => $value) {
                $where .= ' ' . $key . ' = :' . $key . ' AND ';
                $params[':' . $key] = $value;
            }
        }
        
        $where = substr($where, 0, -5);
        $query = "DELETE FROM " . $table . $where;
        $statement = $this->pdo->prepare($query);
        
        if (count($conditions) > 0) {
            foreach ($conditions as $key => $value) {
                if (is_int($value)) {                    
                    $statement->bindValue(':'.$key, $value, PDO::PARAM_INT);
                } else {
                    $statement->bindValue(':'.$key, $value, PDO::PARAM_STR);
                }
            }
        }
        $statement->execute();
    }
    
    /**
     * 
     * @param string $table
     * @param array $conditions
     * @return type
     */
    public function createTable($table, array $conditions) {
        $query = 'CREATE TABLE IF NOT EXISTS ' . $table . ' (';
        
        foreach($conditions as $subconditions) {
            foreach ($subconditions as $condition) {
                $query .= $condition . ' ';
            }
            $query = substr($query, 0, -1);
            $query .= ', ';
        }
        $query = substr($query, 0, -2);
        $query .= ')';
        
        $statement = $this->pdo->exec($query);
        $columns = $this->getTableColumns($table);
        
        if($columns > 0) {
            return true;
        }
    }
        
    /** 	
     * Writes the log and returns the exception
     *
     * @param  string $message
     * @param  string $sql
     * @return string
     */
    private function ExceptionLog($message, $sql = "") {
        $exception = 'Unhandled Exception. <br />';
        $exception .= $message;
        $exception .= "<br /> You can find the error back in the log.";
        if (!empty($sql)) {
            $message .= "\r\nRaw SQL : " . $sql;
        }
        return $exception;
    }
}
