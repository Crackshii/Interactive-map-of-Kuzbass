<?php
define('ROOT_PATH', dirname(__DIR__) . "/src/");

require_once '../src/settings/pdo.php';
require_once '../src/models/Point.php';
require_once '../src/models/User.php';
require_once '../src/models/Comment.php';
require_once '../src/controllers/UserController.php';

use Models\Point;
use Models\User;
use Models\Comment;

$conn = getConnection();
$pdo = $conn[1];

$page = $_GET['page'] ?? 'home';

include '../src/views/layouts/header.php';

switch ($page) {
    case 'home':
        include '../src/views/home.php';
        break;
    case 'my_points':
        include '../src/views/user/my_points.php';
        break;
    case 'profile':
        include '../src/views/user/profile.php';
        break;
    case 'admin':
        include '../src/views/admin/dashboard.php';
        break;
    case 'admin_users':
        include '../src/views/admin/users.php';
        break;
    default:
        include '../src/views/404.php';
        break;
}

include '../src/views/layouts/footer.php';
?>
