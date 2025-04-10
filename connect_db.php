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
    echo "Подключение к базе данных \"$dbname\" успешно!<br>";
} catch (PDOException $e) {
    $errorMessage = "Ошибка подключения: " . $e->getMessage() . "<br>";
    $errorMessage .= "Трассировка: " . $e->getTraceAsString() . "<br>";
    
    // Логирование ошибки
    file_put_contents('db_errors.log', $errorMessage, FILE_APPEND);
    
    // Вывод ошибки на экран
    die($errorMessage);
}

// SQL-запрос для получения данных из таблицы products
$sql = "SELECT id, name, category, proteins, fats, carbohydrates, calories, image_url FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();

// Получаем данные в виде ассоциативного массива
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Закрываем соединение
$conn = null;

// Ограничиваем вывод первыми двумя строками
$limitedProducts = array_slice($products, 0, 2);

// Вывод данных в виде HTML-таблицы
echo "<h2>Таблица продуктов (первые две строки)</h2>";
if (!empty($limitedProducts)) {
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr>
            <th>ID</th>
            <th>Название</th>
            <th>Категория</th>
            <th>Белки (г)</th>
            <th>Жиры (г)</th>
            <th>Углеводы (г)</th>
            <th>Калории (ккал)</th>
            <th>Изображение</th>
          </tr>";

    foreach ($limitedProducts as $product) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($product['id'] ?? "") . "</td>";
        echo "<td>" . htmlspecialchars($product['name'] ?? "") . "</td>";
        echo "<td>" . htmlspecialchars($product['category'] ?? "") . "</td>";
        echo "<td>" . htmlspecialchars($product['proteins'] ?? "") . "</td>";
        echo "<td>" . htmlspecialchars($product['fats'] ?? "") . "</td>";
        echo "<td>" . htmlspecialchars($product['carbohydrates'] ?? "") . "</td>";
        echo "<td>" . htmlspecialchars($product['calories'] ?? "") . "</td>";
        echo "<td><img src='" . htmlspecialchars($product['image_url'] ?? "") . "' alt='" . htmlspecialchars($product['name'] ?? "") . "' width='100'></td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>Таблица products пуста или содержит меньше двух строк.</p>";
}
?>