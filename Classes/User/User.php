<?php
session_start();

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function signUp($username, $email, $password, $confirm_password) {
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
                $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
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
?>
