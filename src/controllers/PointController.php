<?php

require_once __DIR__ . '/../models/Point.php';
require_once __DIR__ . '/../models/PointStory.php';

use Models\Point;
use Models\PointStory;

class PointController
{
    public static function store(?PDO $pdo = null): void
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Метод не разрешен');
        }

        if (!$pdo instanceof PDO) {
            http_response_code(500);
            exit('Нет подключения к базе данных');
        }

        $userId = AuthController::getCurrentUserId();

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

    public static function getMapPoints(PDO $pdo): array
    {
        $points = Point::getAll($pdo);
        $mapPoints = [];

        foreach ($points as $point) {
            $user = $point->getUser();
            $stories = PointStory::getByPointId($pdo, $point->id);
            $story = $stories[0] ?? null;
            $comments = [];

            foreach ($point->getComments() as $comment) {
                $commentUser = $comment->getUser();
                $comments[] = [
                    'id' => $comment->id,
                    'title' => $comment->title,
                    'text' => $comment->text,
                    'user_id' => $comment->user_id,
                    'username' => $commentUser->username ?? '',
                ];
            }

            $mapPoints[] = [
                'id' => $point->id,
                'x' => (float) $point->x,
                'y' => (float) $point->y,
                'user_id' => $point->user_id,
                'photo' => $point->photo,
                'username' => $user->username ?? '',
                'role' => $user->role ?? '',
                'status' => $story ? $story->status : '',
                'date' => $story ? $story->date : '',
                'comments' => $comments,
            ];
        }

        return $mapPoints;
    }
}
