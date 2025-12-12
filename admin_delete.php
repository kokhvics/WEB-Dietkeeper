<?php
session_start();

// Проверка авторизации
if (!(isset($_SESSION['user_id']) || (isset($_SESSION['github_oauth']) && $_SESSION['github_oauth'] === true))) {
    header('Location: login.php');
    exit;
}

// Подключаем БД-функции
require_once 'connect_db.php';

// Обработка AJAX-запроса на удаление
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            throw new Exception('Некорректный ID продукта');
        }

        deleteProduct($id); // Ваша функция из connect_db.php

        echo json_encode(['success' => true, 'message' => 'Продукт успешно удалён']);
        exit;

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Получаем все продукты для отображения
try {
    $products = getAllProducts();
} catch (Exception $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удаление — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="STYLE/styles.css">
    <link rel="stylesheet" href="STYLE/admin.css">
</head>
<body>
    <!-- Шапка -->
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
            <!-- Боковое меню -->
            <div class="col-md-2 mb-4 mb-md-0">
                <div class="list-group">
                    <a href="admin_view.php" class="btn btn w-100 text-start mt-3">
                        <i class="bi bi-search me-2"></i> Просмотр
                    </a>
                    <a href="admin_edit.php" class="btn btn w-100 text-start mt-3">
                        <i class="bi bi-file-text me-2"></i> Редактирование
                    </a>
                    <a href="admin_create.php" class="btn btn w-100 text-start mt-3">
                        <i class="bi bi-plus me-2"></i> Создание
                    </a>
                    <a href="admin_delete.php" class="btn btn w-100 text-start mt-3 active-page">
                        <i class="bi bi-trash me-2"></i> Удаление
                    </a>
                </div>
            </div>

            <!-- Основной контент -->
            <div class="col-md-10">
                <h2 class="mb-4">Удаление продуктов</h2>

                <?php if (empty($products)): ?>
                    <div class="alert alert-info">Нет продуктов для удаления.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-header-green">
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Категория</th>
                                    <th>Б</th>
                                    <th>Ж</th>
                                    <th>У</th>
                                    <th>Ккал</th>
                                    <th>Изображение</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                    <tr data-id="<?= htmlspecialchars($p['id']) ?>">
                                        <td><?= htmlspecialchars($p['id']) ?></td>
                                        <td><?= htmlspecialchars($p['name']) ?></td>
                                        <td><?= htmlspecialchars($p['category']) ?></td>
                                        <td><?= htmlspecialchars($p['protein']) ?></td>
                                        <td><?= htmlspecialchars($p['fat']) ?></td>
                                        <td><?= htmlspecialchars($p['carbs']) ?></td>
                                        <td><?= htmlspecialchars($p['calories']) ?></td>
                                        <td>
                                            <?php if (!empty($p['image_url'])): ?>
                                                <img src="<?= htmlspecialchars($p['image_url']) ?>" width="50" class="img-thumbnail">
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-sm delete-btn">Удалить</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = row.dataset.id;

                if (!confirm('Вы уверены, что хотите удалить этот продукт? Это действие нельзя отменить.')) {
                    return;
                }

                fetch('admin_delete.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        row.remove();
                    } else {
                        alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
                    }
                })
                .catch(err => {
                    console.error('Ошибка сети:', err);
                    alert('Ошибка сети. Проверьте консоль браузера.');
                });
            });
        });
    </script>
</body>
</html>