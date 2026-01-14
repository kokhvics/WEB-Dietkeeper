<?php
session_start();
if (!(isset($_SESSION['user_id']) || (isset($_SESSION['github_oauth']) && $_SESSION['github_oauth'] === true))) {
    header('Location: login.php');
    exit;
}
require_once 'connect_db.php';

// Поиск через AJAX (как на странице просмотра)
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

// Обработка обновления - ✅ ИСПРАВЛЕНО: правильная обработка запятых
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    try {
        // ✅ Заменяем запятые на точки для БД
        $protein = str_replace(',', '.', $_POST['protein']);
        $fat = str_replace(',', '.', $_POST['fat']);
        $carbs = str_replace(',', '.', $_POST['carbs']);
        $calories = str_replace(',', '.', $_POST['calories']);
        
        updateProduct(
            $_POST['id'],
            $_POST['name'],
            $_POST['category'],
            $protein,
            $fat,
            $carbs,
            $calories,
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

// Предустановленный список категорий
$categories = ['Овощи', 'Фрукты', 'Ягоды', 'Хлебобулочные изделия', 'Крупы', 'Мясные продукты', 'Рыба и морепродукты', 'Грибы', 'Напитки', 'Молочные продукты', 'Сладости','Орехи и семена', 'Другое'];
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
    <style>
        .error-message { color: #dc3545; font-size: 0.875em; margin-top: 0.25rem; display: none; }
        .error-message.show { display: block; }
    </style>
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
                <input type="text" id="search-input" class="form-control mb-3" placeholder="Поиск по ID, названию или категории">
                
                <?php if (empty($products)): ?>
                    <div class="alert alert-info">Нет продуктов для редактирования.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-header-green">
                                <tr>
                                    <th>ID</th><th>Название</th><th>Категория</th><th>Белки</th><th>Жиры</th><th>Углеводы</th><th>Калории</th><th>Изображение</th><th>Действие</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
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
                    <p id="record-count" class="text-muted">Всего записей: <?= count($products) ?></p>
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
                    <form id="editForm" novalidate>
                        <input type="hidden" id="editId" name="id">
                        
                        <div class="mb-3">
                            <label>Название</label>
                            <input type="text" class="form-control" id="editName" name="name"
                                title="От 2 до 100 символов" maxlength="100" required>
                            <div class="error-message" id="nameError"></div>
                            <div class="form-text text-muted">От 2 до 100 символов</div>
                        </div>
                        
                        <div class="mb-3">
                            <label>Категория</label>
                            <select class="form-select" id="editCategory" name="category" required>
                                <option value="">Выберите категорию</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="error-message" id="categoryError"></div>
                        </div>

                        <div class="mb-3">
                            <label>Белки (г)</label>
                            <input type="text" class="form-control" id="editProtein" name="protein" required>
                            <div class="error-message" id="proteinError"></div>
                            <div class="form-text text-muted">0-100, доп. знаки</div>
                        </div>
                        <div class="mb-3">
                            <label>Жиры (г)</label>
                            <input type="text" class="form-control" id="editFat" name="fat" required>
                            <div class="error-message" id="fatError"></div>
                            <div class="form-text text-muted">0-100, доп. знаки</div>
                        </div>
                        <div class="mb-3">
                            <label>Углеводы (г)</label>
                            <input type="text" class="form-control" id="editCarbs" name="carbs" required>
                            <div class="error-message" id="carbsError"></div>
                            <div class="form-text text-muted">0-100, доп. знаки</div>
                        </div>

                        <div class="mb-3">
                            <label>Калории (ккал)</label>
                            <input type="text" class="form-control" id="editCalories" name="calories" required>
                            <div class="error-message" id="caloriesError"></div>
                            <div class="form-text text-muted">0-1000</div>
                        </div>
                        
                        <div class="mb-3">
                            <label>URL изображения</label>
                            <input type="text" class="form-control" id="editImageUrl" name="image_url">
                            <div class="error-message" id="imageUrlError"></div>
                            <div class="form-text text-muted">url (опционально)</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let originalData = null;
        const categories = <?= json_encode($categories) ?>;

        // ✅ НОВЫЕ ФУНКЦИИ ВАЛИДАЦИИ - правильно обрабатывают 678.00
        function normalizeNumber(value) {
            // Только цифры, одна точка/запятая
            let normalized = value.replace(/[^0-9.,]/g, '');
            // Запятая → точка, только одна
            normalized = normalized.replace(/,/g, '.').replace(/(\..*)\./g, '$1');
            return normalized;
        }

        // Поиск как на странице просмотра
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('table-body');
        const originalHtml = tableBody.innerHTML;
        const originalCount = <?= count($products) ?>;

        searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            if (query.length >= 1) {
                fetch(`admin_edit.php?action=search&query=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(products => {
                        tableBody.innerHTML = '';
                        if (products.length > 0) {
                            products.forEach(p => {
                                const row = `<tr data-id="${p.id || ''}"
                                    data-name="${p.name || ''}"
                                    data-category="${p.category || ''}"
                                    data-protein="${p.protein || ''}"
                                    data-fat="${p.fat || ''}"
                                    data-carbs="${p.carbs || ''}"
                                    data-calories="${p.calories || ''}"
                                    data-image_url="${p.image_url || ''}">
                                    <td>${p.id || ''}</td>
                                    <td>${p.name || ''}</td>
                                    <td>${p.category || ''}</td>
                                    <td>${p.protein || ''}</td>
                                    <td>${p.fat || ''}</td>
                                    <td>${p.carbs || ''}</td>
                                    <td>${p.calories || ''}</td>
                                    <td>${p.image_url ? '<img src="' + p.image_url + '" width="50" class="img-thumbnail">' : '—'}</td>
                                    <td><button class="btn edit-btn">Редактировать</button></td>
                                </tr>`;
                                tableBody.innerHTML += row;
                            });
                            document.getElementById('record-count').textContent = `Всего записей: ${products.length}`;
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Ничего не найдено</td></tr>';
                            document.getElementById('record-count').textContent = 'Всего записей: 0';
                        }
                        initEditButtons();
                    })
                    .catch(err => {
                        console.error('Ошибка поиска:', err);
                        tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Ошибка загрузки</td></tr>';
                    });
            } else {
                tableBody.innerHTML = originalHtml;
                document.getElementById('record-count').textContent = `Всего записей: ${originalCount}`;
                initEditButtons();
            }
        });

        function showFieldError(inputEl, errorEl, message) {
            errorEl.textContent = message;
            errorEl.classList.add('show');
            inputEl.classList.add('is-invalid');
        }

        function hideFieldError(inputEl, errorEl) {
            errorEl.classList.remove('show');
            inputEl.classList.remove('is-invalid');
        }

        // ✅ ИСПРАВЛЕННАЯ валидация калорий и нутриентов
        function validateCaloriesField(value) {
            const errorEl = document.getElementById('caloriesError');
            const inputEl = document.getElementById('editCalories');
            
            const numericValue = normalizeNumber(value);
            if (numericValue !== value) {
                inputEl.value = numericValue;
            }
            
            const num = parseFloat(numericValue);
            if (isNaN(num) || num < 0 || num > 1000) {
                showFieldError(inputEl, errorEl, '0-1000 (678.00 ✅)');
                return false;
            }
            hideFieldError(inputEl, errorEl);
            return true;
        }

        function validateNutrientField(value, errorEl, inputEl, fieldName) {
            const numericValue = normalizeNumber(value);
            if (numericValue !== value) {
                inputEl.value = numericValue;
            }
            
            const num = parseFloat(numericValue);
            if (isNaN(num) || num < 0 || num > 100) {
                showFieldError(inputEl, errorEl, `${fieldName}: 0-100`);
                return false;
            }
            hideFieldError(inputEl, errorEl);
            return true;
        }

        // Остальные функции валидации БЕЗ ИЗМЕНЕНИЙ
        function validateTextField(value, minLength, maxLength, errorEl, inputEl, fieldName) {
            const trimmed = value.trim();
            if (!trimmed) {
                showFieldError(inputEl, errorEl, `${fieldName} обязательно`);
                return false;
            }
            if (trimmed.length < minLength) {
                showFieldError(inputEl, errorEl, `Минимум ${minLength} символа`);
                return false;
            }
            if (trimmed.length > maxLength) {
                showFieldError(inputEl, errorEl, `Максимум ${maxLength} символов`);
                return false;
            }
            hideFieldError(inputEl, errorEl);
            return true;
        }

        function validateName(value) {
            const errorEl = document.getElementById('nameError');
            const inputEl = document.getElementById('editName');
            return validateTextField(value, 2, 100, errorEl, inputEl, 'Название');
        }

        function validateCategory(value) {
            const errorEl = document.getElementById('categoryError');
            const inputEl = document.getElementById('editCategory');
            if (!value || !categories.includes(value)) {
                showFieldError(inputEl, errorEl, 'Выберите из списка');
                return false;
            }
            hideFieldError(inputEl, errorEl);
            return true;
        }

        function validateImageUrl(value) {
            const errorEl = document.getElementById('imageUrlError');
            const inputEl = document.getElementById('editImageUrl');
            if (!value.trim()) {
                hideFieldError(inputEl, errorEl);
                return true;
            }
            if (!/^https?:\/\//i.test(value)) {
                showFieldError(inputEl, errorEl, 'Некорректный URL');
                return false;
            }
            hideFieldError(inputEl, errorEl);
            return true;
        }

        function isFormValid() {
            const nameValid = validateName(document.getElementById('editName').value);
            const categoryValid = validateCategory(document.getElementById('editCategory').value);
            
            const proteinValid = validateNutrientField(
                document.getElementById('editProtein').value, 
                document.getElementById('proteinError'), 
                document.getElementById('editProtein'),
                'Белки'
            );
            const fatValid = validateNutrientField(
                document.getElementById('editFat').value, 
                document.getElementById('fatError'), 
                document.getElementById('editFat'),
                'Жиры'
            );
            const carbsValid = validateNutrientField(
                document.getElementById('editCarbs').value, 
                document.getElementById('carbsError'), 
                document.getElementById('editCarbs'),
                'Углеводы'
            );
            
            const caloriesValid = validateCaloriesField(document.getElementById('editCalories').value);
            const imageUrlValid = validateImageUrl(document.getElementById('editImageUrl').value);

            return nameValid && categoryValid && proteinValid && fatValid && carbsValid && 
                   caloriesValid && imageUrlValid;
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            if (isFormValid()) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-primary');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-secondary');
            }
        }

        function initRealTimeValidation() {
            document.getElementById('editName').addEventListener('input', function() {
                validateName(this.value);
                updateSubmitButton();
            });

            document.getElementById('editCategory').addEventListener('change', function() {
                validateCategory(this.value);
                updateSubmitButton();
            });

            ['editProtein', 'editFat', 'editCarbs'].forEach((id, index) => {
                const input = document.getElementById(id);
                const errorId = id + 'Error';
                const fieldNames = ['Белки', 'Жиры', 'Углеводы'];
                
                input.addEventListener('input', function() {
                    validateNutrientField(this.value, document.getElementById(errorId), this, fieldNames[index]);
                    updateSubmitButton();
                });
            });

            document.getElementById('editCalories').addEventListener('input', function() {
                validateCaloriesField(this.value);
                updateSubmitButton();
            });

            document.getElementById('editImageUrl').addEventListener('input', function() {
                validateImageUrl(this.value);
                updateSubmitButton();
            });
        }

        // Инициализация кнопок редактирования
        function initEditButtons() {
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

                    originalData = {
                        name: row.dataset.name,
                        category: row.dataset.category,
                        protein: row.dataset.protein,
                        fat: row.dataset.fat,
                        carbs: row.dataset.carbs,
                        calories: row.dataset.calories,
                        image_url: row.dataset.image_url
                    };

                    document.querySelectorAll('.error-message').forEach(el => el.classList.remove('show'));
                    document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
                    updateSubmitButton();

                    const modal = new bootstrap.Modal(document.getElementById('editModal'));
                    modal.show();
                });
            });
        }

        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            initRealTimeValidation();
            initEditButtons();
            updateSubmitButton();

            document.getElementById('editForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                if (!isFormValid()) {
                    alert('Исправьте ошибки в форме');
                    return;
                }

                const current = {
                    name: document.getElementById('editName').value,
                    category: document.getElementById('editCategory').value,
                    protein: document.getElementById('editProtein').value,
                    fat: document.getElementById('editFat').value,
                    carbs: document.getElementById('editCarbs').value,
                    calories: document.getElementById('editCalories').value,
                    image_url: document.getElementById('editImageUrl').value
                };

                const hasChanges = Object.keys(current).some(key => current[key] !== originalData[key]);
                if (!hasChanges) {
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                    return;
                }

                const btn = document.getElementById('submitBtn');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Сохранение...';

                const formData = new FormData(this);
                try {
                    const res = await fetch('admin_edit.php', { method: 'POST', body: formData });
                    const data = await res.json();
                    if (data.success) {
                        alert('✅ Продукт обновлён!');
                        location.reload();
                    } else {
                        alert('❌ Ошибка: ' + (data.message || 'Неизвестная ошибка'));
                    }
                } catch (err) {
                    alert('❌ Ошибка сети');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        });
    </script>
</body>
</html>
