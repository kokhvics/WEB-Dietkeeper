<?php
session_start();
if (!(isset($_SESSION['user_id']) || (isset($_SESSION['github_oauth']) && $_SESSION['github_oauth'] === true))) {
    header('Location: login.php');
    exit;
}
require_once 'connect_db.php';

// Обработка обновления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    try {
        updateProduct(
            $_POST['id'],
            $_POST['name'],
            $_POST['category'],
            $_POST['protein'],
            $_POST['fat'],
            $_POST['carbs'],
            $_POST['calories'],
            $_POST['image_url']
        );
        echo json_encode(['success' => true, 'message' => 'Продукт обновлён']);
        exit;
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
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
    <title>Редактирование — Admin</title>
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
                    <a href="admin_edit.php" class="btn btn w-100 text-start mt-3 active-page">
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
                <h2 class="mb-4">Редактирование продуктов</h2>
                <?php if (empty($products)): ?>
                    <div class="alert alert-info">Нет продуктов для редактирования.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-header-green">
                                <tr>
                                    <th>ID</th><th>Название</th><th>Категория</th><th>Б</th><th>Ж</th><th>У</th><th>Ккал</th><th>Изображение</th><th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                    <tr data-id="<?= htmlspecialchars($p['id']) ?>"
                                        data-name="<?= htmlspecialchars($p['name']) ?>"
                                        data-category="<?= htmlspecialchars($p['category']) ?>"
                                        data-protein="<?= htmlspecialchars($p['protein']) ?>"
                                        data-fat="<?= htmlspecialchars($p['fat']) ?>"
                                        data-carbs="<?= htmlspecialchars($p['carbs']) ?>"
                                        data-calories="<?= htmlspecialchars($p['calories']) ?>"
                                        data-image_url="<?= htmlspecialchars($p['image_url']) ?>">
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
                                            <button class="btn edit-btn">Редактировать</button>
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

    <!-- Модальное окно -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Редактировать продукт</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="editId" name="id">
                        <div class="mb-3">
                            <label>Название</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label>Категория</label>
                            <input type="text" class="form-control" id="editCategory" name="category" required>
                        </div>
                        <div class="mb-3">
                            <label>Белки (г)</label>
                            <input type="number" step="0.01" class="form-control" id="editProtein" name="protein" required>
                        </div>
                        <div class="mb-3">
                            <label>Жиры (г)</label>
                            <input type="number" step="0.01" class="form-control" id="editFat" name="fat" required>
                        </div>
                        <div class="mb-3">
                            <label>Углеводы (г)</label>
                            <input type="number" step="0.01" class="form-control" id="editCarbs" name="carbs" required>
                        </div>
                        <div class="mb-3">
                            <label>Калории (ккал)</label>
                            <input type="number" step="0.01" class="form-control" id="editCalories" name="calories" required>
                        </div>
                        <div class="mb-3">
                            <label>URL изображения</label>
                            <input type="url" class="form-control" id="editImageUrl" name="image_url">
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                document.getElementById('editId').value = row.dataset.id;
                document.getElementById('editName').value = row.dataset.name;
                document.getElementById('editCategory').value = row.dataset.category;
                document.getElementById('editProtein').value = row.dataset.protein;
                document.getElementById('editFat').value = row.dataset.fat;
                document.getElementById('editCarbs').value = row.dataset.carbs;
                document.getElementById('editCalories').value = row.dataset.calories;
                document.getElementById('editImageUrl').value = row.dataset.image_url;
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();
            });
        });

        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('admin_edit.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
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