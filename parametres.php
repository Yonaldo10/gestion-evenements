<?php
session_start();

// Redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db_config.php';

// Initialisation des variables
$message = '';
$error = '';
$user = ['nom' => '', 'prenom' => '', 'email' => ''];

// Récupération des infos utilisateur
try {
    $stmt = $conn->prepare("SELECT nom, prenom, email FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        $error = "Utilisateur non trouvé";
    }
} catch (Exception $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Nettoyage des entrées
    $nom = trim(filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING));
    $prenom = trim(filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide";
    }

    if (empty($error)) {
        // Vérification mot de passe si changement demandé
        if (!empty($new_password)) {
            try {
                $stmt = $conn->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = ?");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $user_db = $result->fetch_assoc();

                if (!password_verify($current_password, $user_db['mot_de_passe'])) {
                    $error = "Mot de passe actuel incorrect";
                } elseif ($new_password !== $confirm_password) {
                    $error = "Les nouveaux mots de passe ne correspondent pas";
                } elseif (strlen($new_password) < 8) {
                    $error = "Le mot de passe doit contenir au moins 8 caractères";
                }
            } catch (Exception $e) {
                $error = "Erreur de vérification du mot de passe";
            }
        }

        // Mise à jour si pas d'erreur
        if (empty($error)) {
            try {
                if (!empty($new_password)) {
                    // Mise à jour avec mot de passe
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
                    $stmt->bind_param("ssssi", $nom, $prenom, $email, $hashed_password, $_SESSION['user_id']);
                } else {
                    // Mise à jour sans mot de passe
                    $stmt = $conn->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $nom, $prenom, $email, $_SESSION['user_id']);
                }

                if ($stmt->execute()) {
                    $message = "Informations mises à jour avec succès";
                    // Mise à jour de la session
                    $_SESSION['user_nom'] = $nom;
                    $_SESSION['user_prenom'] = $prenom;
                    // Recharger les données utilisateur
                    $user = ['nom' => $nom, 'prenom' => $prenom, 'email' => $email];
                } else {
                    $error = "Erreur lors de la mise à jour";
                }
            } catch (Exception $e) {
                $error = "Erreur de base de données : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres du compte</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .settings-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background: #45a049;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success {
            background: #dff0d8;
            color: #3c763d;
        }
        .error {
            background: #f2dede;
            color: #a94442;
        }
        .password-fields {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="header-center">
                <h2>Paramètres du compte</h2>
            </div>
            <div class="header-right">
                <a href="espace_utilisateur.php" class="btn">Retour</a>
                <form action="logout.php" method="post" style="display: inline;">
                    <button type="submit" class="logout-btn">Déconnexion</button>
                </form>
            </div>
        </div>
        <nav>
            <a href="index2.php">Accueil</a>
            <a href="calendrier.php">Calendrier</a>
            <a href="espace_utilisateur.php">Espace Utilisateur</a>
            <a href="parametres.php" class="active">Paramètres</a>
        </nav>
    </header>

    <main class="settings-container">
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="parametres.php" method="post">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="form-group password-fields">
                <label>Changer le mot de passe (laisser vide pour ne pas changer):</label>
                <input type="password" name="current_password" placeholder="Mot de passe actuel">
                <input type="password" name="new_password" placeholder="Nouveau mot de passe (min. 8 caractères)">
                <input type="password" name="confirm_password" placeholder="Confirmer le nouveau mot de passe">
            </div>
            
            <button type="submit" class="btn">Enregistrer les modifications</button>
        </form>
    </main>
</body>
</html>