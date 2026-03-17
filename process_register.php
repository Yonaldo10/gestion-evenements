<?php
session_start();
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: register.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Format d'email invalide.";
        header("Location: register.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
        header("Location: register.php");
        exit;
    }

    if (strlen($password) < 8) {
        $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères.";
        header("Location: register.php");
        exit;
    }

    // Vérifier si l'email existe déjà
    $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Cet email est déjà utilisé.";
        header("Location: register.php");
        exit;
    }
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Cet email est déjà utilisé. <a href='login.php'>Connectez-vous</a> ou utilisez une autre adresse.";
        header("Location: register.php");
        exit;
    }

    // Hachage du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertion dans la base de données
    $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nom, $prenom, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Une erreur est survenue lors de l'inscription.";
        header("Location: register.php");
    }

    $stmt->close();
    $conn->close();
}
?>