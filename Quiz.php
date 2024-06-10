<?php
include('Config.php');

$themes = ['animaux', 'voitures', 'informatique'];

try {
    // Vérifier si le thème est spécifié dans l'URL
    if (isset($_GET['theme']) && in_array($_GET['theme'], $themes)) {
        $theme = $_GET['theme'];

        echo "Theme selected: $theme<br>";

        // Sélectionner les quiz associés à ce thème
        $stmt_quiz = $conn->prepare("SELECT * FROM quizzes WHERE theme = :theme");
        $stmt_quiz->bindParam(':theme', $theme);
        $stmt_quiz->execute();
        $quizzes = $stmt_quiz->fetchAll(PDO::FETCH_ASSOC);

        if (!$quizzes) {
            throw new Exception("Aucun quiz trouvé pour le thème '$theme'.");
        }

        // Afficher les titres des quiz et leurs questions associées
        echo "<h1>Quizzes on " . ucfirst($theme) . "</h1>";
        foreach ($quizzes as $quiz) {
            echo "<h2>" . htmlspecialchars($quiz['title']) . "</h2>";

            // Sélectionner les questions pour ce quiz
            $stmt_question = $conn->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id");
            $stmt_question->bindParam(':quiz_id', $quiz['id']);
            $stmt_question->execute();
            $questions = $stmt_question->fetchAll(PDO::FETCH_ASSOC);

            if (!$questions) {
                throw new Exception("Aucune question trouvée pour le quiz '{$quiz['title']}'.");
            }

            echo "<ul>";
            foreach ($questions as $question) {
                echo "<li>" . htmlspecialchars($question['question_text']) . "</li>";
            }
            echo "</ul>";
        }
    } else {
        // Si aucun thème n'est spécifié, affichez la liste des thèmes disponibles
        echo "<h1>Select a Theme</h1>";
        echo "<ul>";
        foreach ($themes as $theme_option) {
            echo "<li><a href='?theme=$theme_option'>" . ucfirst($theme_option) . "</a></li>";
        }
        echo "</ul>";
    }
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
