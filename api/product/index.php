<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
include_once('../../config/Database.php');
include_once('../../models/Product.php');
include_once('../../config/AuthKey.php');

error_reporting(E_ALL);
ini_set('display_error', 1);

Header('Acess-Contro-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Acess-Control-Allow-Method: POST');

$database = new Database();
$databaseConnection = $database->getConnection();
$product = new Product($databaseConnection);
$requestedMethod = $_SERVER['REQUEST_METHOD'];


switch ($requestedMethod) {
  
    case "GET":
        if (isset($_GET['id'])) {
            $productRecord  = $product->getProductById($_GET['id']);

            if ($productRecord->rowCount()) {

                while ($row = $productRecord->fetch(PDO::FETCH_OBJ)) {
                    $productRow = [
                        "id"=>$row->id,
                        "name"=>$row->nome,
                        "description"=>$row->descricao,
                        "quantity"=>$row->quantidade,
                        "created_at"=>$row->criado_em,
                        "updated_at"=>$row->atualizado_em
                    ];
                    echo json_encode($productRow);
                    break;
                }
            } else {
                echo json_encode([
                    "message" => "Não foi encontrado nenhum produto"
                ]);
                break;
            }
        } else {
            $productList  = $product->getAllProducts();
            if ($productList->rowCount()) {
                $products = [];
                while ($row = $productList->fetch(PDO::FETCH_OBJ)) {
                    $productRow = [
                        "id"=>$row->id,
                        "name"=>$row->nome,
                        "description"=>$row->descricao,
                        "quantity"=>$row->quantidade,
                        "created_at"=>$row->criado_em,
                        "updated_at"=>$row->atualizado_em
                    ];
                    array_push($products, $productRow);
                }
                echo json_encode($products);
                break;
            } else {
                echo json_encode([
                    "message" => "Não foi encontrado nenhum produto"
                ]);
                break;
            }
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data)) {
            $params = [
                "name" => $data->name,
                "quantity" => $data->quantity,
                "description" => $data->description
            ];
        } else if (isset($_POST)) {
            $params = [
                "name" => $_POST["name"],
                "quantity" => $_POST["quantity"],
                "description" => $_POST["description"]
            ];
        } else {
            echo json_encode(["message" => "Falha ao criar produto."]);
        }

        if ($product->insertProduct($params)) {
            echo json_encode(["message" => "Produto criado com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao criar produto."]);
        }

        break;
    case "PUT":
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data) && isset($data->id)) {
            $params = [
                "id" => $data->id,
                "name" => $data->name,
                "quantity" => $data->quantity,
                "description" => $data->description
            ];
        } else {
            echo json_encode(["message" => "Falha ao atualizar produto."]);
            exit;
        }

        if ($product->updateProduct($params)) {
            echo json_encode(["message" => "Produto atualizado com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao atualizar produto."]);
        }
        break;

    case "DELETE":
        
        $data = json_decode(file_get_contents('php://input'));
        $id = null;
        if (isset($data) && isset($data->id)) {
            $id = $data->id;
        } else {
            echo json_encode(["message" => "Falha ao excluir produto."]);
            return;
        }

        if ($product->deleteProductById($id)) {
            echo json_encode(["message" => "Produto excluido com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao excluir produto."]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["message" => "Invalid Method"]);
        break;
}
