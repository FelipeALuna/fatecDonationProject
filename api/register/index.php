<?php

error_reporting(E_ALL);
ini_set('display_error', 1);
Header('Acess-Contro-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Acess-Control-Allow-Method: POST');

include_once('../../config/Database.php');
include_once('../../models/User.php');

$database = new Database();
$databaseConnection = $database->getConnection();
$user = new User($databaseConnection);
$requestedMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestedMethod) {

    case "POST":
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data)) {
            $params = [
                "email" => $data->email,
                "password" => $data->password,
                "name" => $data->name,
                "adress" => $data->adress,
                "phone" => $data->phone
            ];
        } else if (isset($_POST)) {
            $params = [
                "name" => $_POST["name"],
                "email" => $_POST["email"],
                "password" => $_POST["password"],
                "adress" => $_POST["adress"],
                "phone" => $_POST["phone"]
            ];
        } else {
            echo json_encode(["message" => "Falha ao criar usuario."]);
        }

        echo json_encode ($user->insertUser($params));

        break;     
        
    default:
        http_response_code(405);
        echo json_encode(["message" => "Invalid Method"]);
        break;
}
