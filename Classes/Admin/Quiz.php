<?php

class Quiz {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createQuiz($title, $description, $theme) {
        $stmt = $this->conn->prepare("INSERT INTO quizzes (title, description, theme) VALUES (:title, :description, :theme)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':theme', $theme);
        $stmt->execute();
    }

    public function getAllQuizzes() {
        $stmt = $this->conn->prepare("SELECT * FROM quizzes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
