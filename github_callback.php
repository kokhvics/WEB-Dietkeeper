<?php
// github_callback.php
session_start();
require_once 'connect_db.php';
require_once 'config.php';

try {
    // Загружаем конфигурацию
    $config = include 'config.php';
    
    // Проверяем наличие настроек GitHub
    if (!isset($config['github'])) {
        throw new Exception('Настройки GitHub не найдены в config.php');
    }
    
    $github_config = $config['github'];

    // Проверяем наличие обязательных параметров
    if (!isset($github_config['client_id']) || empty($github_config['client_id'])) {
        throw new Exception('Client ID не настроен');
    }
    
    if (!isset($github_config['client_secret']) || empty($github_config['client_secret'])) {
        throw new Exception('Client Secret не настроен');
    }
    
    if (!isset($github_config['redirect_uri']) || empty($github_config['redirect_uri'])) {
        throw new Exception('Redirect URI не настроен');
    }

    // Проверяем наличие кода авторизации
    if (!isset($_GET['code'])) {
        throw new Exception('Код авторизации не получен от GitHub');
    }

    // Обмениваем код на access token
    $token_url = 'https://github.com/login/oauth/access_token';
    $token_data = [
        'client_id' => $github_config['client_id'],
        'client_secret' => $github_config['client_secret'],
        'code' => $_GET['code'],
        'redirect_uri' => $github_config['redirect_uri']
    ];

    // Настройка cURL для получения токена
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $token_url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($token_data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'User-Agent: DietKeeper-App'
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30
    ]);

    $token_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        throw new Exception('Ошибка при получении токена: HTTP ' . $http_code);
    }

    $token_data = json_decode($token_response, true);

    if (isset($token_data['error'])) {
        throw new Exception('GitHub вернул ошибку: ' . $token_data['error_description'] ?? $token_data['error']);
    }

    if (!isset($token_data['access_token'])) {
        throw new Exception('Не удалось получить access token от GitHub');
    }

    // Получаем информацию о пользователе
    $user_url = 'https://api.github.com/user';
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $user_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: token ' . $token_data['access_token'],
            'User-Agent: DietKeeper-App',
            'Accept: application/json'
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30
    ]);

    $user_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        throw new Exception('Ошибка при получении данных пользователя: HTTP ' . $http_code);
    }

    $user_data = json_decode($user_response, true);

    if (!isset($user_data['id'])) {
        throw new Exception('Не удалось получить ID пользователя от GitHub');
    }

    // Успешная авторизация - сохраняем данные в сессию
    $_SESSION['user_id'] = $user_data['id'];
    $_SESSION['username'] = $user_data['login'];
    $_SESSION['email'] = $user_data['email'] ?? '';
    $_SESSION['name'] = $user_data['name'] ?? $user_data['login'];
    $_SESSION['avatar'] = $user_data['avatar_url'] ?? '';
    $_SESSION['github_oauth'] = true;

    // Перенаправляем в админ-панель
    header('Location: admin_view.php');
    exit;

} catch (Exception $e) {
    // В случае ошибки возвращаем на страницу входа
    error_log('GitHub OAuth Error: ' . $e->getMessage());
    $_SESSION['error'] = 'Ошибка авторизации через GitHub: ' . $e->getMessage();
    header('Location: login.php');
    exit;
}
?>