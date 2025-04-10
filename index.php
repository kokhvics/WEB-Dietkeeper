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
    <link rel="stylesheet" href="styles.css">
    <style>
        .list-group-item.active-category {
            background-color: #9ACD32;
            color: white;
            border-color: #9ACD32;
        }
        .show-all-btn {
            margin-top: 10px;
            border-radius: 20px !important;
        }
    </style>
</head>
<body>
    <!-- Шапка -->
    <header class="bg-light sticky-top">
        <div class="container d-flex justify-content-between align-items-center py-2">
                <!-- Логотип -->
                <div class="navbar-brand">
                    <img src="SVG/logo.svg" alt="DietKeeper Logo" width="30" height="30" class="d-inline-block align-text-top">
                    <span class="site-title">DietKeeper</span>
                </div>

            <!-- Навигация -->
            <div class="d-flex gap-2">
                <!-- Кнопка "Категории" в шапке -->
                <a href="categories.html" class="btn btn-outline-success rounded-pill px-4 d-none d-md-block">
                    <i class="bi bi-grid-fill me-2"></i> Категории
                </a>

                <!-- Кнопка "Продукты" -->
                <a href="products.php" class="btn btn-success rounded-pill px-4">
                    <i class="bi bi-basket-fill me-2"></i> Продукты
                </a>
            </div>
        </div>
    </header>

    <!-- Основной контент -->
    <div class="container mt-4">
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

        <!-- Результаты поиска -->
        <div id="search-results" class="row row-cols-1 row-cols-md-3 g-4 d-none"></div>

        <!-- Кнопка "Показать категории" для мобильных -->
        <button class="btn btn-success w-100 show-categories d-md-none mb-3">
            Показать категории <i class="bi bi-chevron-down"></i>
        </button>
        <!-- Категории -->
        <div class="row">
            <div class="col-md-3 categories-sidebar active">
                <div class="list-group">
                    <?php
                    $categories = [
                        'Овощи', 'Фрукты', 'Крупы', 'Мясо', 'Рыба', 'Грибы', 'Напитки'
                    ];
                    
                    $selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
                    $isAllSelected = empty($selectedCategory);
                    
                    // Выводим кнопку "Показать все" в начале списка
                    echo '<a href="?" class="list-group-item yellow-block ' . ($isAllSelected ? 'active-category' : '') . '">';
                    echo '    <i class="bi bi-grid-3x3-gap-fill me-2"></i>Показать все';
                    echo '</a>';
                    
                    // Выводим категории
                    foreach ($categories as $category) {
                        $isActive = ($selectedCategory === $category) ? 'active-category' : '';
                        echo '<a href="?category=' . urlencode($category) . '" class="list-group-item list-group-item-action yellow-block ' . $isActive . '">' . htmlspecialchars($category) . '</a>';
                    }
                    ?>
                    <!-- Кнопка "Показать еще" -->
                    <a href="categories.html" class="btn btn-success w-100 d-md-none mt-3">
                        Показать еще <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-9">
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
                        $sql = "SELECT id, name, category, proteins, fats, carbohydrates, calories, image_url FROM products";
                        $params = [];
                        
                        if (!empty($selectedCategory)) {
                            $sql .= " WHERE category = :category";
                            $params[':category'] = $selectedCategory;
                        }
                        
                        $stmt = $conn->prepare($sql);
                        $stmt->execute($params);
                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
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
                                echo '                <small class="text-muted">';
                                echo '                    К: ' . htmlspecialchars($product['calories']) . 'ккал | ';
                                echo '                    Б: ' . htmlspecialchars($product['proteins']) . 'г | ';
                                echo '                    Ж: ' . htmlspecialchars($product['fats']) . 'г | ';
                                echo '                    У: ' . htmlspecialchars($product['carbohydrates']) . 'г';
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
                                                        Б: ${product.proteins}г | 
                                                        Ж: ${product.fats}г | 
                                                        У: ${product.carbohydrates}г
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