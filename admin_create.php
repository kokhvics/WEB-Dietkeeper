<?php
session_start();
if (!(isset($_SESSION['user_id']) || (isset($_SESSION['github_oauth']) && $_SESSION['github_oauth'] === true))) {
    header('Location: login.php');
    exit;
}
require_once 'connect_db.php';

// Обработка создания ОДНОГО продукта
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_FILES['csv_file'])) {
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

// Обработка загрузки CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    header('Content-Type: application/json; charset=utf-8');
    try {
        $file = $_FILES['csv_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Ошибка загрузки файла');
        }
        if (!in_array($file['type'], ['text/csv', 'application/vnd.ms-excel', 'application/csv'])) {
            throw new Exception('Разрешены только CSV-файлы');
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            throw new Exception('Не удалось открыть CSV-файл');
        }

        $added = 0;
        $errors = [];
        $lineNumber = 0;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $lineNumber++;
            if (count($row) < 6) {
                $errors[] = "Строка $lineNumber: недостаточно данных (ожидается 6+ колонок)";
                continue;
            }

            // Очистка значений от лишних пробелов
            $name = trim($row[0]);
            $category = trim($row[1]);
            $protein = trim($row[2]);
            $fat = trim($row[3]);
            $carbs = trim($row[4]);
            $calories = trim($row[5]);
            $image_url = isset($row[6]) ? trim($row[6]) : '';

            // Пропуск пустых строк
            if (empty($name) && empty($category)) continue;

            try {
                insertProduct($name, $category, $protein, $fat, $carbs, $calories, $image_url);
                $added++;
            } catch (Exception $e) {
                $errors[] = "Строка $lineNumber: " . $e->getMessage();
            }
        }

        fclose($handle);
        echo json_encode([
            'success' => true,
            'message' => "Успешно добавлено $added продуктов",
            'errors' => $errors
        ]);
        exit;

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

