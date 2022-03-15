<?php

    namespace Core;
    use \PDO;
    use \PDOException;

    class DB
    {
        private static $_instance = null;
        private $_pdo;
        private $_mysql;
        private $_query;
        private $_error = false;
        public $errorInfo = null;
        private $_result;
        private $_count = 0;
        private $_lastInsertID = null;

        private function __construct()
        {
            require_once(ROOT_DIR . DS . 'config' . DS . 'config.php');

            $config = CONFIG;
            $env = 'live';

            $host = $config[$env]["db"]["host"];
            $user = $config[$env]["db"]["user"];
            $pass = $config[$env]["db"]["password"];
            $dbName = $config[$env]["db"]["dbname"];

            try
            {
                $this->_pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            }
            catch(PDOException $e)
            {
                dnd($e->getMessage());
            }
        }

        public static function getInstance()
        {
            if(!isset(self::$_instance))
            {
                self::$_instance = new DB();
            }

            return self::$_instance;
        }

        public function query($sql, $params = [], $fetch = false)
        {
            $this->_error = false;

            $this->_pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
            
            if($this->_query = $this->_pdo->prepare($sql))
            {
                $x = 1;
                if(!empty($params))
                {
                    foreach($params as $param)
                    {
                        $this->_query->bindValue($x, $param);
                        $x++;
                    }
                }

                if($this->_query->execute())
                {
                    if(!$fetch)
                    {
                        $this->_result = $this->_query->fetchALL(PDO::FETCH_OBJ);
                    }
                    $this->_count = $this->_query->rowCount();
                    $this->_lastInsertID = $this->_pdo->lastInsertId();
                }
                else
                {
                    $this->_error = true;
                    $this->errorInfo = $this->_pdo->errorInfo();
                }
            }

            return $this;
        }

        public function insert($table, $data = [])
        {
            $fields = "";
            $values = [];
            $valueString = "";

            foreach($data as $field => $value)
            {
                $fields .= "`".$field."`,";
                $valueString .= "?,";
                $values[] = $value;
            }

            $fields = rtrim($fields, ",");
            $valueString = rtrim($valueString, ",");

            $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$valueString})";
            
            if(!$this->query($sql, $values, true)->error())
            {
                return true;
            }

            return false;
        }

        public function update($table, $data = [], $condition)
        {
            $fields = "";
            $values = [];

            foreach($data as $field => $value)
            {
                $fields .= $field . " = ?, ";
                $values[] = $value;
            }

            $fields = rtrim($fields, ", ");

            $sql = "UPDATE {$table} SET {$fields} WHERE {$condition}";
            
            if(!$this->query($sql, $values, true)->error())
            {
                return true;
            }

            return false;
        }

        public function delete($table, $condition)
        {
            $sql =  "DELETE FROM {$table} WHERE {$condition}";

            if(!$this->query($sql, [], true)->error())
            {
                return true;
            }

            return false;
        }

        public function first()
        {
            return (!empty($this->_result)) ? $this->_result[0] : [];
        }

        public function count()
        {
            return $this->_count;
        }

        public function lastInsertID()
        {
            return $this->_lastInsertID;
        }

        public function showColumns($table)
        {
            $sql = "SHOW COLUMNS FROM {$table}";
            $columns = [];
            $column = array();

            if(!$this->query($sql)->error())
            {
                $res = $this->query($sql)->result();

                foreach($res as $val)
                {
                    $columns["field"] = $val->Field;
                    $columns["type"] = $val->Type;
                    $columns["null"] = $val->Null;

                    array_push($column, $columns);

                    $columns = [];
                }

                return $column;
            }

            return $this->error();
        }

        public function result()
        {
            return $this->_result;
        }

        public function error()
        {
            return $this->_error;
        }

        public function showError()
        {
            return $this->errorInfo;
        }
    }
?>