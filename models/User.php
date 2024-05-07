<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
error_reporting(E_ALL);
ini_set('display_error', 1);


class User
{

    private $id;
    private $email;
    private $password;
    private $adress;
    private $name;
    private $phone;
    private $create_at;
    private $updated_at;


    private $connection;
    private $table = "pessoas";


    public function __construct($database)
    {
        $this->connection = $database;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE IF NOT EXISTS $this->table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100),
            nome VARCHAR(250),
            endereco VARCHAR(255),
            telefone VARCHAR(15),
            senha VARCHAR(255),
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $this->connection->exec($sql);
    }

    private function getUserDataByEmail($email)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email=:email LIMIT 0,1';
        $userConnection = $this->connection->prepare($query);
        $userConnection->bindValue('email', $email);
        $userConnection->execute();
        if ($userConnection->rowCount()) {
            $userObject = $userConnection->fetch(PDO::FETCH_OBJ);
            return [
                "userFound" => true,
                "userData" => $userObject
            ];
        } else {
            return [
                "userFound" => false,
                "password" => ""
            ];
        }
    }

    public function insertUser($params)
    {
        try {
            $emailExists = $this->getUserDataByEmail($params["email"]);
            if ($emailExists["userFound"]) {
                return [
                    "error" => true,
                    "message" => "E-mail ja vinculado a outra conta"
                ];
            } else {

                $this->email = $params["email"];
                $this->password = $params["password"];
                $this->phone = $params["phone"];
                $this->adress = $params["adress"];
                $this->name = $params["name"];
                $this->password = password_hash($this->password, PASSWORD_DEFAULT);
                $query = 'INSERT INTO ' . $this->table . ' 
                SET
                telefone=:phone,
                endereco=:adress,
                nome=:name,
                email=:email,
                senha=:password';

                $userConnection = $this->connection->prepare($query);

                $userConnection->bindValue('email', $this->email);
                $userConnection->bindValue('password', $this->password);
                $userConnection->bindValue('phone', $this->phone);
                $userConnection->bindValue('adress', $this->adress);
                $userConnection->bindValue('name', $this->name);

                if ($userConnection->execute()) {
                    return [
                        "error" => false,
                        "message" => "Usuário criado com sucesso!"
                    ];
                } else {
                    return [
                        "error" => true,
                        "message" => "Falha ao criar usuário!"
                    ];
                }
            }
        } catch (PDOException $exeption) {
            echo $exeption->getMessage();
        }
    }

    public function login($params)
    {
        try {
            $userData = $this->getUserDataByEmail($params["email"]);
            if ($userData["userFound"]) {
                $userData = $userData['userData'];
                if (password_verify($params['password'], $userData->senha)) {
                    $issueDate = time();
                    $expirationdate = time() * 3600;
                    $payload = [
                        "error" => false,
                        'iss' => $userData->id,
                        'aud' => 'http://localhost',
                        'iat' => $issueDate,
                        'exp' => $expirationdate,
                        'name'=>$userData->nome,
                        'message' => "Login efetuado com sucesso!",
                        'userEmail' => $userData->email
                    ];
                    $authKey = new AuthKey();
                    $authKey = $authKey->getAuthKey();
                    $jwtToken = JWT::encode($payload, $authKey, 'HS256');
                    return [
                        "token" => $jwtToken,
                        'expires' => $expirationdate
                    ];
                } else {

                    return [
                        "error" => true,
                        "message" => "Senha inválida/incorreta."
                    ];
                }
            } else {
                return [
                    "error" => true,
                    "message" => "E-mail inválido/incorreto."
                ];
            }
        } catch (PDOException $exeption) {
            return [
                "error" => true,
                "message" => "Erro ao realizar Login."
            ];
        }
    }
}
