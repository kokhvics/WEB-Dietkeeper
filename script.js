document.addEventListener('DOMContentLoaded', function () {
	const filtersContainer = document.querySelector('.filters-container');
	const showFiltersButton = document.createElement('button');
	showFiltersButton.className = 'btn btn-success w-100';
	showFiltersButton.textContent = 'Показать фильтры';

	// Добавляем кнопку "Показать фильтры" на мобильных устройствах
	if (window.matchMedia('(max-width: 767px)').matches) {
			const container = document.querySelector('.container');
			container.insertBefore(showFiltersButton, container.firstChild);

			showFiltersButton.addEventListener('click', function () {
					filtersContainer.style.display = filtersContainer.style.display === 'block' ? 'none' : 'block';
					this.textContent = filtersContainer.style.display === 'block' ? 'Скрыть фильтры' : 'Показать фильтры';
			});
	}

	// Обработка toggle-list
	const toggleHeaders = document.querySelectorAll('.toggle-header');

	toggleHeaders.forEach(header => {
			header.addEventListener('click', function () {
					const toggleItem = header.parentElement;
					const toggleContent = toggleItem.querySelector('.toggle-content');

					// Переключаем класс active
					toggleItem.classList.toggle('active');

					// Если есть стрелка, поворачиваем её
					const arrow = header.querySelector('.arrow');
					if (arrow) {
							arrow.style.transform = toggleItem.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0deg)';
					}

					// Плавное отображение/скрытие содержимого
					if (toggleItem.classList.contains('active')) {
							toggleContent.style.maxHeight = toggleContent.scrollHeight + 'px'; // Устанавливаем высоту по содержимому
					} else {
							toggleContent.style.maxHeight = '0'; // Скрываем содержимое
					}
			});
	});
		// Показать/скрыть фильтры при клике на кнопку
		showFiltersButton.addEventListener('click', function () {
				filtersContainer.style.display = filtersContainer.style.display === 'block' ? 'none' : 'block';
				this.textContent = filtersContainer.style.display === 'block' ? 'Скрыть фильтры' : 'Показать фильтры';
		});
});