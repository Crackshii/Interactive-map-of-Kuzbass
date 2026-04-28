<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Интерактивная карта</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/style2.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v10.8.0/ol.css">
</head>
<body>

<header class="navbar">
    <nav class="nav-links">
        <a href="?page=home">Главная Карта</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="?page=my_points">Мои Точки</a>
            <a href="?page=profile">Профиль</a>
            <a href="?page=reports">Отчёты</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="?page=admin">Панель Администратора</a>
            <?php endif; ?>
            <a href="?page=logout">Выход</a>
        <?php else: ?>
            <a href="?page=login">Вход</a>
            <a href="?page=register">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>

<main class="app-container">
