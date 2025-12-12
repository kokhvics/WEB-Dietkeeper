<?php
// products.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Продукты - DietKeeper</title>
    <!-- Подключение Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Подключение Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="products.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Шапка -->
        <header class="bg-light sticky-top position-relative">
            <div class="container d-flex justify-content-between align-items-center py-2">
                <!-- Логотип -->
                <a href="index.php" class="navbar-brand text-decoration-none text-dark">
                    <img src="SVG/logo.svg" alt="DietKeeper Logo" width="30" height="30" class="d-inline-block align-text-top">
                    <span class="site-title">DietKeeper</span>
                </a>

                <!-- Навигация -->
                <div class="d-flex gap-2 align-items-center">
                    <div class="d-none d-md-flex gap-2">
                        <!-- Кнопка "Примеры меню" -->
                        <a href="categories.html" class="btn">
                            <i class="bi bi-clipboard2-data me-1"></i> Примеры меню
                        </a>

                        <!-- Кнопка "Продукты" -->
                        <a href="products.php" class="btn active-page">
                            <i class="bi bi-basket-fill me-1"></i> Продукты
                        </a>

                        <!-- Кнопка "Помощь" -->
                        <a href="help.html" class="btn">
                            <i class="bi bi-question-circle me-1"></i> Помощь
                        </a>
                    </div>

                    <!-- Кнопка "Ещё" на мобильных -->
                    <div class="d-md-none">
                        <button class="btn btn-light border rounded-circle p-2" id="mobile-nav-toggle">
                            <i class="bi bi-three-dots fs-4"></i>
                        </button>
                        <!-- Выпадающее меню -->
                        <div id="mobile-nav-menu" class="d-none bg-white border rounded shadow position-absolute z-3" style="right: 1rem; top: 100%; min-width: 200px; margin-top: 0.5rem;">
                            <div class="p-2">
                                <a href="categories.html" class="d-flex align-items-center text-decoration-none text-dark py-2">
                                    <i class="bi bi-clipboard2-data me-2"></i> Примеры меню
                                </a>
                                <a href="products.php" class="d-flex align-items-center text-decoration-none text-dark py-2">
                                    <i class="bi bi-basket-fill me-2"></i> Продукты
                                </a>
                                <a href="help.html" class="d-flex align-items-center text-decoration-none text-dark py-2">
                                    <i class="bi bi-question-circle me-2"></i> Помощь
                                </a>
                            </div>
                        </div>
                    </div>   
                </div>
            </div>
        </header>

        <!-- Основной контент -->
        <div class="container mt-4">

            <!-- Фильтры -->
            <div class="filters-container">
                <form id="filters-form" method="GET" action="products.php">
                    <div class="row g-3 align-items-start">
                        <!-- Левый столбец: макронутриенты -->
                        <div class="col-12 col-md-4">
                            <div class="filter-group">
                                <label class="filter-item">
                                    <input type="checkbox" name="low_calorie" class="form-check-input filter-checkbox" <?= isset($_GET['low_calorie']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Низкокалорийные (<150 ккал)</span>
                                </label>
                                <label class="filter-item">
                                    <input type="checkbox" name="high_protein" class="form-check-input filter-checkbox" <?= isset($_GET['high_protein']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Высокий белок (>20г)</span>
                                </label>
                                <label class="filter-item">
                                    <input type="checkbox" name="high_carbs" class="form-check-input filter-checkbox" <?= isset($_GET['high_carbs']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Высокие углеводы (>60г)</span>
                                </label>
                                <label class="filter-item">
                                    <input type="checkbox" name="high_fat" class="form-check-input filter-checkbox" <?= isset($_GET['high_fat']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Высокие жиры (>30г)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Правый столбец: диетические -->
                        <div class="col-12 col-md-4">
                            <div class="filter-group">
                                <label class="filter-item">
                                    <input type="checkbox" name="vegetarian" class="form-check-input filter-checkbox" <?= isset($_GET['vegetarian']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Вегетарианцам</span>
                                </label>
                                <label class="filter-item">
                                    <input type="checkbox" name="no_sugar" class="form-check-input filter-checkbox" <?= isset($_GET['no_sugar']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Без сахара</span>
                                </label>
                                <label class="filter-item">
                                    <input type="checkbox" name="fasting" class="form-check-input filter-checkbox" <?= isset($_GET['fasting']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Можно в пост</span>
                                </label>
                            </div>
                        </div>

                        <!-- Третий столбец: диапазон калорий (на десктопе) -->
                        <div class="col-12 col-md-4">
                            <div class="range-inputs d-flex flex-wrap gap-2 align-items-center justify-content-center">
                                <div class="input-group">
                                    <span class="input-group-text">От</span>
                                    <input type="number" name="calories_min" class="form-control" min="0" max="1000" step="1" 
                                        value="<?= htmlspecialchars($_GET['calories_min'] ?? 0) ?>" 
                                        placeholder="0"
                                        oninput="validateCalorieRange(this, document.getElementById('calories_max'))">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">До</span>
                                    <input type="number" name="calories_max" id="calories_max" class="form-control" min="0" max="1000" step="1" 
                                        value="<?= htmlspecialchars($_GET['calories_max'] ?? 1000) ?>" 
                                        placeholder="1000"
                                        oninput="validateCalorieRange(this, document.getElementById('calories_min'))">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки с увеличенным отступом -->
                    <div class="mt-4 d-flex gap-3 flex-wrap">
                        <button type="submit" class="btn btn-success">Применить фильтры</button>
                        <a href="products.php" class="btn btn-outline-secondary">Сбросить фильтры</a>
                    </div>
                </form>
            </div>

            <!-- Индикатор загрузки -->
            <div id="loading-indicator" class="d-none text-center my-4">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
                <p class="mt-2 text-muted">Применение фильтров...</p>
            </div>

            <!-- Галерея продуктов -->
            <div id="products-gallery" class="gallery">
                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4" id="products-list">
                    <?php
                    // Подключение к базе данных
                    $servername = "localhost";
                    $username = "root";
                    $password = "1234";
                    $dbname = "dietkeeper";

                    try {
                        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        // Базовый SQL-запрос
                        $sql = "SELECT id, name, category, protein, fat, carbs, calories, image_url FROM products WHERE 1=1";
                        $params = [];
                        
                        // Поиск по названию
                        if (!empty($_GET['search'])) {
                            $sql .= " AND name LIKE :search";
                            $params[':search'] = '%' . $_GET['search'] . '%';
                        }
                        
                        // Фильтр по калориям (если заданы значения)
                        if (!empty($_GET['calories_min']) || !empty($_GET['calories_max'])) {
                            $calories_min = $_GET['calories_min'] ?? 0;
                            $calories_max = $_GET['calories_max'] ?? 1000;
                            $sql .= " AND calories BETWEEN :calories_min AND :calories_max";
                            $params[':calories_min'] = $calories_min;
                            $params[':calories_max'] = $calories_max;
                        }
                        
                        // Низкокалорийные продукты
                        if (isset($_GET['low_calorie'])) {
                            $sql .= " AND calories < 150";
                        }
                        
                        // Высокое содержание углеводов
                        if (isset($_GET['high_carbs'])) {
                            $sql .= " AND carbs > 60";
                        }
                        
                        // Высокое содержание белка
                        if (isset($_GET['high_protein'])) {
                            $sql .= " AND protein > 20";
                        }
                        
                        // Высокое содержание жиров
                        if (isset($_GET['high_fat'])) {
                            $sql .= " AND fat > 30";
                        }
                        
                        // Подходит вегетарианцам
                        if (isset($_GET['vegetarian'])) {
                            $sql .= " AND category NOT IN ('Мясные продукты', 'Молочные продукты', 'Рыба и морепродукты')";
                        }
                        
                        // Без сахара
                        if (isset($_GET['no_sugar'])) {
                            $sql .= " AND category != 'Сладости'";
                        }
                        
                        // Можно в пост
                        if (isset($_GET['fasting'])) {
                            $sql .= " AND category NOT IN ('Мясные продукты', 'Молочные продукты', 'Рыба и морепродукты', 'Сладости')";
                        }
                        
                        $stmt = $conn->prepare($sql);
                        $stmt->execute($params);
                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (empty($products)) {
                            echo '<div class="col-12"><div class="alert alert-info">Нет продуктов, соответствующих выбранным фильтрам.</div></div>';
                        } else {
                            foreach ($products as $product) {
                                echo '<div class="col">';
                                echo '    <div class="card h-100">';
                                echo '        <img src="' . htmlspecialchars($product['image_url']) . '" class="card-img-top" alt="' . htmlspecialchars($product['name']) . '">';
                                echo '        <div class="card-body">';
                                echo '            <h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>';
                                echo '            <div class="nutrients">';
                                echo '                <small class="text-muted">';
                                echo '                    К: ' . htmlspecialchars($product['calories']) . 'ккал | ';
                                echo '                    Б: ' . htmlspecialchars($product['protein']) . 'г | ';
                                echo '                    Ж: ' . htmlspecialchars($product['fat']) . 'г | ';
                                echo '                    У: ' . htmlspecialchars($product['carbs']) . 'г';
                                echo '                </small>';
                                echo '            </div>';
                                echo '        </div>';
                                echo '    </div>';
                                echo '</div>';
                            }
                        }
                    } catch(PDOException $e) {
                        echo '<div class="col-12"><div class="alert alert-danger">Ошибка загрузки данных: ' . $e->getMessage() . '</div></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Подключение Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        //переключения меню
        document.getElementById('mobile-nav-toggle')?.addEventListener('click', function(e) {
            e.stopPropagation();
            const menu = document.getElementById('mobile-nav-menu');
            menu.classList.toggle('d-none');
        });

        // Закрывать меню при клике вне его
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('mobile-nav-menu');
            const button = document.getElementById('mobile-nav-toggle');
            if (menu && !menu.contains(e.target) && e.target !== button) {
                menu.classList.add('d-none');
            }
        });
                
        // Инициализация значений при загрузке
        updateSliderValues();
        
        // Функция для валидации диапазона калорий
        function validateCalorieRange(input, otherInput) {
            let value = parseInt(input.value);
            const min = 0;
            const max = 1000;

            // Если значение пустое или не число — устанавливаем минимальное/максимальное
            if (isNaN(value) || value === '') {
                input.value = input.name === 'calories_min' ? min : max;
                return;
            }

            // Ограничиваем значение в пределах [0, 1000]
            if (value < min) {
                input.value = min;
                value = min;
            } else if (value > max) {
                input.value = max;
                value = max;
            }

            // Убедимся, что "От" <= "До"
            const otherValue = parseInt(otherInput.value);
            if (input.name === 'calories_min' && value > otherValue) {
                otherInput.value = value;
            } else if (input.name === 'calories_max' && value < otherValue) {
                input.value = otherValue;
            }
        }


        // === AJAX-фильтрация с визуальной обратной связью ===
        const filtersForm = document.getElementById('filters-form');
        const productsList = document.getElementById('products-list');
        const searchInput = document.querySelector('input[name="search"]');

        // Функция для сериализации формы в URLSearchParams
        function serializeForm(form) {
            const formData = new FormData(form);
            const params = new URLSearchParams();
            for (const [name, value] of formData.entries()) {
                if (value === 'on') params.append(name, '1');
                else if (value) params.append(name, value);
            }
            return params.toString();
        }

        // Функция загрузки продуктов с анимацией
        function loadProducts() {
            const params = serializeForm(filtersForm);
            const url = `products-ajax.php?${params}`;

            // Анимация исчезновения
            productsList.style.opacity = '0.4';
            productsList.style.transition = 'opacity 0.2s';

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    // Плавное появление нового контента
                    setTimeout(() => {
                        productsList.innerHTML = html;
                        productsList.style.opacity = '1';
                    }, 150);
                })
                .catch(err => {
                    console.error('Ошибка фильтрации:', err);
                    productsList.innerHTML = '<div class="col-12"><div class="alert alert-danger">Ошибка при загрузке продуктов.</div></div>';
                    productsList.style.opacity = '1';
                });
        }

        // Обновление при изменении любого фильтра
        filtersForm.addEventListener('change', loadProducts);
        if (searchInput) {
            searchInput.addEventListener('input', loadProducts);
        }

        // Подсветка активных чекбоксов
        document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const label = this.nextElementSibling;
                if (this.checked) {
                    label.classList.add('text-success', 'fw-bold');
                } else {
                    label.classList.remove('text-success', 'fw-bold');
                }
            });
            // Инициализация при загрузке
            if (checkbox.checked) {
                checkbox.nextElementSibling.classList.add('text-success', 'fw-bold');
            }
        });

        // Подсветка значений слайдера
        const minSlider = document.getElementById('calories-range-min');
        const maxSlider = document.getElementById('calories-range-max');
        [minSlider, maxSlider].forEach(slider => {
            if (slider) {
                slider.addEventListener('input', function() {
                    document.getElementById('calories-min-value').textContent = minSlider.value;
                    document.getElementById('calories-max-value').textContent = maxSlider.value;
                });
            }
        });

        // Показ спиннера при отправке формы фильтров
        document.getElementById('filters-form')?.addEventListener('submit', function() {
            const loadingIndicator = document.getElementById('loading-indicator');
            const productsGallery = document.getElementById('products-gallery');
            if (loadingIndicator && productsGallery) {
                loadingIndicator.classList.remove('d-none');
                productsGallery.classList.add('d-none');
            }
        });
    </script>
</body>
</html>