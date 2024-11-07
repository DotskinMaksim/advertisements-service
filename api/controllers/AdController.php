<?php


class AdController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Получение списка объявлений с параметрами сортировки
   public function getAds($page = 1, $limit = 10, $sort = 'created_at', $order = 'DESC') {
    $offset = ($page - 1) * $limit;

    // Проверка и настройка сортировки
    if (!in_array($sort, ['price', 'created_at'])) {
        $sort = 'created_at';
    }
    if (!in_array($order, ['ASC', 'DESC'])) {
        $order = 'DESC';
    }

    // Подсчет общего количества объявлений
    $totalSql = "SELECT COUNT(*) FROM ads";
    $totalStmt = $this->pdo->prepare($totalSql);
    $totalStmt->execute();
    $totalAds = $totalStmt->fetchColumn();

    // Получаем список объявлений
    $sql = "SELECT id, title, main_image FROM ads ORDER BY $sort $order LIMIT $offset, $limit";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return json_encode([
        'ads' => $result,
        'total' => (int)$totalAds
    ]);
}


    public function getAdsByUser($userId) {
        $sql = "SELECT title, main_image, price, created_at  FROM ads WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }

   public function getAd($id) {
    $sql = "SELECT title, price, main_image, description, additional_images FROM ads WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Декодируем дополнительные изображения из JSON
        $result['additional_images'] = json_decode($result['additional_images'], true);
        return json_encode($result);
    } else {
        http_response_code(404);
        return json_encode(['message' => 'Ad not found']);
    }
}

    public function createAd($data) {
        // Валидация данных
        if (empty($data['title']) || strlen($data['title']) > 200) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid title']);
        return;
        }
        if (empty($data['description']) || strlen($data['description']) > 1000) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid description']);
            return;
        }
        if (!isset($data['price']) || !is_numeric($data['price'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid price']);
            return;
        }
        if (isset($data['images']) && count($data['images']) > 3) {
            http_response_code(400);
            echo json_encode(['message' => 'Too many additional images']);
            return;
        }
        $additionalImages = isset($data['images']) ? array_slice($data['images'], 1) : [];

        $sql = "INSERT INTO ads (title, description, price, main_image,additional_images, user_id) VALUES (:title, :description, :price, :main_image, :additional_images, :user_id)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'main_image' => $data['images'][0],
            'additional_images' => json_encode($additionalImages), // Кодируем в JSON
            'user_id' => $data['user_id'] // Убедитесь, что user_id передается из данных
        ]);

        return json_encode(['message' => 'Ad created successfully', 'id' => $this->pdo->lastInsertId()]);
    }

    public function deleteAd($adId) {
    // Проверка существования объявления
    $sqlCheck = "SELECT * FROM ads WHERE id = :id";
    $stmtCheck = $this->pdo->prepare($sqlCheck);
    $stmtCheck->execute(['id' => $adId]);

    if ($stmtCheck->rowCount() === 0) {
        http_response_code(404);
        return json_encode(['message' => 'Ad not found']);
    }

    // Удаление объявления
    $sql = "DELETE FROM ads WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $adId]);

    return json_encode(['message' => 'Ad deleted successfully']);
}
}
