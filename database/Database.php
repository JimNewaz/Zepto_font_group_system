<?php 
    class Database
    {
        private $host = 'localhost';    
        private $dbName = 'font-group';
        private $username = 'root';
        private $password = '';

        private $con; 

        public function __construct()
        {
            $this->connect();
        }

        // Database Connection 
        private function connect()
        {
            try{
                $this->con = new PDO("mysql:host={$this->host};dbname={$this->dbName}", $this->username, $this->password);
                $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(Exception $e){
                echo "Database connection failed: " . $e->getMessage();
                die();
            }
        }
        public function getConnection() {
            return $this->con;
        }

        public function lastInsertId() {
            return $this->con->lastInsertId();
        }

        // Dynamic optional parameter query
        public function query($sql, $params = []) {
            $stmt = $this->con->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        }
    }

?>