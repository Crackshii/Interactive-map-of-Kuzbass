<?php
$search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
$users = UserController::getUsers($pdo, $search);
?>

<section class="admin-page">
    <div class="admin-panel-card">
        <h1>Панель администратора</h1>

        <form method="get" class="admin-search-shell">
            <input type="hidden" name="page" value="admin">
            <label class="admin-search-label" for="admin-user-search">Поиск пользователя</label>
            <input
                id="admin-user-search"
                name="search"
                class="admin-search-input"
                type="text"
                value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                placeholder="Введите имя пользователя"
            >
        </form>

        <div class="admin-users-list">
            <?php if ($users === []): ?>
                <div class="admin-empty-state">
                    <p>Пользователи не найдены.</p>
                </div>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <div class="admin-user-card">
                        <div class="admin-user-meta">
                            <div class="admin-user-avatar"></div>
                            <div class="admin-user-text">
                                <div class="admin-user-name"><?= htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="admin-user-role"><?= htmlspecialchars($user->role, ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>

                        <form method="post" action="?page=admin_delete_user">
                            <input type="hidden" name="user_id" value="<?= (int) $user->id ?>">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="admin-user-delete-button">Удалить</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
