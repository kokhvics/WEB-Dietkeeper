<?php
session_start();
if (!(isset($_SESSION['user_id']) || (isset($_SESSION['github_oauth']) && $_SESSION['github_oauth'] === true))) {
    header('Location: login.php');
    exit;
}
require_once 'connect_db.php';

// Поиск через AJAX (как на главной)
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
    <title>Просмотр — Admin</title>
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
                    <a href="admin_view.php" class="btn btn w-100 text-start mt-3 active-page">
                        <i class="bi bi-search me-2"></i> Просмотр
                    </a>
                    <a href="admin_edit.php" class="btn btn w-100 text-start mt-3">
                        <i class="bi bi-file-text me-2"></i> Редактирование
                    </a>
                    <a href="admin_create.php" class="btn btn w-100 text-start mt-3">
                        <i class="bi bi-plus me-2"></i> Создание
                    </a>
                    <a href="admin_delete.php" class="btn btn w-100 text-start mt-3">
                        <i class="bi bi-trash me-2"></i> Удаление
                    </a>
                </div>
            </div>

            <div class="col-md-10">
                <h2 class="mb-4">Все продукты</h2>
                <input type="text" id="search-input" class="form-control mb-3" placeholder="Поиск по ID, названию или категории">

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-header-green">
                            <tr>
                                <th>ID</th><th>Название</th><th>Категория</th><th>Белки</th><th>Жиры</th><th>Углеводы</th><th>Калории</th><th>Изображение</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php foreach ($products as $p): ?>
                                <tr>
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
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p id="record-count" class="text-muted">Всего записей: <?= count($products) ?></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('table-body');
        const originalHtml = tableBody.innerHTML;
        const originalCount = <?= count($products) ?>;

        searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            if (query.length >= 1) {
                fetch(`admin_view.php?action=search&query=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(products => {
                        tableBody.innerHTML = '';
                        if (products.length > 0) {
                            products.forEach(p => {
                                const row = `<tr>
                                    <td>${p.id || ''}</td>
                                    <td>${p.name || ''}</td>
                                    <td>${p.category || ''}</td>
                                    <td>${p.protein || ''}</td>
                                    <td>${p.fat || ''}</td>
                                    <td>${p.carbs || ''}</td>
                                    <td>${p.calories || ''}</td>
                                    <td>${p.image_url ? '<img src="${p.image_url}" width="50" class="img-thumbnail">' : '—'}</td>
                                </tr>`;
                                tableBody.innerHTML += row;
                            });
                            document.getElementById('record-count').textContent = `Всего записей: ${products.length}`;
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Ничего не найдено</td></tr>';
                            document.getElementById('record-count').textContent = 'Всего записей: 0';
                        }
                    })
                    .catch(err => {
                        console.error('Ошибка поиска:', err);
                        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Ошибка загрузки</td></tr>';
                    });
            } else {
                tableBody.innerHTML = originalHtml;
                document.getElementById('record-count').textContent = `Всего записей: ${originalCount}`;
            }
        });
    </script>
</body>
</html>