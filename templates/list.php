<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Практическая №6 — Заметки</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header><div class="wrap">Практическая №6 — Заметки · Пользователь: <?= escape($displayName) ?></div></header>
<main>
    <section class="panel">
        <h1>Текущий пользователь (session)</h1>
        <form method="post">
            <input type="hidden" name="action" value="choose_user">
            <label for="user">Выберите пользователя</label>
            <select id="user" name="user_id">
                <?php foreach ($users as $user): ?>
                    <option value="<?= (int)$user['id'] ?>"><?= escape($user['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Выбрать</button>
        </form>
        <form method="post">
            <input type="hidden" name="action" value="clear_user">
            <button type="submit">Очистить выбор</button>
        </form>
    </section>
    <section class="panel">
        <h2>Добавить заметку (POST)</h2>
        <?php if (isset($_GET['saved'])): ?><p class="success">Заметка сохранена в JSON.</p><?php endif; ?>
        <?php if ($loadError): ?><p class="error"><?= escape($loadError) ?></p><?php endif; ?>
        <?php foreach ($errors as $error): ?><p class="error"><?= escape($error) ?></p><?php endforeach; ?>
        <form method="post">
            <input type="hidden" name="action" value="add_note">
            <label for="title">Заголовок (3–80 символов)</label>
            <input id="title" name="title" value="<?= escape($title) ?>">
            <label for="body">Текст заметки (3–500 символов)</label>
            <textarea id="body" name="body" rows="4"><?= escape($body) ?></textarea>
            <button type="submit">Добавить</button>
        </form>
    </section>
    <section class="panel">
        <h2>Список заметок (из JSON)</h2>
        <?php if (!$notes): ?><p>Заметок пока нет.</p><?php endif; ?>
        <div class="cards">
            <?php foreach ($notes as $note): ?>
                <article class="card">
                    <h3><?= escape($note['title'] ?? '') ?></h3>
                    <p><?= nl2br(escape($note['body'] ?? '')) ?></p>
                    <small class="muted">№<?= (int)($note['id'] ?? 0) ?> · <?= escape($note['author'] ?? '—') ?></small>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="panel">
        <h2>Сценарии проверки из JSON</h2>
        <?php foreach ($scenarios as $scenario): ?>
            <p><strong><?= escape($scenario['name'] ?? '') ?></strong>: <?= escape($scenario['expected'] ?? '') ?></p>
        <?php endforeach; ?>
    </section>
</main>
</body>
</html>
