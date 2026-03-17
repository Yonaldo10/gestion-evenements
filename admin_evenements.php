<?php
require_once 'check_admin.php';
require_once 'db_config.php';
session_start();

// Double vérification du statut admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Requête pour récupérer les événements avec le nombre d'inscrits
$query = "SELECT e.id, e.titre, COUNT(i.id) AS nombre_inscrits 
          FROM evenements e 
          LEFT JOIN inscriptions i ON e.id = i.event_id 
          GROUP BY e.id 
          ORDER BY e.date_debut DESC";
$result = $conn->query($query);

// Vérifier s'il y a des erreurs
if (!$result) {
    die("Erreur lors de la récupération des données: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nombre d'inscrits par événement</title>
    <link rel="stylesheet" href="styleadmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h2>Nombre d'inscrits par événement</h2>
        
        <div class="admin-actions">
            <a href="index2.php">Accueil</a>
            <a href="calendrier.php">Calendrier</a>
            <a href="admin.php">Gestion des Événements</a>
            <a href="gestion_utilisateurs.php">Gestion des Utilisateurs</a>
            <a href="logout.php">Déconnexion</a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre de l'événement</th>
                    <th>Nombre d'inscrits</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['titre']) ?></td>
                    <td><?= $row['nombre_inscrits'] ?></td>
                    <td>
                        <a href="liste_inscrits.php?event_id=<?= $row['id'] ?>" class="btn-view">
                            <i class="fas fa-users"></i> Voir la liste
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>