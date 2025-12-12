<?php
session_start();
require_once 'connect_db.php';
require_once 'config.php'; // Подключаем конфигурационный файл

// Проверка, авторизован ли пользователь
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    header('Location: admin_view.php');
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
            header('Location: admin_view.php');
            exit;
        } else {
            $error = 'Не удалось войти. Пожалуйста, проверьте, что вы ввели правильное имя пользователя и пароль.';
        }
    } catch (Exception $e) {
        $error = 'Ошибка: ' . $e->getMessage();
    }
}

// Генерация URL для GitHub OAuth с проверками
$github_auth_url = null;
$github_enabled = false;

if (isset($config['github'])) {
    $github_config = $config['github'];
    
    if (isset($github_config['client_id']) && 
        isset($github_config['redirect_uri']) &&
        !empty($github_config['client_id']) &&
        !empty($github_config['redirect_uri'])) {
        
        $github_enabled = true;
        $github_auth_url = "https://github.com/login/oauth/authorize?" . http_build_query([
            'client_id' => $github_config['client_id'],
            'redirect_uri' => $github_config['redirect_uri'],
            'scope' => 'user:email',
            'state' => bin2hex(random_bytes(16))
        ]);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h2 class="text-center mb-4">Вход в админ-панель</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-warning d-flex align-items-start">
                    <i class="bi bi-exclamation-triangle-fill me-2 mt-1" style="font-size: 1.2rem; color: #856404;"></i>
                    <div>
                        <?php echo htmlspecialchars($error); ?><br>
                        <small class="text-muted">
                            Если вы забыли пароль, напишите администратору или воспользуйтесь 
                            <a href="#" class="text-decoration-underline" onclick="alert('Функция восстановления пароля пока недоступна. Обратитесь к администратору.'); return false;">
                                восстановлением пароля
                            </a>.
                        </small>
                    </div>
                </div>
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
                <button type="submit" class="btn w-100">Войти</button>
            </form>

            <?php if ($github_enabled): ?>
            <div class="text-field">
                <span>или войти через</span>
            </div>

            <a href="<?php echo $github_auth_url; ?>" class="btn-github">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="me-2">
                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                </svg>
                Войти через GitHub
            </a>
            <?php else: ?>
                <div class="divider">
                    <span>альтернативный вход</span>
                </div>
                <div class="alert alert-warning text-center">
                    <small>GitHub авторизация не настроена</small>
                </div>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>