<div class="app-container">
    <aside class="sidebar sidebar-left">
        <h3>Фильтры</h3>
        <p>Всего точек: <?= count($mapPoints) ?></p>
    </aside>

    <div class="map-wrapper">
        <div
            id="map"
            data-map-points='<?= htmlspecialchars(json_encode($mapPoints, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>'
            data-selected-point-id="<?= $selectedPointId ?>"
        ></div>

        <div class="map-panel">
            <div>Широта: <span id="lat-value">—</span></div>
            <div>Долгота: <span id="lng-value">—</span></div>

            <form method="post" action="?page=points/store" id="point-form">
                <input type="hidden" name="x" id="point-x">
                <input type="hidden" name="y" id="point-y">
            </form>
        </div>
    </div>

    <aside class="sidebar sidebar-right">
        <h3>Информация о точке</h3>

        <div id="point-details-empty" class="point-details-empty">
            Выберите точку на карте
        </div>

        <div id="point-details-card" class="point-details-card point-details-hidden">
            <div id="point-details-photo" class="point-details-photo point-details-photo-empty">Фото отсутствует</div>

            <div class="point-details-list">
                <div class="point-details-row">
                    <span class="point-details-label">ID точки</span>
                    <span class="point-details-value" id="point-details-id">—</span>
                </div>
                <div class="point-details-row">
                    <span class="point-details-label">Пользователь</span>
                    <span class="point-details-value" id="point-details-username">—</span>
                </div>
                <div class="point-details-row">
                    <span class="point-details-label">ID пользователя</span>
                    <span class="point-details-value" id="point-details-user-id">—</span>
                </div>
                <div class="point-details-row">
                    <span class="point-details-label">Статус</span>
                    <span class="point-details-value" id="point-details-status">—</span>
                </div>
                <div class="point-details-row">
                    <span class="point-details-label">Дата</span>
                    <span class="point-details-value" id="point-details-date">—</span>
                </div>
                <div class="point-details-row">
                    <span class="point-details-label">Координаты</span>
                    <span class="point-details-value" id="point-details-coordinates">—</span>
                </div>
            </div>

            <div class="point-comments-block">
                <h4>Комментарии</h4>
                <div id="point-comments-list" class="point-comments-list"></div>
            </div>

            <?php if ($userId): ?>
                <form method="post" action="?page=comments/store" class="point-comment-form">
                    <input type="hidden" name="point_id" id="comment-point-id">
                    <label class="point-comment-label" for="comment-title">Заголовок</label>
                    <input type="text" name="title" id="comment-title" class="point-comment-input" required>

                    <label class="point-comment-label" for="comment-text">Комментарий</label>
                    <textarea name="text" id="comment-text" class="point-comment-textarea" required></textarea>

                    <button type="submit" class="point-comment-button">Оставить комментарий</button>
                </form>
            <?php else: ?>
                <div class="point-comment-login-note">Авторизуйтесь, чтобы оставить комментарий.</div>
            <?php endif; ?>
        </div>

        <div id="new-point-card" class="point-details-card point-details-hidden">
            <div class="new-point-header">
                <h4>Новая точка</h4>
                <p>Добавьте первый комментарий к новой точке</p>
            </div>

            <?php if ($userId): ?>
                <div class="point-comment-form">
                    <label class="point-comment-label" for="new-point-comment-title">Заголовок комментария</label>
                    <input
                        type="text"
                        name="comment_title"
                        id="new-point-comment-title"
                        class="point-comment-input"
                        placeholder="Введите заголовок комментария"
                        form="point-form"
                    >

                    <label class="point-comment-label" for="new-point-comment-text">Комментарий</label>
                    <textarea
                        name="comment_text"
                        id="new-point-comment-text"
                        class="point-comment-textarea"
                        placeholder="Введите комментарий"
                        form="point-form"
                    ></textarea>

                    <button type="submit" class="point-comment-button" form="point-form">Добавить точку</button>
                </div>
            <?php else: ?>
                <div class="point-comment-login-note">Авторизуйтесь, чтобы добавить новую точку.</div>
            <?php endif; ?>
        </div>
    </aside>
</div>
