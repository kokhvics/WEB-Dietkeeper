<?php
// debug_config.php
echo "<h2>Отладка config.php</h2>";

// Проверяем, загружается ли файл
if (!file_exists('config.php')) {
    die("❌ Файл config.php не существует!");
}

echo "✅ Файл config.php существует<br>";

// Загружаем конфиг
$config = include 'config.php';

echo "<pre>";
echo "Содержимое config.php:\n";
print_r($config);
echo "</pre>";

// Проверяем настройки GitHub
if (isset($config['github'])) {
    echo "✅ Раздел github существует<br>";
    echo "client_id: " . ($config['github']['client_id'] ?? 'НЕТ') . "<br>";
    echo "client_secret: " . ($config['github']['client_secret'] ? 'ЕСТЬ (скрыт)' : 'НЕТ') . "<br>";
    echo "redirect_uri: " . ($config['github']['redirect_uri'] ?? 'НЕТ') . "<br>";
} else {
    echo "❌ Раздел github НЕ существует в config.php<br>";
}
?>