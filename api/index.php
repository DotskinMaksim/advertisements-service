<?php
require_once '../config/config.php';
require_once 'controllers/AdController.php';
require_once 'controllers/UserController.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS"); // Добавьте OPTIONS в список разрешённых методов
header("Access-Control-Allow-Headers: Content-Type");

// Если метод запроса OPTIONS, возвращаем успешный ответ
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
global $pdo;

header("Content-Type: application/json");

// Получаем метод запроса и определяем путь
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$request[0] = str_replace(["\n", "\r"], '', $request[0]);
// Инициализируем контроллеры
$adController = new AdController($pdo);
$userController = new UserController($pdo);

// Обработка запросов
switch ($request[0]) {

    case 'ads':
        switch ($method) {
            case 'GET':
                if (isset($request[1])) {
                    // Получение конкретного объявления
                    echo $adController->getAd($request[1]);
                }
                else {
                    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
                    $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                    echo $adController->getAds($page, $limit, $sort, $order);
                }
                break;
            case 'POST':
                // Создание нового объявления
                $inputData = json_decode(file_get_contents('php://input'), true);
                echo $adController->createAd($inputData);
                break;

            case 'DELETE':
            if (isset($request[1])) {
                // Удаление объявления
                echo $adController->deleteAd($request[1]);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Ad ID is required']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;

        }
        break;
    case 'register':
        if ($method === 'POST') {
            $inputData = json_decode(file_get_contents('php://input'), true);
            echo $userController->registerUser($inputData);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
        }
        break;
    case 'login':
        if ($method === 'POST') {
            $inputData = json_decode(file_get_contents('php://input'), true);
            echo $userController->loginUser($inputData);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
        }
        break;
    case 'users':
        switch ($method) {
            case 'GET':
                if (isset($request[1])) {
                    // Получение конкретного пользователя (по ID)
                    echo $userController->getUser($request[1]);
                } else {
                    // Получение списка пользователей с параметрами сортировки
                    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'name'; // По умолчанию сортировка по имени
                    $order = isset($_GET['order']) ? $_GET['order'] : 'ASC'; // По умолчанию по возрастанию
                    echo $userController->getUsers($sort, $order);
                }
                break;
            case 'POST':
                // Создание нового пользователя
                $inputData = json_decode(file_get_contents('php://input'), true);
                echo $userController->createUser($inputData);
                break;

            case 'DELETE':
                if (isset($request[1])) {
                    // Удаление пользователя по ID
                    echo $userController->deleteUser($request[1]);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'User ID is required']);
                }
            break;
            default:
                http_response_code(405);
                echo json_encode(['message' => 'Method not allowed']);
                break;
        }
        break;

    case 'user-ads':
    switch ($method) {
        case 'GET':
            if (isset($request[1])) {
                // Fetch user-specific ads based on the user ID passed
                echo $adController->getAdsByUser($request[1]);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'User ID is required']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
    break;

    default:
        http_response_code(404);
//        echo json_encode(['message' => 'Not founddddddd']);
            echo json_encode(['message' => 'Not found by :'.$request[0]]);

        break;
}
?>
