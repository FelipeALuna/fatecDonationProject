<?php

error_reporting(E_ALL);
ini_set('display_error',1);
include_once('BasketProduct.php');



class Basket{

    private $id;
    private $quantity;
    private $description;
    private $productList;
    private $create_at;
    private $updated_at;
    private $database;

    private $connection;
    private $table = "CestaDoMes";

    
    public function __construct($database){
        $this->connection = $database;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE IF NOT EXISTS $this->table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            descricao TEXT,
            quantidade INTEGER,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $this->connection->exec($sql);
    }


    public function getAllBasket(){
        $query = "SELECT * FROM $this->table";
        $basketList = $this->connection->prepare($query);
        $basketList->execute();
        return $basketList;
    }

    public function getBasketById($id){
        $this->id = $id;
        $query = 'SELECT * FROM '.$this->table.' WHERE id= ? LIMIT 0,1'; 
        $basket = $this->connection->prepare($query);
        $basket->execute([$this->id]);
        return $basket;

    }

    public function insertBasket($params){
        try{

            $this->quantity =$params["quantity"];
            $this->description =$params["description"];
            $this->productList = $params["products"];
           
            $query = 'INSERT INTO '.$this->table.' 
            SET 
            quantidade=:quantity,
            descricao=:description';

            $basket = $this->connection->prepare($query);

            $basket->bindValue('quantity',$this->quantity);
            $basket->bindValue('description', $this->description);

            if($basket->execute()){
                $basketRecordId = $this->connection->lastInsertId();
                $basketProduct = new BasketProduct($this->connection);
                foreach ($this->productList as $product) {
                    $basketProductRecord = [
                        "productId"=>$product->id,
                        "basketId"=> $basketRecordId
                    ];
                    $basketProduct->insertBasketProduct($basketProductRecord);
                }
                return true;
            }else{
                return false;
            }
           

        }catch(PDOException $exeption){
            echo $exeption->getMessage();
        }
    }

    public function updateBasket($params){
        try{

            $this->id = $params["id"];
            $this->quantity =$params["quantity"];
            $this->description =$params["description"];

            $query = 'UPDATE '.$this->table.' 
            SET 
            quantidade=:quantity,
            descricao=:description 
            WHERE id = :id';

            $basket = $this->connection->prepare($query);
            
            $basket->bindValue('id', $this->id);
            $basket->bindValue('quantity',$this->quantity);
            $basket->bindValue('description', $this->description);

            if($basket->execute()){
                return true;
            }else{
                return false;
            }
           

        }catch(PDOException $exeption){
            echo $exeption->getMessage();
        }
    }

    public function deleteBasketById($id){
        try{
            $this->id = $id;
            $query = 'DELETE FROM '.$this->table.' WHERE id = :id';
            $basket = $this->connection->prepare($query);            
            $basket->bindValue('id', $this->id);

            if($basket->execute()){               
                return true;
            }else{
                return false;
            }
        }catch(PDOException $exeption){
            echo $exeption->getMessage();
        }

    }

}