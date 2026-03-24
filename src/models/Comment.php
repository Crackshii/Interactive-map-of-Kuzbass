<?php
namespace Models;

use PDO;
use PDOException;

class Comment {
    public $id;
    public $title;
    public $text;
    public $point_id;
    public $user_id;

    private $db;

    public function __construct(PDO $db) 
    {
        $this->db = $db;
    }

    public function load($id) 
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM comments WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $this->id = $data['id'];
                $this->title = $data['title'];
                $this->text = $data['text'];
                $this->point_id = $data['point_id'];
                $this->user_id = $data['user_id'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Ошибка загрузки комментария: " . $e->getMessage());
            return false;
        }
    }

    public function save() 
    {
        try {
            if ($this->id) {
                $stmt = $this->db->prepare("UPDATE comments SET title = ?, text = ?, point_id = ?, user_id = ? WHERE id = ?");
                return $stmt->execute([$this->title, $this->text, $this->point_id, $this->user_id, $this->id]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO comments (title, text, point_id, user_id) VALUES (?, ?, ?, ?)");
                $result = $stmt->execute([$this->title, $this->text, $this->point_id, $this->user_id]);
                if ($result) {
                    $this->id = $this->db->lastInsertId();
                }
                return $result;
            }
        } catch (PDOException $e) {
            error_log("Ошибка сохранения комментария: " . $e->getMessage());
            return false;
        }
    }

    public function delete() 
    {
        if (!$this->id) return false;
        try {
            $stmt = $this->db->prepare("DELETE FROM comments WHERE id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Ошибка удаления комментария: " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(PDO $db) 
    {
        try {
            $stmt = $db->query("SELECT * FROM comments ORDER BY id DESC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $comments = [];
            foreach ($rows as $row) {
                $comment = new self($db);
                $comment->id = $row['id'];
                $comment->title = $row['title'];
                $comment->text = $row['text'];
                $comment->point_id = $row['point_id'];
                $comment->user_id = $row['user_id'];
                $comments[] = $comment;
            }
            return $comments;
        } catch (PDOException $e) {
            error_log("Ошибка получения списка комментариев: " . $e->getMessage());
            return [];
        }
    }

    public function getPoint() 
    {
        $point = new Point($this->db);
        $point->load($this->point_id);
        return $point;
    }

    public function getUser() 
    {
        $user = new User($this->db);
        $user->load($this->user_id);
        return $user;
    }
}