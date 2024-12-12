<?php
include_once __DIR__ . '/db.php';

class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $email, $password, $role) {
        $query = "INSERT INTO " . $this->table_name . " (username, email, password, role, created_at) VALUES (:username, :email, :password, :role, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT));
        $stmt->bindParam(':role', $role);
        return $stmt->execute();
    }

    public function login($username, $password) {
        $query = "SELECT id, password, role FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function verifyEmail($user_id) {
        $query = "UPDATE " . $this->table_name . " SET email_verified = TRUE WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }
}

class JobPost {
    private $conn;
    private $table_name = "job_posts";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($title, $description, $location, $expiry_date, $salary, $requirements, $created_by) {
        $query = "INSERT INTO " . $this->table_name . " (title, description, location, expiry_date, salary, requirements, created_by, created_at) VALUES (:title, :description, :location, :expiry_date, :salary, :requirements, :created_by, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':expiry_date', $expiry_date);
        $stmt->bindParam(':salary', $salary);
        $stmt->bindParam(':requirements', $requirements);
        $stmt->bindParam(':created_by', $created_by);
        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class Application {
    private $conn;
    private $table_name = "applications";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function apply($applicant_id, $job_post_id, $resume, $message) {
        $query = "INSERT INTO " . $this->table_name . " (applicant_id, job_post_id, resume, message, created_at) VALUES (:applicant_id, :job_post_id, :resume, :message, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':applicant_id', $applicant_id);
        $stmt->bindParam(':job_post_id', $job_post_id);
        $stmt->bindParam(':resume', $resume);
        $stmt->bindParam(':message', $message);
        return $stmt->execute();
    }

    public function updateStatus($application_id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :application_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':application_id', $application_id);
        return $stmt->execute();
    }

    public function getApplicationsByJob($job_post_id) {
        $query = "SELECT applications.id, applications.status, applications.message, applications.resume, users.username 
                  FROM " . $this->table_name . " 
                  INNER JOIN users ON applications.applicant_id = users.id 
                  WHERE job_post_id = :job_post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':job_post_id', $job_post_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class Message {
    private $conn;
    private $table_name = "messages";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function send($sender_id, $receiver_id, $content, $parent_message_id = null) {
        $query = "INSERT INTO " . $this->table_name . " (sender_id, receiver_id, content, parent_message_id, timestamp) 
                  VALUES (:sender_id, :receiver_id, :content, :parent_message_id, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sender_id', $sender_id);
        $stmt->bindParam(':receiver_id', $receiver_id);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':parent_message_id', $parent_message_id);
        return $stmt->execute();
    }
    
    public function getMessagesBetweenUsers($user_id, $other_user_id) {
        $query = "SELECT messages.id, messages.content, messages.sender_id, messages.timestamp, users.username 
                  FROM " . $this->table_name . " 
                  INNER JOIN users ON messages.sender_id = users.id 
                  WHERE (sender_id = :user_id AND receiver_id = :other_user_id) 
                  OR (sender_id = :other_user_id AND receiver_id = :user_id)
                  ORDER BY messages.timestamp ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':other_user_id', $other_user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getApplicantsWhoMessaged($hr_id) {
        $query = "SELECT DISTINCT users.id, users.username 
                  FROM messages 
                  INNER JOIN users ON messages.sender_id = users.id 
                  WHERE messages.receiver_id = :hr_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hr_id', $hr_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}


?>

