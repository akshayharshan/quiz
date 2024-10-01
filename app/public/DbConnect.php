<?php
class DbConnect
{
    private static $instance =  NULL;
    private $conn;
    private $servername = "mariadb";
    private $username = "root";
    private $password = "aqwe123";
    private $dbname = "quiz";

    private function __construct()
    {
        try {
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

            if ($this->conn->connect_error) {
                throw new Exception($this->conn->connect_error);
            }

            // $this->conn->close();
        } catch (Exception $e) {

            echo "Error: " . $e->getMessage();
        }
    }
    public static function getInstance()
    {

        if (!self::$instance) {
            self::$instance = new dbConnect();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

$instance = DbConnect::getInstance();
$conn = $instance->getConnection();