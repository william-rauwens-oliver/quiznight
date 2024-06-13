<?php
session_start();
include('BDD.php');

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function signUp($username, $password, $confirm_password) {
        $error = "";

        if ($password !== $confirm_password) {
            $error = "Les mots de passe ne correspondent pas!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error = "Nom d'utilisateur déjà pris!";
            } else {
                $stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->execute();

                $_SESSION['user_id'] = $this->conn->lastInsertId();
                header('Location: admin.php');
                exit();
            }
        }

        return $error;
    }

    public function login($username, $password) {
        $error = "";

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: admin.php');
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe invalide";
        }

        return $error;
    }
}

$user = new User($conn);

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['signup'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $error = $user->signUp($username, $password, $confirm_password);
    }

    if(isset($_POST['login'])) {
        $username = $_POST['login_username'];
        $password = $_POST['login_password'];
        $error = $user->login($username, $password);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Slide Navbar</title>
    <link rel="stylesheet" type="text/css" href="slide navbar style.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center">
    <div class="main">      
        <input type="checkbox" id="chk" aria-hidden="true">

        <div class="signup">
            <form method="POST">
                <label for="chk" aria-hidden="true">Inscription</label>
                <input type="text" name="username" placeholder="Nom d'utilisateur" required="">
                <input type="email" name="email" placeholder="Email" required="">
                <input type="password" name="password" placeholder="Mot de passe" required="">
                <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required="">
                <button type="submit" name="signup">S'inscrire</button>
            </form>
        </div>

        <div class="login">
            <form method="POST">
                <label for="chk" aria-hidden="true">Connexion</label>
                <input type="text" name="login_username" placeholder="Nom d'utilisateur" required="">
                <input type="password" name="login_password" placeholder="Mot de passe" required="">
                <button type="submit" name="login">Se connecter</button>
            </form>
        </div>
    </div>

    <?php if (isset($error)) echo "<p>$error</p>"; ?>
</body>
</html>

<style>
    body {
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        font-family: 'Jost', sans-serif;
      
    }

    .main {
        width: 350px;
        height: 500px;
        background: red;
        overflow: hidden;
        background: url("https://doc-08-2c-docs.googleusercontent.com/docs/securesc/68c90smiglihng9534mvqmq1946dmis5/fo0picsp1nhiucmc0l25s29respgpr4j/1631524275000/03522360960922298374/03522360960922298374/1Sx0jhdpEpnNIydS4rnN4kHSJtU1EyWka?e=view&authuser=0&nonce=gcrocepgbb17m&user=03522360960922298374&hash=tfhgbs86ka6divo3llbvp93mg4csvb38") no-repeat center/ cover;
        border-radius: 10px;
        box-shadow: 5px 20px 50px #000;
    }

    #chk {
        display: none;
    }

    .signup {
        position: relative;
        width:100%;
        height: 100%;
    }

    label {
        color: #fff;
        font-size: 2.3em;
        justify-content: center;
        display: flex;
        margin: 50px;
        font-weight: bold;
        cursor: pointer;
        transition: .5s ease-in-out;
    }

    input {
        width: 60%;
        height: 10px;
        background: #e0dede;
        justify-content: center;
        display: flex;
        margin: 20px auto;
        padding: 12px;
        border: none;
        outline: none;
        border-radius: 5px;
    }

    button {
        width: 60%;
        height: 30px;
        margin: 10px auto;
        justify-content: center;
        display: block;
        color: #fff;
        background: #573b8a;
        font-size: 1em;
        font-weight: bold;
        margin-top: 30px;
        outline: none;
        border: none;
        border-radius: 5px;
        transition: .2s ease-in;
        cursor: pointer;
    }

    button:hover {
        background: #6d44b8;
    }

    .login {
        height: 460px;
        background: #eee;
        border-radius: 60% / 10%;
        transform: translateY(-180px);
        transition: .8s ease-in-out;
    }

    .login label {
        color: #573b8a;
        transform: scale(.6);
    }

    #chk:checked ~ .login {
        transform: translateY(-500px);
    }

    #chk:checked ~ .login label {
        transform: scale(1);    
    }

    #chk:checked ~ .signup label {
        transform: scale(.6);
    }
</style>
