<?php

error_reporting(E_ALL);
ini_set('display_error',1);



class Family{

    private $id;
    private $cpf;
    private $memberQuantity;
    private $income;
    private $idPerson;
    private $create_at;
    private $updated_at;


    private $connection;
    private $table = "familias";

    
    public function __construct($database){
        $this->connection = $database;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE IF NOT EXISTS $this->table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            quant_membros INT,
            renda TEXT,
            id_pessoa INT,
            FOREIGN KEY(id_pessoa) REFERENCES pessoas(id),
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $this->connection->exec($sql);
    }


    public function getAllFamily(){
        $query = "SELECT * FROM $this->table";
        $familyList = $this->connection->prepare($query);
        $familyList->execute();
        return $familyList;
    }

    public function getFamilyById($id){
        $this->id = $id;
        $query = 'SELECT * FROM '.$this->table.' WHERE id= ? LIMIT 0,1'; 
        $family = $this->connection->prepare($query);
        $family->execute([$this->id]);
        return $family;

    }

    public function insertFamily($params){
        try{

            $this->memberQuantity = $params["memberQuantity"];
            $this->income =$params["income"];
            $this->idPerson = $params["idPerson"];

            $query = 'INSERT INTO '.$this->table.' 
            SET 
            quant_membros=:memberQuantity,
            renda=:income,
            id_pessoa=:idPerson';

            $family = $this->connection->prepare($query);

            $family->bindValue('memberQuantity', $this->memberQuantity);
            $family->bindValue('income',$this->income);
            $family->bindValue('idPerson', $this->idPerson);

            if($family->execute()){
                return true;
            }else{
                return false;
            }
           

        }catch(PDOException $exeption){
            echo $exeption->getMessage();
        }
    }

    public function updateFamily($params){
        try{

            $this->id = $params["id"];
            $this->memberQuantity = $params["memberQuantity"];
            $this->income =$params["income"];
            $this->idPerson = $params["idPerson"];

            $query = 'UPDATE '.$this->table.' 
            SET 
            quant_membros=:memberQuantity,
            renda=:income,
            id_pessoa=:idPerson
            WHERE id = :id';

            $family = $this->connection->prepare($query);
    
            $family->bindValue('id', $this->id);
            $family->bindValue('memberQuantity', $this->memberQuantity);
            $family->bindValue('income',$this->income);
            $family->bindValue('idPerson', $this->idPerson);

            if($family->execute()){
                return true;
            }else{
                return false;
            }
           

        }catch(PDOException $exeption){
            echo $exeption->getMessage();
        }
    }

    public function deleteFamilyById($id){
        try{
            $this->id = $id;
            $query = 'DELETE FROM '.$this->table.' WHERE id = :id';
            $family = $this->connection->prepare($query);            
            $family->bindValue('id', $this->id);

            if($family->execute()){
                return true;
            }else{
                return false;
            }
        }catch(PDOException $exeption){
            echo $exeption->getMessage();
        }

    }

}