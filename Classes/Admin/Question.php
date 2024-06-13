<?php

class Question {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createQuestion($quiz_id, $question_text) {
        $stmt = $this->conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (:quiz_id, :question_text)");
        $stmt->bindParam(':quiz_id', $quiz_id);
        $stmt->bindParam(':question_text', $question_text);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
}
