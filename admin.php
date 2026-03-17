<?php
require_once 'check_admin.php';
require_once 'db_config.php';
session_start();

// Double vérification du statut admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Récupérer tous les événements
$query = "SELECT * FROM evenements ORDER BY date_debut DESC";
$events = $conn->query($query);

// Vérifier s'il y a des erreurs
if (!$events) {
    die("Erreur lors de la récupération des événements: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <link rel="stylesheet" href="styleadmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<script> //Confirmation avant suppression des evenements
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('a[href*="supprimer_evenement.php"]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h2>Gestion des Événements</h2>
        
        <div class="admin-actions">
            <a href="index2.php">Accueil</a>
            <a href="calendrier.php">Calendrier</a>
            <a href="gestion_utilisateurs.php">Gestion des Utilisateurs</a>
            <a href="ajout_evenement.php" class="btn">+ Ajouter un événement</a>
            <a href="admin_evenements.php">Nombre d'inscrits</a>
            <a href="stats_utilisateurs.php">Statistiques</a>
            <a href="logout.php">Déconnexion</a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Date</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($event = $events->fetch_assoc()): ?>
                <tr>
                    <td><?= $event['id'] ?></td>
                    <td><?= htmlspecialchars($event['titre']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($event['date_debut'])) ?></td>
                    <td>
                    
<?php if(!empty($event['image_url']) && file_exists($event['image_url'])): ?>
    <img src="<?= htmlspecialchars($event['image_url']) ?>" width="50" alt="<?= htmlspecialchars($event['titre']) ?>">
<?php else: ?>
    <span>Aucune image</span>
<?php endif; ?>
                    </td>
                    <td>
    <a href="editer_evenement.php?id=<?= $event['id'] ?>" class="btn-edit">
        <i class="fas fa-edit"></i> Éditer
    </a>
    <a href="supprimer_evenement.php?id=<?= $event['id'] ?>" class="btn-delete">
        <i class="fas fa-trash"></i> Supprimer
    </a>
</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>