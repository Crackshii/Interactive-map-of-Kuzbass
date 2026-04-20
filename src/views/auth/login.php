<div class="auth-container">
    <h1>Вход</h1>

    <?php if (isset($error)): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="?page=login">
        <div>
            <label>Логин:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Пароль:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Войти</button>
    </form>

    <p>Нет аккаунта? <a href="?page=register">Зарегистрироваться</a></p>
</div>  