<?php
$search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
$users = UserController::getUsers($pdo, $search);
?>

<section class="admin-page">
    <div class="admin-panel-card">
        <h1>Пользователи</h1>

        <form method="get" class="admin-search-shell">
            <input type="hidden" name="page" value="admin_users">
            <label class="admin-search-label" for="users-search">Поиск пользователя</label>
            <input
                id="users-search"
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
                    <div class="admin-user-card admin-user-card-static">
                        <div class="admin-user-meta">
                            <div class="admin-user-avatar"></div>
                            <div class="admin-user-text">
                                <div class="admin-user-name"><?= htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="admin-user-role"><?= htmlspecialchars($user->role, ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
