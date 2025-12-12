<?php
session_start();
if (!(isset($_SESSION['user_id']) || (isset($_SESSION['github_oauth']) && $_SESSION['github_oauth'] === true))) {
    header('Location: login.php');
    exit;
}
require_once 'connect_db.php';

// Обработка создания
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    try {
        insertProduct(
            $_POST['name'],
            $_POST['category'],
            $_POST['protein'],
            $_POST['fat'],
            $_POST['carbs'],
            $_POST['calories'],
            $_POST['image_url']
        );
        echo json_encode(['success' => true, 'message' => 'Продукт создан']);
        exit;
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="STYLE/admin.css">
    <link rel="stylesheet" href="STYLE/styles.css">
</head>
<body>
    <header class="bg-light sticky-top">
        <div class="container d-flex justify-content-between align-items-center py-2">
            <a href="admin_view.php" class="navbar-brand text-decoration-none text-dark">
                <img src="../SVG/logo.svg" alt="DietKeeper Logo" width="30" height="30" class="d-inline-block align-text-top">
                <span class="site-title">DietKeeper</span>
            </a>
            <a href="../logout.php" class="btn">Выйти</a>
        </div>
    </header>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-2 mb-4 mb-md-0">
                <div class="list-group">
                    <a href="admin_view.php" class="btn btn w-100 text-start mt-3">
                        <i class="bi bi-search me-2"></i> Просмотр
                    </a>
                    <a href="admin_edit.php" class="btn btn w-100 text-start mt-3">
                        <i class="bi bi-file-text me-2"></i> Редактирование
                    </a>
                    <a href="admin_create.php" class="btn btn w-100 text-start mt-3 active-page">
                        <i class="bi bi-plus me-2"></i> Создание
                    </a>
                    <a href="admin_delete.php" class="btn btn w-100 text-start mt-3">
                        <i class="bi bi-trash me-2"></i> Удаление
                    </a>
                </div>
            </div>

            <div class="col-md-10">
                <h2 class="mb-4">Создание нового продукта</h2>
                <form id="createForm">
                    <div class="mb-3">
                        <label for="createName" class="form-label">Название</label>
                        <input type="text" class="form-control" id="createName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="createCategory" class="form-label">Категория</label>
                        <select class="form-select" id="createCategory" name="category" required>
                            <option value="">Выберите категорию</option>
                            <option value="Овощи">Овощи</option>
                            <option value="Фрукты">Фрукты</option>
                            <option value="Крупы">Крупы</option>
                            <option value="Мясные продукты">Мясные продукты</option>
                            <option value="Рыба и морепродукты">Рыба и морепродукты</option>
                            <option value="Грибы">Грибы</option>
                            <option value="Напитки">Напитки</option>
                            <option value="Молочные продукты">Молочные продукты</option>
                            <option value="Сладости">Сладости</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="createProtein" class="form-label">Белки (г)</label>
                        <input type="number" step="0.01" class="form-control" id="createProtein" name="protein" required>
                    </div>
                    <div class="mb-3">
                        <label for="createFat" class="form-label">Жиры (г)</label>
                        <input type="number" step="0.01" class="form-control" id="createFat" name="fat" required>
                    </div>
                    <div class="mb-3">
                        <label for="createCarbs" class="form-label">Углеводы (г)</label>
                        <input type="number" step="0.01" class="form-control" id="createCarbs" name="carbs" required>
                    </div>
                    <div class="mb-3">
                        <label for="createCalories" class="form-label">Калории (ккал)</label>
                        <input type="number" step="0.01" class="form-control" id="createCalories" name="calories" required>
                    </div>
                    <div class="mb-3">
                        <label for="createImageUrl" class="form-label">URL изображения</label>
                        <input type="url" class="form-control" id="createImageUrl" name="image_url">
                    </div>
                    <button type="submit" class="btn btn-success">Создать продукт</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('createForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('admin_create.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    document.getElementById('createForm').reset();
                } else {
                    alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
                }
            })
            .catch(err => {
                console.error('Ошибка сети:', err);
                alert('Ошибка сети');
            });
        });
    </script>
</body>
</html>