<?php
namespace Models;

use PDO;
use PDOException;

class PointStory {
    public $id;
    public $date;
    public $status;
    public $point_id;

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function load($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM point_stories WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $this->id = $data['id'];
                $this->date = $data['date'];
                $this->status = $data['status'];
                $this->point_id = $data['point_id'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Ошибка загрузки записи истории: " . $e->getMessage());
            return false;
        }
    }

    public function save() {
        try {
            if ($this->id) {
                $stmt = $this->db->prepare("UPDATE point_stories SET date = ?, status = ?, point_id = ? WHERE id = ?");
                return $stmt->execute([$this->date, $this->status, $this->point_id, $this->id]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO point_stories (date, status, point_id) VALUES (?, ?, ?)");
                $result = $stmt->execute([$this->date, $this->status, $this->point_id]);
                if ($result) {
                    $this->id = $this->db->lastInsertId();
                }
                return $result;
            }
        } catch (PDOException $e) {
            error_log("Ошибка сохранения записи истории: " . $e->getMessage());
            return false;
        }
    }

    public function delete() {
        if (!$this->id) return false;
        try {
            $stmt = $this->db->prepare("DELETE FROM point_stories WHERE id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Ошибка удаления записи истории: " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(PDO $db) {
        try {
            $stmt = $db->query("SELECT * FROM point_stories ORDER BY date DESC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stories = [];
            foreach ($rows as $row) {
                $story = new self($db);
                $story->id = $row['id'];
                $story->date = $row['date'];
                $story->status = $row['status'];
                $story->point_id = $row['point_id'];
                $stories[] = $story;
            }
            return $stories;
        } catch (PDOException $e) {
            error_log("Ошибка получения списка истории: " . $e->getMessage());
            return [];
        }
    }

    public static function getByPointId(PDO $db, $pointId) {
        try {
            $stmt = $db->prepare("SELECT * FROM point_stories WHERE point_id = ? ORDER BY date DESC");
            $stmt->execute([$pointId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stories = [];
            foreach ($rows as $row) {
                $story = new self($db);
                $story->id = $row['id'];
                $story->date = $row['date'];
                $story->status = $row['status'];
                $story->point_id = $row['point_id'];
                $stories[] = $story;
            }
            return $stories;
        } catch (PDOException $e) {
            error_log("Ошибка получения истории точки: " . $e->getMessage());
            return [];
        }
    }

    public function getPoint() {
        $point = new Point($this->db);
        $point->load($this->point_id);
        return $point;
    }
}