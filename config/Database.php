<?php

class Database
{

    private $host = 'localhost';
    private $dbName = 'gestao_ong';
    private $userName  = 'root';
    private $password = '';
    

    public function getConnection()
    {
        try {
            $pdo = new PDO("mysql:host=localhost", $this->userName, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbname = "`" . str_replace("`", "``", $this->dbName) . "`";
            $pdo->query("CREATE DATABASE IF NOT EXISTS $dbname");
            $pdo->query("use $dbname");
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
        }
        return $pdo;
    }
}
