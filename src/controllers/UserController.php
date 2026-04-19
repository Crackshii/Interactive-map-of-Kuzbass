<?php

use Models\User;

class UserController
{
    // Получить текущего авторизованного пользователя
    public static function getCurrentUser(PDO $db): ?User
    {
        session_start();
        
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            return null;
        }
        
        $user = new User($db);
        if ($user->load($userId)) {
            return $user;
        }
        
        return null;
    }
    
    // Получить профиль пользователя для отображения
    public static function profile(PDO $db): void
    {
        session_start();
        
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            header('Location: ?page=login');
            exit;
        }
        
        $user = new User($db);
        $user->load($userId);
        
        $pointsCount = count($user->getPoints());
        
        include '../src/views/layouts/header.php';
        include '../src/views/user/profile.php';
        include '../src/views/layouts/footer.php';
        exit;
    }
    
    // Получить все точки пользователя
    public static function myPoints(PDO $db): void
    {
        session_start();
        
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            header('Location: ?page=login');
            exit;
        }
        
        $user = new User($db);
        $user->load($userId);
        
        $points = $user->getPoints();
        
        include '../src/views/layouts/header.php';
        include '../src/views/user/my_points.php';
        include '../src/views/layouts/footer.php';
        exit;
    }
}