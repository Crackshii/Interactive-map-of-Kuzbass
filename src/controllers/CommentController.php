<?php

use Models\Comment;

class CommentController
{
    public static function store(PDO $pdo): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Метод не разрешен');
        }

        $userId = AuthController::getCurrentUserId();

        if ($userId === null) {
            http_response_code(401);
            exit('Пользователь не авторизован');
        }

        $pointId = isset($_POST['point_id']) ? (int) $_POST['point_id'] : 0;
        $title = trim((string) ($_POST['title'] ?? ''));
        $text = trim((string) ($_POST['text'] ?? ''));

        if ($pointId <= 0 || $title === '' || $text === '') {
            http_response_code(422);
            exit('Данные комментария не переданы');
        }

        $comment = new Comment($pdo);
        $comment->title = $title;
        $comment->text = $text;
        $comment->point_id = $pointId;
        $comment->user_id = $userId;
        $comment->save();

        header('Location: ?page=home&point_id=' . $pointId);
        exit;
    }
}
