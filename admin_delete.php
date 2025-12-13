<?php
session_start();

// Проверка авторизации
if (!(isset($_SESSION['user_id']) || (isset($_SESSION['github_oauth']) && $_SESSION['github_oauth'] === true))) {
    header('Location: login.php');
    exit;
}

// Подключаем БД-функции
require_once 'connect_db.php';

// Поиск через AJAX (как на других страницах)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search') {
    header('Content-Type: application/json; charset=utf-8');
    try {
        $query = $_GET['query'] ?? '';
        $results = searchProducts($query);
        echo json_encode($results);
        exit;
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

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
                
                <!-- Поле поиска -->
                <input type="text" id="search-input" class="form-control mb-3" placeholder="Поиск по ID, названию или категории">

                <?php if (empty($products)): ?>
                    <div class="alert alert-info">Нет продуктов для удаления.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-header-green">
                                <tr>
                            <tr>
                                <th>ID</th><th>Название</th><th>Категория</th><th>Белки</th><th>Жиры</th><th>Углеводы</th><th>Калории</th><th>Изображение</th><th>Действие</th>
                            </tr>
                            </thead>
                            <tbody id="table-body">
                                <?php foreach ($products as $p): ?>
                                    <tr data-id="<?= htmlspecialchars($p['id']) ?>"
                                        data-name="<?= htmlspecialchars($p['name']) ?>"
                                        data-category="<?= htmlspecialchars($p['category']) ?>">
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
                    <p id="record-count" class="text-muted">Всего записей: <?= count($products) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Динамический поиск (как на других страницах)
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('table-body');
        const originalHtml = tableBody.innerHTML;
        const originalCount = <?= count($products) ?>;

        searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            if (query.length >= 1) {
                fetch(`admin_delete.php?action=search&query=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(products => {
                        tableBody.innerHTML = '';
                        if (products.length > 0) {
                            products.forEach(p => {
                                const row = `<tr data-id="${p.id || ''}"
                                    data-name="${p.name || ''}"
                                    data-category="${p.category || ''}">
                                    <td>${p.id || ''}</td>
                                    <td>${p.name || ''}</td>
                                    <td>${p.category || ''}</td>
                                    <td>${p.protein || ''}</td>
                                    <td>${p.fat || ''}</td>
                                    <td>${p.carbs || ''}</td>
                                    <td>${p.calories || ''}</td>
                                    <td>${p.image_url ? '<img src="' + p.image_url + '" width="50" class="img-thumbnail">' : '—'}</td>
                                    <td><button class="btn btn-danger btn-sm delete-btn">Удалить</button></td>
                                </tr>`;
                                tableBody.innerHTML += row;
                            });
                            document.getElementById('record-count').textContent = `Всего записей: ${products.length}`;
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Ничего не найдено</td></tr>';
                            document.getElementById('record-count').textContent = 'Всего записей: 0';
                        }
                        // Перепривязка обработчиков кнопок удаления
                        initDeleteButtons();
                    })
                    .catch(err => {
                        console.error('Ошибка поиска:', err);
                        tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Ошибка загрузки</td></tr>';
                    });
            } else {
                tableBody.innerHTML = originalHtml;
                document.getElementById('record-count').textContent = `Всего записей: ${originalCount}`;
                // Перепривязка обработчиков кнопок удаления
                initDeleteButtons();
            }
        });

        // Инициализация кнопок удаления
        function initDeleteButtons() {
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
                            // Обновляем счетчик
                            const currentCount = tableBody.querySelectorAll('tr').length - 1; // -1 для заголовка "Ничего не найдено"
                            document.getElementById('record-count').textContent = `Всего записей: ${currentCount}`;
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
        }

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            initDeleteButtons();
        });
    </script>
</body>
</html>
