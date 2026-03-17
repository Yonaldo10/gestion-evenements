<?php
session_start();
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: login.php");
        exit;
    }

    // Recherche de l'utilisateur avec le champ is_admin
    $stmt = $conn->prepare("SELECT id, nom, prenom, email, mot_de_passe, is_admin FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Vérification du mot de passe
        if (password_verify($password, $user['mot_de_passe'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Redirection en fonction du statut admin
            if ($user['is_admin'] == 1) {
                header("Location: admin.php");
            } else {
                header("Location: espace_utilisateur.php");
            }
            exit;
        }
    }
    
    // Si on arrive ici, c'est qu'il y a une erreur
    $_SESSION['error'] = "Email ou mot de passe incorrect.";
    header("Location: login.php");
    exit;
}
?>