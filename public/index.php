<?php
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
}

require_once '../src/settings/pdo.php';
require_once '../src/models/Point.php';
require_once '../src/models/User.php';
require_once '../src/models/Comment.php';
require_once '../src/models/PointStory.php';
require_once '../src/models/Report.php';
require_once '../src/controllers/PointController.php';
require_once '../src/controllers/UserController.php';
require_once '../src/controllers/AuthController.php';
require_once '../src/controllers/CommentController.php';
require_once '../src/controllers/ReportController.php';

$conn = getConnection();
$pdo = $conn[1];

$page = $_GET['page'] ?? 'home';

// POST маршруты
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($page === 'login') {
        AuthController::login($pdo);
    }
    if ($page === 'register') {
        AuthController::register($pdo);
    }
    if ($page === 'points/store') {
        PointController::store($pdo);
    }
    if ($page === 'comments/store') {
        CommentController::store($pdo);
    }
}

// GET маршруты
if ($page === 'logout') {
    AuthController::logout();
}

if ($page === 'profile') {
    UserController::profile($pdo);
    exit;
}

if ($page === 'my_points') {
    UserController::myPoints($pdo);
    exit;
}

if ($page === 'reports') {
    ReportController::index($pdo);
    exit;
}

if ($page === 'reports/export') {
    ReportController::export($pdo);
    exit;
}

if ($page === 'admin_delete_user') {
    UserController::deleteUser($pdo);
    exit;
}

// Для остальных страниц
session_start();
$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['role'] ?? null;
$mapPoints = [];
$selectedPointId = isset($_GET['point_id']) ? (int) $_GET['point_id'] : 0;

if ($page === 'home') {
    $mapPoints = PointController::getMapPoints($pdo);
}

include '../src/views/layouts/header.php';

switch ($page) {
    case 'home':
        include '../src/views/home.php';
        break;
    case 'login':
        include '../src/views/auth/login.php';
        break;
    case 'register':
        include '../src/views/auth/register.php';
        break;
    case 'admin':
        if ($userRole !== 'admin') {
            header('Location: ?page=home');
            exit;
        }
        include '../src/views/admin/dashboard.php';
        break;
    case 'admin_users':
        if ($userRole !== 'admin') {
            header('Location: ?page=home');
            exit;
        }
        include '../src/views/admin/users.php';
        break;
    default:
        include '../src/views/404.php';
        break;
}

include '../src/views/layouts/footer.php';
