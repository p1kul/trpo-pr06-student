<?php
session_start();
require_once __DIR__ . '/../src/storage.php';

$page = $_GET['page'] ?? 'notes';
if ($page !== 'notes') {
    http_response_code(404);
    echo '<!doctype html><html lang="ru"><meta charset="UTF-8"><title>404</title><body><h1>404 — Страница не найдена</h1><a href="/">На главную</a></body></html>';
    exit;
}

$path = __DIR__ . '/../data/notes.json';
$errors = [];
$loadError = '';
$title = '';
$body = '';

try { $notes = loadJson($path); }
catch (RuntimeException $exception) { $notes = []; $loadError = $exception->getMessage(); }
try { $users = loadJson(__DIR__ . '/../data/users.json'); }
catch (RuntimeException $exception) { $users = []; $errors[] = $exception->getMessage(); }
try { $scenarios = loadJson(__DIR__ . '/../data/session_scenarios.json'); }
catch (RuntimeException $exception) { $scenarios = []; $errors[] = $exception->getMessage(); }

$userNames = [];
foreach ($users as $user) {
    $userNames[(int)$user['id']] = $user['name'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'choose_user') {
        $id = (int)($_POST['user_id'] ?? 0);
        if (isset($userNames[$id])) { $_SESSION['user_id'] = $id; }
        header('Location: /');
        exit;
    }
    if ($action === 'clear_user') {
        unset($_SESSION['user_id']);
        header('Location: /');
        exit;
    }
    if ($action === 'add_note') {
        $title = trim((string)($_POST['title'] ?? ''));
        $body = trim((string)($_POST['body'] ?? ''));
        if (preg_match_all('/[\s\S]/u', $title) < 3 || preg_match_all('/[\s\S]/u', $title) > 80) { $errors[] = 'Заголовок: от 3 до 80 символов'; }
        if (preg_match_all('/[\s\S]/u', $body) < 3 || preg_match_all('/[\s\S]/u', $body) > 500) { $errors[] = 'Текст: от 3 до 500 символов'; }
        if ($loadError !== '') { $errors[] = 'Сохранение недоступно: ' . $loadError; }
        if (!$errors) {
            $ids = array_column($notes, 'id');
            $newId = $ids ? max($ids) + 1 : 1;
            $notes[] = [
                'id' => $newId,
                'title' => $title,
                'body' => $body,
                'author' => $userNames[$_SESSION['user_id'] ?? 0] ?? 'Гость',
            ];
            try {
                saveNotes($path, $notes);
                header('Location: /?saved=1'); // PRG: обновление не добавит заметку снова
                exit;
            } catch (RuntimeException $exception) {
                $errors[] = $exception->getMessage();
            }
        }
    }
}

$displayName = $userNames[$_SESSION['user_id'] ?? 0] ?? 'Гость';
render('list', compact('notes', 'users', 'scenarios', 'errors', 'loadError', 'title', 'body', 'displayName'));
