<?php

require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
include_once('../../config/Database.php');
include_once('../../models/DonationProduct.php');
include_once('../../config/AuthKey.php');

error_reporting(E_ALL);
ini_set('display_error', 1);

Header('Acess-Contro-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Acess-Control-Allow-Method: POST');

$database = new Database();
$databaseConnection = $database->getConnection();
$donationProduct = new DonationProduct($databaseConnection);
$requestedMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestedMethod) {
    case "GET":

        if (isset($_GET['id'])) {
            $donationProductRecord  = $donationProduct->getDonationProductById($_GET['id']);

            if ($donationProductRecord->rowCount()) {

                while ($row = $donationProductRecord->fetch(PDO::FETCH_OBJ)) {
                    $donationProductRow = [
                        "id"=>$row->id_doador_produto,
                        "quantity"=>$row->quantidade,
                        "idProduct"=>$row->id_produtos,
                        "idPerson"=>$row->id_pessoa,
                        "donationDate"=>$row->data_doacao,
                        "created_at"=>$row->criado_em,
                        "updated_at"=>$row->atualizado_em
                    ];
                    echo json_encode($donationProductRow);
                }
            } else {
                echo json_encode([
                    "message" => "Não foi encontrado nenhum registro"
                ]);
            }
        } else {
            $donationProductList  = $donationProduct->getAllDonationProduct();
            if ($donationProductList->rowCount()) {
                $donationProducts = [];
                while ($row = $donationProductList->fetch(PDO::FETCH_OBJ)) {
                    $donationProductRow = [
                        "id"=>$row->id_doador_produto,
                        "quantity"=>$row->quantidade,
                        "idProduct"=>$row->id_produtos,
                        "idPerson"=>$row->id_pessoa,
                        "donationDate"=>$row->data_doacao,
                        "created_at"=>$row->criado_em,
                        "updated_at"=>$row->atualizado_em
                    ];
                    array_push($donationProducts, $donationProductRow);
                }
                echo json_encode($donationProducts);
            } else {
                echo json_encode([
                    "message" => "Não foi encontrado nenhum registro"
                ]);
            }
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data)) {
            $params = [
                "quantity"=>$data->quantity,
                "idProduct"=>$data->idProduct,
                "idPerson"=>$data->idPerson,
                "donationDate"=>$data->donationDate
            ];
        } else if (isset($_POST)) {
            $params = [
                "quantity"=>$_POST["quantity"],
                "idProduct"=>$_POST["idProduct"],
                "idPerson"=>$_POST["idPerson"],
                "donationDate"=>$_POST["donationDate"]
            ];
        } else {
            echo json_encode(["message" => "Falha ao criar registro."]);
        }

        if ($donationProduct->insertDonationProduct($params)) {
            echo json_encode(["message" => "Registro criado com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao criar registro."]);
        }

        break;
    case "PUT":
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data) && isset($data->id)) {
            $params = [
                "id"=>$data->id,
                "quantity"=>$data->quantity,
                "idProduct"=>$data->idProduct,
                "idPerson"=>$data->idPerson,
                "donationDate"=>$data->donationDate
            ];
        } else {
            echo json_encode(["message" => "Falha ao atualizar registro."]);
            exit;
        }

        if ($donationProduct->updateDonationProduct($params)) {
            echo json_encode(["message" => "Produto atualizado com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao atualizar registro."]);
        }
        break;

    case "DELETE":
        
        $data = json_decode(file_get_contents('php://input'));
        $id = null;
        if (isset($data) && isset($data->id)) {
            $id = $data->id;
        } else {
            echo json_encode(["message" => "Falha ao excluir registro."]);
            return;
        }

        if ($donationProduct->deleteDonationProductById($id)) {
            echo json_encode(["message" => "Registro excluido com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao excluir registro."]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["message" => "Método invalido"]);
        break;
}
