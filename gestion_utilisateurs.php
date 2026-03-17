<?php
require_once 'check_admin.php';
require_once 'db_config.php';
session_start();

// Vérification du statut admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Récupérer tous les utilisateurs
$query = "SELECT id, nom, prenom, email, is_admin, date_inscription FROM utilisateurs ORDER BY date_inscription DESC";
$users = $conn->query($query);

// Vérifier s'il y a des erreurs
if (!$users) {
    die("Erreur lors de la récupération des utilisateurs: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="style_utilisateurs.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmation avant suppression des utilisateurs
        const deleteButtons = document.querySelectorAll('a[href*="supprimer_utilisateur.php"]');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h2>Gestion des Utilisateurs</h2>
        
        <div class="admin-actions">
            <a href="admin.php">Retour au tableau de bord</a>
            <a href="index2.php">Accueil</a>
            <a href="logout.php">Déconnexion</a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['is_admin'] ? 'Admin' : 'Utilisateur' ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($user['date_inscription'])) ?></td>
                    <td>
                        <a href="editer_utilisateur.php?id=<?= $user['id'] ?>">Éditer</a>
                        <?php if($user['id'] != $_SESSION['user_id']): // Empêche de se supprimer soi-même ?>
                            <a href="supprimer_utilisateur.php?id=<?= $user['id'] ?>">Supprimer</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="stats">
            <p>Total utilisateurs: <?= $users->num_rows ?></p>
            <p>Administrateurs: <?php 
                $admin_count = $conn->query("SELECT COUNT(*) FROM utilisateurs WHERE is_admin = 1")->fetch_row()[0];
                echo $admin_count;
            ?></p>
        </div>
    </div>
</body>
</html>