<?php

use Models\PointStory;
use Models\User;

class UserController
{
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

    // Получить данные для страницы профиля
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
        
        $profileUser = $user;
        $profilePointsCount = $pointsCount;
        
        include '../src/views/layouts/header.php';
        include '../src/views/user/profile.php';
        include '../src/views/layouts/footer.php';
        exit;
    }
    
    // Получить данные для страницы "Мои точки"
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

        $myPoints = [];

        foreach ($points as $point) {
            $stories = PointStory::getByPointId($db, $point->id);

            $myPoints[] = [
                'point' => $point,
                'story' => $stories[0] ?? null,
            ];
        }
        
        include '../src/views/layouts/header.php';
        include '../src/views/user/my_points.php';
        include '../src/views/layouts/footer.php';
        exit;
    }
}
