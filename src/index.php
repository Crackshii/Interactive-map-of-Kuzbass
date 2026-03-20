<?php
    define('ROOT_PATH', dirname(__DIR__) . "/src/");

    include_once ROOT_PATH . "settings/pdo.php";
        
    $conn = getConnection();
    if ($conn[0]) {
        $pdo = $conn[1];
        $stmt = $pdo->query('SELECT * FROM point');
        $points = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($points as $point) {
            echo "Точка ID: {$point['id']}, координаты: ({$point['x']}, {$point['y']})<br>";
        }
    }
    else {
        echo $conn[1];
    }
?>