<?php
class Database{
// put your own details 
    private $host = "localhost";
    private $dbname = "api"; // the database you created earlier
    private $username = "root";  // your username
    private $password = "";      //password
    public $conn;
    public function getConnection(){
        $this->conn = null;
     try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->username, $this->password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>