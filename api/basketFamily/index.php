<?php



require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
include_once('../../config/Database.php');
include_once('../../models/BasketFamily.php');
include_once('../../config/AuthKey.php');

error_reporting(E_ALL);
ini_set('display_error', 1);

Header('Acess-Contro-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Acess-Control-Allow-Method: POST');

$database = new Database();
$databaseConnection = $database->getConnection();
$basket = new BasketFamily($databaseConnection);
$requestedMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestedMethod) {
    case "GET":

        if (isset($_GET['id'])) {
            $basketRecord  = $basket->getBasketFamilyById($_GET['id']);

            if ($basketRecord->rowCount()) {

                while ($row = $basketRecord->fetch(PDO::FETCH_OBJ)) {
                    $basketFamilyRow = [
                        "id"=>$row->id_cesta_mes_familias,
                        "quantity"=>$row->quantidade,
                        "pickUpDate"=>$row->data_retirada,
                        "familyId"=>$row->id_cesta_mes,
                        "basketId"=>$row->id_familias,
                    ];
                    echo json_encode($basketFamilyRow);
                }
            } else {
                echo json_encode([
                    "message" => "Não foi encontrado nenhum registro"
                ]);
            }
        } else {
            $basketList  = $basket->getAllBasketFamily();
            if ($basketList->rowCount()) {
                $baskets = [];
                while ($row = $basketList->fetch(PDO::FETCH_OBJ)) {
                    $basketFamilyRow = [
                        "id"=>$row->id_cesta_mes_familias,
                        "quantity"=>$row->quantidade,
                        "pickUpDate"=>$row->data_retirada,
                        "familyId"=>$row->id_cesta_mes,
                        "basketId"=>$row->id_familias,
                    ];
                    array_push($baskets, $basketFamilyRow);
                }
                echo json_encode($baskets);
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
                "pickUpDate" => $data->pickUpDate,
                "quantity" => $data->quantity,
                "familyId" => $data->familyId,
                "basketId" => $data->basketId

            ];
        } else if (isset($_POST)) {
            $params = [
                "pickUpDate" => $_POST["pickUpDate"],
                "quantity" => $_POST["quantity"],
                "familyId" => $_POST["familyId"],
                "basketId" => $_POST["basketId"]
            ];
        } else {
            echo json_encode(["message" => "Falha ao criar registro."]);
        }

        if ($basket->insertBasketFamily($params)) {
            echo json_encode(["message" => "Cesta criada com sucesso!"]);
        } else {
            echo json_encode(["message" => "Falha ao criar registro."]);
        }

        break;
    case "PUT":
        $params = '';
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data) && isset($data->id)) {
            $params = [
                "id" => $data->id,
                "pickUpDate" => $data->pickUpDate,
                "quantity" => $data->quantity,
                "familyId" => $data->familyId,
                "basketId" => $data->basketId
            ];
        } else {
            echo json_encode(["message" => "Falha ao atualizar registro."]);
            exit;
        }

        if ($basket->updateBasketFamily($params)) {
            echo json_encode(["message" => "Registro atualizado com sucesso!"]);
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

        if ($basket->deleteBasketFamilyById($id)) {
            echo json_encode(["message" => "Registro excluido com sucesso!"]);
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
