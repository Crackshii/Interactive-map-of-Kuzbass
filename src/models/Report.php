<?php
namespace Models;

use PDO;

class Report
{
    public static function getReports(PDO $db): array
    {
        return [
            self::getPointsReport($db),
            self::getUserPointsReport($db),
            self::getCommentsReport($db),
            self::getHistoryReport($db),
            self::getSummaryReport($db),
        ];
    }

    public static function getReport(PDO $db, string $type): ?array
    {
        switch ($type) {
            case 'points':
                return self::getPointsReport($db);
            case 'user_points':
                return self::getUserPointsReport($db);
            case 'comments':
                return self::getCommentsReport($db);
            case 'history':
                return self::getHistoryReport($db);
            case 'summary':
                return self::getSummaryReport($db);
        }

        return null;
    }

    private static function getPointsReport(PDO $db): array
    {
        $sql = "
            SELECT
                p.id,
                p.x,
                p.y,
                u.id AS user_id,
                u.username,
                u.role,
                p.photo,
                (
                    SELECT ps.status
                    FROM point_stories ps
                    WHERE ps.point_id = p.id
                    ORDER BY ps.date DESC, ps.id DESC
                    LIMIT 1
                ) AS last_status,
                (
                    SELECT ps.date
                    FROM point_stories ps
                    WHERE ps.point_id = p.id
                    ORDER BY ps.date DESC, ps.id DESC
                    LIMIT 1
                ) AS last_status_date
            FROM points p
            INNER JOIN users u ON u.id = p.user_id
            ORDER BY p.id DESC
        ";

        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return [
            'type' => 'points',
            'title' => 'Общий список точек интерактивной карты',
            'description' => 'Все точки, добавленные на карту, с пользователем и последним статусом.',
            'filename' => 'map_points_report',
            'columns' => [
                'id' => 'ID точки',
                'x' => 'Широта / X',
                'y' => 'Долгота / Y',
                'user_id' => 'ID пользователя',
                'username' => 'Имя пользователя',
                'role' => 'Роль пользователя',
                'photo' => 'Фото',
                'last_status' => 'Последний статус точки',
                'last_status_date' => 'Дата последнего статуса',
            ],
            'rows' => array_map(function (array $row): array {
                return [
                    'id' => (string) $row['id'],
                    'x' => (string) $row['x'],
                    'y' => (string) $row['y'],
                    'user_id' => (string) $row['user_id'],
                    'username' => (string) $row['username'],
                    'role' => (string) $row['role'],
                    'photo' => $row['photo'] ? 'Есть' : 'Нет',
                    'last_status' => $row['last_status'] ?: 'Нет статуса',
                    'last_status_date' => $row['last_status_date'] ?: 'Нет даты',
                ];
            }, $rows),
        ];
    }

    private static function getUserPointsReport(PDO $db): array
    {
        $sql = "
            SELECT
                u.id,
                u.username,
                u.role,
                COUNT(p.id) AS points_count,
                SUM(CASE WHEN p.photo IS NOT NULL AND p.photo <> '' THEN 1 ELSE 0 END) AS points_with_photo,
                SUM(CASE WHEN p.id IS NOT NULL AND (p.photo IS NULL OR p.photo = '') THEN 1 ELSE 0 END) AS points_without_photo
            FROM users u
            LEFT JOIN points p ON p.user_id = u.id
            GROUP BY u.id, u.username, u.role
            ORDER BY u.id DESC
        ";

        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return [
            'type' => 'user_points',
            'title' => 'Отчёт по пользователям и количеству добавленных точек',
            'description' => 'Статистика активности пользователей по количеству созданных точек.',
            'filename' => 'user_points_report',
            'columns' => [
                'id' => 'ID пользователя',
                'username' => 'Имя пользователя',
                'role' => 'Роль',
                'points_count' => 'Количество добавленных точек',
                'points_with_photo' => 'Количество точек с фото',
                'points_without_photo' => 'Количество точек без фото',
            ],
            'rows' => array_map(function (array $row): array {
                return [
                    'id' => (string) $row['id'],
                    'username' => (string) $row['username'],
                    'role' => (string) $row['role'],
                    'points_count' => (string) ($row['points_count'] ?? 0),
                    'points_with_photo' => (string) ($row['points_with_photo'] ?? 0),
                    'points_without_photo' => (string) ($row['points_without_photo'] ?? 0),
                ];
            }, $rows),
        ];
    }

