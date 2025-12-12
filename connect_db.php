<?php

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
        throw new Exception("Ошибка подключения к БД: " . $e->getMessage());
    }
}

function getAllProducts() {
    $conn = getDbConnection();
    $sql = "SELECT id, name, category, protein, fat, carbs, calories, image_url FROM products";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function searchProducts($search) {
    if (empty(trim($search))) {
        return getAllProducts();
    }
    $conn = getDbConnection();
    $is_numeric = is_numeric($search) ? 1 : 0;
    $id = (int)$search;
    $search_like = "%{$search}%";

    $sql = "SELECT id, name, category, protein, fat, carbs, calories, image_url 
            FROM products 
            WHERE 
                (:is_numeric = 1 AND id = :id) 
                OR name LIKE :search_like 
                OR category LIKE :search_like";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':is_numeric', $is_numeric, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':search_like', $search_like, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateProduct($id, $name, $category, $protein, $fat, $carbs, $calories, $image_url) {
    $conn = getDbConnection();
    $sql = "UPDATE products SET 
                name = :name, category = :category, protein = :protein, 
                fat = :fat, carbs = :carbs, calories = :calories, image_url = :image_url 
            WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':category' => $category,
        ':protein' => $protein,
        ':fat' => $fat,
        ':carbs' => $carbs,
        ':calories' => $calories,
        ':image_url' => $image_url
    ]);
    if ($stmt->rowCount() === 0) {
        throw new Exception("Продукт с ID $id не найден");
    }
}

function insertProduct($name, $category, $protein, $fat, $carbs, $calories, $image_url) {
    $conn = getDbConnection();
    $sql = "INSERT INTO products (name, category, protein, fat, carbs, calories, image_url) 
            VALUES (:name, :category, :protein, :fat, :carbs, :calories, :image_url)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':category' => $category,
        ':protein' => $protein,
        ':fat' => $fat,
        ':carbs' => $carbs,
        ':calories' => $calories,
        ':image_url' => $image_url
    ]);
}

function deleteProduct($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM products WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    if ($stmt->rowCount() === 0) {
        throw new Exception("Продукт с ID $id не найден");
    }
}