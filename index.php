<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DietKeeper</title>
    <!-- Подключение Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Подключение Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="STYLE/styles.css">
    <link rel="stylesheet" href="STYLE/index.css">
</head>
<body>
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
                        <a href="products.php" class="btn">
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
    <div class="container mt-5">
        <!-- Поиск -->
        <form class="d-flex mb-4" id="search-form">
            <div class="input-group input-group-lg search-bar">
                <input type="text" id="search-input" class="search-bar form-control" placeholder="Поиск продукта/категории" aria-label="Search">
                <span class="input-group-text bg-transparent border-start-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                </span>
            </div>
        </form>

        <!-- Кнопка "Показать категории" для мобильных -->
        <button class="btn btn-success w-100 show-categories d-md-none mb-3">
            Показать категории <i class="bi bi-chevron-down"></i>
        </button>
        <!-- Категории -->
        <div class="row">
            <div class="col-md-3 categories-sidebar active">
                <div class="list-group">
                    <?php
                    // Массив категорий с соответствующими иконками Bootstrap Icons
                    $categories = [
                    'Овощи' => 'fas fa-carrot',
                    'Фрукты' => 'fas fa-apple-alt',
                    'Крупы' => 'fas fa-wheat-awn',
                    'Мясные продукты' => 'fas fa-drumstick-bite',
                    'Рыба и морепродукты' => 'fas fa-fish',
                    'Грибы' => 'fas fa-leaf',
                    'Напитки' => 'fas fa-glass-water'
                ];
                    
                    $selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
                    $isAllSelected = empty($selectedCategory);
                    
                    // Кнопка "Показать все" - всегда зеленая
                    echo '<a href="?" class="btn ' . ($isAllSelected ? 'active-category' : '') . '">';
                    echo '    <i class="bi bi-grid-3x3-gap-fill me-2"></i>Показать все';
                    echo '</a>';
                    
                    // Выводим категории как кнопки с основным стилем
                    foreach ($categories as $category => $icon) {
                        $isActive = ($selectedCategory === $category);
                        echo '<a href="?category=' . urlencode($category) . '" class="btn mb-2 ' . ($isActive ? 'active-category' : '') . '">';
                        echo '    <i class="' . $icon . ' me-2"></i>' . htmlspecialchars($category);
                        echo '</a>';
                    }
                    ?>
                    <!-- Кнопка "Показать еще" -->
                    <a href="categories.html" class="btn btn-success w-100 d-md-none mt-3">
                        Показать еще <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-9">
                <!-- Индикатор загрузки -->
                <div id="loading-indicator" class="d-none text-center my-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    <p class="mt-2 text-muted">Загрузка данных...</p>
                </div>
                <!-- Продукты -->
                <div class="row row-cols-1 row-cols-md-3 g-4" id="products-container">
                    <?php
                    // Подключение к базе данных
                    $servername = "localhost";
                    $username = "root";
                    $password = "1234";
                    $dbname = "dietkeeper";

                    try {
                        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        // Определяем SQL-запрос в зависимости от выбранной категории
                        $sql = "SELECT id, name, category, protein, fat, carbs, calories, image_url FROM products";
                        $params = [];
                        
                        if (!empty($selectedCategory)) {
                            $sql .= " WHERE category = :category";
                            $params[':category'] = $selectedCategory;
                        }
                        
                        $stmt = $conn->prepare($sql);
                        $stmt->execute($params);
                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        function formatNutrient($value) {
                            // Убираем лишние нули: 20.0 → 20, 5.50 → 5.5
                            return rtrim(rtrim($value, '0'), '.');
                        }

                        if (empty($products)) {
                            echo '<div class="col-12"><div class="alert alert-info">Нет продуктов в выбранной категории.</div></div>';
                        } else {
                            foreach ($products as $product) {
                                echo '<div class="col">';
                                echo '    <div class="card h-100">';
                                echo '        <img src="' . htmlspecialchars($product['image_url']) . '" class="card-img-top" alt="' . htmlspecialchars($product['name']) . '">';
                                echo '        <div class="card-body">';
                                echo '            <h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>';
                                echo '            <div class="nutrients">';
                                echo '                <small class="text-muted nutrients-info">';
                                echo '                    <span data-bs-toggle="tooltip" data-bs-title="Калории">Б</span>:   ' . formatNutrient($product['calories']) . 'ккал | ';
                                echo '                    <span data-bs-toggle="tooltip" data-bs-title="Белки">Б</span>: ' . formatNutrient($product['protein']) . 'г | ';
                                echo '                    <span data-bs-toggle="tooltip" data-bs-title="Жиры">Ж</span>: ' . formatNutrient($product['fat']) . 'г | ';
                                echo '                    <span data-bs-toggle="tooltip" data-bs-title="Углеводы">У</span>: ' . formatNutrient($product['carbs']) . 'г';
                                echo '                </small>';
                                echo '            </div>';
                                echo '        </div>';
                                echo '    </div>';
                                echo '</div>';
                            }
                        }
                    } catch(PDOException $e) {
                        // В случае ошибки выводим сообщение
                        echo '<div class="col-12">';
                        echo '    <div class="alert alert-danger">Ошибка загрузки данных: ' . $e->getMessage() . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Подключение Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Обработчик для кнопки показа категорий на мобильных устройствах
        document.querySelector('.show-categories').addEventListener('click', function() {
            const sidebar = document.querySelector('.categories-sidebar');
            sidebar.classList.toggle('active');
            
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('active')) {
                icon.classList.remove('bi-chevron-up');
                icon.classList.add('bi-chevron-down');
            } else {
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-up');
            }
        });

        // Динамический поиск с перезаписью карточек
        const searchInput = document.getElementById('search-input');
        const productsContainer = document.getElementById('products-container');
        let originalHtml = productsContainer.innerHTML; // Сохраняем исходные карточки

        searchInput.addEventListener('input', function () {
            const query = this.value.trim();

            if (query.length >= 1) {
                // Показываем загрузку (опционально — см. предыдущий ответ)
                productsContainer.innerHTML = `
                    <div class="col-12 text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Загрузка...</span>
                        </div>
                    </div>
                `;

                fetch(`search.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        productsContainer.innerHTML = ''; // Очистка перед выводом
                        if (data.length > 0) {
                            data.forEach(product => {
                                const card = `
                                    <div class="col">
                                        <div class="card h-100">
                                            <img src="${product.image_url}" class="card-img-top" alt="${product.name}">
                                            <div class="card-body">
                                                <h5 class="card-title">${product.name}</h5>
                                                <div class="nutrients">
                                                    <small class="text-muted nutrients-info">
                                                        <span data-bs-toggle="tooltip" data-bs-title="Калории">Б</span>: ${product.calories}ккал | 
                                                        <span data-bs-toggle="tooltip" data-bs-title="Белки">Б</span>: ${product.protein}г | 
                                                        <span data-bs-toggle="tooltip" data-bs-title="Жиры">Ж</span>: ${product.fat}г | 
                                                        <span data-bs-toggle="tooltip" data-bs-title="Углеводы">У</span>: ${product.carbs}г
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                productsContainer.innerHTML += card;
                            });
                        } else {
                            productsContainer.innerHTML = `
                                <div class="col-12">
                                    <div class="alert alert-info">Ничего не найдено.</div>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка при выполнении запроса:', error);
                        productsContainer.innerHTML = `
                            <div class="col-12">
                                <div class="alert alert-danger">Произошла ошибка при поиске.</div>
                            </div>
                        `;
                    });
            } else {
                // Восстанавливаем исходные карточки (все или по категории)
                productsContainer.innerHTML = originalHtml;
            }
        });
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'top',
                trigger: 'hover'
            });
        });

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
              
    </script>
</body>
</html>