<?php

class Option {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function addOption($question_id, $option_text, $is_correct) {
        $stmt = $this->conn->prepare("INSERT INTO options (id, option_text, is_correct) VALUES (:question_id, :option_text, :is_correct)");
        $stmt->bindParam(':question_id', $question_id);
        $stmt->bindParam(':option_text', $option_text);
        $stmt->bindParam(':is_correct', $is_correct);
        $stmt->execute();
    }
}