    private static function getCommentsReport(PDO $db): array
    {
        $sql = "
            SELECT
                c.id,
                c.title,
                c.text,
                p.id AS point_id,
                p.x,
                p.y,
                u.id AS user_id,
                u.username
            FROM comments c
            INNER JOIN points p ON p.id = c.point_id
            INNER JOIN users u ON u.id = c.user_id
            ORDER BY c.id DESC
        ";

        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return [
            'type' => 'comments',
            'title' => 'Отчёт по комментариям к точкам',
            'description' => 'Комментарии пользователей к объектам интерактивной карты.',
            'filename' => 'comments_report',
            'columns' => [
                'id' => 'ID комментария',
                'title' => 'Заголовок комментария',
                'text' => 'Текст комментария',
                'point_id' => 'ID точки',
                'coordinates' => 'Координаты точки',
                'user_id' => 'ID пользователя',
                'username' => 'Имя пользователя',
            ],
            'rows' => array_map(function (array $row): array {
                return [
                    'id' => (string) $row['id'],
                    'title' => $row['title'] ?: 'Без заголовка',
                    'text' => (string) $row['text'],
                    'point_id' => (string) $row['point_id'],
                    'coordinates' => $row['x'] . ', ' . $row['y'],
                    'user_id' => (string) $row['user_id'],
                    'username' => (string) $row['username'],
                ];
            }, $rows),
        ];
    }

    private static function getHistoryReport(PDO $db): array
    {
        $sql = "
            SELECT
                ps.id,
                p.id AS point_id,
                p.x,
                p.y,
                ps.status,
                ps.date,
                u.id AS user_id,
                u.username
            FROM point_stories ps
            INNER JOIN points p ON p.id = ps.point_id
            INNER JOIN users u ON u.id = p.user_id
            ORDER BY ps.date DESC, ps.id DESC
        ";

        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return [
            'type' => 'history',
            'title' => 'История изменения статусов точек',
            'description' => 'Все записи истории по точкам карты от новых к старым.',
            'filename' => 'point_history_report',
            'columns' => [
                'id' => 'ID записи истории',
                'point_id' => 'ID точки',
                'coordinates' => 'Координаты точки',
                'status' => 'Статус',
                'date' => 'Дата изменения статуса',
                'user_id' => 'ID пользователя',
                'username' => 'Имя пользователя',
            ],
            'rows' => array_map(function (array $row): array {
                return [
                    'id' => (string) $row['id'],
                    'point_id' => (string) $row['point_id'],
                    'coordinates' => $row['x'] . ', ' . $row['y'],
                    'status' => (string) $row['status'],
                    'date' => $row['date'] ?: 'Нет даты',
                    'user_id' => (string) $row['user_id'],
                    'username' => (string) $row['username'],
                ];
            }, $rows),
        ];
    }

    private static function getSummaryReport(PDO $db): array
    {
        $rows = [
            [
                'name' => 'Всего пользователей',
                'value' => (string) $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            ],
            [
                'name' => 'Всего точек',
                'value' => (string) $db->query("SELECT COUNT(*) FROM points")->fetchColumn(),
            ],
            [
                'name' => 'Всего комментариев',
                'value' => (string) $db->query("SELECT COUNT(*) FROM comments")->fetchColumn(),
            ],
            [
                'name' => 'Всего записей истории',
                'value' => (string) $db->query("SELECT COUNT(*) FROM point_stories")->fetchColumn(),
            ],
            [
                'name' => 'Количество администраторов',
                'value' => (string) $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn(),
            ],
            [
                'name' => 'Количество обычных пользователей',
                'value' => (string) $db->query("SELECT COUNT(*) FROM users WHERE role <> 'admin'")->fetchColumn(),
            ],
            [
                'name' => 'Количество точек с фото',
                'value' => (string) $db->query("SELECT COUNT(*) FROM points WHERE photo IS NOT NULL AND photo <> ''")->fetchColumn(),
            ],
            [
                'name' => 'Количество точек без фото',
                'value' => (string) $db->query("SELECT COUNT(*) FROM points WHERE photo IS NULL OR photo = ''")->fetchColumn(),
            ],
        ];

        return [
            'type' => 'summary',
            'title' => 'Сводный отчёт по состоянию проекта',
            'description' => 'Краткая статистика по пользователям, точкам, комментариям и истории.',
            'filename' => 'project_summary_report',
            'columns' => [
                'name' => 'Показатель',
                'value' => 'Значение',
            ],
            'rows' => $rows,
        ];
    }
}
