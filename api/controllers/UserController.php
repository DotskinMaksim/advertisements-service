<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT;
class UserController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function registerUser($input) {
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

        $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $hashedPassword
        ]);

        $userId = $this->pdo->lastInsertId();

        $token = $this->generateJWT($userId);

        return json_encode(['message' => 'User registered successfully', 'token' => $token, 'id' => $userId , 'userId' => $userId]);}
    public function loginUser($data) {
    $email = $data['email'];
    $password = $data['password'];

    $sql = "SELECT id, name, email, password FROM users WHERE email = :email";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $jwt = $this->generateJWT($user['id']);

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
    $expirationTime = $issuedAt + 3600;
    $payload = [
        'iss' => 'your-issuer',
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'sub' => $userId,
    ];

    $secretKey = 'your-secret-key';

    $jwt = JWT::encode($payload, $secretKey, 'HS256');
    return $jwt;
}

    public function getUsers($sort = 'name', $order = 'ASC') {
        if (!in_array($sort, ['name', 'email', 'created_at'])) {
            $sort = 'name';
        }
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'ASC';
        }

        $sql = "SELECT id, name, email, created_at FROM users ORDER BY $sort $order";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }

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

    public function createUser($input) {
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
    $sqlCheck = "SELECT * FROM users WHERE id = :id";
    $stmtCheck = $this->pdo->prepare($sqlCheck);
    $stmtCheck->execute(['id' => $userId]);

    if ($stmtCheck->rowCount() === 0) {
        http_response_code(404);
        return json_encode(['message' => 'User not found']);
    }

    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);

    return json_encode(['message' => 'User deleted successfully']);
}

}
?>
