<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();


class AdController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

 public function getAds($page = 1, $limit = 10, $sort = 'created_at', $order = 'DESC') {
    $offset = ($page - 1) * $limit;

    if ($sort == 'price') {
        $sort = 'price';
    } elseif ($sort == 'created_at') {
        $sort = 'created_at';
    } elseif ($sort == 'title') {
        $sort = 'title';
    } else {
        $sort = 'created_at';
    }

    if (!in_array($order, ['ASC', 'DESC'])) {
        $order = 'DESC';
    }

    $totalSql = "SELECT COUNT(*) FROM ads";
    $totalStmt = $this->pdo->prepare($totalSql);
    $totalStmt->execute();
    $totalAds = $totalStmt->fetchColumn();

    $sql = "SELECT id, title, main_image, price, created_at FROM ads ORDER BY $sort $order LIMIT $offset, $limit";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $ads = array_map(function($ad) {
        $additionalImages = json_decode($ad['additional_images'], true);
        if ($additionalImages === null) {
            $additionalImages = [];
        }

        return [
            'id' => $ad['id'],
            'title' => $ad['title'],
            'main_image' => $ad['main_image'],
            'price' => $ad['price'],
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
        $result['additional_images'] = json_decode($result['additional_images'], true);

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

  public function uploadAdImage($data) {
    if (!isset($_FILES['image'])) {
        http_response_code(400);
        return json_encode(['message' => 'No image file provided']);
    }

    $file = $_FILES['image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        return json_encode(['message' => 'File upload error']);
    }

    $apiKey =  $_ENV['IMGBB_API_KEY'];
    $url = 'https://api.imgbb.com/1/upload?key=' . $apiKey;

    $postData = [
        'image' => base64_encode(file_get_contents($file['tmp_name'])), // Кодируем файл в base64
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        http_response_code(500);
        return json_encode(['message' => 'Error while uploading image to imgBB: ' . curl_error($ch)]);
    }

    curl_close($ch);

    $responseData = json_decode($response, true);
    if ($responseData['success']) {
        $imageUrl = $responseData['data']['url'];
        return json_encode(['url' => $imageUrl]);
    } else {
        http_response_code(400);
        return json_encode(['message' => 'Failed to upload image to imgBB', 'error' => $responseData['message']]);
    }
}
    public function deleteAd($adId) {
    $sqlCheck = "SELECT * FROM ads WHERE id = :id";
    $stmtCheck = $this->pdo->prepare($sqlCheck);
    $stmtCheck->execute(['id' => $adId]);

    if ($stmtCheck->rowCount() === 0) {
        http_response_code(404);
        return json_encode(['message' => 'Ad not found']);
    }

    $sql = "DELETE FROM ads WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $adId]);

    return json_encode(['message' => 'Ad deleted successfully']);
}
}
