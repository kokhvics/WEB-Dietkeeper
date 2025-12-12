<?php
// Настройка отображения ошибок (для разработки)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Параметры подключения
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "dietkeeper";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = trim($_GET['query'] ?? '');

    // Если запрос пуст — возвращаем пустой массив
    if (strlen($query) < 1) {
        echo json_encode([]);
        exit;
    }

    // Подготавливаем SQL: ищем либо по ID (если запрос — число), либо по названию
    $sql = "SELECT id, name, category, protein, fat, carbs, calories, image_url 
            FROM products 
            WHERE 
                (:is_numeric = 1 AND id = :id) 
                OR 
                (name LIKE :name_like)";

    $is_numeric = is_numeric($query) ? 1 : 0;
    $id = (int)$query;
    $name_like = "%{$query}%";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':is_numeric', $is_numeric, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':name_like', $name_like, PDO::PARAM_STR);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>