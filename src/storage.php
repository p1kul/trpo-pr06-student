<?php
function escape($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Загружаем JSON; при отсутствии или ошибке показываем понятное сообщение
function loadJson(string $path): array {
    if (!is_file($path)) {
        throw new RuntimeException('Файл ' . basename($path) . ' не найден');
    }
    $raw = file_get_contents($path);
    if ($raw === false) {
        throw new RuntimeException('Не удалось прочитать ' . basename($path));
    }
    try {
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        throw new RuntimeException('Ошибка JSON в файле ' . basename($path));
    }
    if (!is_array($data) || !array_is_list($data)) {
        throw new RuntimeException('Неверная структура файла ' . basename($path));
    }
    return $data;
}

function saveNotes(string $path, array $notes): void {
    $json = json_encode($notes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    if (file_put_contents($path, $json . "\n", LOCK_EX) === false) {
        throw new RuntimeException('Не удалось сохранить заметки');
    }
}

function render(string $template, array $data): void {
    extract($data);
    require __DIR__ . '/../templates/' . $template . '.php';
}
