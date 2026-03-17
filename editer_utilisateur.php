<?php
require_once 'check_admin.php';
require_once 'db_config.php';
session_start();

// Vérification du statut admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Initialisation des variables
$user = null;
$error = '';
$success = '';

// Récupérer l'utilisateur si ID fourni
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, nom, prenom, email, is_admin FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        $_SESSION['error'] = "Utilisateur introuvable";
        header("Location: gestion_utilisateurs.php");
        exit;
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $change_password = isset($_POST['change_password']);
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($nom) || empty($prenom) || empty($email)) {
        $error = "Tous les champs obligatoires doivent être remplis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide";
    } else {
        try {
            // Vérifier si l'email existe déjà pour un autre utilisateur
            $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                $error = "Cet email est déjà utilisé par un autre utilisateur";
            } else {
                // Mise à jour avec ou sans mot de passe
                if ($change_password && !empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, is_admin = ?, mot_de_passe = ? WHERE id = ?");
                    $stmt->bind_param("sssisi", $nom, $prenom, $email, $is_admin, $hashed_password, $user_id);
                } else {
                    $stmt = $conn->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, is_admin = ? WHERE id = ?");
                    $stmt->bind_param("sssii", $nom, $prenom, $email, $is_admin, $user_id);
                }
                
                $stmt->execute();
                $success = "Utilisateur mis à jour avec succès";
                
                // Recharger les données utilisateur
                $stmt = $conn->prepare("SELECT id, nom, prenom, email, is_admin FROM utilisateurs WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
            }
        } catch (Exception $e) {
            $error = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// Si pas d'utilisateur chargé (normalement impossible à ce stade)
if (!$user) {
    header("Location: gestion_utilisateurs.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Éditer Utilisateur</title>
    <link rel="stylesheet" href="styleadmin.css">
    <link rel="stylesheet" href="style_utilisateurs.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h2>Éditer Utilisateur</h2>
        
        <div class="admin-actions">
            <a href="gestion_utilisateurs.php">← Retour à la liste</a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="post" class="user-form">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_admin" value="1" <?= $user['is_admin'] ? 'checked' : '' ?>>
                        Administrateur
                    </label>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="change_password" id="change_password">
                        Modifier le mot de passe
                    </label>
                </div>
            </div>
            
            <div class="form-row password-fields" style="display: none;">
                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password">
                </div>
            </div>
            
            <div class="form-row">
                <button type="submit" class="btn-edit">Enregistrer les modifications</button>
            </div>
        </form>
    </div>

    <script>
    // Afficher/masquer les champs mot de passe
    document.getElementById('change_password').addEventListener('change', function() {
        document.querySelector('.password-fields').style.display = this.checked ? 'block' : 'none';
    });
    </script>
</body>
</html>