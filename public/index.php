<?php
define('ROOT_PATH', dirname(__DIR__) . "/src/");

include_once ROOT_PATH . "settings/pdo.php";
include_once ROOT_PATH . "models/Point.php";
require_once '../src/models/User.php';
require_once '../src/models/Comment.php';

use Models\Point;
use Models\User;
use Models\Comment;

$conn = getConnection();
if ($conn[0]) {
    $pdo = $conn[1];
    
    $point = new Point($pdo);
    $point->load(1);
    $author = $point->getUser();
    echo "Точку создал: {$author->username}";

    // Получить комментарии к точке
    $comments = $point->getComments();
    foreach ($comments as $comment) {
    echo "Комментарий: {$comment->title}";
    }

} else {
    echo $conn[1];
}