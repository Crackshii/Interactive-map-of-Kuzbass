<?php
namespace Models;

use PDO;
use PDOException;

class User {
    public $id;
    public $username;
    public $password;
    public $role;

    private $db;

    public function __construct(PDO $db) 
    {
        $this->db = $db;
    }

    public function load($id) 
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $this->id = $data['id'];
                $this->username = $data['username'];
                $this->password = $data['password'];
                $this->role = $data['role'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Ошибка загрузки пользователя: " . $e->getMessage());
            return false;
        }
    }

    public function save() 
    {
        try {
            if ($this->id) {
                $stmt = $this->db->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
                return $stmt->execute([$this->username, $this->password, $this->role, $this->id]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $result = $stmt->execute([$this->username, $this->password, $this->role]);
                if ($result) {
                    $this->id = $this->db->lastInsertId();
                }
                return $result;
            }
        } catch (PDOException $e) {
            error_log("Ошибка сохранения пользователя: " . $e->getMessage());
            return false;
        }
    }

    public function delete() 
    {
        if (!$this->id) return false;
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Ошибка удаления пользователя: " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(PDO $db) 
    {
        try {
            $stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $users = [];
            foreach ($rows as $row) {
                $user = new self($db);
                $user->id = $row['id'];
                $user->username = $row['username'];
                $user->password = $row['password'];
                $user->role = $row['role'];
                $users[] = $user;
            }
            return $users;
        } catch (PDOException $e) {
            error_log("Ошибка получения списка пользователей: " . $e->getMessage());
            return [];
        }
    }

    public function getPoints() 
    {
        $stmt = $this->db->prepare("SELECT * FROM points WHERE user_id = ?");
        $stmt->execute([$this->id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $points = [];
        foreach ($rows as $row) {
            $point = new Point($this->db);
            $point->id = $row['id'];
            $point->x = $row['x'];
            $point->y = $row['y'];
            $point->user_id = $row['user_id'];
            $point->photo = $row['photo'];
            $points[] = $point;
        }
        return $points;
    }
}