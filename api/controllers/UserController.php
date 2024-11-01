<?php


class UserController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Получение списка пользователей
     // Получение списка пользователей с параметрами сортировки
    public function getUsers($sort = 'name', $order = 'ASC') {
        // Валидация параметров сортировки
        if (!in_array($sort, ['name', 'email', 'created_at'])) {
            $sort = 'name'; // По умолчанию
        }
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'ASC'; // По умолчанию
        }

        $sql = "SELECT id, name, email, created_at FROM users ORDER BY $sort $order";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }

    // Получение конкретного пользователя
    public function getUser($id) {
        $sql = "SELECT id, name, email, created_at FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return json_encode($user);
        } else {
            http_response_code(404);
            return json_encode(['message' => 'User not found']);
        }
    }

    // Создание нового пользователя
    public function createUser($input) {
        // Валидация данных
        if (empty($input['name']) || strlen($input['name']) > 100) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid name']);
            return;
        }
        if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid email']);
            return;
        }
        if (empty($input['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Password is required']);
            return;
        }

        // Хэширование пароля
        $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $hashedPassword
        ]);

        echo json_encode(['message' => 'User created successfully', 'id' => $this->pdo->lastInsertId()]);
    }
    public function deleteUser($userId) {
    // Проверка существования пользователя
    $sqlCheck = "SELECT * FROM users WHERE id = :id";
    $stmtCheck = $this->pdo->prepare($sqlCheck);
    $stmtCheck->execute(['id' => $userId]);

    if ($stmtCheck->rowCount() === 0) {
        http_response_code(404);
        return json_encode(['message' => 'User not found']);
    }

    // Удаление пользователя
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);

    return json_encode(['message' => 'User deleted successfully']);
}

}
?>
