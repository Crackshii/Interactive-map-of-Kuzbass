<?php

require_once __DIR__ . '/../models/Point.php';

use Models\Point;

class PointController
{
    public static function store(?PDO $pdo = null): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Метод не разрешен');
        }

        if (!$pdo instanceof PDO) {
            http_response_code(500);
            exit('Нет подключения к базе данных');
        }

        $userId = self::resolveUserId($pdo);

        if ($userId === null) {
            http_response_code(401);
            exit('Пользователь не авторизован');
        }

        $x = isset($_POST['x']) ? (float) $_POST['x'] : null;
        $y = isset($_POST['y']) ? (float) $_POST['y'] : null;

        if ($x === null || $y === null) {
            http_response_code(422);
            exit('Координаты не переданы');
        }

        if ($x < -90 || $x > 90 || $y < -180 || $y > 180) {
            http_response_code(422);
            exit('Некорректные координаты');
        }

        Point::create($pdo, $userId, $x, $y);

        header('Location: ?page=home');
        exit;
    }

    private static function resolveUserId(PDO $pdo): ?int
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
            $stmt = $pdo->query('SELECT id FROM users ORDER BY id ASC LIMIT 2');
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Throwable $e) {
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