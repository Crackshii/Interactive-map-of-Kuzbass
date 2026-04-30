<?php

require_once __DIR__ . '/../models/Point.php';
require_once __DIR__ . '/../models/PointStory.php';
require_once __DIR__ . '/../models/Comment.php';

use Models\Comment;
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
        $commentTitle = trim((string) ($_POST['comment_title'] ?? ''));
        $commentText = trim((string) ($_POST['comment_text'] ?? ''));

        if ($x === null || $y === null) {
            http_response_code(422);
            exit('Координаты не переданы');
        }

        if ($commentTitle === '' || $commentText === '') {
            http_response_code(422);
            exit('Заполните заголовок и текст комментария');
        }

        if ($x < -90 || $x > 90 || $y < -180 || $y > 180) {
            http_response_code(422);
            exit('Некорректные координаты');
        }

        try {
            $pdo->beginTransaction();

            $point = Point::create($pdo, $userId, $x, $y);

            if (!$point instanceof Point) {
                throw new RuntimeException('Не удалось создать точку');
            }

            $comment = new Comment($pdo);
            $comment->title = $commentTitle;
            $comment->text = $commentText;
            $comment->point_id = (int) $point->id;
            $comment->user_id = $userId;

            if (!$comment->save()) {
                throw new RuntimeException('Не удалось создать комментарий');
            }

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            http_response_code(500);
            exit('Не удалось добавить точку');
        }

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
