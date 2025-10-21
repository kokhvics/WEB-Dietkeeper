<?php
session_start();
require_once 'connect_db.php';
require_once 'config.php'; // Подключаем конфигурационный файл

// Проверка, авторизован ли пользователь
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    header('Location: admin/adminpanel.php');
    exit;
}

// Загружаем ключ шифрования
$config = include 'config.php';
$encryption_key = $config['encryption_key'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            SELECT id, username, AES_DECRYPT(encrypted_password, :key) AS decrypted_password, email 
            FROM admin_users_encrypted 
            WHERE username = :username
        ");
        $stmt->execute([
            ':username' => $username,
            ':key' => $encryption_key
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['decrypted_password'] === $password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: adminpanel.php');
            exit;
        } else {
            $error = 'Неверный логин или пароль';
        }
    } catch (Exception $e) {
        $error = 'Ошибка: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-form {
            width: 30%;
            min-width: 300px;
        }
    </style>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h2 class="text-center mb-4">Вход в админ-панель</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Имя пользователя</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>
        </div>
    </div>
</body>
</html>