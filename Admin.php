<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: User.php');
    exit();
}

include('BDD.php');

class Admin {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function createQuiz($title, $description, $theme) {
        $stmt = $this->conn->prepare("INSERT INTO quizzes (title, description, theme) VALUES (:title, :description, :theme)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':theme', $theme);
        $stmt->execute();
    }

    public function createQuestion($quiz_id, $question_text, $answers, $correct_answer) {
        if (empty($quiz_id)) {
            throw new Exception("Veuillez sélectionner un quiz.");
        }

        $this->conn->beginTransaction();

        try {
            $stmt = $this->conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (:quiz_id, :question_text)");
            $stmt->bindParam(':quiz_id', $quiz_id);
            $stmt->bindParam(':question_text', $question_text);
            $stmt->execute();

            $question_id = $this->conn->lastInsertId();

            $stmt = $this->conn->prepare("INSERT INTO options (id, option_text, is_correct) VALUES (:id, :option_text, :is_correct)");
            for ($i = 0; $i < 4; $i++) {
                $is_correct = ($i + 1 == $correct_answer) ? 1 : 0;
                $stmt->bindParam(':id', $question_id);
                $stmt->bindParam(':option_text', $answers[$i]);
                $stmt->bindParam(':is_correct', $is_correct);
                $stmt->execute();
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception("Failed: " . $e->getMessage());
        }
    }

    public function getQuizzes() {
        $stmt = $this->conn->prepare("SELECT * FROM quizzes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$admin = new Admin($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['create_quiz'])) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $theme = $_POST['theme'];

            $admin->createQuiz($title, $description, $theme);
            header('Location: admin.php');
            exit();
        } elseif (isset($_POST['create_question'])) {
            $quiz_id = $_POST['quiz_id'];
            $question_text = $_POST['question_text'];
            $answers = [
                $_POST['answer1'],
                $_POST['answer2'],
                $_POST['answer3'],
                $_POST['answer4']
            ];
            $correct_answer = $_POST['correct_answer'];

            $admin->createQuestion($quiz_id, $question_text, $answers, $correct_answer);
            header('Location: admin.php');
            exit();
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

$quizzes = $admin->getQuizzes();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panneau d'administration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-3xl">
        <h1 class="text-2xl font-bold text-gray-800 text-center mb-6">Créer un nouveau quiz</h1>
        <form method="POST" class="space-y-4">
            <input type="text" name="title" placeholder="Titre du quiz" class="w-full p-3 border border-gray-300 rounded-lg" required>
            <textarea name="description" placeholder="Description du quiz" class="w-full p-3 border border-gray-300 rounded-lg" required></textarea>
            <input type="text" name="theme" placeholder="Thème du quiz" class="w-full p-3 border border-gray-300 rounded-lg" required>
            <button type="submit" name="create_quiz" class="w-full bg-purple-600 text-white p-3 rounded-lg font-bold hover:bg-purple-700 transition duration-300">Créer le quiz</button>
        </form>

        <h1 class="text-2xl font-bold text-gray-800 text-center my-6">Créer une nouvelle question</h1>
        <form method="POST" class="space-y-4">
            <label for="quiz_id" class="block text-gray-700 font-medium">Sélectionnez le quiz :</label>
            <select name="quiz_id" id="quiz_id" class="w-full p-3 border border-gray-300 rounded-lg" required>
                <option value="" disabled selected>Sélectionnez un quiz</option>
                <?php foreach ($quizzes as $quiz): ?>
                    <option value="<?php echo $quiz['quiz_id']; ?>"><?php echo htmlspecialchars($quiz['title']); ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST['quiz_id'])): ?>
                <p class="text-red-500">Veuillez sélectionner un quiz.</p>
            <?php endif; ?>
            <textarea name="question_text" placeholder="Texte de la question" class="w-full p-3 border border-gray-300 rounded-lg" required></textarea>
            <input type="text" name="answer1" placeholder="Réponse 1" class="w-full p-3 border border-gray-300 rounded-lg" required>
            <input type="text" name="answer2" placeholder="Réponse 2" class="w-full p-3 border border-gray-300 rounded-lg" required>
            <input type="text" name="answer3" placeholder="Réponse 3" class="w-full p-3 border border-gray-300 rounded-lg" required>
            <input type="text" name="answer4" placeholder="Réponse 4" class="w-full p-3 border border-gray-300 rounded-lg" required>
            <label for="correct_answer" class="block text-gray-700 font-medium">Réponse correcte :</label>
            <select name="correct_answer" id="correct_answer" class="w-full p-3 border border-gray-300 rounded-lg" required>
                <option value="1">Réponse 1</option>
                <option value="2">Réponse 2</option>
                <option value="3">Réponse 3</option>
                <option value="4">Réponse 4</option>
            </select>
            <button type="submit" name="create_question" class="w-full bg-purple-600 text-white p-3 rounded-lg font-bold hover:bg-purple-700 transition duration-300">Créer la question</button>
        </form>

        <h1 class="text-2xl font-bold text-gray-800 text-center my-6">Quiz existants</h1>
        <ul class="space-y-4">
            <?php foreach ($quizzes as $quiz): ?>
                <li class="bg-gray-100 p-4 rounded-lg shadow-md hover:shadow-lg transition duration-300"><?php echo htmlspecialchars($quiz['title']) . " - " . htmlspecialchars($quiz['theme']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
