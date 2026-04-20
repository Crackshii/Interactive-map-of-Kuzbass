<div class="points-container">
    <div class="points-header">
        <h2>Мои точки на карте</h2>
        <a href="?page=home" class="btn-profile-action" style="margin-top: 0; width: auto;">+ Добавить новую</a>
    </div>

    <div class="points-scroll-area">
        <div class="points-grid">
            <?php if ($myPoints === []): ?>
                <div class="point-card">
                    <span class="point-title">У вас пока нет точек</span>
                    <p class="point-desc">Добавьте первую точку на главной странице.</p>
                </div>
            <?php else: ?>
                <?php foreach ($myPoints as $item): ?>
                    <?php
                    $point = $item['point'];
                    $story = $item['story'];
                    $photo = $point->photo ?? '';
                    $status = $story && !empty($story->status) ? $story->status : 'Нет статуса';
                    $date = $story && !empty($story->date) ? date('d.m.Y H:i', strtotime($story->date)) : 'Нет даты';
                    ?>
                    <div class="point-card">
                        <?php if (!empty($photo)): ?>
                            <img src="<?= htmlspecialchars($photo, ENT_QUOTES, 'UTF-8') ?>" alt="Фото точки" class="point-photo">
                        <?php else: ?>
                            <div class="point-photo point-photo-empty">Фото отсутствует</div>
                        <?php endif; ?>

                        <span class="point-title">Точка #<?= htmlspecialchars((string) $point->id, ENT_QUOTES, 'UTF-8') ?></span>
                        <p class="point-desc">Статус: <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="point-meta">
                            <span>Координаты: <?= htmlspecialchars((string) $point->x, ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) $point->y, ENT_QUOTES, 'UTF-8') ?></span>
                            <span><?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
