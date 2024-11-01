<?php
global $pdo;
header("Content-Type: application/json");
include 'conf.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        handleGet($pdo);
        break;
    case 'POST':
        handlePost($pdo, $input);
        break;
    case 'PUT':
        handlePut($pdo, $input);
        break;
    case 'DELETE':
        handleDelete($pdo, $input);
        break;
    default:
        echo json_encode(['message' => 'Invalid request method']);
        break;
}

function handleGet($pdo) {
    $sql = "SELECT * FROM ads";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
}

function handlePost($pdo, $input) {
    $sql = "INSERT INTO ads (title, description, price, main_image, additional_images) VALUES (:title, :description, :price, :main_image, :additional_images)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'title' => $input['title'],
        'description' => $input['description'],
        'price' => $input['price'],
        'main_image' => $input['main_image'],
        'additional_images' => json_encode($input['additional_images']) // Преобразуем массив в JSON
    ]);
    echo json_encode(['message' => 'Ad created successfully']);
}

function handlePut($pdo, $input) {
    $sql = "UPDATE ads SET title = :title, description = :description, price = :price, main_image = :main_image, additional_images = :additional_images WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'title' => $input['title'],
        'description' => $input['description'],
        'price' => $input['price'],
        'main_image' => $input['main_image'],
        'additional_images' => json_encode($input['additional_images']),
        'id' => $input['id']
    ]);
    echo json_encode(['message' => 'Ad updated successfully']);
}

function handleDelete($pdo, $input) {
    $sql = "DELETE FROM ads WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $input['id']]);
    echo json_encode(['message' => 'Ad deleted successfully']);
}
?>
