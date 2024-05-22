<?php

error_reporting(E_ALL);
ini_set('display_error', 1);



class DonationProduct
{

    private $id;
    private $idPerson;
    private $idProduct;
    private $quantity;
    private $donationDate;
    private $create_at;
    private $updated_at;
    private $approved;

    private $connection;
    private $table = "doador_produto";


    public function __construct($database)
    {
        $this->connection = $database;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE IF NOT EXISTS " .$this->table." (
            id_doador_produto INT AUTO_INCREMENT PRIMARY KEY,
            quantidade INTEGER NOT NULL,
            data_doacao DATE NOT NULL,
            id_produtos INTEGER,
            id_pessoa INTEGER,
            aprovado BOOLEAN,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY(id_produtos) REFERENCES produtos(id),
            FOREIGN KEY(id_pessoa) REFERENCES pessoas(id)
        );";

        $this->connection->exec($sql);
    }


    public function getAllDonationProduct()
    {
        $query = "SELECT * FROM $this->table";
        $donationProductList = $this->connection->prepare($query);
        $donationProductList->execute();
        return $donationProductList;
    }

    public function getDonationProductById($id)
    {
        $this->id = $id;
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id_doador_produto= ? LIMIT 0,1';
        $donationProduct = $this->connection->prepare($query);
        $donationProduct->execute([$this->id]);
        return $donationProduct;
    }

    public function insertDonationProduct($params)
    {
        try {

            $this->donationDate = $params["donationDate"];
            $this->quantity = $params["quantity"];
            $this->idProduct = $params["idProduct"];
            $this->idPerson = $params["idPerson"];
            $this->approved = $params["approved"];

            $query = 'INSERT INTO ' . $this->table . ' 
            SET 
            data_doacao=:donationDate,
            quantidade=:quantity,
            id_produtos=:idProduct,
            id_pessoa=:idPerson,
            aprovado=:approved';

            $donationProduct = $this->connection->prepare($query);

            $donationProduct->bindValue('donationDate', $this->donationDate);
            $donationProduct->bindValue('quantity', $this->quantity);
            $donationProduct->bindValue('idProduct', $this->idProduct);
            $donationProduct->bindValue('idPerson', $this->idPerson);
            $donationProduct->bindValue('approved',$this->approved);

            if ($donationProduct->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
        }
    }

    public function updateDonationProduct($params)
    {
        try {

            $this->id = $params["id"];
            $this->donationDate = $params["donationDate"];
            $this->quantity = $params["quantity"];
            $this->idProduct = $params["idProduct"];
            $this->idPerson = $params["idPerson"];
            $this->approved = $params["approved"];

            $query = 'UPDATE ' . $this->table . ' 
            SET 
            data_doacao=:donationDate,
            quantidade=:quantity,
            id_produtos=:idProduct,
            id_pessoa=:idPerson,
            aprovado=:approved 
            WHERE id_doador_produto = :id';

            $donationProduct = $this->connection->prepare($query);

            $donationProduct->bindValue('id', $this->id);
            $donationProduct->bindValue('donationDate', $this->donationDate);
            $donationProduct->bindValue('quantity', $this->quantity);
            $donationProduct->bindValue('idProduct', $this->idProduct);
            $donationProduct->bindValue('idPerson', $this->idPerson);
            $donationProduct->bindValue('approved',$this->approved);

            if ($donationProduct->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
        }
    }

    public function deleteDonationProductById($id)
    {
        try {
            $this->id = $id;
            $query = 'DELETE FROM ' . $this->table . ' WHERE id_doador_produto = :id';
            $donationProduct = $this->connection->prepare($query);
            $donationProduct->bindValue('id', $this->id);

            if ($donationProduct->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
        }
    }
}
