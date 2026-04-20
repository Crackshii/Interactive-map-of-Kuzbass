<?php
namespace Models;

use PDO;
use PDOException;
use Models\User;
use Models\Comment;

class Point
{
    public $id;
    public $x;
    public $y;
    public $user_id;
    public $photo;

    private $db;

    public static function create(PDO $db, int $userId, float $x, float $y): ?self
    {
        $sql = "INSERT INTO points (photo, user_id, x, y) VALUES (NULL, :user_id, :x, :y)";
        $stmt = $db->prepare($sql);
        
        $result = $stmt->execute([
            ':user_id' => $userId,
            ':x' => $x,
            ':y' => $y,
        ]);
        
        if (!$result) {
            return null;
        }
        
        $point = new self($db);
        $point->id = $db->lastInsertId();
        $point->x = $x;
        $point->y = $y;
        $point->user_id = $userId;
        $point->photo = null;
        
        return $point;
    }

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function load($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM points WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $this->id = $data['id'];
                $this->x = $data['x'];
                $this->y = $data['y'];
                $this->user_id = $data['user_id'];
                $this->photo = $data['photo'];
                return true;
            }

            return false;
        } catch (PDOException $e) {
            error_log("Ошибка загрузки точки: " . $e->getMessage());
            return false;
        }
    }

    public function save()
    {
        try {
            if ($this->id) {
                $stmt = $this->db->prepare("UPDATE points SET x = ?, y = ?, user_id = ?, photo = ? WHERE id = ?");
                return $stmt->execute([$this->x, $this->y, $this->user_id, $this->photo, $this->id]);
            }

            $stmt = $this->db->prepare("INSERT INTO points (x, y, user_id, photo) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$this->x, $this->y, $this->user_id, $this->photo]);

            if ($result) {
                $this->id = $this->db->lastInsertId();
                $this->addToHistory('created');
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Ошибка сохранения точки: " . $e->getMessage());
            return false;
        }
    }

    public function delete()
    {
        if (!$this->id) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM points WHERE id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Ошибка удаления точки: " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(PDO $db)
    {
        try {
            $stmt = $db->query("SELECT * FROM points ORDER BY id DESC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $points = [];
            foreach ($rows as $row) {
                $point = new self($db);
                $point->id = $row['id'];
                $point->x = $row['x'];
                $point->y = $row['y'];
                $point->user_id = $row['user_id'];
                $point->photo = $row['photo'];
                $points[] = $point;
            }

            return $points;
        } catch (PDOException $e) {
            error_log("Ошибка получения списка точек: " . $e->getMessage());
            return [];
        }
    }

    public function getUser()
    {
        $user = new User($this->db);
        $user->load($this->user_id);
        return $user;
    }

    public function getComments()
    {
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE point_id = ?");
        $stmt->execute([$this->id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $comments = [];
        foreach ($rows as $row) {
            $comment = new Comment($this->db);
            $comment->id = $row['id'];
            $comment->title = $row['title'];
            $comment->text = $row['text'];
            $comment->point_id = $row['point_id'];
            $comment->user_id = $row['user_id'];
            $comments[] = $comment;
        }

        return $comments;
    }

    public function addToHistory($status): bool
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO point_stories (point_id, status, date) VALUES (?, ?, NOW())");
            return $stmt->execute([$this->id, $status]);
        } catch (PDOException $e) {
            error_log("Ошибка добавления в историю: " . $e->getMessage());
            return false;
        }
    }
}
?>