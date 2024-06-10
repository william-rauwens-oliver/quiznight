<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: Authentification.php');
    exit();
}

include('Config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_quiz'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $theme = $_POST['theme'];

        $stmt = $conn->prepare("INSERT INTO quizzes (title, description, theme) VALUES (:title, :description, :theme)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':theme', $theme);
        $stmt->execute();

        header('Location: admin.php');
        exit();
    } elseif (isset($_POST['create_question'])) {
        $quiz_id = $_POST['quiz_id'];
        $question_text = $_POST['question_text'];

        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (:quiz_id, :question_text)");
        $stmt->bindParam(':quiz_id', $quiz_id);
        $stmt->bindParam(':question_text', $question_text);
        $stmt->execute();

        header('Location: admin.php');
        exit();
    }
}

$stmt = $conn->prepare("SELECT * FROM quizzes");
$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <?php foreach ($quizzes as $quiz): ?>
                    <option value="<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['title']); ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="question_text" placeholder="Texte de la question" class="w-full p-3 border border-gray-300 rounded-lg" required></textarea>
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
