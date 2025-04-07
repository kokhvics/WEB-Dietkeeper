document.addEventListener('DOMContentLoaded', function () {
	const filtersContainer = document.querySelector('.filters-container');
	const showFiltersButton = document.querySelector('.show-filters-button');
	
	// Инициализация состояния кнопки
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
	window.addEventListener('resize', function() {
			if (window.matchMedia('(min-width: 992px)').matches) {
					filtersContainer.classList.remove('active');
					showFiltersButton.innerHTML = 'Показать фильтры <i class="bi bi-funnel"></i>';
			}
	});

	// Обработка toggle-list (ваш существующий код)
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
});