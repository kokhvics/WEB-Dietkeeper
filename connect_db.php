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
    
} catch (PDOException $e) {
    $errorMessage = "Ошибка подключения: " . $e->getMessage() . "<br>";
    $errorMessage .= "Трассировка: " . $e->getTraceAsString() . "<br>";
    
    // Логирование ошибки
    file_put_contents('db_errors.log', $errorMessage, FILE_APPEND);
    
    // Вывод ошибки на экран
    die($errorMessage);
}
function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "1234";
    $dbname = "dietkeeper";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        throw new Exception("Ошибка подключения: " . $e->getMessage());
    }
}
// Функция для получения всех продуктов
function getAllProducts() {
    try {
        $conn = getDbConnection();
        $sql = "SELECT id, name, category, protein, fat, carbs, calories, image_url FROM products";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}


?>