<?php

class AuthController
{
    public static function getCurrentUserId(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['user_id'])) {
            return (int) $_SESSION['user_id'];
        }

        return null;
    }

    public static function login(PDO $pdo): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: ?page=home');
            exit;
        } else {
            $error = "Неверный логин или пароль";
            include '../src/views/layouts/header.php';
            include '../src/views/auth/login.php';
            include '../src/views/layouts/footer.php';
            exit;
        }
    }
    
    public static function register(PDO $pdo): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        if ($password !== $passwordConfirm) {
            $error = "Пароли не совпадают";
            include '../src/views/layouts/header.php';
            include '../src/views/auth/register.php';
            include '../src/views/layouts/footer.php';
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Логин уже занят";
            include '../src/views/layouts/header.php';
            include '../src/views/auth/register.php';
            include '../src/views/layouts/footer.php';
            exit;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $result = $stmt->execute([$username, $hashedPassword, $role]);
        
        if ($result) {
            $userId = $pdo->lastInsertId();
            session_start();
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            header('Location: ?page=home');
            exit;
        } else {
            $error = "Ошибка регистрации";
            include '../src/views/layouts/header.php';
            include '../src/views/auth/register.php';
            include '../src/views/layouts/footer.php';
            exit;
        }
    }
    
    public static function logout(): void
    {
        session_start();
        session_destroy();
        header('Location: ?page=home');
        exit;
    }
    
    public static function checkAuth(): void
    {
        session_start();
        if (empty($_SESSION['user_id'])) {
            header('Location: ?page=login');
            exit;
        }
    }
    
    public static function checkAdmin(): void
    {
        session_start();
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ?page=home');
            exit;
        }
    }
}
