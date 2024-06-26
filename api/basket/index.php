<?php



require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
include_once('../../config/Database.php');
include_once('../../models/Basket.php');
include_once('../../config/AuthKey.php');

error_reporting(E_ALL);
ini_set('display_error', 1);

Header('Acess-Contro-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Acess-Control-Allow-Method: POST');

$database = new Database();
$databaseConnection = $database->getConnection();
$basket = new Basket($databaseConnection);
$requestedMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestedMethod) {
    case "GET":

        if (isset($_GET['id'])) {
            $basketRecord  = $basket->getBasketById($_GET['id']);

            if ($basketRecord->rowCount()) {

                while ($row = $basketRecord->fetch(PDO::FETCH_OBJ)) {
                    $basketRow = [
                        "id"=> $row->id,
                        "description" => $row->quantidade,
                        "quantity"=> $row->descricao,
                        "created_at"=>$row->criado_em,
                        "updated_at"=>$row->atualizado_em
                    ];
                    echo json_encode($basketRow);
                }
            } else {
                echo json_encode([
                    "message" => "Não foi encontrado nenhuma cesta"
                ]);
            }
        } else {
            $basketList  = $basket->getAllBasket();
            if ($basketList->rowCount()) {
                $baskets = [];
                while ($row = $basketList->fetch(PDO::FETCH_OBJ)) {
                    $basketRow = [
                        "id"=> $row->id,
                        "description" => $row->quantidade,
                        "quantity"=> $row->descricao,
                        "created_at"=>$row->criado_em,
                        "updated_at"=>$row->atualizado_em
                    ];
                    $basketProducts = new BasketProduct($databaseConnection);
                    $basketProducts = $basketProducts->getProductsOfBasket($row->id);
                    $basketRow["products"] = $basketProducts;
                    array_push($baskets, $basketRow);
                }
                echo json_encode($baskets);
            } else {
                echo json_encode([
                    "message" => "Não foi encontrado nenhuma cesta"
                ]);
            }
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data)) {
            $params = [
                "quantity" => $data->quantity,
                "description" => $data->description,
                "products" => $data->products
            ];
        } else if (isset($_POST)) {
            $params = [
                "quantity" => $_POST["quantity"],
                "description" => $_POST["description"],
                "products" => $_POST["products"]
            ];
        } else {
            echo json_encode(["message" => "Falha ao criar cesta."]);
        }

        if ($basket->insertBasket($params)) {
            echo json_encode(["message" => "Cesta criada com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao criar cesta."]);
        }

        break;
    case "PUT":
        $params = '';
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data) && isset($data->id)) {
            $params = [
                "id" => $data->id,
                "quantity" => $data->quantity,
                "description" => $data->description,
                "products" => $data->products
            ];
        } else {
            echo json_encode(["message" => "Falha ao atualizar cesta."]);
            exit;
        }

        if ($basket->updateBasket($params)) {
            echo json_encode(["message" => "Cesta atualizada com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao atualizar cesta."]);
        }
        break;

    case "DELETE":

        $data = json_decode(file_get_contents('php://input'));
        $id = null;
        if (isset($data) && isset($data->id)) {
            $id = $data->id;
        } else {
            echo json_encode(["message" => "Falha ao excluir cesta."]);
            return;
        }

        if ($basket->deleteBasketById($id)) {
            echo json_encode(["message" => "Cesta excluida com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao excluir cesta."]);
            exit;
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Invalid Method"]);
        break;
}
