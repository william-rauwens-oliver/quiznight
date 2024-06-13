<?php

class Question {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getQuestion($quiz_id, $question_index) {
        $stmt_question = $this->conn->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id LIMIT 1 OFFSET :offset");
        $stmt_question->bindParam(':quiz_id', $quiz_id, PDO::PARAM_INT);
        $stmt_question->bindParam(':offset', $question_index, PDO::PARAM_INT);
        $stmt_question->execute();
        return $stmt_question->fetch(PDO::FETCH_ASSOC);
    }
}
?>
