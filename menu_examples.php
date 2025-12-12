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
                        <a href="menu_examples.php" class="btn active-page">
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