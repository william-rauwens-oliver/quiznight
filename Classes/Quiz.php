<?php

class Quiz {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getThemes() {
        try {
            $stmt_themes = $this->conn->query("SELECT DISTINCT theme FROM quizzes");
            return $stmt_themes->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            throw new Exception("Erreur PDO : " . $e->getMessage());
        }
    }

    public function getQuizByTheme($theme) {
        $stmt_quiz = $this->conn->prepare("SELECT * FROM quizzes WHERE theme = :theme");
        $stmt_quiz->bindParam(':theme', $theme);
        $stmt_quiz->execute();
        return $stmt_quiz->fetch(PDO::FETCH_ASSOC);
    }
}
?>
