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
    if ($sort == 'price') {
        $sort = 'price';
    } elseif ($sort == 'created_at') {
        $sort = 'created_at';
    } elseif ($sort == 'title') {
        $sort = 'title';
    } else {
        $sort = 'created_at'; // По умолчанию сортировка по дате
    }

    // Проверка на порядок сортировки
    if (!in_array($order, ['ASC', 'DESC'])) {
        $order = 'DESC';
    }

    // Подсчет общего количества объявлений
    $totalSql = "SELECT COUNT(*) FROM ads";
    $totalStmt = $this->pdo->prepare($totalSql);
    $totalStmt->execute();
    $totalAds = $totalStmt->fetchColumn();

    // Получаем список объявлений с учетом сортировки
    $sql = "SELECT id, title, main_image, price, created_at FROM ads ORDER BY $sort $order LIMIT $offset, $limit";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Формируем результат
    $ads = array_map(function($ad) {
        // Преобразуем additional_images из строки в массив (если это строка JSON)
        $additionalImages = json_decode($ad['additional_images'], true);
        if ($additionalImages === null) {
            $additionalImages = []; // Если вдруг было пусто или некорректно, задаем пустой массив
        }

        return [
            'id' => $ad['id'],
            'title' => $ad['title'],
            'main_image' => $ad['main_image'],
            'price' => $ad['price'], // добавляем цену в ответ
            'additional_images' => $additionalImages
        ];
    }, $result);

    return json_encode([
        'ads' => $ads,
        'total' => (int)$totalAds
    ]);
}

    public function getAdsByUser($userId) {
    $sql = "SELECT id, title, main_image, price, created_at FROM ads WHERE user_id = :user_id";
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
        // Преобразуем поле additional_images из строки JSON в массив URL
        $result['additional_images'] = json_decode($result['additional_images'], true);

        // Если поле additional_images пустое или некорректное, устанавливаем пустой массив
        if ($result['additional_images'] === null) {
            $result['additional_images'] = [];
        }

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
    if (isset($data['additional_images']) && count($data['additional_images']) > 3) {
        http_response_code(400);
        echo json_encode(['message' => 'Too many additional images']);
        return;
    }

    // Запись объявления в базу данных
    $sql = "INSERT INTO ads (title, description, price, main_image, additional_images, user_id) 
            VALUES (:title, :description, :price, :main_image, :additional_images, :user_id)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'title' => $data['title'],
        'description' => $data['description'],
        'price' => $data['price'],
        'main_image' => $data['main_image'],
        'additional_images' => json_encode($data['additional_images']),  // Кодируем в JSON
        'user_id' => $data['user_id']  // Убедитесь, что user_id передается
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
