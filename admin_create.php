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
$categories = ['Овощи', 'Фрукты', 'Крупы', 'Мясные продукты', 'Рыба и морепродукты', 'Грибы', 'Напитки', 'Молочные продукты','Сладости', 'Другое'];
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
        .required-tooltip:hover::after {
            content: "Поле обязательно к заполнению";
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 0.3rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8em;
            white-space: nowrap;
            z-index: 1000;
            margin-bottom: 0.25rem;
        }
        .btn:disabled {
            opacity: 0.6;
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
                <form id="createForm" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Название <span class="required-icon required-tooltip">!</span></label>
                        <input type="text" class="form-control" id="createName" name="name"
                            title="От 2 до 100 символов" maxlength="100" required>
                        <div class="form-text text-muted">От 2 до 100 символов</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Категория <span class="required-icon required-tooltip">!</span></label>
                        <select class="form-select" id="createCategory" name="category" required>
                            <option value="">Выберите категорию</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Белки (г) <span class="required-icon required-tooltip">!</span></label>
                        <input type="text" class="form-control" id="createProtein" name="protein" required>
                        <div class="form-text text-muted">0-100, один десятичный знак</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Жиры (г) <span class="required-icon required-tooltip">!</span></label>
                        <input type="text" class="form-control" id="createFat" name="fat" required>
                        <div class="form-text text-muted">0-100, один десятичный знак</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Углеводы (г) <span class="required-icon required-tooltip">!</span></label>
                        <input type="text" class="form-control" id="createCarbs" name="carbs" required>
                        <div class="form-text text-muted">0-100, один десятичный знак</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Калории (ккал) <span class="required-icon required-tooltip">!</span></label>
                        <input type="text" class="form-control" id="createCalories" name="calories" required>
                        <div class="form-text text-muted">Целое число 0-1000</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">URL изображения</label>
                        <input type="text" class="form-control" id="createImageUrl" name="image_url">
                        <div class="form-text text-muted">jpg, png, webp (опционально)</div>
                    </div>
                    
                    <button type="submit" class="btn btn-success" id="submitBtn">Создать продукт</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const categories = <?= json_encode($categories) ?>;

        // Универсальная функция валидации текстового поля (БЕЗ визуальных ошибок)
        function validateTextField(value, minLength, maxLength, fieldName) {
            const trimmed = value.trim();
            
            if (!trimmed) return false;
            if (trimmed.length < minLength) return false;
            if (trimmed.length > maxLength) return false;
            
            return true;
        }

        function validateName(value) {
            return validateTextField(value, 2, 100, 'Название');
        }

        function validateCategory(value) {
            return value && categories.includes(value);
        }

        function validateNutrientField(value, inputEl, fieldName) {
            // БЛОКИРОВКА БУКВ - только цифры, точка, запятая
            const numericValue = value.replace(/[^0-9.,]/g, '');
            if (numericValue !== value) {
                inputEl.value = numericValue;
            }
            
            return validateTextField(numericValue, 1, 6, fieldName);
        }

        function validateCaloriesField(value, inputEl) {
            // БЛОКИРОВКА БУКВ - только цифры
            const numericValue = value.replace(/[^0-9]/g, '');
            if (numericValue !== value) {
                inputEl.value = numericValue;
            }
            
            const num = parseInt(numericValue);
            return !isNaN(num) && num >= 0 && num <= 1000;
        }

        function validateImageUrl(value) {
            if (!value.trim()) return true;
            return /^https?:\/\//i.test(value);
        }

        function isFormValid() {
            const nameValid = validateName(document.getElementById('createName').value);
            const categoryValid = validateCategory(document.getElementById('createCategory').value);
            
            const proteinValid = validateNutrientField(
                document.getElementById('createProtein').value, 
                document.getElementById('createProtein'),
                'Белки'
            );
            const fatValid = validateNutrientField(
                document.getElementById('createFat').value, 
                document.getElementById('createFat'),
                'Жиры'
            );
            const carbsValid = validateNutrientField(
                document.getElementById('createCarbs').value, 
                document.getElementById('createCarbs'),
                'Углеводы'
            );
            
            const caloriesValid = validateCaloriesField(
                document.getElementById('createCalories').value,
                document.getElementById('createCalories')
            );
            const imageUrlValid = validateImageUrl(document.getElementById('createImageUrl').value);

            return nameValid && categoryValid && proteinValid && fatValid && carbsValid && 
                   caloriesValid && imageUrlValid;
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            if (isFormValid()) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-success');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-success');
                submitBtn.classList.add('btn-secondary');
            }
        }

        // Инициализация динамической валидации (БЕЗ подсветки)
        function initRealTimeValidation() {
            // Название
            document.getElementById('createName').addEventListener('input', function() {
                updateSubmitButton();
            });

            // Категория
            document.getElementById('createCategory').addEventListener('change', function() {
                updateSubmitButton();
            });

            // БЖУ - БЛОКИРОВКА БУКВ
            ['createProtein', 'createFat', 'createCarbs'].forEach((id, index) => {
                const input = document.getElementById(id);
                const fieldNames = ['Белки', 'Жиры', 'Углеводы'];
                
                input.addEventListener('input', function() {
                    validateNutrientField(this.value, this, fieldNames[index]);
                    updateSubmitButton();
                });
            });

            // Калории - БЛОКИРОВКА БУКВ
            document.getElementById('createCalories').addEventListener('input', function() {
                validateCaloriesField(this.value, this);
                updateSubmitButton();
            });

            // URL изображения
            document.getElementById('createImageUrl').addEventListener('input', function() {
                updateSubmitButton();
            });
        }

        // Проверка дубликатов названия
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

        // Отправка формы
        document.getElementById('createForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!isFormValid()) {
                alert('Исправьте ошибки в форме');
                return;
            }

            const btn = document.getElementById('submitBtn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Создание...';

            const formData = new FormData(this);
            try {
                const res = await fetch('admin_create.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert('✅ Продукт создан!');
                    this.reset();
                    updateSubmitButton();
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

        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            initRealTimeValidation();
            updateSubmitButton();
        });
    </script>
</body>
</html>
