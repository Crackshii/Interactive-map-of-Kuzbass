<?php $profileUser = UserController::getProfileUser($pdo); ?>

<div class="profile-wrapper">
    <div class="profile-block">
        <div class="profile-left">
            <h2 class="profile-name">
                <?= htmlspecialchars($profileUser ? $profileUser->username : 'Пользователь не найден', ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <span class="profile-role">
                <?= htmlspecialchars($profileUser ? $profileUser->role : 'Нет данных', ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>

        <div class="profile-right">
            <h3>Личная информация</h3>

            <div class="profile-info-list">
                <div class="info-item">
                    <span class="info-label">Имя пользователя</span>
                    <span class="info-value">
                        <?= htmlspecialchars($profileUser ? $profileUser->username : 'Нет данных', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">ID аккаунта</span>
                    <span class="info-value">
                        <?= htmlspecialchars($profileUser ? (string) $profileUser->id : 'Нет данных', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
