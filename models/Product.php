<?php

error_reporting(E_ALL);
ini_set('display_error',1);



class Product{

    private $id;
    private $name;
    private $quantity;
    private $description;
    private $create_at;
    private $updated_at;


    private $connection;
    private $table = "produtos";

    
    public function __construct($database){
        $this->connection = $database;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE IF NOT EXISTS $this->table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255),
            descricao TEXT,
            quantidade INTEGER,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $this->connection->exec($sql);
    }


    public function getAllProducts(){
        $query = "SELECT * FROM $this->table";
        $productList = $this->connection->prepare($query);
        $productList->execute();
        return $productList;
    }

    public function getProductById($id){
        $this->id = $id;
        $query = 'SELECT * FROM '.$this->table.' WHERE id= ? LIMIT 0,1'; 
        $product = $this->connection->prepare($query);
        $product->execute([$this->id]);
        return $product;

    }

    public function insertProduct($params){
        try{

            $this->name = $params["name"];
            $this->quantity =$params["quantity"];
            $this->description =$params["description"];

            $query = 'INSERT INTO '.$this->table.' 
            SET 
            nome=:name,
            quantidade=:quantity,
            descricao=:description';

            $product = $this->connection->prepare($query);

            $product->bindValue('name', $this->name);
            $product->bindValue('quantity',$this->quantity);
            $product->bindValue('description', $this->description);

            if($product->execute()){
                return true;
            }else{
                return false;
            }
           

        }catch(PDOException $exeption){
            echo $exeption->getMessage();
        }
    }

    public function updateProduct($params){
        try{

            $this->id = $params["id"];
            $this->name = $params["name"];
            $this->quantity =$params["quantity"];
            $this->description =$params["description"];

            $query = 'UPDATE '.$this->table.' 
            SET 
            nome=:name,
            quantidade=:quantity,
            descricao=:description 
            WHERE id = :id';

            $product = $this->connection->prepare($query);
            
            $product->bindValue('id', $this->id);
            $product->bindValue('name', $this->name);
            $product->bindValue('quantity',$this->quantity);
            $product->bindValue('description', $this->description);

            if($product->execute()){
                return true;
            }else{
                return false;
            }
           

        }catch(PDOException $exeption){
            echo $exeption->getMessage();
        }
    }

    public function deleteProductById($id){
        try{
            $this->id = $id;
            $query = 'DELETE FROM '.$this->table.' WHERE id = :id';
            $product = $this->connection->prepare($query);            
            $product->bindValue('id', $this->id);

            if($product->execute()){
                return true;
            }else{
                return false;
            }
        }catch(PDOException $exeption){
            echo $exeption->getMessage();
        }

    }

}