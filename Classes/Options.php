<?php

class Options {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getOptions($question_id) {
        $stmt_options = $this->conn->prepare("SELECT * FROM options WHERE id = :question_id");
        $stmt_options->bindParam(':question_id', $question_id, PDO::PARAM_INT);
        $stmt_options->execute();
        return $stmt_options->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
