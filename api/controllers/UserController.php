<?php

require_once '/Users/maksimdockin/PhpstormProjects/AdsService/vendor/autoload.php';
use Firebase\JWT\JWT;
class UserController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function registerUser($input) {
    // Validate input
        if (empty($input['name']) || strlen($input['name']) > 100) {
            http_response_code(400);
            return json_encode(['message' => 'Invalid name']);
        }
        if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            return json_encode(['message' => 'Invalid email']);
        }
        if (empty($input['password'])) {
            http_response_code(400);
            return json_encode(['message' => 'Password is required']);
        }
         // Check if the email or nickname is already taken
        $sqlCheckEmail = "SELECT id FROM users WHERE email = :email";
        $stmtCheckEmail = $this->pdo->prepare($sqlCheckEmail);
        $stmtCheckEmail->execute(['email' => $input['email']]);
        if ($stmtCheckEmail->rowCount() > 0) {
            http_response_code(400);
            return json_encode(['message' => 'Email is already taken']);
        }

        $sqlCheckName = "SELECT id FROM users WHERE name = :name";
        $stmtCheckName = $this->pdo->prepare($sqlCheckName);
        $stmtCheckName->execute(['name' => $input['name']]);
        if ($stmtCheckName->rowCount() > 0) {
            http_response_code(400);
            return json_encode(['message' => 'Nickname is already taken']);
        }

        // Hash password
        $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $hashedPassword
        ]);

        $userId = $this->pdo->lastInsertId();

        // Генерация JWT токена (предполагаем, что у вас есть библиотека для генерации JWT)
        $token = $this->generateJWT($userId);

        return json_encode(['message' => 'User registered successfully', 'token' => $token, 'id' => $userId , 'userId' => $userId]);}
    // В UserController.php
    public function loginUser($data) {
    $email = $data['email'];
    $password = $data['password'];

    // Проверяем, существует ли пользователь с данным email
    $sql = "SELECT id, name, email, password FROM users WHERE email = :email";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Проверяем правильность пароля
        if (password_verify($password, $user['password'])) {
            // Генерация JWT токена
            $jwt = $this->generateJWT($user['id']);

            // Отправляем токен в ответе
            return json_encode(['token' => $jwt, 'userId' => $user['id']]);
        } else {
            http_response_code(401);
            return json_encode(['message' => 'Invalid password']);
        }
    } else {
        http_response_code(404);
        return json_encode(['message' => 'User not found']);
    }
}
   public function generateJWT($userId) {
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;  // токен действителен 1 час
    $payload = [
        'iss' => 'your-issuer',  // кто создал JWT
        'iat' => $issuedAt,      // время выпуска
        'exp' => $expirationTime, // время истечения
        'sub' => $userId,        // идентификатор пользователя
    ];

    $secretKey = 'your-secret-key'; // ваш секретный ключ

    // Теперь нужно передать три аргумента: payload, key и algo (если необходимо)
    $jwt = JWT::encode($payload, $secretKey, 'HS256'); // 'HS256' - алгоритм по умолчанию
    return $jwt;
}

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
