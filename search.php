<?php
// Настройка отображения ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Параметры подключения
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "dietkeeper";

try {
    // Подключение к базе данных
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Получаем параметр запроса
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';

    if (strlen($query) >= 2) {
        // SQL-запрос для поиска
        $sql = "SELECT id, name, category, proteins, fats, carbohydrates, calories, image_url 
                FROM products 
                WHERE name LIKE :query 
                LIMIT 10"; // Ограничиваем количество результатов

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Возвращаем результаты в формате JSON
        echo json_encode($results);
    } else {
        echo json_encode([]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>