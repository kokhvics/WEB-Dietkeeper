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

</head>
<body>
    <div class="container-fluid">
        <!-- Шапка -->
        <header class="bg-light sticky-top">
            <div class="container d-flex justify-content-between align-items-center py-2">
                <!-- Логотип с ссылкой на index.php -->
                <a href="index.php" class="navbar-brand text-decoration-none text-dark">
                    <img src="SVG/logo.svg" alt="DietKeeper Logo" width="30" height="30" class="d-inline-block align-text-top">
                    <span class="site-title">DietKeeper</span>
                </a>

                <!-- Навигация -->
                <div class="d-flex gap-2">
                    <!-- Кнопка "Категории" -->
                    <a href="categories.html" class="btn btn-outline-success rounded-pill px-4">
                        <i class="bi bi-grid-fill me-2"></i> Категории
                    </a>
                </div>
            </div>
        </header>

        <!-- Основной контент -->
        <div class="container mt-4">
            <!-- Поиск -->
            <form class="d-flex mb-4" method="GET" action="">
                <div class="input-group input-group-lg search-bar">
                    <input type="text" name="search" class="search-bar form-control" placeholder="Поиск продукта/категории" aria-label="Search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button type="submit" class="input-group-text bg-transparent border-start-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Результаты поиска -->
            <div id="search-results" class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4 d-none"></div>

            <!-- Кнопка "Показать фильтры" для мобильных устройств -->
            <button class="btn btn-success w-100 show-filters-button d-lg-none mb-3">
                Показать фильтры <i class="bi bi-funnel"></i>
            </button>

            <!-- Фильтры -->
            <div class="filters-container">
                <form method="GET" action="">
                    <div class="d-flex flex-wrap justify-content-between align-items-start">
                        <div class="filters-left">
                            <div class="filter-group">
                                <label class="filter-item">
                                    <input type="checkbox" name="low_calorie" class="form-check-input filter-checkbox" <?= isset($_GET['low_calorie']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Низкокалорийные продукты (<150 ккал)</span>
                                </label>
                                <label class="filter-item">
                                    <input type="checkbox" name="high_carbs" class="form-check-input filter-checkbox" <?= isset($_GET['high_carbs']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Высокое содержание углеводов (>60г)</span>
                                </label>
                                <label class="filter-item">
                                    <input type="checkbox" name="high_protein" class="form-check-input filter-checkbox" <?= isset($_GET['high_protein']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Высокое содержание белка (>20г)</span>
                                </label>
                                <label class="filter-item">
                                    <input type="checkbox" name="high_fat" class="form-check-input filter-checkbox" <?= isset($_GET['high_fat']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Высокое содержание жиров (>30г)</span>
                                </label>
                            </div>
                        </div>
                        <div class="filter-group mt-3">
                            <div class="filter-group">
                                <label class="filter-item">
                                    <input type="checkbox" name="vegetarian" class="form-check-input filter-checkbox" <?= isset($_GET['vegetarian']) ? 'checked' : '' ?>>
                                    <span class="filter-label">Подходит вегетарианцам</span>
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
                        <!-- Слайдер для калорий -->
                        <div class="range-slider">
                            <label for="calories-range">Диапазон ккал:</label>
                            <div class="slider-container">
                                <input type="range" name="calories_min" class="form-range calories-range" id="calories-range-min" min="0" max="1000" step="10" 
                                       value="<?= htmlspecialchars($_GET['calories_min'] ?? 0) ?>">
                                <input type="range" name="calories_max" class="form-range calories-range" id="calories-range-max" min="0" max="1000" step="10" 
                                       value="<?= htmlspecialchars($_GET['calories_max'] ?? 1000) ?>">
                                <div class="slider-values">
                                    <span id="calories-min-value"><?= htmlspecialchars($_GET['calories_min'] ?? 0) ?></span> - 
                                    <span id="calories-max-value"><?= htmlspecialchars($_GET['calories_max'] ?? 1000) ?></span> ккал
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Применить фильтры</button>
                    <a href="products.php" class="btn btn-outline-secondary">Сбросить фильтры</a>
                </form>
            </div>

            <!-- Галерея продуктов -->
            <div class="gallery">
                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
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
        // Показать/скрыть фильтры на мобильных устройствах
        document.querySelector('.show-filters-button').addEventListener('click', function() {
            document.querySelector('.filters-container').classList.toggle('show');
        });
        
        // Обновление значений слайдера
        const minSlider = document.getElementById('calories-range-min');
        const maxSlider = document.getElementById('calories-range-max');
        const minValue = document.getElementById('calories-min-value');
        const maxValue = document.getElementById('calories-max-value');
        
        function updateSliderValues() {
            minValue.textContent = minSlider.value;
            maxValue.textContent = maxSlider.value;
        }
        
        minSlider.addEventListener('input', updateSliderValues);
        maxSlider.addEventListener('input', updateSliderValues);
        
        // Инициализация значений при загрузке
        updateSliderValues();

        // Динамический поиск
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');
        const productsContainer = document.getElementById('products-container');

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            if (query.length >= 3) {
                fetch(`search.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        productsContainer.classList.add('d-none');
                        searchResults.classList.remove('d-none');

                        if (data.length > 0) {
                            data.forEach(product => {
                                const card = `
                                    <div class="col">
                                        <div class="card h-100">
                                            <img src="${product.image_url}" class="card-img-top" alt="${product.name}">
                                            <div class="card-body">
                                                <h5 class="card-title">${product.name}</h5>
                                                <div class="nutrients">
                                                    <small class="text-muted">
                                                        К: ${product.calories}ккал | 
                                                        Б: ${product.protein}г | 
                                                        Ж: ${product.fat}г | 
                                                        У: ${product.carbs}г
                                                                                                                                   </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                searchResults.innerHTML += card;
                            });
                        } else {
                            searchResults.innerHTML = '<div class="col-12"><div class="alert alert-info">Ничего не найдено.</div></div>';
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка при выполнении запроса:', error);
                        searchResults.innerHTML = '<div class="col-12"><div class="alert alert-danger">Произошла ошибка при поиске.</div></div>';
                    });
            } else {
                searchResults.classList.add('d-none');
                productsContainer.classList.remove('d-none');
            }
        });


    </script>
</body>
</html>