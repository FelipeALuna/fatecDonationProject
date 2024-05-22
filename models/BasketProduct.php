<?php

error_reporting(E_ALL);
ini_set('display_error', 1);
include_once('Product.php');

class BasketProduct
{

    private $id;
    private $productId;
    private $basketId;
    private $create_at;
    private $updated_at;


    private $connection;
    private $table = "produtos_cesta_mes";


    public function __construct($database)
    {
        $this->connection = $database;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE IF NOT EXISTS $this->table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cesta_do_mes_id INTEGER,
            produto_id INTEGER,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (cesta_do_mes_id) REFERENCES cesta_mes(id),
            FOREIGN KEY (produto_id) REFERENCES produtos(id)
        )";

        $this->connection->exec($sql);
    }


    public function getAllBasketProduct()
    {
        $query = "SELECT * FROM $this->table";
        $basketList = $this->connection->prepare($query);
        $basketList->execute();
        return $basketList;
    }

    public function getBasketByProductId($id)
    {
        $this->id = $id;
        $query = 'SELECT * FROM ' . $this->table . ' WHERE produto_id= ?';
        $basket = $this->connection->prepare($query);
        $basket->execute([$this->productId]);
        return $basket;
    }


    public function getBasketByBasketId($id)
    {
        $this->id = $id;
        $query = 'SELECT * FROM ' . $this->table . ' WHERE cesta_do_mes_id= ?';
        $basket = $this->connection->prepare($query);
        $basket->execute([$this->id]);
        return $basket;
    }

    public function insertBasketProduct($params)
    {
        try {

            $this->productId = $params["productId"];
            $this->basketId = $params["basketId"];

            $query = 'INSERT INTO ' . $this->table . ' 
            SET 
            produto_id=:productId,
            cesta_do_mes_id=:basketId';

            $basket = $this->connection->prepare($query);

            $basket->bindValue('productId', $this->productId);
            $basket->bindValue('basketId', $this->basketId);

            if ($basket->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
        }
    }

    public function updateBasketProductsOfBasket($basketId, $productList)
    {
        try {
            $this->basketId = $basketId;

            $query = 'SELECT * FROM ' . $this->table . ' WHERE  cesta_do_mes_id =:basketId';

            $basketProductConnection = $this->connection->prepare($query);
            $basketProductConnection->bindValue('basketId', $this->basketId);

            if ($basketProductConnection->execute()) {
                echo ($basketProductConnection->rowCount());
                while($basketProductRow = $basketProductConnection->fetch(PDO::FETCH_OBJ)) {
                    $this->deleteBasketById($basketProductRow->id);
                }
            }
            if (isset($productList)) {
                foreach ($productList as $product) {
                    $this->insertBasketProduct([
                        "basketId" => $basketId,
                        "productId" => $product->id
                    ]);
                }
            }

            return true;
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
            return false;
        }
    }

    public function deleteBasketById($id)
    {
        try {
            $this->id = $id;
            $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
            $basket = $this->connection->prepare($query);
            $basket->bindValue('id', $this->id);

            if ($basket->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
        }
    }
    public function deleteBasketProductByBasket($basketId){
        try {
            $this->basketId = $basketId;
            $query = 'DELETE FROM ' . $this->table . ' WHERE cesta_do_mes_id = :basketId';
            $basket = $this->connection->prepare($query);
            $basket->bindValue('basketId', $this->basketId);

            if ($basket->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
        }
    }
    public function getProductsOfBasket($basketId){
        
        $recordList = $this->getBasketByBasketId($basketId);
        $productList = [];
        while($record = $recordList->fetch(PDO::FETCH_OBJ)){
            $productId = (int)$record->produto_id;
            $product = new Product($this->connection);
            $product = $product->getProductById($productId)->fetch(PDO::FETCH_OBJ);
            $productRow = [
                "id"=>$product->id,
                "name"=>$product->nome,
                "description"=>$product->descricao,
                "quantity"=>$product->quantidade,
                "created_at"=>$product->criado_em,
                "updated_at"=>$product->atualizado_em
            ];
            array_push($productList,$productRow);
        }

        return $productList;
    }

}
