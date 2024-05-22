<?php

error_reporting(E_ALL);
ini_set('display_error', 1);



class BasketFamily
{

    private $id;
    private $pickUpDate;
    private $quantity;
    private $familyId;
    private $basketId;
    private $create_at;
    private $updated_at;


    private $connection;
    private $table = "cesta_mes_familias";


    public function __construct($database)
    {
        $this->connection = $database;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE IF NOT EXISTS $this->table (
            id_cesta_mes_familias INTEGER PRIMARY KEY AUTO_INCREMENT,
            quantidade INTEGER NOT NULL,
            data_retirada DATE NOT NULL,
            id_cesta_mes INTEGER,
            id_familias INTEGER,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY(id_cesta_mes) REFERENCES cesta_mes(id),
            FOREIGN KEY(id_familias) REFERENCES familias(id)
        )";

        $this->connection->exec($sql);
    }


    public function getAllBasketFamily()
    {
        $query = "SELECT * FROM $this->table";
        $basketFamilyList = $this->connection->prepare($query);
        $basketFamilyList->execute();
        return $basketFamilyList;
    }

    public function getBasketFamilyById($id)
    {
        $this->id = $id;
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id_cesta_mes_familias= ? LIMIT 0,1';
        $basketFamily = $this->connection->prepare($query);
        $basketFamily->execute([$this->id]);
        return $basketFamily;
    }

    public function insertBasketFamily($params)
    {
        try {

            $this->pickUpDate = $params["pickUpDate"];
            $this->quantity = $params["quantity"];
            $this->familyId = $params["familyId"];
            $this->basketId = $params["basketId"];

            $query = 'INSERT INTO ' . $this->table . ' 
            SET 
            quantidade=:quantity,
            data_retirada=:pickUpDate,
            id_cesta_mes=:basketId,
            id_familias=:familyId';

            $basketFamily = $this->connection->prepare($query);

            $basketFamily->bindValue('pickUpDate', $this->pickUpDate);
            $basketFamily->bindValue('quantity', $this->quantity);
            $basketFamily->bindValue('basketId', $this->basketId);
            $basketFamily->bindValue('familyId', $this->familyId);

            if ($basketFamily->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
        }
    }

    public function updateBasketFamily($params)
    {
        try {

            $this->id = $params["id"];
            $this->quantity = $params["quantity"];
            $this->pickUpDate = $params["pickUpDate"];
            $this->familyId = $params["familyId"];
            $this->basketId = $params["basketId"];

            $query = 'UPDATE ' . $this->table . ' 
            SET 
            quantidade=:quantity,
            data_retirada=:pickUpDate,
            id_cesta_mes=:basketId,
            id_familias=:familyId 
            WHERE id = :id';

            $basketFamily = $this->connection->prepare($query);

            $basketFamily->bindValue('id', $this->id);
            $basketFamily->bindValue('pickUpDate', $this->pickUpDate);
            $basketFamily->bindValue('familyId', $this->familyId);
            $basketFamily->bindValue('basketId', $this->basketId);
            $basketFamily->bindValue('quantity', $this->quantity);

            if ($basketFamily->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
        }
    }

    public function deleteBasketFamilyById($id)
    {
        try {
            $this->id = $id;
            $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
            $basketFamily = $this->connection->prepare($query);
            $basketFamily->bindValue('id', $this->id);

            if ($basketFamily->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
        }
    }
}
