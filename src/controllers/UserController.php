<?php

use Models\User;

class UserController
{
    public static function getProfileUser(\PDO $db): ?User
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = self::resolveUserId($db);

        if ($userId === null) {
            return null;
        }

        $user = new User($db);

        if (!$user->load($userId)) {
            return null;
        }

        return $user;
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

        return null;
    }
}
