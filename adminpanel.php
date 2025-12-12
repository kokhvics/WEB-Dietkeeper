<?php
session_start();

// Отладка: что в сессии?
echo "<!-- Отладка сессии: ";
echo "user_id: " . ($_SESSION['user_id'] ?? 'НЕТ');
echo ", username: " . ($_SESSION['username'] ?? 'НЕТ');
echo " -->";

// Проверка авторизации для обычных пользователей
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // Пользователь авторизован - НЕ делаем редирект!
    echo "<!-- Пользователь авторизован -->";
} 
// Проверка для GitHub пользователей
elseif (isset($_SESSION['github_oauth']) && $_SESSION['github_oauth'] === true) {
    // Пользователь авторизован через GitHub - НЕ делаем редирект!
    echo "<!-- Пользователь авторизован через GitHub -->";
}
else {
    // Пользователь НЕ авторизован - только тогда делаем редирект
    echo "<!-- Пользователь НЕ авторизован, делаем редирект -->";
    header('Location: login.php');
    exit;
}

// Подключаем файл с функциями БД
require_once 'connect_db.php';

// Обработка AJAX-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    header('Content-Type: application/json');

    try {
        if ($action === 'update') {
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $category = $_POST['category'] ?? '';
            $protein = $_POST['protein'] ?? 0;
            $fat = $_POST['fat'] ?? 0;
            $carbs = $_POST['carbs'] ?? 0;
            $calories = $_POST['calories'] ?? 0;
            $image_url = $_POST['image_url'] ?? '';

            updateProduct($id, $name, $category, $protein, $fat, $carbs, $calories, $image_url);
            echo json_encode(['success' => true, 'message' => 'Продукт обновлен']);
            exit;
        } elseif ($action === 'create') {
            $name = $_POST['name'] ?? '';
            $category = $_POST['category'] ?? '';
            $protein = $_POST['protein'] ?? 0;
            $fat = $_POST['fat'] ?? 0;
            $carbs = $_POST['carbs'] ?? 0;
            $calories = $_POST['calories'] ?? 0;
            $image_url = $_POST['image_url'] ?? '';

            insertProduct($name, $category, $protein, $fat, $carbs, $calories, $image_url);
            echo json_encode(['success' => true, 'message' => 'Продукт создан']);
            exit;
        } elseif ($action === 'delete') {
            $id = $_POST['id'] ?? 0;
            deleteProduct($id);
            echo json_encode(['success' => true, 'message' => 'Продукт удален']);
            exit;
        } elseif ($action === 'search') {
            $search = $_POST['search'] ?? '';
            $products = searchProducts($search);
            echo json_encode(['success' => true, 'products' => $products]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Получаем все продукты для вкладок
try {
    $products = getAllProducts();
} catch (Exception $e) {
    $error = $e->getMessage();
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - DietKeeper</title>
    <!-- Подключение Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Подключение Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header class="bg-light sticky-top">
        <div class="container d-flex justify-content-between align-items-center py-2">
            <a href="adminpanel.php" class="navbar-brand text-decoration-none text-dark">
                <img src="../SVG/logo.svg" alt="DietKeeper Logo" width="30" height="30" class="d-inline-block align-text-top">
                <span class="site-title">DietKeeper</span>
            </a>
            <a href="../logout.php" class="btn btn-danger">Выйти</a>
        </div>
    </header>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Левый контейнер (1/5 ширины) -->
            <div class="col-md-2 sidebar">
                <ul class="nav flex-column nav-tabs" id="adminTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="btn active-page mt-3" id="view-tab" data-bs-toggle="tab" href="#view" role="tab" aria-controls="view" aria-selected="true">
                            <i class="bi bi-search me-2"></i> Просмотр
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="btn mt-3" id="edit-tab" data-bs-toggle="tab" href="#edit" role="tab" aria-controls="edit" aria-selected="false">
                            <i class="bi bi-file-text me-2"></i> Редактирование
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="btn mt-3" id="create-tab" data-bs-toggle="tab" href="#create" role="tab" aria-controls="create" aria-selected="false">
                            <i class="bi bi-plus me-2"></i> Создание
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="btn mt-3" id="delete-tab" data-bs-toggle="tab" href="#delete" role="tab" aria-controls="delete" aria-selected="false">
                            <i class="bi bi-trash me-2"></i> Удаление
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Основной контент -->
            <div class="col-md-10 main-content">
                <h1 class="mb-4 fw-bold">Административная панель</h1>
                
                <div class="tab-content" id="adminTabsContent">
                    <!-- Вкладка Просмотр -->
                    <div class="tab-pane fade show active" id="view" role="tabpanel" aria-labelledby="view-tab">
                        <h2>Все продукты</h2>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Поиск по ID или названию">
                        </div>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="viewTable">
                                    <thead class="table-header-green">
                                        <tr>
                                            <th>ID</th>
                                            <th>Название</th>
                                            <th>Категория</th>
                                            <th>Белки (г)</th>
                                            <th>Жиры (г)</th>
                                            <th>Углеводы (г)</th>
                                            <th>Калории (ккал)</th>
                                            <th>Изображение</th>
                                        </tr>
                                    </thead>
                                    <tbody id="viewTableBody">
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($product['id'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['name'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['category'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['protein'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['fat'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['carbs'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['calories'] ?? ""); ?></td>
                                                <td>
                                                    <?php if (!empty($product['image_url'])): ?>
                                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name'] ?? ""); ?>" width="50" class="img-thumbnail">
                                                    <?php else: ?>
                                                        Нет изображения
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-muted" id="recordCount">Всего записей: <?php echo count($products); ?></p>
                            <?php if (empty($products)): ?>
                                <div class="alert alert-info">
                                    <p>Таблица products пуста.</p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Вкладка Редактирование -->
                    <div class="tab-pane fade" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                        <h2>Редактирование продуктов</h2>
                        <?php if (!empty($products)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="editTable">
                                    <thead class="table-header-green">
                                        <tr>
                                            <th>ID</th>
                                            <th>Название</th>
                                            <th>Категория</th>
                                            <th>Белки (г)</th>
                                            <th>Жиры (г)</th>
                                            <th>Углеводы (г)</th>
                                            <th>Калории (ккал)</th>
                                            <th>Изображение</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <tr data-id="<?php echo htmlspecialchars($product['id']); ?>"
                                                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                                data-category="<?php echo htmlspecialchars($product['category']); ?>"
                                                data-protein="<?php echo htmlspecialchars($product['protein']); ?>"
                                                data-fat="<?php echo htmlspecialchars($product['fat']); ?>"
                                                data-carbs="<?php echo htmlspecialchars($product['carbs']); ?>"
                                                data-calories="<?php echo htmlspecialchars($product['calories']); ?>"
                                                data-image_url="<?php echo htmlspecialchars($product['image_url']); ?>">
                                                <td><?php echo htmlspecialchars($product['id'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['name'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['category'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['protein'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['fat'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['carbs'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['calories'] ?? ""); ?></td>
                                                <td>
                                                    <?php if (!empty($product['image_url'])): ?>
                                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name'] ?? ""); ?>" width="50" class="img-thumbnail">
                                                    <?php else: ?>
                                                        Нет изображения
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm edit-btn">Редактировать</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <p>Нет продуктов для редактирования.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Вкладка Создание -->
                    <div class="tab-pane fade" id="create" role="tabpanel" aria-labelledby="create-tab">
                        <h2>Создание нового продукта</h2>
                        <form id="createForm">
                            <div class="mb-3">
                                <label for="createName" class="form-label">Название</label>
                                <input type="text" class="form-control" id="createName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="createCategory" class="form-label">Категория</label>
                                <input type="text" class="form-control" id="createCategory" name="category" required>
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
                            <button type="submit" class="btn btn-success">Создать</button>
                        </form>
                    </div>

                    <!-- Вкладка Удаление -->
                    <div class="tab-pane fade" id="delete" role="tabpanel" aria-labelledby="delete-tab">
                        <h2>Удаление продуктов</h2>
                        <?php if (!empty($products)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="deleteTable">
                                    <thead class="table-header-green">
                                        <tr>
                                            <th>ID</th>
                                            <th>Название</th>
                                            <th>Категория</th>
                                            <th>Белки (г)</th>
                                            <th>Жиры (г)</th>
                                            <th>Углеводы (г)</th>
                                            <th>Калории (ккал)</th>
                                            <th>Изображение</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <tr data-id="<?php echo htmlspecialchars($product['id']); ?>">
                                                <td><?php echo htmlspecialchars($product['id'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['name'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['category'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['protein'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['fat'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['carbs'] ?? ""); ?></td>
                                                <td><?php echo htmlspecialchars($product['calories'] ?? ""); ?></td>
                                                <td>
                                                    <?php if (!empty($product['image_url'])): ?>
                                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name'] ?? ""); ?>" width="50" class="img-thumbnail">
                                                    <?php else: ?>
                                                        Нет изображения
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
                        <?php else: ?>
                            <div class="alert alert-info">
                                <p>Нет продуктов для удаления.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Редактирование продукта</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="editId" name="id">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Название</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCategory" class="form-label">Категория</label>
                            <input type="text" class="form-control" id="editCategory" name="category" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProtein" class="form-label">Белки (г)</label>
                            <input type="number" step="0.01" class="form-control" id="editProtein" name="protein" required>
                        </div>
                        <div class="mb-3">
                            <label for="editFat" class="form-label">Жиры (г)</label>
                            <input type="number" step="0.01" class="form-control" id="editFat" name="fat" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCarbs" class="form-label">Углеводы (г)</label>
                            <input type="number" step="0.01" class="form-control" id="editCarbs" name="carbs" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCalories" class="form-label">Калории (ккал)</label>
                            <input type="number" step="0.01" class="form-control" id="editCalories" name="calories" required>
                        </div>
                        <div class="mb-3">
                            <label for="editImageUrl" class="form-label">URL изображения</label>
                            <input type="url" class="form-control" id="editImageUrl" name="image_url">
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Подключение Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript для обработки форм, поиска и AJAX

        // Обработка поиска
        // Динамический поиск через search.php (как на главной)
        document.getElementById('searchInput').addEventListener('input', function () {
            const query = this.value.trim();
            const tableBody = document.getElementById('viewTableBody');
            const recordCount = document.getElementById('recordCount');

            if (query.length >= 1) {
                fetch(`search.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
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
                                    <td>${p.image_url ? `<img src="${p.image_url}" width="50" class="img-thumbnail">` : 'Нет изображения'}</td>
                                </tr>`;
                                tableBody.innerHTML += row;
                            });
                            if (recordCount) recordCount.textContent = `Всего записей: ${products.length}`;
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Ничего не найдено</td></tr>';
                            if (recordCount) recordCount.textContent = 'Всего записей: 0';
                        }
                    })
                    .catch(err => {
                        console.error('Ошибка поиска:', err);
                        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Ошибка загрузки</td></tr>';
                    });
            } else {
                // Восстанавливаем ИСХОДНУЮ таблицу БЕЗ перезагрузки!
                // Для этого нужно сохранить её при загрузке
                if (!window.originalTableHtml) {
                    window.originalTableHtml = tableBody.innerHTML;
                }
                tableBody.innerHTML = window.originalTableHtml;
                if (recordCount) {
                    recordCount.textContent = `Всего записей: ${document.querySelectorAll('#viewTableBody tr').length}`;
                }
            }
        });

        // Обработка клика на "Редактировать"
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                document.getElementById('editId').value = row.dataset.id;
                document.getElementById('editName').value = row.dataset.name;
                document.getElementById('editCategory').value = row.dataset.category;
                document.getElementById('editProtein').value = row.dataset.protein;
                document.getElementById('editFat').value = row.dataset.fat;
                document.getElementById('editCarbs').value = row.dataset.carbs;
                document.getElementById('editCalories').value = row.dataset.calories;
                document.getElementById('editImageUrl').value = row.dataset.image_url;

                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            });
        });

        // Обработка формы редактирования
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update');

            fetch('adminpanel.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Перезагрузка для обновления данных
                } else {
                    alert('Ошибка: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Обработка формы создания
        document.getElementById('createForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'create');

            fetch('adminpanel.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Перезагрузка для обновления данных
                } else {
                    alert('Ошибка: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Обработка клика на "Удалить"
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = row.dataset.id;
                if (confirm('Вы уверены, что хотите удалить этот продукт?')) {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id', id);

                    fetch('adminpanel.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload(); // Перезагрузка для обновления данных
                        } else {
                            alert('Ошибка: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    </script>

</body>
</html>
```