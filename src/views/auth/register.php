<div class="auth-container">
    <h1>Регистрация</h1>

    <?php if (isset($error)): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="?page=register">
        <div>
            <label>Логин:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Пароль:</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Подтверждение пароля:</labe  l>
            <input type="password" name="password_confirm" required>
        </div>
        <button type="submit">Зарегистрироваться</button>
    </form>

    <p>Уже есть аккаунт? <a href="?page=login">Войти</a></p>
</div>