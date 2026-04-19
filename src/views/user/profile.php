<div class="profile-wrapper">
    <div class="profile-block">
        <div class="profile-left">
            <h2 class="profile-name">
                <?= htmlspecialchars($profileUser->username ?? 'Пользователь не найден') ?>
            </h2>
            <span class="profile-role">
                <?= htmlspecialchars($profileUser->role ?? 'Нет данных') ?>
            </span>
        </div>

        <div class="profile-right">
            <h3>Личная информация</h3>

            <div class="profile-info-list">
                <div class="info-item">
                    <span class="info-label">Имя пользователя</span>
                    <span class="info-value">
                        <?= htmlspecialchars($profileUser->username ?? 'Нет данных') ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">ID аккаунта</span>
                    <span class="info-value">
                        <?= htmlspecialchars((string) ($profileUser->id ?? 'Нет данных')) ?>
                    </span>
                </div>
            </div>

            <h3>Дополнительно</h3>

            <div class="profile-info-list">
                <div class="info-item">
                    <span class="info-label">Ваши точки на карте</span>
                    <span class="info-value">
                        <?= htmlspecialchars((string) ($profilePointsCount ?? 0)) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>