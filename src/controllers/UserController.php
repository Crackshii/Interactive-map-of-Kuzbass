<?php

use Models\User;

class UserController
{
<<<<<<< HEAD
    // Получить текущего авторизованного пользователя
    public static function getCurrentUser(PDO $db): ?User
=======
    public static function getUsers(\PDO $db, string $search = ''): array
    {
        $users = User::getAll($db);

        if ($search === '') {
            return $users;
        }

        return array_values(array_filter($users, function (User $user) use ($search) {
            return mb_stripos($user->username, $search, 0, 'UTF-8') !== false;
        }));
    }

    public static function getProfileUser(\PDO $db): ?User
>>>>>>> 483df2521e49956af65d327eeb512e7c29ee405c
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
<<<<<<< HEAD
        
=======

        return $user;
    }

    public static function getProfilePointsCount(\PDO $db): int
    {
        $user = self::getProfileUser($db);

        if ($user === null) {
            return 0;
        }

        return count($user->getPoints());
    }

    public static function deleteUser(\PDO $db): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Метод не разрешен');
        }

        $userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;

        if ($userId > 0) {
            $user = new User($db);

            if ($user->load($userId)) {
                $user->delete();
            }
        }

        $search = isset($_POST['search']) ? trim((string) $_POST['search']) : '';
        $redirect = '?page=admin';

        if ($search !== '') {
            $redirect .= '&search=' . urlencode($search);
        }

        header('Location: ' . $redirect);
        exit;
    }

    private static function resolveUserId(\PDO $db): ?int
    {
        if (!empty($_SESSION['user']['id'])) {
            return (int) $_SESSION['user']['id'];
        }

        if (!empty($_SESSION['user_id'])) {
            return (int) $_SESSION['user_id'];
        }

        if (!empty($_SESSION['id'])) {
            return (int) $_SESSION['id'];
        }

        try {
            $stmt = $db->query("SELECT id FROM users ORDER BY id ASC LIMIT 2");
            $userIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            return null;
        }

        if (count($userIds) === 1) {
            $userId = (int) $userIds[0];
            $_SESSION['user'] = ['id' => $userId];
            $_SESSION['user_id'] = $userId;
            $_SESSION['id'] = $userId;
            return $userId;
        }

>>>>>>> 483df2521e49956af65d327eeb512e7c29ee405c
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