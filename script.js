document.addEventListener('DOMContentLoaded', function () {
	// === Блок 1: Обработка фильтров ===
	const filtersContainer = document.querySelector('.filters-container');
	const showFiltersButton = document.querySelector('.show-filters-button');

	// Инициализация состояния кнопки фильтров
	let isFiltersVisible = false;

	// Обработчик для кнопки "Показать/скрыть фильтры"
	if (showFiltersButton) {
			showFiltersButton.addEventListener('click', function () {
					isFiltersVisible = !isFiltersVisible;
					filtersContainer.classList.toggle('active');

					// Обновление иконки и текста
					const icon = this.querySelector('i');
					icon.classList.toggle('bi-funnel-fill');
					icon.classList.toggle('bi-funnel');
					this.innerHTML = isFiltersVisible 
							? 'Скрыть фильтры <i class="bi bi-funnel-fill"></i>' 
							: 'Показать фильтры <i class="bi bi-funnel"></i>';
			});
	}

	// Адаптация при изменении размера окна
	window.addEventListener('resize', function () {
			if (window.matchMedia('(min-width: 992px)').matches) {
					filtersContainer.classList.remove('active');
					showFiltersButton.innerHTML = 'Показать фильтры <i class="bi bi-funnel"></i>';
			}
	});

	// === Блок 2: Обработка toggle-list ===
	const toggleHeaders = document.querySelectorAll('.toggle-header');
	toggleHeaders.forEach(header => {
			header.addEventListener('click', function () {
					const toggleItem = header.parentElement;
					const toggleContent = toggleItem.querySelector('.toggle-content');
					toggleItem.classList.toggle('active');

					const arrow = header.querySelector('.arrow');
					if (arrow) {
							arrow.style.transform = toggleItem.classList.contains('active') 
									? 'rotate(180deg)' 
									: 'rotate(0deg)';
					}

					toggleContent.style.maxHeight = toggleItem.classList.contains('active') 
							? toggleContent.scrollHeight + 'px' 
							: '0';
			});
	});

	// === Блок 3: Обработка показа/скрытия категорий ===
	const showCategoriesButton = document.querySelector('.show-categories');
	const categoriesSidebar = document.querySelector('.categories-sidebar');

	if (showCategoriesButton && categoriesSidebar) {
			showCategoriesButton.addEventListener('click', function() {
					categoriesSidebar.classList.toggle('active');
					
					// Сохраняем иконку перед изменением текста
					const icon = this.querySelector('i');
					const iconClass = categoriesSidebar.classList.contains('active') 
							? 'bi-chevron-up' 
							: 'bi-chevron-down';
					
					// Полностью пересобираем содержимое кнопки
					this.innerHTML = categoriesSidebar.classList.contains('active') 
							? 'Скрыть категории <i class="bi ' + iconClass + '"></i>' 
							: 'Показать категории <i class="bi ' + iconClass + '"></i>';
			});
	}
});