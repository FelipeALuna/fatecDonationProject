<?php

error_reporting(E_ALL);
ini_set('display_error',1);



class BasketProduct{

    private $id;
    private $productId;
    private $basketId;
    private $create_at;
    private $updated_at;


    private $connection;
    private $table = "ProdutosCestaDoMes";

    
    public function __construct($database){
        $this->connection = $database;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE IF NOT EXISTS $this->table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cesta_do_mes_id INTEGER,
            produto_id INTEGER,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (cesta_do_mes_id) REFERENCES cestadomes(id),
            FOREIGN KEY (produto_id) REFERENCES produtos(id)
        )";
        
        $this->connection->exec($sql);
    }


    public function getAllBasketProduct(){
        $query = "SELECT * FROM $this->table";
        $basketList = $this->connection->prepare($query);
        $basketList->execute();
        return $basketList;
    }

    public function getBasketByProductId($id){
        $this->id = $id;
        $query = 'SELECT * FROM '.$this->table.' WHERE id= ? LIMIT 0,1'; 
        $basket = $this->connection->prepare($query);
        $basket->execute([$this->id]);
        return $basket;

    }

    
    public function getBasketByBasketId($id){
        $this->id = $id;
        $query = 'SELECT * FROM '.$this->table.' WHERE id= ? LIMIT 0,1'; 
        $basket = $this->connection->prepare($query);
        $basket->execute([$this->id]);
        return $basket;

    }

    
    public function getBasketByBasketProductId($id){
        $this->id = $id;
        $query = 'SELECT * FROM '.$this->table.' WHERE id= ? LIMIT 0,1'; 
        $basket = $this->connection->prepare($query);
        $basket->execute([$this->id]);
        return $basket;

    }

    public function insertBasketProduct($params){
        try{

            $this->productId =$params["productId"];
            $this->basketId =$params["basketId"];

            $query = 'INSERT INTO '.$this->table.' 
            SET 
            produto_id=:productId,
            cesta_do_mes_id=:basketId';

            $basket = $this->connection->prepare($query);

            $basket->bindValue('productId',$this->productId);
            $basket->bindValue('basketId', $this->basketId);

            if($basket->execute()){
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
            $this->productId =$params["productId"];
            $this->basketId =$params["basketId"];

            $query = 'UPDATE '.$this->table.' 
            SET 
            product_id=:product,
            cesta_do_mes_id=:basket 
            WHERE id = :id';

            $basket = $this->connection->prepare($query);
            
            $basket->bindValue('id', $this->id);
            $basket->bindValue('product',$this->productId);
            $basket->bindValue('basket', $this->basketId);

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