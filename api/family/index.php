<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
include_once('../../config/Database.php');
include_once('../../models/Family.php');
include_once('../../config/AuthKey.php');

error_reporting(E_ALL);
ini_set('display_error', 1);

Header('Acess-Contro-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Acess-Control-Allow-Method: POST');


$database = new Database();
$databaseConnection = $database->getConnection();
$family = new Family($databaseConnection);
$requestedMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestedMethod) {
    case "GET":

        if (isset($_GET['id'])) {
            $familyRecord  = $family->getFamilyById($_GET['id']);

            if ($familyRecord->rowCount()) {

                while ($row = $familyRecord->fetch(PDO::FETCH_OBJ)) {
                    $familyRow = [
                        "id"=>$row->id,
                        "memberQuantity"=>$row->quant_membros,
                        "income"=>$row->renda,
                        "idPerson"=>$row->id_pessoa,
                        "created_at"=>$row->criado_em,
                        "updated_at"=>$row->atualizado_em
                    ];
                    echo json_encode($familyRow);
                }
            } else {
                echo json_encode([
                    "message" => "Não foi encontrado nenhuma familia."
                ]);
            }
        } else {
            $familyList  = $family->getAllFamily();
            if ($familyList->rowCount()) {
                $families = [];
                while ($row = $familyList->fetch(PDO::FETCH_OBJ)) {
                    $familyRow = [
                        "id"=>$row->id,
                        "memberQuantity"=>$row->quant_membros,
                        "income"=>$row->renda,
                        "idPerson"=>$row->id_pessoa,
                        "created_at"=>$row->criado_em,
                        "updated_at"=>$row->atualizado_em
                    ];
                    array_push($families, $familyRow);
                }
                echo json_encode($families);
            } else {
                echo json_encode([
                    "message" => "Não foi encontrado nenhuma familia."
                ]);
            }
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data)) {
            $params = [
                "memberQuantity" => $data->memberQuantity,
                "idPerson" => $data->idPerson,
                "income" => $data->income
            ];
        } else if (isset($_POST)) {
            $params = [
                "memberQuantity" => $_POST["memberQuantity"],
                "idPerson" => $_POST["idPerson"],
                "income" => $_POST["income"]
            ];
        } else {
            echo json_encode(["message" => "Falha ao criar familia."]);
        }

        if ($family->insertfamily($params)) {
            echo json_encode(["message" => "Familia criada com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao criar familia.."]);
        }

        break;
    case "PUT":
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data) && isset($data->id)) {
            $params = [
                "id" => $data->id,
                "memberQuantity" => $data->memberQuantity,
                "idPerson" => $data->idPerson,
                "income" => $data->income
            ];
        } else {
            echo json_encode(["message" => "Falha ao atualizar familia."]);
            exit;
        }

        if ($family->updatefamily($params)) {
            echo json_encode(["message" => "familia. atualizado com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao atualizar familia."]);
        }
        break;

    case "DELETE":
        
        $data = json_decode(file_get_contents('php://input'));
        $id = null;
        if (isset($data) && isset($data->id)) {
            $id = $data->id;
        } else {
            echo json_encode(["message" => "Falha ao excluir familia."]);
            return;
        }

        if ($family->deletefamilyById($id)) {
            echo json_encode(["message" => "familia. excluido com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao excluir familia."]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["message" => "Invalid Method"]);
        break;
}
