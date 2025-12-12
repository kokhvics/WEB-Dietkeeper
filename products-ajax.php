<?php
header('Content-Type: text/html; charset=utf-8');

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "dietkeeper";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT id, name, category, protein, fat, carbs, calories, image_url FROM products WHERE 1=1";
    $params = [];

    if (!empty($_GET['search'])) {
        $sql .= " AND name LIKE :search";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }
    if (!empty($_GET['calories_min']) || !empty($_GET['calories_max'])) {
        $cal_min = (int)($_GET['calories_min'] ?? 0);
        $cal_max = (int)($_GET['calories_max'] ?? 1000);
        $sql .= " AND calories BETWEEN :cal_min AND :cal_max";
        $params[':cal_min'] = $cal_min;
        $params[':cal_max'] = $cal_max;
    }
    if (!empty($_GET['low_calorie'])) $sql .= " AND calories < 150";
    if (!empty($_GET['high_carbs'])) $sql .= " AND carbs > 60";
    if (!empty($_GET['high_protein'])) $sql .= " AND protein > 20";
    if (!empty($_GET['high_fat'])) $sql .= " AND fat > 30";
    if (!empty($_GET['vegetarian'])) $sql .= " AND category NOT IN ('Мясные продукты', 'Молочные продукты', 'Рыба и морепродукты')";
    if (!empty($_GET['no_sugar'])) $sql .= " AND category != 'Сладости'";
    if (!empty($_GET['fasting'])) $sql .= " AND category NOT IN ('Мясные продукты', 'Молочные продукты', 'Рыба и морепродукты', 'Сладости')";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($products)) {
        echo '<div class="col-12"><div class="alert alert-info">Нет продуктов, соответствующих фильтрам.</div></div>';
    } else {
        foreach ($products as $product) {
            echo '<div class="col">';
            echo '    <div class="card h-100">';
            echo '        <img src="' . htmlspecialchars($product['image_url']) . '" class="card-img-top" alt="' . htmlspecialchars($product['name']) . '">';
            echo '        <div class="card-body">';
            echo '            <h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>';
            echo '            <div class="nutrients">';
            echo '                <small class="text-muted">';
            echo '                    К: ' . htmlspecialchars($product['calories']) . 'ккал | ';
            echo '                    Б: ' . htmlspecialchars($product['protein']) . 'г | ';
            echo '                    Ж: ' . htmlspecialchars($product['fat']) . 'г | ';
            echo '                    У: ' . htmlspecialchars($product['carbs']) . 'г';
            echo '                </small>';
            echo '            </div>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';
        }
    }
} catch (Exception $e) {
    echo '<div class="col-12"><div class="alert alert-danger">Ошибка: ' . htmlspecialchars($e->getMessage()) . '</div></div>';
}