$categories = ['Овощи', 'Фрукты', 'Крупы', 'Мясные продукты', 'Рыба и морепродукты', 'Грибы', 'Напитки', 'Молочные продукты', 'Сладости','Орехи и семена', 'Другое'];
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
    <style>
        .required-icon {
            color: #dc3545;
            cursor: help;
            font-size: 0.8em;
            margin-left: 0.25rem;
            vertical-align: middle;
        }
        .btn:disabled {
            opacity: 0.6;
        }
        .csv-section {
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #dee2e6;
        }
        .csv-help {
            font-size: 0.875em;
            color: #6c757d;
        }
        .csv-result {
            margin-top: 1rem;
        }
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

                <!-- Форма создания одного продукта -->
                <form id="createForm" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Название <span class="required-icon">!</span></label>
                        <input type="text" class="form-control" id="createName" name="name" maxlength="100" required>
                        <div class="form-text text-muted">От 2 до 100 символов</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Категория <span class="required-icon">!</span></label>
                        <select class="form-select" id="createCategory" name="category" required>
                            <option value="">Выберите категорию</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Белки (г) <span class="required-icon">!</span></label>
                        <input type="text" class="form-control" id="createProtein" name="protein" required>
                        <div class="form-text text-muted">0–100, один знак после запятой</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Жиры (г) <span class="required-icon">!</span></label>
                        <input type="text" class="form-control" id="createFat" name="fat" required>
                        <div class="form-text text-muted">0–100, один знак после запятой</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Углеводы (г) <span class="required-icon">!</span></label>
                        <input type="text" class="form-control" id="createCarbs" name="carbs" required>
                        <div class="form-text text-muted">0–100, один знак после запятой</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Калории (ккал) <span class="required-icon">!</span></label>
                        <input type="text" class="form-control" id="createCalories" name="calories" required>
                        <div class="form-text text-muted">Целое число 0–1000</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL изображения</label>
                        <input type="text" class="form-control" id="createImageUrl" name="image_url">
                        <div class="form-text text-muted">jpg, png, webp (опционально)</div>
                    </div>
                    <button type="submit" class="btn btn-success" id="submitBtn">Создать продукт</button>
                </form>

                <!-- Загрузка CSV -->
                <div class="csv-section">
                    <h3>Массовое добавление из CSV</h3>
                    <form id="csvUploadForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="csvFile" class="form-label">CSV-файл с продуктами</label>
                            <input type="file" class="form-control" id="csvFile" name="csv_file" accept=".csv" required>
                            <div class="csv-help">
                                Формат: <code>название,категория,белки,жиры,углеводы,калории,url_изображения</code><br>
                                Пример: <code>Яблоко,Фрукты,0.3,0.4,11.8,52,https://example.com/apple.jpg</code>
                            </div>
                        </div>
                        <button type="submit" class="btn mb-5">Загрузить CSV</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const categories = <?= json_encode($categories) ?>;

        // === Валидация (сохранена как есть) ===
        function validateTextField(value, minLength, maxLength) {
            const trimmed = value.trim();
            if (!trimmed || trimmed.length < minLength || trimmed.length > maxLength) return false;
            return true;
        }

        function validateName(value) {
            return validateTextField(value, 2, 100);
        }

        function validateCategory(value) {
            return value && categories.includes(value);
        }

        function validateNutrient(value) {
            const clean = value.replace(/[^0-9.,]/g, '');
            return validateTextField(clean, 1, 6);
        }

        function validateCalories(value) {
            const clean = value.replace(/[^0-9]/g, '');
            const num = parseInt(clean);
            return !isNaN(num) && num >= 0 && num <= 1000;
        }

        function validateImageUrl(value) {
            if (!value.trim()) return true;
            return /^https?:\/\//i.test(value);
        }

        function isFormValid() {
            return validateName(document.getElementById('createName').value) &&
                   validateCategory(document.getElementById('createCategory').value) &&
                   validateNutrient(document.getElementById('createProtein').value) &&
                   validateNutrient(document.getElementById('createFat').value) &&
                   validateNutrient(document.getElementById('createCarbs').value) &&
                   validateCalories(document.getElementById('createCalories').value) &&
                   validateImageUrl(document.getElementById('createImageUrl').value);
        }

        function updateSubmitButton() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = !isFormValid();
        }

        ['createName', 'createCategory', 'createProtein', 'createFat', 'createCarbs', 'createCalories', 'createImageUrl']
            .forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', updateSubmitButton);
                    if (id === 'createCategory') el.addEventListener('change', updateSubmitButton);
                }
            });

        // Проверка дубликатов
        document.getElementById('createName').addEventListener('blur', async function() {
            const name = this.value.trim();
            if (name.length < 2) return;
            try {
                const res = await fetch(`search.php?query=${encodeURIComponent(name)}`);
                const products = await res.json();
                if (products.some(p => p.name.toLowerCase() === name.toLowerCase())) {
                    alert('Продукт с таким названием уже существует');
                    this.value = '';
                    updateSubmitButton();
                }
            } catch (err) {
                console.error('Ошибка проверки дубликатов:', err);
            }
        });

        // Отправка одного продукта
        document.getElementById('createForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!isFormValid()) return alert('Исправьте ошибки в форме');
            const btn = document.getElementById('submitBtn');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Создание...';
            const formData = new FormData(this);
            try {
                const res = await fetch('admin_create.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert('✅ ' + data.message);
                    this.reset();
                    updateSubmitButton();
                } else {
                    alert('❌ Ошибка: ' + (data.message || 'Неизвестная ошибка'));
                }
            } catch (err) {
                alert('❌ Ошибка сети');
            } finally {
                btn.disabled = false;
                btn.innerHTML = original;
            }
        });

        // Загрузка CSV
        document.getElementById('csvUploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('csvFile');
            const formData = new FormData();
            formData.append('csv_file', fileInput.files[0]);

            const btn = this.querySelector('button[type="submit"]');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Загрузка...';

            try {
                const res = await fetch('admin_create.php', { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    let message = '✅ ' + data.message;
                    if (data.errors && data.errors.length > 0) {
                        message += '\n\nОшибки:\n' + data.errors.join('\n');
                    }
                    alert(message);
                    fileInput.value = '';
                } else {
                    alert('❌ Ошибка: ' + (data.message || 'Неизвестная ошибка'));
                }
            } catch (err) {
                alert('❌ Ошибка сети: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = original;
            }
        });
        // Инициализация
        document.addEventListener('DOMContentLoaded', () => {
            updateSubmitButton();
        });
    </script>
</body>
</html>